<?php
/**
 * LicenseManager for Lemon Squeezy (validate/activate/deactivate + local cache file)
 *
 * Stores license state in: /userdata/config/license.json   (relative to project root)
 *
 * Requirements:
 * - PHP cURL extension enabled
 *
 * Usage:
 *   $lm = new LicenseManager(__DIR__ . '/..', getenv('LEMON_SQUEEZY_API_KEY'));
 *   $instance = $lm->getOrCreateInstanceId(); // stable per installation
 *   $lm->saveLicenseKey($_POST['license_key']);
 *   $result = $lm->sync('activate'); // or 'validate' or 'deactivate'
 *   if ($lm->isLicensed()) { ... }
 */

final class LicenseManager
{
    private string $projectRoot;
    private string $configDir;
    private string $licenseFile;

    private string $apiBase = 'https://api.lemonsqueezy.com/v1';
    private string $apiKey;

    // Cache / Grace defaults
    private int $cacheTtlSeconds = 12 * 60 * 60;   // 12h
    private int $gracePeriodSeconds = 7 * 24 * 60 * 60; // 7d

    public function __construct(string $projectRoot, string $apiKey)
    {
        $this->projectRoot = rtrim($projectRoot, "/\\");
        $this->configDir   = $this->projectRoot . '/userdata/config';
        $this->licenseFile = $this->configDir . '/license.json';
        $this->apiKey      = trim($apiKey);

        if ($this->apiKey === '') {
            throw new RuntimeException('LicenseManager: Missing Lemon Squeezy API key.');
        }

        $this->ensureStorage();
    }

    /**
     * Create storage folder if missing.
     */
    private function ensureStorage(): void
    {
        if (!is_dir($this->configDir)) {
            if (!mkdir($this->configDir, 0775, true) && !is_dir($this->configDir)) {
                throw new RuntimeException('LicenseManager: Could not create config dir: ' . $this->configDir);
            }
        }
        if (!file_exists($this->licenseFile)) {
            $this->writeState($this->defaultState());
        }
    }

    /**
     * Get current state from file.
     */
    public function getState(): array
    {
        $raw = @file_get_contents($this->licenseFile);
        if ($raw === false || trim($raw) === '') {
            return $this->defaultState();
        }
        $data = json_decode($raw, true);
        return is_array($data) ? array_replace_recursive($this->defaultState(), $data) : $this->defaultState();
    }

    /**
     * Save license key (does not call API).
     */
    public function saveLicenseKey(string $licenseKey): void
    {
        $licenseKey = trim($licenseKey);
        if ($licenseKey === '') {
            throw new InvalidArgumentException('LicenseManager: Empty license key.');
        }
        $state = $this->getState();
        $state['license_key'] = $licenseKey;
        // reset last sync markers
        $state['last_checked_at'] = null;
        $state['last_result'] = null;
        $state['valid'] = false;
        $state['status'] = null;

        $this->writeState($state);
    }

    /**
     * Remove license key and state.
     */
    public function clear(): void
    {
        $this->writeState($this->defaultState());
    }

    /**
     * Returns a stable instance id (saved in license file).
     * You can override by passing your own domain-based instance in sync().
     */
    public function getOrCreateInstanceId(): string
    {
        $state = $this->getState();
        if (!empty($state['instance_id'])) {
            return (string)$state['instance_id'];
        }

        // generate stable random id
        $id = $this->uuidV4();
        $state['instance_id'] = $id;
        $this->writeState($state);
        return $id;
    }

