<?php
declare(strict_types=1);

require_once __DIR__ . '/../../functions/function_backend.php';

/**
 * Logging zentral bündeln.
 * Schreibt strukturierte Einträge ins PHP-Error-Log.
 */
function log_debug(string $message, array $context = []): void {
    // Bei Bedarf Pfad setzen:
    // ini_set('error_log', __DIR__ . '/../../logs/app.log');
    ini_set('log_errors', '1');
    ini_set('display_errors', '0');

    if (!empty($context)) {
        $message .= ' | ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    error_log($message);
}

$saveAction = filter_input(INPUT_GET, 'save', FILTER_UNSAFE_RAW) ?? null;
$method     = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Fall 1: Aktiv-Flag speichern
if ($method === 'POST' && $saveAction === 'active') {
    // nav_enabled kann "1" oder "0" sein
    $navEnabledRaw = filter_input(INPUT_POST, 'nav_enabled', FILTER_UNSAFE_RAW);
    $navEnabled    = ($navEnabledRaw === '1' || $navEnabledRaw === 1);

    $data = ['custom_nav' => $navEnabled];

    log_debug('Saving settings: custom_nav toggle received', [
        'nav_enabled_raw' => $navEnabledRaw,
        'custom_nav'      => $data['custom_nav'],
    ]);

    try {
        $return = saveSettings($data);

        log_debug('saveSettings() result', ['success' => (bool)$return]);

        if ($return) {
            header('Location: ../dashboard-menu.php', true, 302);
            exit;
        } else {
            // Kein Redirect, aber auch keine Ausgabe — nur Log
            log_debug('saveSettings() failed, staying on page');
        }
    } catch (Throwable $e) {
        log_debug('Exception in saveSettings()', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ]);
        // Optional: HTTP-Status setzen, aber keine Body-Ausgabe
        http_response_code(500);
        exit;
    }
}

// Fall 2: Menü speichern
if ($method === 'POST' && $saveAction === 'menu') {
    $menuJson = $_POST['menu_json'] ?? '[]';
    $menu     = json_decode($menuJson, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        log_debug('Invalid menu_json payload', [
            'json_error' => json_last_error_msg(),
            'payload'    => mb_substr($menuJson, 0, 5000), // truncate for safety
        ]);
        http_response_code(400);
        exit;
    }

    if (!is_array($menu)) {
        log_debug('menu_json decoded but not an array', ['decoded_type' => gettype($menu)]);
        http_response_code(400);
        exit;
    }

    log_debug('Saving navigation YAML via save_navigation()', [
        'items_count' => count($menu),
    ]);

    try {
        $return = save_navigation($menu); // speichert in YAML

        log_debug('save_navigation() result', ['success' => (bool)$return]);

        if ($return) {
            header('Location: ../dashboard-menu.php', true, 302);
            exit;
        } else {
            log_debug('save_navigation() failed, staying on page');
            http_response_code(500);
            exit;
        }
    } catch (Throwable $e) {
        log_debug('Exception in save_navigation()', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ]);
        http_response_code(500);
        exit;
    }
}

// Optional: Unerwartete Requests protokollieren (ohne Ausgabe)
log_debug('No matching route', [
    'method' => $method,
    'save'   => $saveAction,
]);
// Hier bewusst keine Ausgabe und kein Redirect.
// Falls du hier eine 405/400 willst:
http_response_code(204); // No Content
exit;
