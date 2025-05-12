<?php

    function hasBlogPosts()
    {
        $hasPosts = false;

        $postDir = __DIR__ . '/../../userdata/content/essays/';

        $folderCount = count_subfolders($postDir);

        if($folderCount > 0)
        {
            $hasPosts = true;
        }

        return $hasPosts;
    }

    function count_posts()
    {
        $postDir = __DIR__ . '/../../userdata/content/essays/';

        $folderCount = count_subfolders($postDir);


        return $folderCount;
    }


    function count_subfolders($postDir)
    {
        if (!is_dir($postDir)) {
            error_log("Verzeichnis nicht gefunden: $postDir");
            return 0;
        }
    
        $items = scandir($postDir);
    
        $folders = array_filter($items, function ($item) use ($postDir) {
            if (!is_string($item) || trim($item) === '.' || trim($item) === '..') {
                error_log("Übersprungen: >" . var_export($item, true) . "<");
                return false; // nur OK im Filter-Callback
            }
        
            $path = rtrim($postDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $item;
        
            $isDir = is_dir($path);
            error_log("Pfad geprüft: $path → " . ($isDir ? 'Ordner' : 'kein Ordner'));
        
            return $isDir;
        });
    
        return count($folders);
    }

    function getBlogPosts(): array
    {
        $baseDir = realpath(__DIR__ . '/../../userdata/content/essays/');
        if (!$baseDir) {
            return [];
        }

        $posts = [];

        foreach (glob($baseDir . '/*/data.json') as $jsonFile) {
            $dir = dirname($jsonFile);
            $slug = basename($dir); // Verzeichnisname = slug

            $json = json_decode(file_get_contents($jsonFile), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $json['slug'] = $slug;

                // Fallbacks, falls Felder fehlen
                $json['title'] = $json['title'] ?? ucfirst($slug);
                $json['date'] = $json['created_at'] ?? '1970-01-01';
                $json['excerpt'] = mb_substr(strip_tags($json['content'] ?? ''), 0, 150) . '...';

                $posts[] = $json;
            }
        }

        // Nach Datum sortieren (neueste zuerst)
        usort($posts, fn($a, $b) => strcmp($b['date'], $a['date']) * -1);
        
        return $posts;
    }

