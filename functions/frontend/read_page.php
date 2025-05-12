<?php

    function getPageList()
    {
        $baseDir = __DIR__ . '/../../userdata/content/pages/';
        $posts = [];
        $debug = false;

        if (!is_dir($baseDir)) {
            error_log("Verzeichnis nicht gefunden: $baseDir");
            return [];
        }

        $folders = scandir($baseDir);

        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $folderPath = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $folder;
            if (!is_dir($folderPath)) continue;

            $jsonFiles = glob($folderPath . DIRECTORY_SEPARATOR . '*.json');

            foreach ($jsonFiles as $filePath) {
                if (!file_exists($filePath)) continue;

                $jsonContent = file_get_contents($filePath);
                if (!$jsonContent) continue;

                $data = json_decode($jsonContent, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("JSON-Fehler in $filePath: " . json_last_error_msg());
                    continue;
                }



                // Kürze den String
                $data['content'] = mb_substr($data['content'], 0, 500) . '...';

                $data['source_path'] = basename(dirname($filePath));
                $posts[] = $data;
            }
        }

        usort($posts, function ($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });

        return $posts;
    }