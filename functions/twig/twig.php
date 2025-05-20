<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$templateDir = __DIR__ . '/../../userdata/template/' . $theme;
$navItems = buildNavigation($templateDir);
    
$data = [
    'title' => ucfirst($uri) ?: 'Home',
    'site_title' => $settings['site_title'] ?? 'Image Portfolio',
    'theme' => $theme,
    'themepath' => "/userdata/template/{$theme}",
    'settings' => $settings,
    'navItems' => $navItems,
    'current_path' => $current_path,
];

// Einzelne statische Seite per Slug: /p/<slug>
if (preg_match('#^p/([\w\-]+)$#', $uri, $matches)) {
    $slug = $matches[1]; // z. B. "impressum"
    $pageDir = realpath(__DIR__ . '/../../userdata/content/pages/' . $slug);

    if ($pageDir && file_exists($pageDir . '/data.json')) {
        $json = json_decode(file_get_contents($pageDir . '/data.json'), true);

        // use Parsedown;
        $Parsedown = new Parsedown();

        if (json_last_error() === JSON_ERROR_NONE) {
            $json['content'] = $Parsedown->text($json['content']);
            $json['cover'] = get_cacheimage($json['cover']);
            $data['page'] = $json;            
            $data['title'] = $json['title'];
            echo $twig->render('page.twig', $data);
            exit;
        }
    }
}


// Dynamischer Blogpost: /blog/<slug>
// Einzelner Blog-Post anhand des Slugs aus dem Verzeichnisnamen
if (preg_match('#^blog/([\w\-]+)$#', $uri, $matches)) {
    $slug = $matches[1]; // z. B. "lorem-ipsum"
    $jsonPath = realpath(__DIR__ . "/../../userdata/content/essays/$slug/data.json");

    if ($jsonPath && file_exists($jsonPath)) {
        $json = json_decode(file_get_contents($jsonPath), true);

        if (json_last_error() === JSON_ERROR_NONE) {
            
            $parsedown = new Parsedown();
            $json['slug'] = $slug;
            $json['content'] = $parsedown->text($json['content'] ?? '');
            $json['cover'] = get_cacheimage($json['cover']);

            $data['post'] = $json;
            $data['title'] = $json['title'] ?? ucfirst($slug);
            

            echo $twig->render('post.twig', $data);
            exit;
        }
    }

    // Fehler: Datei nicht vorhanden oder ungültig
    http_response_code(404);
    echo $twig->render('404.twig', $data);
    exit;
}


if (preg_match('#^gallery/([\w\-]+)$#', $uri, $matches)) {
    $slug = $matches[1];
    $albumDir = realpath(__DIR__ . '/../../userdata/content/album/');
    $albumFile = $albumDir . '/' . $slug . '.yml';
    $markdownFile = $albumDir . '/' . $slug . '.md';

    if (file_exists($albumFile)) {
        $yaml = Symfony\Component\Yaml\Yaml::parseFile($albumFile);
        $album = $yaml['album'] ?? [];

        $parsedown = new Parsedown();
        $imageSize = $settings['default_image_size'] ?? 'M';

        $imageDir = realpath(__DIR__ . '/../../userdata/content/images/');
        $cacheDir = '/cache/images/';
        $imageList = [];

        // Bilder verarbeiten
        foreach ($album['images'] ?? [] as $img) {
            $jsonFile = $imageDir . '/' . pathinfo($img, PATHINFO_FILENAME) . '.json';

            if (file_exists($jsonFile)) {
                $meta = json_decode(file_get_contents($jsonFile), true);
                if (json_last_error() === JSON_ERROR_NONE && !empty($meta['guid'])) {
                    $guid = $meta['guid'];
                    $imageList[] = [
                        'file' => $cacheDir . $guid . '_' . $imageSize . '.jpg',
                        'url'  => '/i/' . rawurlencode($img),
                        'title' => $meta['title'] ?? '',
                    ];
                }
            }
        }

        // Cover-Bild
        $cover = null;
        if (!empty($album['headImage'])) {
            $coverJson = $imageDir . '/' . pathinfo($album['headImage'], PATHINFO_FILENAME) . '.json';
            if (file_exists($coverJson)) {
                $meta = json_decode(file_get_contents($coverJson), true);
                if (json_last_error() === JSON_ERROR_NONE && !empty($meta['guid'])) {
                    $cover = $cacheDir . $meta['guid'] . '_' . $imageSize . '.jpg';
                }
            }
        }

        // Markdown-Beschreibung
        $mdContent = file_exists($markdownFile) ? file_get_contents($markdownFile) : '';
        $descriptionHtml = $parsedown->text($mdContent);

        // Twig-Daten
        $data['album'] = [
            'slug' => $slug,
            'title' => $album['name'] ?? $slug,
            'description' => $descriptionHtml,
            'images' => $imageList,
            'cover' => $cover,
        ];
        $data['title'] = $album['name'] ?? $slug;

        echo $twig->render('album.twig', $data);
        exit;
    }

    // Album nicht gefunden
    http_response_code(404);
    echo $twig->render('404.twig', $data);
    exit;
}



