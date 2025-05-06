<?php

    function delete_post($filename)
    {
        $dir = __DIR__ . "/../../userdata/content/essays/" . $filename;

        // Prüfen, ob das Verzeichnis existiert
        if (!is_dir($dir)) {
            return false;
        }
    
        // Rekursive Funktion zum Löschen von Inhalten
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    
        foreach($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
    
        // Schließlich das Hauptverzeichnis löschen
        return rmdir($dir);
    }