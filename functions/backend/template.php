<?php

    function getInstalledTemplates(): array
    {
        $templateDir = __DIR__ . '/../../userdata/template';
        $templates = [];

        if (!is_dir($templateDir)) {
            return $templates;
        }

        $dirs = scandir($templateDir);

        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            $fullPath = $templateDir . '/' . $dir;
            if (!is_dir($fullPath)) {
                continue;
            }

            $jsonPath = $fullPath . '/theme.json';
            $imagePath = $fullPath . '/image.png';

            if (!file_exists($jsonPath) || !file_exists($imagePath)) {
                continue;
            }

            $jsonContent = file_get_contents($jsonPath);
            $decoded = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                continue;
            }

            // Unterstützt beide Formate:
            // 1. [ { ... } ]
            // 2. { ... }

            if (isset($decoded[0]) && is_array($decoded[0])) {
                // Format 1: Array mit einem Objekt
                $template = $decoded[0];
            } elseif (is_array($decoded) && isset($decoded['name'])) {
                // Format 2: einzelnes Objekt
                $template = $decoded;
            } else {
                continue; // Ungültiges Format
            }

            $template['image'] = realpath($imagePath);
            $template['folder'] = $dir;

            $templates[] = $template;
        }

        return $templates;
    }
