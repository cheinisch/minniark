<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

/**
 * app/classes/LicenseManager.php
 *
 * Minniark LicenseManager (Proxy + Lemon Squeezy via api.minniark.com)
 *
 * Storage:
 * - /userdata/config/settings.yml
 *     - license: <string>   (empty string treated as "removed")
 *     - uuid:    <string>   (generated if missing when license exists)
 * - /userdata/config/.env
 *     - MINNIARK_PROXY_KEY=<string>
 *     - MINNIARK_NO_PROXY=1   (optional)
 * - /userdata/config/license.json
 *     - valid_until (ISO8601)
 *     - cached_at   (ISO8601)
 *     - data        (validate result)
 *
 * Proxy:
 * - Base: https://api.minniark.com/v1/data/lemonsqueezy
 * - POST /register    -> returns {"proxy_key":"..."} (requires {"minniark":"minniark"})
 * - POST /validate    -> requires X-Minniark-Proxy-Key header
 * - POST /activate    -> requires X-Minniark-Proxy-Key header
 * - POST /deactivate  -> requires X-Minniark-Proxy-Key header
 *
 * Client payload for proxy calls:
 * {
 *   "minniark": "minniark",
 *   "license_key": "...",
 *   "instance_name": "uuid"
 * }
 *
 * Behavior:
 * - getSummary(): never throws; returns 'last_error' for dashboard
 * - isLicensed(): never throws; uses getSummary()
 * - Cache:
 *   - only for validate
 *   - valid_until = min(now+14days, license expiry if provided)
 * - Retry:
 *   - on proxy_key_invalid -> registers new key and retries exactly once
 * - Removal:
 *   - if license removed/empty -> best-effort deactivate old + remove uuid + remove proxy env + delete cache
 * - No Proxy mode:
 *   - MINNIARK_NO_PROXY=1 (in userdata/config/.env or real ENV)
 *   - validate: uses cache if valid, otherwise returns clean error (no proxy call)
 *   - activate/deactivate: blocked with clean error (no proxy call)
 */
final class LicenseManager
{
    private string $root;
    private string $configDir;
    private string $settingsFile;
    private string $envFile;
    private string $cacheFile;

    private string $proxyBaseUrl;

    private ?string $licenseKey = null;
    private ?string $uuid = null;
    private ?string $proxyKey = null;

    private bool $noProxy = false;

    private ?string $lastError = null;

    /**
     * @param string|null $projectRoot  If null, auto-detect from this file location.
     * @param string      $proxyBaseUrl Default: https://api.minniark.com/v1/data/lemonsqueezy
     */
    public function __construct(?string $projectRoot = null, string $proxyBaseUrl = 'https://api.minniark.com/v1/data/testkey')
    {
        $this->root = rtrim($projectRoot ?: $this->detectProjectRoot(), '/');
        $this->configDir    = $this->root . '/userdata/config';
        $this->settingsFile = $this->configDir . '/settings.yml';
        $this->envFile      = $this->configDir . '/.env';
        $this->cacheFile    = $this->configDir . '/license.json';

        $this->proxyBaseUrl = rtrim($proxyBaseUrl, '/');

        $this->ensureConfigDir();
        $this->loadEnv();
        $this->loadSettings();
    }

    /* =========================================================
     * Public API (safe for dashboard)
     * ======================================================= */

    /** Never throws; returns false on any error */
    public function isLicensed(): bool
    {
        $summary = $this->getSummary();
        return (bool)($summary['valid'] ?? false);
    }

