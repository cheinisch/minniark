<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

/**
 * app/classes/LicenseManager.php
 *
 * Minniark LicenseManager (supports BOTH providers via proxy):
 * - LemonSqueezy proxy:  https://api.minniark.com/v1/data/lemonsqueezy
 * - Creem proxy:         https://api.minniark.com/v1/data/creem
 *
 * KEY FIXES (requested):
 * 1) License removal triggers BEST-EFFORT DEACTIVATE (using correct identifier), then clears local state.
 * 2) Switching to a DIFFERENT license key:
 *    - best-effort deactivate OLD key (with OLD instance identifiers)
 *    - clears cache (IMPORTANT: removes Creem instance_id)
 *    - ensures NEW key does not reuse old instance_id
 * 3) Creem behavior:
 *    - activate uses uuid (instance_name)
 *    - validate/deactivate should use cached instance_id (lki_...)
 *    - if validate is called without instance_id, auto-activate once to obtain instance_id
 *
 * Storage:
 * - /userdata/config/settings.yml
 *     - license:   <string> (removed when empty)
 *     - uuid:      <string> (installation id)
 *     - provider:  <string> (optional; 'creem'|'lemonsqueezy')
 * - /userdata/config/.env
 *     - MINNIARK_PROXY_KEY=<string>
 *     - MINNIARK_NO_PROXY=1   (optional)
 * - /userdata/config/license.json
 *     - valid_until (ISO8601)
 *     - cached_at   (ISO8601)
 *     - data        (validate result)
 *     - instance_id (Creem: stored after activate, used for validate/deactivate)
 */
final class LicenseManager
{
    private string $root;
    private string $configDir;
    private string $settingsFile;
    private string $envFile;
    private string $cacheFile;

    private string $proxyBaseUrl;
    private string $provider; // 'creem'|'lemonsqueezy'

    private ?string $licenseKey = null;
    private ?string $uuid = null;

    private ?string $proxyKey = null;
    private bool $noProxy = false;

    private ?string $lastError = null;

    public function __construct(
        ?string $projectRoot = null,
        string $proxyBaseUrl = 'https://api.minniark.com/v1/data/lemonsqueezy',
        ?string $provider = null
    ) {
        $this->root = rtrim($projectRoot ?: $this->detectProjectRoot(), '/');
        $this->configDir    = $this->root . '/userdata/config';
        $this->settingsFile = $this->configDir . '/settings.yml';
        $this->envFile      = $this->configDir . '/.env';
        $this->cacheFile    = $this->configDir . '/license.json';

        $this->proxyBaseUrl = rtrim($proxyBaseUrl, '/');

        $this->ensureConfigDir();
        $this->loadEnv();
        $this->loadSettings(); // may set $this->licenseKey, $this->uuid, $this->provider

        // Provider selection priority:
        // 1) constructor $provider
        // 2) settings.yml provider
        // 3) detect from URL
        if ($provider !== null && trim($provider) !== '') {
            $p = strtolower(trim($provider));
            $this->provider = in_array($p, ['creem', 'lemonsqueezy'], true)
                ? $p
                : $this->detectProviderFromUrl($this->proxyBaseUrl);
        } else {
            $this->provider = $this->provider ?: $this->detectProviderFromUrl($this->proxyBaseUrl);
        }
    }

    /* =========================================================
     * Public API
     * ======================================================= */

