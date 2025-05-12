<?php

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
                $albumData['Slug'] = pathinfo($file, PATHINFO_FILENAME);
    
                $albums[] = $albumData;
            }
        }
    
        return $albums;
    }
