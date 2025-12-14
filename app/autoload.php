<?php
/**
 * Simple class autoloader for /app/classes
 */

spl_autoload_register(function (string $class) {

    // Keine Namespaces → Klassenname = Dateiname
    $file = __DIR__ . '/classes/' . $class . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});
