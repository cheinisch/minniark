<?php
/**
 * Session handling, CSRF protection and access helpers shared by the
 * dashboard (functions/backend) and the JSON API (functions/api).
 *
 * Token transport (any one is accepted):
 *   - POST field      csrf_token          (added to forms by dashboard/js/csrf.js)
 *   - HTTP header     X-CSRF-Token        (added to fetch/XHR by dashboard/js/csrf.js)
 *   - query parameter csrf_token          (added to endpoint links by dashboard/js/csrf.js)
 */

if (!function_exists('minniark_session_start')) {

    /**
     * Starts the session once, with hardened cookie flags.
     */
    function minniark_session_start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (!headers_sent()) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => minniark_is_https(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            ini_set('session.use_strict_mode', '1');
        }

        session_start();
    }

    function minniark_is_https(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? null) == 443)
            || (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    }

    /**
     * Returns the CSRF token of the current session, creating it if needed.
     */
    function csrf_token(): string
    {
        minniark_session_start();

        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Hidden input for server-rendered forms.
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Meta tag plus the client script that attaches the token to forms,
     * fetch/XHR requests and endpoint links. Echo it inside <head>.
     */
    function csrf_head(): string
    {
        $token  = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
        $script = 'js/csrf.js';
        $file   = __DIR__ . '/../dashboard/js/csrf.js';
        if (is_file($file)) {
            $script .= '?v=' . filemtime($file);
        }

        return '<meta name="csrf-token" content="' . $token . '">' . "\n"
             . '  <script src="' . $script . '"></script>' . "\n";
    }

    function csrf_is_valid(): bool
    {
        minniark_session_start();

        $expected = $_SESSION['csrf_token'] ?? '';
        if (!is_string($expected) || $expected === '') {
            return false;
        }

        $sent = $_POST['csrf_token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? $_GET['csrf_token']
            ?? '';

        return is_string($sent) && hash_equals($expected, $sent);
    }

    /**
     * Aborts with 403 unless the request carries a valid token.
     * Use it in endpoints that change data on GET requests.
     */
    function csrf_require(): void
    {
        if (csrf_is_valid()) {
            return;
        }

        error_log('CSRF check failed for ' . ($_SERVER['REQUEST_METHOD'] ?? '?') . ' ' . ($_SERVER['SCRIPT_NAME'] ?? '?'));
        minniark_deny(403, 'Invalid or missing security token. Please reload the page and try again.');
    }

    /**
     * Requires a valid token for every state-changing HTTP method.
     */
    function csrf_protect(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (!in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            csrf_require();
        }
    }

    function minniark_is_admin(): bool
    {
        minniark_session_start();
        return !empty($_SESSION['loggedin']) && !empty($_SESSION['admin']);
    }

    /**
     * True when the request expects JSON (API calls, fetch with JSON body).
     */
    function minniark_wants_json(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $type   = $_SERVER['CONTENT_TYPE'] ?? '';
        $script = $_SERVER['SCRIPT_NAME'] ?? '';

        return str_contains($accept, 'application/json')
            || str_contains($type, 'application/json')
            || (bool)preg_match('#/api/[^/]+\.php$#', $script);
    }

    /**
     * Ends the request with an error status, as JSON or plain text.
     */
    function minniark_deny(int $status, string $message): never
    {
        http_response_code($status);

        if (minniark_wants_json()) {
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            echo json_encode(['success' => false, 'error' => $message]);
        } else {
            if (!headers_sent()) {
                header('Content-Type: text/plain; charset=utf-8');
            }
            echo $message;
        }

        exit;
    }

    /**
     * True once the installer has created settings and at least one account.
     */
    function minniark_is_installed(): bool
    {
        $base = dirname(__DIR__) . '/userdata/config';
        return is_file($base . '/settings.yml') && (bool)glob($base . '/user/*.yml');
    }
}
