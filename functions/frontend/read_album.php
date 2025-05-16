<?php

//require_once __DIR__ . '/../../vendor/autoload.php';

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

    function getGalleryAlbums(): array
    {
        $albumDir = realpath(__DIR__ . '/../../userdata/content/albums/');
        if (!$albumDir) return [];

        $albums = [];

        $parsedown = new Parsedown();

        foreach (glob($albumDir . '/*.php') as $file) {
            // Neues Scope für Variablen erzwingen
            $Name = $Description = $HeadImage = '';
            $slug = basename($file, '.php');

            include $file;

            $albums[] = [
                'slug' => $slug,
                'title' => $Name,
                'description' => $parsedown->text($Description),
                'cover' => get_cacheimage($HeadImage),
            ];
        }

        return $albums;
    }

    function readGalleryAlbum(string $slug, array $settings): array
    {
        $albumFile = realpath(__DIR__ . '/../../userdata/content/albums/' . $slug . '.php');
        if (!$albumFile || !file_exists($albumFile)) {
            return [];
        }

        $Name = $Description = $Password = $HeadImage = '';
        $Images = [];
        include $albumFile;

        $parsedown = new Parsedown();

        $imageSize = $settings['image_size'] ?? 'M';
        $imageDir = realpath(__DIR__ . '/../../userdata/content/images/');
        $cacheDir = '/cache/images/';

        $imageList = [];

        foreach ($Images as $img) {
            $jsonFile = $imageDir . '/' . pathinfo($img, PATHINFO_FILENAME) . '.json';
            if (file_exists($jsonFile)) {
                $meta = json_decode(file_get_contents($jsonFile), true);
                if (json_last_error() === JSON_ERROR_NONE && !empty($meta['guid'])) {
                    $guid = $meta['guid'];
                    $cachedImagePath = $cacheDir . $guid . '_' . $imageSize . '.jpg';
                    $imageUrl = '/i/' . rawurlencode($img);

                    $imageList[] = [
                        'file' => $cachedImagePath,   // z. B. /cache/images/abc123_M.jpg
                        'url'  => $imageUrl,          // z. B. /i/IMG_1234.jpg
                        'title' => $meta['title'] ?? '',
                    ];
                }
            }
        }

        // Cover-Bild
        $cover = null;
        if (!empty($HeadImage)) {
            $coverJson = $imageDir . '/' . pathinfo($HeadImage, PATHINFO_FILENAME) . '.json';
            if (file_exists($coverJson)) {
                $meta = json_decode(file_get_contents($coverJson), true);
                if (json_last_error() === JSON_ERROR_NONE && !empty($meta['guid'])) {
                    $cover = $cacheDir . $meta['guid'] . '_' . $imageSize . '.jpg';
                }
            }
        }

        return [
            'slug' => $slug,
            'title' => $Name,
            'description' => $parsedown->text($Description),
            'images' => $imageList,
            'cover' => $cover,
        ];
    }