if (preg_match('#^i/(.+)$#', $uri, $matches)) {
    $filename = basename($matches[1]);

    $imageDir = __DIR__.'/../../userdata/content/images';
    $jsonFile = $imageDir . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.json';

    if (file_exists($jsonFile)) {
        $meta = json_decode(file_get_contents($jsonFile), true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $parsedown = new Parsedown();

            $exif = $meta['exif'] ?? [];

            // Name Fix
            $exif['Camera'] = str_replace('Canon Canon', 'Canon',$exif['Camera']);

            // Rohwerte holen
            $apertureRaw = $exif['Aperture'] ?? '';
            $shutterSpeedRaw = $exif['Shutter Speed'] ?? '';

            // Blende formatieren (f/28/10 → f/2.8)
            $aperture = "Unknown";
            if (preg_match('/f\/(\d+)\/(\d+)/i', $apertureRaw, $matches)) {
                $apertureValue = round($matches[1] / $matches[2], 1);
                $aperture = "f/" . $apertureValue;
            } elseif (!empty($apertureRaw)) {
                $aperture = $apertureRaw;
            }

            // Belichtungszeit formatieren (z. B. 1/250 → 1/250s, 4/1 → 4s)
            $shutterSpeed = "Unknown";
            if (preg_match('/(\d+)\/(\d+)/', $shutterSpeedRaw, $matches)) {
                $numerator = (int)$matches[1];
                $denominator = (int)$matches[2];

                if ($numerator >= $denominator) {
                    $shutterSpeed = round($numerator / $denominator, 1) . "s";
                } else {
                    $shutterSpeed = "1/" . round($denominator / $numerator) . "s";
                }
            } elseif (!empty($shutterSpeedRaw)) {
                $shutterSpeed = $shutterSpeedRaw;
            }

            // Formatiert in EXIF zurückspeichern
            $exif['aperture'] = $aperture;
            $exif['shutter_speed'] = $shutterSpeed;


            // EXIF-Schlüssel vereinheitlichen: Leerzeichen durch Unterstrich, alles klein
            $normalizedExif = [];
            foreach ($exif as $key => $value) {
                $normalizedKey = strtolower(str_replace(' ', '_', $key));
                $normalizedExif[$normalizedKey] = $value;
            }

            $imageData = [
                'title' => $meta['title'] ?? '',
                'description' => $parsedown->text($meta['description'] ?? ''),
                'filename' => $meta['filename'] ?? $filename,
                'guid' => $meta['guid'] ?? '',
                'rating' => $meta['rating'] ?? '',
                'upload_date' => $meta['upload_date'] ?? '',
                'exif' => $normalizedExif ?? [],
                'file' =>  get_cacheimage($filename),
            ];

            $data['image'] = $imageData;
            $data['title'] = $imageData['title'];

            echo $twig->render('image.twig', $data);
            exit;
        }
    }

    http_response_code(404);
    echo $twig->render('404.twig');
    exit;
}




if ($uri === 'home' || $uri === '') {
    // Lade Startseiten-Konfiguration
    
    $homeConfigPath = __DIR__ . '/../../userdata/config/home.json';
    $home = file_exists($homeConfigPath)
        ? json_decode(file_get_contents($homeConfigPath), true)
        : [];


    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(500);
        echo $twig->render('404.twig', ['message' => 'Invalid home.json']);
        exit;
    }

    $style = $home['style'] ?? 'start';

    

    switch ($style) {
        case 'blog':
            header("Location: /blog");
            exit;

        case 'page':
            $slug = $home['startcontent'] ?? '';
            header("Location: /p/" . urlencode($slug));
            exit;

        case 'album':
            $albumSlug = $home['startcontent'] ?? '';
            $album = readGalleryAlbum($albumSlug, $settings);
            $data['album'] = $album;
            $data['title'] = $album['title'] ?? 'Album';
            echo $twig->render('home_album.twig', $data);
            exit;

        case 'start':
        default:
            $data['title'] = $home['headline'] ?? 'Welcome';
            $data['headline'] = $home['headline'] ?? '';
            $data['sub_headline'] = $home['sub-headline'] ?? '';
            $data['content'] = $home['content'] ?? '';

            // Bildlogik
            $imgStyle = $home['default_image_style'] ?? null;
            $imgRef   = $home['default_image'] ?? null;
            $imgDir   = realpath(__DIR__ . '/../../userdata/content/images/');
            $size     = $settings['default_image_size'] ?? 'M';

            if ($imgStyle === 'album' && $imgRef) {
                $album = readGalleryAlbum($imgRef, $settings);
                $data['home_images'] = $album['images'] ?? [];
            } elseif ($imgStyle === 'image' && $imgRef) {
                $jsonFile = $imgDir . '/' . pathinfo($imgRef, PATHINFO_FILENAME) . '.json';
                if (file_exists($jsonFile)) {
                    $meta = json_decode(file_get_contents($jsonFile), true);
                    if (json_last_error() === JSON_ERROR_NONE && !empty($meta['guid'])) {
                        $guid = $meta['guid'];
                        $data['home_image'] = '/cache/images/' . $guid . '_' . $size . '.jpg';
                    }
                }
            }

            echo $twig->render('home.twig', $data);
            exit;
    }
}