    public function isLicensed(): bool
    {
        $s = $this->getSummary();
        return (bool)($s['valid'] ?? false);
    }

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
            'provider' => $this->provider,
            'uuid' => $this->uuid,
            'instance_id' => $this->getCachedInstanceId(),
        ];

        if ($rawKey === '') {
            return $summary;
        }

        try {
            $info = $this->getLicenseInformation();

            $summary['valid']  = $this->normalizeValid($info);
            $summary['status'] = $summary['valid'] ? 'valid' : 'invalid';

            $summary['expires_at'] = $this->extractExpiresAt($info);

            $summary['activation_limit'] =
                $info['activation_limit'] ?? $info['timesActivatedMax'] ?? $info['activationLimit'] ?? null;

            $summary['activation_usage'] =
                $info['activation'] ?? $info['timesActivated'] ?? $info['activation_usage'] ?? null;

            $cache = $this->readCache();
            if (is_array($cache) && isset($cache['valid_until'])) {
                $summary['cache_valid_until'] = $cache['valid_until'];
            }

            if (!empty($this->lastError)) {
                $summary['last_error'] = $this->lastError;
            }
        } catch (\Throwable $e) {
            $msg = $e->getMessage();

            if ($this->noProxy || str_contains($msg, 'MINNIARK_NO_PROXY=1')) {
                $this->lastError = $this->humanizeError($msg);
                $summary['status'] = 'no_proxy';
                $summary['valid'] = false;
                $summary['last_error'] = $this->lastError;

                $cache = $this->readCache();
                if (is_array($cache) && isset($cache['valid_until'])) {
                    $summary['cache_valid_until'] = $cache['valid_until'];
                    $cachedData = $cache['data'] ?? null;
                    if (is_array($cachedData)) {
                        $summary['valid']  = $this->normalizeValid($cachedData);
                        $summary['status'] = $summary['valid'] ? 'valid_cached' : 'invalid_cached';
                        $summary['expires_at'] = $this->extractExpiresAt($cachedData);
                    }
                }

                return $summary;
            }

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

    public function getLastError(): ?string { return $this->lastError; }
    public function getRawLicenseKey(): string { return trim((string)$this->licenseKey); }
    public function getUuid(): ?string { return $this->uuid; }
    public function getProxyKey(): ?string { return $this->proxyKey; }
    public function isNoProxy(): bool { return $this->noProxy; }
    public function getProvider(): string { return $this->provider; }

    /**
     * Change provider and clear cache (so instance_id from old provider cannot leak).
     */
    public function setProvider(string $provider): void
    {
        $p = strtolower(trim($provider));
        if (!in_array($p, ['creem', 'lemonsqueezy'], true)) {
            throw new \InvalidArgumentException('Invalid provider: ' . $provider);
        }

        $this->provider = $p;
        $this->saveSettingsProvider($p);
        $this->clearCache();
    }

    /**
     * Save license key with correct behavior:
     * - If removed (empty): best-effort deactivate old, then clear local state + cache + proxyKey
     * - If changed (different key): best-effort deactivate old, clear cache (removes Creem instance_id),
     *   save new key, keep uuid (installation id), do NOT reuse old instance_id
     * - If same: just save and clear cache
     */
    public function saveLicenseKey(string $key): void
    {
        $newKey = trim($key);
        $oldKey = $this->getRawLicenseKey();

        // nothing to do
        if ($newKey === '' && $oldKey === '') {
            $this->licenseKey = null;
            $this->uuid = null;
            $this->saveSettings();
            $this->clearCache();
            $this->removeProxyKey();
            return;
        }

        // License removed
        if ($newKey === '') {
            $this->bestEffortDeactivateKey($oldKey);
            $this->licenseKey = null;
            $this->uuid = null;
            $this->saveSettings();
            $this->clearCache();
            $this->removeProxyKey();
            return;
        }

        // License changed (old -> new)
        if ($oldKey !== '' && !hash_equals($oldKey, $newKey)) {
            $this->bestEffortDeactivateKey($oldKey);
        }

        // Set new key (keep uuid as "installation id")
        $this->licenseKey = $newKey;

        // Ensure uuid exists for this installation
        if ($this->uuid === null || trim($this->uuid) === '') {
            $this->uuid = $this->generateUuid();
        }

        // IMPORTANT: new key must not reuse old cache/instance_id
        $this->clearCache();

        $this->saveSettings();
    }

    /**
     * Best-effort deactivate + clear local state.
     */
    public function deactivateAndClear(): array
    {
        $result = ['ok' => true];

        $oldKey = $this->getRawLicenseKey();
        if ($oldKey !== '') {
            try {
                $result = $this->sync('deactivate', null, true);
            } catch (\Throwable $e) {
                $this->lastError = $this->humanizeError($e->getMessage());
                $result = ['ok' => false, 'error' => $this->lastError];
            }
        }

        $this->licenseKey = null;
        $this->uuid = null;
        $this->saveSettings();
        $this->clearCache();
        $this->removeProxyKey();

        return $result;
    }

    /**
     * Validate/activate/deactivate via proxy (provider-aware).
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

        // validate cache
        if (!$force && $action === 'validate') {
            $cached = $this->readCache();
            if ($cached && $this->cacheStillValid($cached)) {
                return $cached['data'] ?? [];
            }
        }

        $uuid = $this->getOrCreateUuid();

        if (!$this->proxyKey) {
            $this->registerProxyKey();
        }

        // Determine identifier to send as instance_name (contract stays stable)
        $instanceIdent = $instanceName ?: $uuid;

        if ($this->provider === 'creem') {
            if ($action === 'activate') {
                // activate uses uuid
                $instanceIdent = $uuid;
            } else {
                // validate/deactivate should use instance_id, if known
                $cachedInstanceId = $this->getCachedInstanceId();

                // If validate is requested and we have no instance_id yet, auto-activate once
                if ($action === 'validate' && $cachedInstanceId === null) {
                    $actRes = $this->proxyCallWithRetry('activate', [
                        'minniark' => 'minniark',
                        'license_key' => $this->getRawLicenseKey(),
                        'instance_name' => $uuid,
                    ]);

                    $newInstanceId = $this->extractCreemInstanceId($actRes);
                    if ($newInstanceId !== null) {
                        $this->saveCachedInstanceId($newInstanceId);
                        $cachedInstanceId = $newInstanceId;
                    }
                }

                if ($cachedInstanceId !== null) {
                    $instanceIdent = $cachedInstanceId;
                }
            }
        }

        $payload = [
            'minniark' => 'minniark',
            'license_key' => $this->getRawLicenseKey(),
            'instance_name' => $instanceIdent,
        ];

        $res = $this->proxyCallWithRetry($action, $payload);

        // Creem: store instance_id after activate
        if ($this->provider === 'creem' && $action === 'activate') {
            $newInstanceId = $this->extractCreemInstanceId($res);
            if ($newInstanceId !== null) {
                $this->saveCachedInstanceId($newInstanceId);
            }
        }

        if ($action === 'validate') {
            $this->writeValidateCache($res);
        }

        return $res;
    }

    /* =========================================================
     * Internals: deactivate old key safely
     * ======================================================= */

    /**
     * Deactivate a specific license key using identifiers we have.
     * Never throws. Does NOT change local licenseKey.
     *
     * Creem: prefer cached instance_id, fallback uuid
     * LS: uuid
     */
    private function bestEffortDeactivateKey(string $licenseKey): void
    {
        $licenseKey = trim($licenseKey);
        if ($licenseKey === '') return;

        if ($this->noProxy) {
            $this->lastError = 'Proxy disabled (MINNIARK_NO_PROXY=1). Deactivation skipped.';
            return;
        }

        // must have uuid to have any chance
        $uuid = $this->uuid;
        if ($uuid === null || trim($uuid) === '') {
            return;
        }

        try {
            if (!$this->proxyKey) {
                $this->registerProxyKey();
            }

            $instanceIdent = $uuid;

            if ($this->provider === 'creem') {
                $cachedInstanceId = $this->getCachedInstanceId();
                if ($cachedInstanceId !== null) {
                    $instanceIdent = $cachedInstanceId;
                }
            }

            $this->proxyCallWithRetry('deactivate', [
                'minniark' => 'minniark',
                'license_key' => $licenseKey,
                'instance_name' => $instanceIdent,
            ]);
        } catch (\Throwable $e) {
            $this->lastError = $this->humanizeError($e->getMessage());
        }
    }

    /* =========================================================
     * License info with caching
     * ======================================================= */

    private function getLicenseInformation(): array
    {
        $cached = $this->readCache();
        if ($cached && $this->cacheStillValid($cached)) {
            return $cached['data'] ?? [];
        }

        return $this->sync('validate', null, true);
    }

    private function writeValidateCache(array $validateResult): void
    {
        $validUntilTs = strtotime('+14 days');

        $expiresAt = $this->extractExpiresAt($validateResult);
        if ($expiresAt !== null) {
            $expTs = strtotime($expiresAt);
            if ($expTs !== false) {
                $validUntilTs = min($validUntilTs, $expTs);
            }
        }

        $cache = $this->readCache() ?? [];
        $cache['valid_until'] = date('c', $validUntilTs);
        $cache['cached_at']   = date('c');
        $cache['data']        = $validateResult;

        // preserve instance_id if present
        if (isset($cache['instance_id'])) {
            $cache['instance_id'] = trim((string)$cache['instance_id']);
            if ($cache['instance_id'] === '') unset($cache['instance_id']);
        }

        $this->writeCache($cache);
    }

    /* =========================================================
     * Proxy calls + retry
     * ======================================================= */

    private function proxyCallWithRetry(string $action, array $payload): array
    {
        $tries = 0;
        $last = null;

        while ($tries < 2) {
            $tries++;

            try {
                return $this->proxyCall($action, $payload);
            } catch (\RuntimeException $e) {
                $last = $e;

                if (str_contains($e->getMessage(), 'proxy_key_invalid') && $tries < 2) {
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
            $snippet = mb_substr((string)$resp, 0, 300);
            throw new \RuntimeException("Proxy/API error (HTTP {$code}): {$snippet}");
        }

        if ($code >= 400) {
            $errCode = (string)($decoded['error'] ?? '');
            $msg     = $decoded['message'] ?? $decoded['error'] ?? 'Unknown error';
            if (is_array($msg)) $msg = implode('; ', array_map('strval', $msg));
            $msg = (string)$msg;

            if ($errCode !== '') {
                throw new \RuntimeException("Proxy/API error (HTTP {$code}): {$errCode} {$msg}");
            }
            throw new \RuntimeException("Proxy/API error (HTTP {$code}): {$msg}");
        }

        return $decoded;
    }

    private function registerProxyKey(): void
    {
        if ($this->noProxy) {
            throw new \RuntimeException('Proxy disabled (MINNIARK_NO_PROXY=1). Cannot register proxy key.');
        }

        $uuid = $this->getOrCreateUuid();

        $url = $this->proxyBaseUrl . '/register';

        // Keep proxy contract stable
        $payload = [
            'minniark' => 'minniark',
            'instance_id' => $uuid,
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
            $msg = $decoded['message'] ?? $decoded['error'] ?? 'unknown';
            if (is_array($msg)) $msg = implode('; ', array_map('strval', $msg));
            throw new \RuntimeException("LicenseManager: register failed (HTTP {$code}): " . (string)$msg);
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
        $this->provider = '';

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
            $pr = isset($data['provider']) ? strtolower(trim((string)$data['provider'])) : '';

            $this->licenseKey = ($lk === '') ? null : $lk;
            $this->uuid       = ($uu === '') ? null : $uu;

            if (in_array($pr, ['creem', 'lemonsqueezy'], true)) {
                $this->provider = $pr;
            }

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
                // ignore
            }
        }

        if ($this->licenseKey === null || trim((string)$this->licenseKey) === '') {
            $this->licenseKey = null;
            $this->uuid = null;
        }

        if ($this->licenseKey !== null) {
            $data['license'] = $this->licenseKey;
        } else {
            unset($data['license']);
        }

        if ($this->uuid !== null && trim($this->uuid) !== '') {
            $data['uuid'] = $this->uuid;
        } else {
            unset($data['uuid']);
        }

        if (!empty($this->provider) && in_array($this->provider, ['creem', 'lemonsqueezy'], true)) {
            $data['provider'] = $this->provider;
        } else {
            unset($data['provider']);
        }

        file_put_contents($this->settingsFile, Yaml::dump($data, 4, 2));
    }

    private function saveSettingsProvider(string $provider): void
    {
        $provider = strtolower(trim($provider));
        if (!in_array($provider, ['creem', 'lemonsqueezy'], true)) return;

        $data = [];
        if (is_file($this->settingsFile)) {
            try {
                $parsed = Yaml::parseFile($this->settingsFile);
                if (is_array($parsed)) $data = $parsed;
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $data['provider'] = $provider;
        file_put_contents($this->settingsFile, Yaml::dump($data, 4, 2));
    }

    private function getOrCreateUuid(): string
    {
        if ($this->uuid !== null && trim($this->uuid) !== '') {
            return $this->uuid;
        }

        if ($this->licenseKey === null || $this->getRawLicenseKey() === '') {
            throw new \RuntimeException('Cannot create UUID without license');
        }

        $this->uuid = $this->generateUuid();
        $this->saveSettings();
        return $this->uuid;
    }

    /* =========================================================
     * .env storage
     * ======================================================= */

    private function loadEnv(): void
    {
        $envNoProxy = (string)($_ENV['MINNIARK_NO_PROXY'] ?? getenv('MINNIARK_NO_PROXY') ?? '');
        $this->noProxy = $this->toBool($envNoProxy);

        $envProxyKey = (string)($_ENV['MINNIARK_PROXY_KEY'] ?? getenv('MINNIARK_PROXY_KEY') ?? '');
        $envProxyKey = trim($envProxyKey);
        if ($envProxyKey !== '') {
            $this->proxyKey = $envProxyKey;
        }

        if (!is_file($this->envFile)) return;

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
                    if ($k === 'MINNIARK_PROXY_KEY') continue;

                    $linesOut[] = $line;
                }
            }
        }

        $linesOut[] = 'MINNIARK_PROXY_KEY=' . $proxyKey;

        $this->ensureConfigDir();
        file_put_contents($this->envFile, implode("\n", $linesOut) . "\n");
    }

    private function removeProxyKey(): void
    {
        $this->proxyKey = null;

        if (!is_file($this->envFile)) return;

        $lines = file($this->envFile, FILE_IGNORE_NEW_LINES);
        if (!is_array($lines)) return;

        $out = [];
        foreach ($lines as $line) {
            $trim = trim($line);
            if (str_starts_with($trim, 'MINNIARK_PROXY_KEY=')) continue;
            $out[] = $line;
        }

        file_put_contents($this->envFile, implode("\n", $out) . "\n");
    }

    /* =========================================================
     * Cache helpers
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

    private function getCachedInstanceId(): ?string
    {
        $cache = $this->readCache();
        if (!is_array($cache)) return null;

        $id = trim((string)($cache['instance_id'] ?? ''));
        return $id !== '' ? $id : null;
    }

    private function saveCachedInstanceId(string $instanceId): void
    {
        $instanceId = trim($instanceId);
        if ($instanceId === '') return;

        $cache = $this->readCache() ?? [];
        $cache['instance_id'] = $instanceId;

        if (!isset($cache['cached_at'])) {
            $cache['cached_at'] = date('c');
        }
        if (!isset($cache['valid_until'])) {
            $cache['valid_until'] = date('c', strtotime('+1 day'));
        }

        $this->writeCache($cache);
    }

    /* =========================================================
     * Provider normalization
     * ======================================================= */

    private function normalizeValid(array $info): bool
    {
        $status = strtolower(trim((string)($info['status'] ?? '')));
        if ($status !== '') {
            if (in_array($status, ['active', 'valid', 'ok'], true)) return true;
            if (in_array($status, ['inactive', 'invalid', 'expired', 'revoked'], true)) return false;
        }

        if (array_key_exists('valid', $info)) return (bool)$info['valid'];
        if (array_key_exists('is_valid', $info)) return (bool)$info['is_valid'];

        $exp = $this->extractExpiresAt($info);
        if ($exp !== null) {
            $ts = strtotime($exp);
            if ($ts !== false) return $ts > time();
        }

        return false;
    }

    private function extractExpiresAt(array $info): ?string
    {
        $exp =
            $info['expires_at']
            ?? $info['expiresAt']
            ?? $info['expired_date']
            ?? $info['expires']
            ?? null;

        if (!is_string($exp)) return null;

        $exp = trim($exp);
        return $exp !== '' ? $exp : null;
    }

    private function extractCreemInstanceId(array $res): ?string
    {
        $candidates = [
            $res['instance']['id'] ?? null,
            $res['data']['instance']['id'] ?? null,
            $res['instance_id'] ?? null,
            $res['data']['instance_id'] ?? null,
        ];

        foreach ($candidates as $c) {
            if (is_string($c) && trim($c) !== '') {
                return trim($c);
            }
        }
        return null;
    }

    private function detectProviderFromUrl(string $url): string
    {
        $u = strtolower($url);
        if (str_contains($u, 'creem')) return 'creem';
        if (str_contains($u, 'lemonsqueezy') || str_contains($u, 'lemon')) return 'lemonsqueezy';
        return 'lemonsqueezy';
    }

    /* =========================================================
     * Helpers
     * ======================================================= */

    private function detectProjectRoot(): string
    {
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
        if (str_contains($msg, 'missing CREEM_API_KEY')) {
            return 'Proxy misconfigured: CREEM_API_KEY missing on proxy server.';
        }
        return $msg;
    }
}
