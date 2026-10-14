<?php
/**
 * Brute-force protection for the dashboard login.
 *
 * Failed attempts are counted per (IP + username) and per IP within a time window.
 * When a limit is reached, further attempts are rejected until the lock expires.
 * State lives in userdata/config/login_attempts.json; keys are hashed, so neither
 * IP addresses nor usernames are stored in plain text.
 */

const LOGIN_THROTTLE_WINDOW       = 900; // seconds (15 minutes), also the lock duration
const LOGIN_THROTTLE_MAX_PER_USER = 5;   // attempts per IP + username
const LOGIN_THROTTLE_MAX_PER_IP   = 20;  // attempts per IP across all usernames

function login_throttle_file(): string
{
    return dirname(__DIR__, 2) . '/userdata/config/login_attempts.json';
}

/**
 * @return array<string, array{key: string, limit: int}>
 */
function login_throttle_keys(string $username): array
{
    $ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user = mb_strtolower(trim($username));

    return [
        'user' => ['key' => hash('sha256', 'u|' . $ip . '|' . $user), 'limit' => LOGIN_THROTTLE_MAX_PER_USER],
        'ip'   => ['key' => hash('sha256', 'ip|' . $ip),              'limit' => LOGIN_THROTTLE_MAX_PER_IP],
    ];
}

/**
 * Opens the state file under an exclusive lock, lets $fn modify the data and writes it back.
 */
function login_throttle_update(callable $fn)
{
    $file = login_throttle_file();
    $dir  = dirname($file);
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
        error_log('Login throttle: config directory not available');
        return $fn([])[1] ?? null;
    }

    $fh = @fopen($file, 'c+');
    if (!$fh) {
        error_log('Login throttle: cannot open state file');
        return $fn([])[1] ?? null;
    }

    try {
        flock($fh, LOCK_EX);
        $raw  = stream_get_contents($fh);
        $data = json_decode($raw ?: '[]', true);
        if (!is_array($data)) {
            $data = [];
        }

        // Drop expired entries
        $now = time();
        foreach ($data as $key => $entry) {
            $expires = max((int)($entry['first'] ?? 0) + LOGIN_THROTTLE_WINDOW, (int)($entry['locked_until'] ?? 0));
            if ($expires < $now) {
                unset($data[$key]);
            }
        }

        [$data, $result] = $fn($data);

        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, json_encode($data));
        fflush($fh);
        flock($fh, LOCK_UN);

        return $result;
    } finally {
        fclose($fh);
    }
}

/**
 * Seconds until a new attempt is allowed for this username from this IP (0 = allowed).
 */
function login_throttle_retry_after(string $username): int
{
    $keys = login_throttle_keys($username);

    return (int)login_throttle_update(function (array $data) use ($keys) {
        $now  = time();
        $wait = 0;
        foreach ($keys as $k) {
            $until = (int)($data[$k['key']]['locked_until'] ?? 0);
            if ($until > $now) {
                $wait = max($wait, $until - $now);
            }
        }
        return [$data, $wait];
    });
}

/**
 * Counts one attempt (wrong username, wrong password, or a sent login code).
 */
function login_throttle_hit(string $username): void
{
    $keys = login_throttle_keys($username);

    login_throttle_update(function (array $data) use ($keys) {
        $now = time();
        foreach ($keys as $k) {
            $entry = $data[$k['key']] ?? ['count' => 0, 'first' => $now, 'locked_until' => 0];
            if ($entry['first'] + LOGIN_THROTTLE_WINDOW < $now) {
                $entry = ['count' => 0, 'first' => $now, 'locked_until' => 0];
            }
            $entry['count']++;
            if ($entry['count'] >= $k['limit']) {
                $entry['locked_until'] = $now + LOGIN_THROTTLE_WINDOW;
            }
            $data[$k['key']] = $entry;
        }
        return [$data, null];
    });

    error_log('Login: failed attempt from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
}

/**
 * Clears the per-user counter after a successful login.
 */
function login_throttle_reset(string $username): void
{
    $keys = login_throttle_keys($username);

    login_throttle_update(function (array $data) use ($keys) {
        unset($data[$keys['user']['key']]);
        return [$data, null];
    });
}
