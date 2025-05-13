<?php

    function get_homedata(): array
    {
        $path = realpath(__DIR__ . '/../../userdata/config/home.json');

        if ($path && file_exists($path)) {
            $json = file_get_contents($path);
            $data = json_decode($json, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }

        // Rückgabe eines leeren Arrays im Fehlerfall
        return [];
    }
