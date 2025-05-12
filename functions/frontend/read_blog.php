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

    function getBlogPosts()
    {
        
    }