    /** Never throws; best-effort status for UI */
    public function getSummary(): array
    {
        $rawKey = $this->getRawLicenseKey();

        $summary = [
            'valid' => false,
            'status' => $rawKey === '' ? 'no_key' : 'unknown',
            'expires_at' => null,
            'activation_limit' => null,
            'activation_usage' => null,
            'masked_key' => $this->maskKey($rawKey),
            'last_error' => null,
            'no_proxy' => $this->noProxy,
            'cache_valid_until' => null,
        ];

        if ($rawKey === '') {
            return $summary;
        }

        try {
            $info = $this->getLicenseInformation();

            $summary['valid'] = (bool)($info['valid'] ?? false);
            $summary['status'] = $summary['valid'] ? 'valid' : 'invalid';

            $summary['expires_at'] =
                $info['expires_at']
                ?? $info['expiresAt']
                ?? $info['expired_date']
                ?? $info['expires']
                ?? null;

            $summary['activation_limit'] = $info['timesActivatedMax'] ?? $info['activation_limit'] ?? null;
            $summary['activation_usage'] = $info['timesActivated'] ?? $info['activation_usage'] ?? null;

            $cache = $this->readCache();
            if (is_array($cache) && isset($cache['valid_until'])) {
                $summary['cache_valid_until'] = $cache['valid_until'];
            }

            if (!empty($this->lastError)) {
                $summary['last_error'] = $this->lastError;
            }
        } catch (\Throwable $e) {
            $msg = $e->getMessage();

            // NO_PROXY ist kein "Error", sondern ein Zustand
            if ($this->noProxy || str_contains($msg, 'MINNIARK_NO_PROXY=1')) {
                $this->lastError = $this->humanizeError($msg);
                $summary['status'] = 'no_proxy';
                $summary['valid'] = false;
                $summary['last_error'] = $this->lastError;

                // falls Cache existiert: zeigen, wann er ausläuft
                $cache = $this->readCache();
                if (is_array($cache) && isset($cache['valid_until'])) {
                    $summary['cache_valid_until'] = $cache['valid_until'];

                    // optional: wenn Cache-Daten valid=true sind, kannst du "cached" anzeigen
                    $cachedData = $cache['data'] ?? null;
                    if (is_array($cachedData) && isset($cachedData['valid'])) {
                        $summary['valid'] = (bool)$cachedData['valid'];
                        $summary['status'] = $summary['valid'] ? 'valid_cached' : 'invalid_cached';
                        $summary['expires_at'] =
                            $cachedData['expires_at']
                            ?? $cachedData['expiresAt']
                            ?? $cachedData['expired_date']
                            ?? $cachedData['expires']
                            ?? null;
                    }
                }

                return $summary;
            }

            // echte Fehler
            $this->lastError = $this->humanizeError($msg);
            $summary['status'] = 'error';
            $summary['valid'] = false;
            $summary['last_error'] = $this->lastError;

            $cache = $this->readCache();
            if (is_array($cache) && isset($cache['valid_until'])) {
                $summary['cache_valid_until'] = $cache['valid_until'];
            }
        }


        return $summary;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function getRawLicenseKey(): string
    {
        return trim((string)$this->licenseKey);
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getProxyKey(): ?string
    {
        return $this->proxyKey;
    }

    public function isNoProxy(): bool
    {
        return $this->noProxy;
    }

    /**
     * Save new license key into settings.yml and clear validate cache.
     * Empty string will remove license + uuid in settings.yml.
     */
    public function saveLicenseKey(string $key): void
    {
        $k = trim($key);

        // If key is empty -> treat as removed
        if ($k === '') {
            $this->licenseKey = null;
            $this->uuid = null;
        } else {
            $this->licenseKey = $k;
            // uuid will be created lazily when needed
        }

        $this->saveSettings();
        $this->clearCache();
    }

    /**
     * Full cleanup when license removed:
     * - best-effort deactivate old license (if possible)
     * - remove license + uuid from settings
     * - clear cache
     * - remove proxy key from userdata/config/.env
     */
    public function deactivateAndClear(): array
    {
        $result = ['ok' => true];

        // take snapshot before clearing
        $oldKey = $this->getRawLicenseKey();

        if ($oldKey !== '') {
            // best-effort deactivate (might be blocked by NO_PROXY)
            try {
                $result = $this->sync('deactivate', null, true);
            } catch (\Throwable $e) {
                $this->lastError = $this->humanizeError($e->getMessage());
                $result = ['ok' => false, 'error' => $this->lastError];
            }
        }

        // clear local state
        $this->licenseKey = null;
        $this->uuid = null;
        $this->saveSettings();
        $this->clearCache();
        $this->removeProxyKey(); // IMPORTANT: this deletes MINNIARK_PROXY_KEY line

        return $result;
    }

    /**
     * Convenience for your settings_save.php flow:
     * Call this after settings.yml was saved.
     *
     * - If $newKey is empty: same as deactivateAndClear (best-effort)
     * - Else: updates internal key and clears cache
     */
    public function onLicenseSaved(string $newKey): void
    {
        $newKey = trim($newKey);

        if ($newKey === '') {
            // we want to deactivate using the old key if we still have it
            // load settings first to know old key
            $this->loadSettings();
            $this->deactivateAndClear();
            return;
        }

        // set key (and keep uuid if exists)
        $this->licenseKey = $newKey;
        if ($this->uuid !== null && trim($this->uuid) === '') {
            $this->uuid = null;
        }
        $this->saveSettings();
        $this->clearCache();
    }

    /**
     * Activate a (new) license key immediately (best-effort).
     * Returns proxy response array.
     */
    public function activate(string $licenseKey): array
    {
        $licenseKey = trim($licenseKey);
        if ($licenseKey === '') {
            throw new \RuntimeException('No license key set');
        }

        // Update memory but don't force-save here (caller usually already saved settings.yml)
        $this->licenseKey = $licenseKey;

        // ensure uuid exists when activating
        $this->getOrCreateUuid();

        return $this->sync('activate', null, true);
    }

    /* =========================================================
     * Core: Proxy action (activate/deactivate/validate)
     * ======================================================= */

    /**
     * Generic proxy action (activate/deactivate/validate).
     * - ensures uuid exists (if license exists)
     * - ensures proxy key exists (register if missing)
     * - on proxy_key_invalid: register again and retry exactly once
     *
     * $force=true ignores validate cache.
     */
    public function sync(string $action, ?string $instanceName = null, bool $force = false): array
    {
        $action = strtolower(trim($action));
        if (!in_array($action, ['validate', 'activate', 'deactivate'], true)) {
            throw new \InvalidArgumentException("Invalid action: {$action}");
        }

        if ($this->getRawLicenseKey() === '') {
            throw new \RuntimeException('No license key set');
        }

        // ✅ NO-PROXY MODE: stop BEFORE any uuid/register/proxy call
        if ($this->noProxy) {
            if ($action === 'validate') {
                $cached = $this->readCache();
                if ($cached && $this->cacheStillValid($cached)) {
                    return $cached['data'] ?? [];
                }
                throw new \RuntimeException('Proxy disabled (MINNIARK_NO_PROXY=1) and no valid cache available.');
            }
            throw new \RuntimeException('Proxy disabled (MINNIARK_NO_PROXY=1). Action not possible: ' . $action);
        }

        // validate can use cache (unless forced)
        if (!$force && $action === 'validate') {
            $cached = $this->readCache();
            if ($cached && $this->cacheStillValid($cached)) {
                return $cached['data'] ?? [];
            }
        }

        $uuid = $instanceName ?: $this->getOrCreateUuid();

        // Ensure proxy key exists locally; register if missing
        if (!$this->proxyKey) {
            $this->registerProxyKey();
        }

        $payload = [
            'minniark' => 'minniark',
            'license_key' => $this->getRawLicenseKey(),
            'instance_name' => $uuid,
        ];

        $res = $this->proxyCallWithRetry($action, $payload);

        // write cache only for validate
        if ($action === 'validate') {
            $this->writeValidateCache($res);
        }

        return $res;
    }

    /* =========================================================
     * Internals: License Information with caching
     * ======================================================= */

    private function getLicenseInformation(): array
    {
        if ($this->getRawLicenseKey() === '') {
            throw new \RuntimeException('No license key set');
        }

        // use cache first (even in no-proxy mode)
        $cached = $this->readCache();
        if ($cached && $this->cacheStillValid($cached)) {
            return $cached['data'] ?? [];
        }

        // force validate (also refresh cache)
        return $this->sync('validate', null, true);
    }

    private function writeValidateCache(array $validateResult): void
    {
        $validUntilTs = strtotime('+14 days');

        // Reduce cache ttl to license expiry if proxy returns expiry date
        $expiresAt = $validateResult['expires_at'] ?? $validateResult['expired_date'] ?? $validateResult['expiresAt'] ?? null;
        if (is_string($expiresAt) && trim($expiresAt) !== '') {
            $expTs = strtotime($expiresAt);
            if ($expTs !== false) {
                $validUntilTs = min($validUntilTs, $expTs);
            }
        }

        $cache = [
            'valid_until' => date('c', $validUntilTs),
            'cached_at' => date('c'),
            'data' => $validateResult,
        ];

        $this->writeCache($cache);
    }

    /* =========================================================
     * Internals: Proxy calls + retry
     * ======================================================= */

    private function proxyCallWithRetry(string $action, array $payload): array
    {
        $tries = 0;
        $last = null;

        while ($tries < 2) { // first try + exactly one retry
            $tries++;

            try {
                return $this->proxyCall($action, $payload);
            } catch (\RuntimeException $e) {
                $last = $e;

                if (str_contains($e->getMessage(), 'proxy_key_invalid') && $tries < 2) {
                    // re-register and retry once
                    $this->registerProxyKey();
                    continue;
                }

                throw $e;
            }
        }

        throw $last ?: new \RuntimeException('Proxy call failed');
    }

    private function proxyCall(string $action, array $payload): array
    {
        $url = $this->proxyBaseUrl . '/' . $action;

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        // for validate/activate/deactivate the proxy expects the key
        if (!empty($this->proxyKey)) {
            $headers[] = 'X-Minniark-Proxy-Key: ' . $this->proxyKey;
        }

        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        if ($body === false) {
            throw new \RuntimeException('Failed to encode JSON payload');
        }

        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('curl_init failed');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => $body,
        ]);

        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resp === false) {
            throw new \RuntimeException('Proxy request failed: ' . ($err ?: 'unknown'));
        }