    /**
     * Main method: calls Lemon Squeezy and updates local state.
     *
     * $mode:
     *  - validate:   checks key validity
     *  - activate:   activates for this instance (also validates)
     *  - deactivate: deactivates for this instance
     *
     * Returns: array with keys: ok(bool), mode, message, api_response(optional)
     */
    public function sync(string $mode = 'validate', ?string $instanceName = null, bool $force = false): array
    {
        $mode = strtolower(trim($mode));
        if (!in_array($mode, ['validate', 'activate', 'deactivate'], true)) {
            throw new InvalidArgumentException('LicenseManager: Invalid sync mode: ' . $mode);
        }

        $state = $this->getState();
        $key = trim((string)$state['license_key']);
        if ($key === '') {
            return $this->fail($mode, 'No license key saved.');
        }

        $instanceName = $instanceName ?: $this->getOrCreateInstanceId();

        // Cache check for validate/activate (deactivate should generally not be cached)
        if (!$force && $mode !== 'deactivate' && $this->isCacheFresh($state)) {
            return [
                'ok' => true,
                'mode' => $mode,
                'message' => 'Using cached license status.',
                'cached' => true,
                'state' => $this->publicState($state),
            ];
        }

        $endpoint = match ($mode) {
            'validate'   => $this->apiBase . '/licenses/validate',
            'activate'   => $this->apiBase . '/licenses/activate',
            'deactivate' => $this->apiBase . '/licenses/deactivate',
        };

        $payload = [
            'license_key'   => $key,
            'instance_name' => $instanceName,
        ];

        try {
            $api = $this->request($endpoint, $payload);
        } catch (Throwable $e) {
            // if API fails, decide based on grace period
            $state = $this->applyApiFailure($state, $e->getMessage());
            $this->writeState($state);

            if ($this->withinGracePeriod($state)) {
                return [
                    'ok' => true,
                    'mode' => $mode,
                    'message' => 'API error, but license is within grace period.',
                    'error' => $e->getMessage(),
                    'state' => $this->publicState($state),
                ];
            }
            return $this->fail($mode, 'API error and grace period exceeded: ' . $e->getMessage(), $this->publicState($state));
        }

        // Update state with API result
        $state = $this->applyApiResult($state, $api, $mode, $instanceName);
        $this->writeState($state);

        // Determine outcome
        $ok = (bool)($state['valid'] ?? false);
        $msg = $ok ? 'License ok.' : 'License invalid.';

        // Special case: deactivate successful should result in not licensed
        if ($mode === 'deactivate') {
            $ok = true;
            $msg = 'Deactivated (local state updated).';
        }

        return [
            'ok' => $ok,
            'mode' => $mode,
            'message' => $msg,
            'api_response' => $api,
            'state' => $this->publicState($state),
        ];
    }

    /**
     * True if currently licensed (valid + status active + not expired if expires_at set).
     */
    public function isLicensed(): bool
    {
        $s = $this->getState();
        if (empty($s['valid'])) return false;

        // Accept only active unless you want to support other statuses
        $status = (string)($s['status'] ?? '');
        if ($status !== 'active') return false;

        $expiresAt = $s['expires_at'] ?? null;
        if ($expiresAt) {
            $ts = strtotime((string)$expiresAt);
            if ($ts !== false && $ts < time()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return minimal info for UI.
     */
    public function getSummary(): array
    {
        $s = $this->getState();
        return $this->publicState($s);
    }

    /**
     * Configure cache/grace behavior.
     */
    public function setCacheTtlSeconds(int $seconds): void
    {
        $this->cacheTtlSeconds = max(0, $seconds);
    }

    public function setGracePeriodSeconds(int $seconds): void
    {
        $this->gracePeriodSeconds = max(0, $seconds);
    }

    // ------------------------
    // Internal helpers
    // ------------------------

    private function defaultState(): array
    {
        return [
            'license_key' => null,
            'instance_id' => null,

            'valid' => false,
            'status' => null,
            'expires_at' => null,
            'activation_limit' => null,
            'activation_usage' => null,

            'last_checked_at' => null,    // unix timestamp
            'last_ok_at' => null,         // unix timestamp (last successful valid check)
            'last_error' => null,

            'last_mode' => null,
            'last_result' => null,        // raw API response (can be big)
        ];
    }

    private function publicState(array $s): array
    {
        // Do not leak full key in UI
        $masked = $this->maskKey((string)($s['license_key'] ?? ''));
        return [
            'license_key' => $masked ?: null,
            'instance_id' => $s['instance_id'] ?? null,
            'valid' => (bool)($s['valid'] ?? false),
            'status' => $s['status'] ?? null,
            'expires_at' => $s['expires_at'] ?? null,
            'activation_limit' => $s['activation_limit'] ?? null,
            'activation_usage' => $s['activation_usage'] ?? null,
            'last_checked_at' => $s['last_checked_at'] ?? null,
            'last_ok_at' => $s['last_ok_at'] ?? null,
            'last_error' => $s['last_error'] ?? null,
            'last_mode' => $s['last_mode'] ?? null,
        ];
    }

    private function writeState(array $state): void
    {
        $json = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('LicenseManager: Failed to encode license state JSON.');
        }

        // Atomic write
        $tmp = $this->licenseFile . '.tmp';
        if (@file_put_contents($tmp, $json) === false) {
            throw new RuntimeException('LicenseManager: Failed to write temp license file: ' . $tmp);
        }
        if (!@rename($tmp, $this->licenseFile)) {
            @unlink($tmp);
            throw new RuntimeException('LicenseManager: Failed to replace license file: ' . $this->licenseFile);
        }
    }

    private function isCacheFresh(array $state): bool
    {
        $checked = (int)($state['last_checked_at'] ?? 0);
        if ($checked <= 0) return false;
        if ($this->cacheTtlSeconds <= 0) return false;
        return (time() - $checked) < $this->cacheTtlSeconds;
    }

    private function withinGracePeriod(array $state): bool
    {
        $okAt = (int)($state['last_ok_at'] ?? 0);
        if ($okAt <= 0) return false;
        if ($this->gracePeriodSeconds <= 0) return false;
        return (time() - $okAt) <= $this->gracePeriodSeconds;
    }

    private function applyApiFailure(array $state, string $error): array
    {
        $state['last_checked_at'] = time();
        $state['last_error'] = $error;
        $state['last_mode'] = $state['last_mode'] ?? null;
        // do not flip valid to false immediately if you want grace behavior; keep it as-is
        return $state;
    }

    private function applyApiResult(array $state, array $api, string $mode, string $instanceName): array
    {
        $state['last_checked_at'] = time();
        $state['last_error'] = null;
        $state['last_mode'] = $mode;
        $state['last_result'] = $api;

        if ($mode === 'deactivate') {
            // After deactivation we consider it not licensed locally
            $state['valid'] = false;
            $state['status'] = 'deactivated';
            $state['expires_at'] = null;
            $state['activation_limit'] = null;
            $state['activation_usage'] = null;
            return $state;
        }

        // LemonSqueezy usually returns: { valid: bool, license_key: {...}, ... }
        $valid = (bool)($api['valid'] ?? false);
        $state['valid'] = $valid;

        $lk = $api['license_key'] ?? [];
        if (is_array($lk)) {
            $state['status'] = $lk['status'] ?? $state['status'];
            $state['expires_at'] = $lk['expires_at'] ?? $state['expires_at'];
            $state['activation_limit'] = $lk['activation_limit'] ?? $state['activation_limit'];
            $state['activation_usage'] = $lk['activation_usage'] ?? $state['activation_usage'];
        }

        if ($valid && (($state['status'] ?? null) === 'active')) {
            $state['last_ok_at'] = time();
        }

        // Keep instance id consistent if user passes custom instance name
        if (empty($state['instance_id'])) {
            $state['instance_id'] = $instanceName;
        }

        return $state;
    }

    private function request(string $url, array $payload): array
    {
        $body = json_encode($payload);
        if ($body === false) {
            throw new RuntimeException('LicenseManager: Failed to encode request JSON.');
        }

        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('LicenseManager: Failed to init cURL.');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => $body,
        ]);

        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resp === false) {
            throw new RuntimeException('LicenseManager: cURL error: ' . ($err ?: 'unknown'));
        }

