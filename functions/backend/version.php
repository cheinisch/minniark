<?php

    function getVersion(): array
    {
        $versionFile = __DIR__ . '/../../VERSION';
        $appVersion = is_readable($versionFile) ? trim(file_get_contents($versionFile)) : 'unknown';

        return [
            'Application'       => 'Minniark',
            'App Version'       => $appVersion,
            'Operating System'  => php_uname('s'),
            'PHP Version'       => PHP_VERSION,
            'Webserver'         => $_SERVER['SERVER_SOFTWARE'] ?? 'CLI or unknown',
            'Loaded Extensions' => get_loaded_extensions(),
        ];
    }