        $decoded = json_decode((string)$resp, true);
        if (!is_array($decoded)) {
            $snippet = mb_substr((string)$resp, 0, 200);
            throw new \RuntimeException("Proxy/API error (HTTP {$code}): {$snippet}");
        }

        if ($code >= 400) {
            $errCode = (string)($decoded['error'] ?? '');
            $msg     = (string)($decoded['message'] ?? $decoded['error'] ?? 'Unknown error');

            if ($errCode !== '') {
                throw new \RuntimeException("Proxy/API error (HTTP {$code}): {$errCode}");
            }
            throw new \RuntimeException("Proxy/API error (HTTP {$code}): {$msg}");
        }

        return $decoded;
    }

    /**
     * Register / refresh proxy key (stored in userdata/config/.env).
     * No proxy key header needed for register call.
     */
    private function registerProxyKey(): void
    {
        // If noProxy is on, never try to register
        if ($this->noProxy) {
            throw new \RuntimeException('Proxy disabled (MINNIARK_NO_PROXY=1). Cannot register proxy key.');
        }

        // ensure uuid exists for register payload (proxy may use it)
        if ($this->uuid === null && $this->getRawLicenseKey() !== '') {
            // create uuid if we have a license (safe)
            $this->uuid = $this->generateUuid();
            $this->saveSettings();
        }

        $url = $this->proxyBaseUrl . '/register';

        $payload = [
            'minniark' => 'minniark',
            'uuid' => $this->uuid ?: '',
            'site_url' => $this->guessSiteUrl(),
        ];

        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        if ($body === false) {
            throw new \RuntimeException('LicenseManager: register failed: invalid_payload');
        }

        $ch = curl_init($url);
        if ($ch === false) {
            throw new \RuntimeException('LicenseManager: register failed: curl_init');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => $body,
        ]);

        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resp === false) {
            throw new \RuntimeException('LicenseManager: register failed: ' . ($err ?: 'unknown'));
        }

        $decoded = json_decode((string)$resp, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException("LicenseManager: register failed (HTTP {$code}): invalid_json");
        }

        if ($code >= 400) {
            $msg = (string)($decoded['error'] ?? $decoded['message'] ?? 'unknown');
            throw new \RuntimeException("LicenseManager: register failed (HTTP {$code}): {$msg}");
        }

        $newKey = trim((string)($decoded['proxy_key'] ?? ''));
        if ($newKey === '') {
            throw new \RuntimeException("LicenseManager: register failed (HTTP {$code}): missing_proxy_key");
        }

        $this->proxyKey = $newKey;
        $this->saveEnvKey($newKey);
    }

    /* =========================================================
     * Settings + UUID
     * ======================================================= */

    private function loadSettings(): void
    {
        if (!is_file($this->settingsFile)) {
            $this->licenseKey = null;
            $this->uuid = null;
            return;
        }

        try {
            $data = Yaml::parseFile($this->settingsFile);
            if (!is_array($data)) $data = [];

            $lk = isset($data['license']) ? trim((string)$data['license']) : '';
            $uu = isset($data['uuid']) ? trim((string)$data['uuid']) : '';

            $this->licenseKey = ($lk === '') ? null : $lk;
            $this->uuid       = ($uu === '') ? null : $uu;

            // If license missing -> uuid should not exist
            if ($this->licenseKey === null) {
                $this->uuid = null;
            }
        } catch (\Throwable $e) {
            $this->lastError = 'Failed to parse settings.yml';
            $this->licenseKey = null;
            $this->uuid = null;
        }
    }

    private function saveSettings(): void
    {
        $data = [];
        if (is_file($this->settingsFile)) {
            try {
                $parsed = Yaml::parseFile($this->settingsFile);
                if (is_array($parsed)) $data = $parsed;
            } catch (\Throwable $e) {
                // ignore, rewrite
            }
        }

        // normalize: if license removed -> remove uuid too
        if ($this->licenseKey === null || trim((string)$this->licenseKey) === '') {
            $this->licenseKey = null;
            $this->uuid = null;
        }

        if ($this->licenseKey !== null) {
            $data['license'] = $this->licenseKey;
        } else {
            // keep as empty string? better remove entirely
            unset($data['license']);
        }

        if ($this->uuid !== null && trim($this->uuid) !== '') {
            $data['uuid'] = $this->uuid;
        } else {
            unset($data['uuid']);
        }

        file_put_contents($this->settingsFile, Yaml::dump($data, 4, 2));
    }

    private function getOrCreateUuid(): string
    {
        if ($this->uuid !== null && trim($this->uuid) !== '') {
            return $this->uuid;
        }

        // only create uuid if license exists
        if ($this->licenseKey === null || $this->getRawLicenseKey() === '') {
            throw new \RuntimeException('Cannot create UUID without license');
        }

        $this->uuid = $this->generateUuid();
        $this->saveSettings();
        return $this->uuid;
    }

    /* =========================================================
     * .env storage (proxy key + no-proxy flag)
     * ======================================================= */

    private function loadEnv(): void
    {
        // Prefer real ENV as base (works if server sets env vars)
        $envNoProxy = (string)($_ENV['MINNIARK_NO_PROXY'] ?? getenv('MINNIARK_NO_PROXY') ?? '');
        $this->noProxy = $this->toBool($envNoProxy);

        $envProxyKey = (string)($_ENV['MINNIARK_PROXY_KEY'] ?? getenv('MINNIARK_PROXY_KEY') ?? '');
        $envProxyKey = trim($envProxyKey);
        if ($envProxyKey !== '') {
            $this->proxyKey = $envProxyKey;
        }

        if (!is_file($this->envFile)) {
            return;
        }

        $lines = file($this->envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!is_array($lines)) return;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;

            $pos = strpos($line, '=');
            if ($pos === false) continue;

            $k = trim(substr($line, 0, $pos));
            $v = $this->stripQuotes(trim(substr($line, $pos + 1)));

            if ($k === 'MINNIARK_PROXY_KEY') {
                $this->proxyKey = $v;
            } elseif ($k === 'MINNIARK_NO_PROXY') {
                $this->noProxy = $this->toBool($v);
            }
        }
    }

    private function saveEnvKey(string $proxyKey): void
    {
        $linesOut = [];

        if (is_file($this->envFile)) {
            $lines = file($this->envFile, FILE_IGNORE_NEW_LINES);
            if (is_array($lines)) {
                foreach ($lines as $line) {
                    $trim = trim($line);
                    if ($trim === '' || str_starts_with($trim, '#') || !str_contains($trim, '=')) {
                        $linesOut[] = $line;
                        continue;
                    }

                    [$k] = explode('=', $trim, 2);
                    $k = trim($k);
                    if ($k === 'MINNIARK_PROXY_KEY') {
                        continue; // replace
                    }
                    $linesOut[] = $line;
                }
            }
        }

        $linesOut[] = 'MINNIARK_PROXY_KEY=' . $proxyKey;

        $this->ensureConfigDir();
        file_put_contents($this->envFile, implode("\n", $linesOut) . "\n");
    }

    private function saveNoProxyFlag(bool $enabled): void
    {
        $linesOut = [];

        if (is_file($this->envFile)) {
            $lines = file($this->envFile, FILE_IGNORE_NEW_LINES);
            if (is_array($lines)) {
                foreach ($lines as $line) {
                    $trim = trim($line);
                    if ($trim === '' || str_starts_with($trim, '#') || !str_contains($trim, '=')) {
                        $linesOut[] = $line;
                        continue;
                    }

                    [$k] = explode('=', $trim, 2);
                    $k = trim($k);
                    if ($k === 'MINNIARK_NO_PROXY') {
                        continue; // replace
                    }
                    $linesOut[] = $line;
                }
            }
        }

        $linesOut[] = 'MINNIARK_NO_PROXY=' . ($enabled ? '1' : '0');

        $this->ensureConfigDir();
        file_put_contents($this->envFile, implode("\n", $linesOut) . "\n");
    }

    /* =========================================================
     * Cache (license.json)
     * ======================================================= */

    private function readCache(): ?array
    {
        if (!is_file($this->cacheFile)) return null;

        $raw = file_get_contents($this->cacheFile);
        if ($raw === false || trim($raw) === '') return null;

        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }

    private function writeCache(array $cache): void
    {
        $this->ensureConfigDir();
        file_put_contents(
            $this->cacheFile,
            json_encode($cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n"
        );
    }

    private function clearCache(): void
    {
        if (is_file($this->cacheFile)) {
            @unlink($this->cacheFile);
        }
    }

    private function cacheStillValid(array $cache): bool
    {
        $vu = (string)($cache['valid_until'] ?? '');
        if ($vu === '') return false;

        $ts = strtotime($vu);
        if ($ts === false) return false;

        return $ts > time();
    }

    /* =========================================================
     * Helpers
     * ======================================================= */

    private function detectProjectRoot(): string
    {
        // this file: <root>/app/classes/LicenseManager.php
        return dirname(__DIR__, 2);
    }

    private function ensureConfigDir(): void
    {
        if (!is_dir($this->configDir)) {
            @mkdir($this->configDir, 0775, true);
        }
    }

    private function generateUuid(): string
    {
        $hex = bin2hex(random_bytes(16));
        return substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4) . '-' . substr($hex, 16, 4) . '-' . substr($hex, 20);
    }

    private function maskKey(string $key): string
    {
        $key = trim($key);
        if ($key === '') return '';
        if (strlen($key) <= 8) return str_repeat('*', strlen($key));
        return substr($key, 0, 4) . str_repeat('*', max(0, strlen($key) - 8)) . substr($key, -4);
    }

    private function stripQuotes(string $v): string
    {
        $v = trim($v);
        if (strlen($v) >= 2) {
            $f = $v[0];
            $l = $v[strlen($v) - 1];
            if (($f === '"' && $l === '"') || ($f === "'" && $l === "'")) {
                return substr($v, 1, -1);
            }
        }
        return $v;
    }

    private function toBool(string $v): bool
    {
        $v = strtolower(trim($v));
        return in_array($v, ['1', 'true', 'yes', 'on'], true);
    }

    private function guessSiteUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = (string)($_SERVER['HTTP_HOST'] ?? '');
        if ($host === '') return '';
        return $scheme . '://' . $host;
    }

    private function humanizeError(string $msg): string
    {
        if (str_contains($msg, 'Proxy disabled (MINNIARK_NO_PROXY=1)')) {
            return 'License check disabled (MINNIARK_NO_PROXY=1).';
        }
        if (str_contains($msg, 'license_key not found') || str_contains($msg, 'license_key_not_found')) {
            return 'License key not found (check your key).';
        }
        if (str_contains($msg, 'proxy_key_invalid')) {
            return 'Proxy key invalid. Re-registering proxy key failed or proxy rejected it.';
        }
        if (str_contains($msg, 'missing MINNIARK_PROXY_KEY')) {
            return 'Proxy misconfigured: MINNIARK_PROXY_KEY missing on proxy server.';
        }
        if (str_contains($msg, 'missing LEMON_SQUEEZY_API_KEY')) {
            return 'Proxy misconfigured: LEMON_SQUEEZY_API_KEY missing on proxy server.';
        }
        return $msg;
    }

    /**
     * Removes MINNIARK_PROXY_KEY from /userdata/config/.env
     */
    private function removeProxyKey(): void
    {
        $this->proxyKey = null;

        if (!is_file($this->envFile)) {
            return;
        }

        $lines = file($this->envFile, FILE_IGNORE_NEW_LINES);
        if (!is_array($lines)) return;

        $out = [];
        foreach ($lines as $line) {
            $trim = trim($line);
            if (str_starts_with($trim, 'MINNIARK_PROXY_KEY=')) {
                continue;
            }
            $out[] = $line;
        }

        file_put_contents($this->envFile, implode("\n", $out) . "\n");
    }
}
