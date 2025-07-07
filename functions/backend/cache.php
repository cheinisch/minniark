<?php


    function clearImageCache()
    {
        $imageCacheDir = __DIR__ . '/../../cache/images/';

        if (!is_dir($imageCacheDir)) {
            error_log("Image-Cache-Verzeichnis nicht gefunden: $imageCacheDir");
            return false;
        }

        $files = glob($imageCacheDir . '*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        error_log("Image-Cache wurde geleert.");
        return true;
    }

    function rebuildCache()
    {
        generate_image_cache();
    }

    function clearTwigCache()
    {

        error_log("Start TwigCache");

        $twigCacheDir = __DIR__ . '/../../cache/pages/';

        if (!is_dir($twigCacheDir)) {
            error_log("Twig-Cache-Verzeichnis nicht gefunden: $twigCacheDir");
            return false;
        }

        $success = deleteDirectoryContents($twigCacheDir);

        if ($success) {
            error_log("Twig-Cache wurde vollständig geleert.");
        } else {
            error_log("Fehler beim Leeren des Twig-Caches.");
        }

        return $success;
    }


    function deleteDirectoryContents($dir): bool
    {
        if (!is_dir($dir)) return false;

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $fullPath = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($fullPath)) {
                // rekursiv Verzeichnisinhalt löschen
                deleteDirectoryContents($fullPath);
                rmdir($fullPath);
            } else {
                unlink($fullPath);
            }
        }

        return true;
    }