        $data = json_decode($resp, true);
        if (!is_array($data)) {
            throw new RuntimeException('LicenseManager: Invalid JSON response (HTTP ' . $code . ').');
        }

        if ($code >= 400) {
            $msg = $data['error'] ?? ($data['message'] ?? 'HTTP ' . $code);
            throw new RuntimeException('LicenseManager: API error: ' . (is_string($msg) ? $msg : ('HTTP ' . $code)));
        }

        return $data;
    }

    private function fail(string $mode, string $message, ?array $state = null): array
    {
        $out = ['ok' => false, 'mode' => $mode, 'message' => $message];
        if ($state !== null) $out['state'] = $state;
        return $out;
    }

    private function uuidV4(): string
    {
        $data = random_bytes(16);
        // set version to 0100
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        // set bits 6-7 to 10
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        $hex = bin2hex($data);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    private function maskKey(string $key): string
    {
        $key = trim($key);
        if ($key === '') return '';
        // keep last 4 chars
        $last = substr($key, -4);
        return str_repeat('•', max(0, strlen($key) - 4)) . $last;
    }

    public function getRawLicenseKey(): string
    {
        $s = $this->getState();
        return trim((string)($s['license_key'] ?? ''));
    }

    /**
     * Deaktiviert die aktuelle Instanz (wenn möglich) und löscht danach die lokale Lizenzdatei (state).
     */
    public function deactivateAndClear(?string $instanceName = null): array
    {
        $state = $this->getState();
        $key = trim((string)($state['license_key'] ?? ''));
        if ($key === '') {
            $this->clear();
            return ['ok' => true, 'mode' => 'deactivate', 'message' => 'No key present. Local state cleared.'];
        }

        $instanceName = $instanceName ?: ($state['instance_id'] ?? null) ?: $this->getOrCreateInstanceId();

        // Deactivate should run "force" (no cache)
        $res = $this->sync('deactivate', (string)$instanceName, true);

        // Egal wie das API-Ergebnis ist: lokal entfernen (du willst: wenn entfernt, ist keiner vorhanden)
        $this->clear();

        // Optional: Res zurückgeben (für UI/Logs)
        return $res;
    }

}
