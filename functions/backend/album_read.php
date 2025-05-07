<?php

    // Funktion, die Albumnamen ausliest und als Array zurückgibt
    function getAlbumList() {
        $albumDir = __DIR__ . '/../../userdata/content/albums';
        if (!is_dir($albumDir)) {
            return [];
        }
    
        $files = scandir($albumDir);
        $albums = [];
    
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $albumData = [];
                include $albumDir . '/' . $file;
                $albumData['Name'] = $Name;
                $albumData['Description'] = $Description;
                $albumData['Password'] = $Password;
                $albumData['Images'] = $Images;
                $albumData['HeadImage'] = $HeadImage;
    
                $albums[] = $albumData;
            }
        }
    
        return $albums;
    }

    function getAlbumData($albumName) {
        $albumFile = __DIR__ . '/../../userdata/content/albums/' . preg_replace('/[^a-z0-9]/i', '_', strtolower($albumName)) . '.php';
    
        if (!file_exists($albumFile)) {
            error_log("Album not found");
            return null; // oder throw new Exception("Album not found");
        }
    
        // Variablen isoliert einlesen
        $albumData = [];
        include $albumFile;
    
        // Rückgabe aller enthaltenen Variablen
        $albumData['Name'] = $Name ?? null;
        $albumData['Description'] = $Description ?? null;
        $albumData['Password'] = $Password ?? null;
        $albumData['Images'] = $Images ?? [];
        $albumData['HeadImage'] = $HeadImage ?? null;
    
        return $albumData;
    }
    
