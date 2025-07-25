<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$templateDir = __DIR__ . '/../../userdata/template/' . $theme;
$navItems = buildNavigation($templateDir);

require_once __DIR__ . '/../../vendor/autoload.php'; // für Yaml
use Symfony\Component\Yaml\Yaml;

$data = [
    'title' => ucfirst($uri) ?: 'Home',
    'site_title' => $settings['site_title'] ?? 'Minniark',
    'theme' => $theme,
    'themepath' => "/userdata/template/{$theme}",
    'settings' => $settings,
    'navItems' => $navItems,
    'current_path' => $current_path,
];

$currentUrl = (
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http'
) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$data['current_url'] = $currentUrl;



$pluginTwigSnippets = [];
$pluginTwigData = [];

$pluginDirs = glob(__DIR__ . '/../../userdata/plugins/*', GLOB_ONLYDIR);
foreach ($pluginDirs as $pluginDir) {
    $key = basename($pluginDir);
    $settingsFile = $pluginDir . '/settings.json';
    $twigPhp = $pluginDir . '/twig.php';


    if (!file_exists($settingsFile)) continue;

    $settings = json_decode(file_get_contents($settingsFile), true);
    if (empty($settings['enabled'])) continue;

    // twig.php ausführen und als Twig-Datenstruktur speichern
    if (file_exists($twigPhp)) {
        $pluginVars = include $twigPhp;
        
        if (is_array($pluginVars)) {
            $pluginTwigData[$key] = $pluginVars[$key] ?? [];
        }
    }
}

$data['plugin'] = $pluginTwigData;
$data['plugin_snippet'] = $pluginTwigSnippets;

// Einzelne statische Seite per Slug: /p/<slug>

if (preg_match('#^p/([\w\-]+)$#', $uri, $matches)) {
    $slug = $matches[1]; // z. B. "impressum"

    $baseDir = realpath(__DIR__ . '/../../userdata/content/page/' . $slug);
    $yamlPath = $baseDir . '/' . $slug . '.yml';
    $mdPath = $baseDir . '/' . $slug . '.md';

    if ($baseDir && file_exists($yamlPath) && file_exists($mdPath)) {
        $yaml = Yaml::parseFile($yamlPath);
        $page = $yaml['page'] ?? [];

        if (!isset($page['title'])) {
            http_response_code(404);
            echo $twig->render('404.twig', $data);
            exit;
        }

        $Parsedown = new Parsedown();
        $content = file_get_contents($mdPath);

        $page['slug'] = $slug;
        $page['content'] = $Parsedown->text($content);
        $page['cover'] = get_cacheimage($page['cover'] ?? '');

        $data['page'] = $page;
        $data['title'] = $page['title'];

        if($page['is_published'] == false)
        {
            echo $twig->render('404.twig', $data);
            exit;    
        }

        echo $twig->render('page.twig', $data);
        exit;
    }

    // Fehlerfall
    http_response_code(404);
    echo $twig->render('404.twig', $data);
    exit;
}


// Dynamischer Blogpost: /blog/<slug>
// Einzelner Blog-Post anhand des Slugs aus dem Verzeichnisnamen
if (preg_match('#^blog/([\w\-]+)$#', $uri, $matches)) {
    $slug = $matches[1]; // z. B. "lorem-ipsum"

    $baseDir = realpath(__DIR__ . '/../../userdata/content/essay/');
    $essayDir = $baseDir . '/' . $slug;

    $yamlPath = $essayDir . '/' . $slug . '.yml';
    $mdPath = $essayDir . '/' . $slug . '.md';

    if (file_exists($yamlPath) && file_exists($mdPath)) {
        $yaml = Yaml::parseFile($yamlPath);
        $essay = $yaml['essay'] ?? [];

        if (!isset($essay['title'])) {
            $essay['title'] = ucfirst($slug);
        }

        // Markdown parsen
        $parsedown = new Parsedown();
        $essay['slug'] = $slug;
        $essay['content'] = $parsedown->text(file_get_contents($mdPath));
        $essay['cover'] = get_cacheimage($essay['cover'] ?? '');

        // Twig-Daten setzen
        $data['post'] = $essay;
        $data['title'] = $essay['title'];

        if($essay['is_published'] == false)
        {
            echo $twig->render('404.twig', $data);
            exit;    
        }

        echo $twig->render('post.twig', $data);
        exit;
    }

    // Fehler: Datei nicht vorhanden oder unvollständig
    http_response_code(404);
    echo $twig->render('404.twig', $data);
    exit;
}


if (preg_match('#^album/([\w\-]+)$#', $uri, $matches)) {
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

        foreach ($album['images'] ?? [] as $img) {
            $ymlFile = $imageDir . '/' . pathinfo($img, PATHINFO_FILENAME) . '.yml';

            if (file_exists($ymlFile)) {
                $meta = Symfony\Component\Yaml\Yaml::parseFile($ymlFile)['image'] ?? null;
                if (!empty($meta['guid'])) {
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
            $coverYml = $imageDir . '/' . pathinfo($album['headImage'], PATHINFO_FILENAME) . '.yml';
            if (file_exists($coverYml)) {
                $meta = Symfony\Component\Yaml\Yaml::parseFile($coverYml)['image'] ?? null;
                if (!empty($meta['guid'])) {
                    $cover = $cacheDir . $meta['guid'] . '_' . $imageSize . '.jpg';
                }
            }
        }

        // Album URL
        $url = '/album/'.$slug;

        // Markdown-Beschreibung
        $mdContent = file_exists($markdownFile) ? file_get_contents($markdownFile) : '';
        $descriptionHtml = $parsedown->text($mdContent);

        $data['album'] = [
            'slug' => $slug,
            'title' => $album['name'] ?? $slug,
            'description' => $descriptionHtml,
            'images' => $imageList,
            'cover' => $cover,
            'url' => $url,
        ];
        $data['title'] = $album['name'] ?? $slug;

        echo $twig->render('album.twig', $data);
        exit;
    }

    http_response_code(404);
    echo $twig->render('404.twig', $data);
    exit;
}


if (preg_match('#^i/(.+)$#', $uri, $matches)) {
    $filename = basename($matches[1]);

    $imageDir = __DIR__.'/../../userdata/content/images';
    $ymlFile = $imageDir . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.yml';

    if (file_exists($ymlFile)) {
        $meta = Symfony\Component\Yaml\Yaml::parseFile($ymlFile)['image'] ?? null;

        if ($meta) {
            $parsedown = new Parsedown();
            $exif = $meta['exif'] ?? [];

            $exif['Camera'] = str_replace('Canon Canon', 'Canon', $exif['Camera'] ?? '');

            // Formatierung
            $apertureRaw = $exif['Aperture'] ?? '';
            $shutterSpeedRaw = $exif['Shutter Speed'] ?? '';

            $aperture = "Unknown";
            if (preg_match('/f\/(\d+)\/(\d+)/i', $apertureRaw, $matches)) {
                $apertureValue = round($matches[1] / $matches[2], 1);
                $aperture = "f/" . $apertureValue;
            } elseif (!empty($apertureRaw)) {
                $aperture = $apertureRaw;
            }

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

            $exif['aperture'] = $aperture;
            $exif['shutter_speed'] = $shutterSpeed;

            $normalizedExif = [];
            foreach ($exif as $key => $value) {
                $normalizedKey = strtolower(str_replace(' ', '_', $key));
                $normalizedExif[$normalizedKey] = $value;
            }

            $mdFile = $imageDir . '/' . pathinfo($filename, PATHINFO_FILENAME) . '.md';
            $descriptionText = file_exists($mdFile) ? file_get_contents($mdFile) : '';
            $descriptionHtml = $parsedown->text($descriptionText);

            $imageData = [
                'title' => $meta['title'] ?? '',
                'description' => $descriptionHtml,
                'filename' => $meta['filename'] ?? $filename,
                'guid' => $meta['guid'] ?? '',
                'rating' => $meta['rating'] ?? '',
                'upload_date' => $meta['uploaded_at'] ?? '',
                'exif' => $normalizedExif,
                'file' => get_cacheimage($filename),
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

// Collection content
if (preg_match('#^collections$#', $uri)) {
    $collectionDir = __DIR__ . '/../../userdata/content/collection/';
    $collections = [];

    foreach (glob($collectionDir . '*.yml') as $filePath) {
        $slug = basename($filePath, '.yml');
        try {
            $yaml = Symfony\Component\Yaml\Yaml::parseFile($filePath);
            $collection = $yaml['collection'] ?? [];
            $title = $collection['name'] ?? $slug;
            $cover = $collection['image'] ?? null;
            $coverUrl = null;

            if ($cover) {
                $imageSlug = pathinfo($cover, PATHINFO_FILENAME);
                $imageMetaPath = __DIR__ . '/../../userdata/content/images/' . $imageSlug . '.yml';

                if (file_exists($imageMetaPath)) {
                    $imageMeta = Symfony\Component\Yaml\Yaml::parseFile($imageMetaPath);
                    
                    $guid = $imageMeta['image']['guid'] ?? null;
                    if ($guid) {
                        $coverUrl = "/cache/images/{$guid}_M.jpg";
                    }
                }
            }

            $url = '/collection/'.$slug;

            $collections[] = [
                'slug' => $slug,
                'title' => $title,
                'cover' => $coverUrl,
                'url' => $url,
            ];
        } catch (Exception $e) {
            continue;
        }
    }

    $data['collections'] = $collections;
    $data['title'] = 'Collections';

    echo $twig->render('collection.list.twig', $data);

    exit;
}


if (preg_match('#^collection/([\w\-]+)$#', $uri, $matches)) {
    $slug = $matches[1];
    $collectionFile = __DIR__ . '/../../userdata/content/collection/' . $slug . '.yml';
    $descriptionFile = __DIR__ . '/../../userdata/content/collection/' . $slug . '.md';

    if (!file_exists($collectionFile)) {
        http_response_code(404);
        echo $twig->render('404.twig');
        exit;
    }

    $imageSize = $settings['default_image_size'] ?? 'M';

    $yaml = Symfony\Component\Yaml\Yaml::parseFile($collectionFile);
    $collection = $yaml['collection'] ?? [];
    $title = $collection['name'] ?? $slug;

    $Parsedown = new Parsedown();
    $descriptionMd = file_exists($descriptionFile) ? file_get_contents($descriptionFile) : '';
    $descriptionHtml = $Parsedown->text($descriptionMd ?? '');

    $coverUrl = null;
    if (!empty($collection['image'])) {
        $imageSlug = pathinfo($collection['image'], PATHINFO_FILENAME);
        $imageMetaPath = __DIR__ . '/../../userdata/content/images/' . $imageSlug . '.yml';
        if (file_exists($imageMetaPath)) {
            $imageMeta = Symfony\Component\Yaml\Yaml::parseFile($imageMetaPath);
            $guid = $imageMeta['image']['guid'] ?? null;
            if ($guid) {
                $coverUrl = "/cache/images/{$guid}_{$imageSize}.jpg";
            }
        }
    }

    // Alben laden
    $albumList = [];
    foreach ($collection['albums'] ?? [] as $albumSlug) {
        $albumFile = __DIR__ . '/../../userdata/content/album/' . $albumSlug . '.yml';
        if (!file_exists($albumFile)) continue;

        $albumYaml = Symfony\Component\Yaml\Yaml::parseFile($albumFile);
        $album = $albumYaml['album'] ?? [];
        $albumTitle = $album['name'] ?? $albumSlug;
        $headImage = $album['headImage'] ?? null;

        $imageSize = $settings['default_image_size'] ?? 'M';

        $albumCover = null;
        if ($headImage) {
            $imageSlug = pathinfo($headImage, PATHINFO_FILENAME);
            $imageMetaPath = __DIR__ . '/../../userdata/content/images/' . $imageSlug . '.yml';
            
            if (file_exists($imageMetaPath)) {
                $meta = Symfony\Component\Yaml\Yaml::parseFile($imageMetaPath);
                $guid = $meta['image']['guid'] ?? null;
                if ($guid) {
                    
                    $coverUrl = "/cache/images/{$guid}_{$imageSize}.jpg";
                }
            }
        }

        $url = '/album/'.$albumSlug;

        $albumList[] = [
            'slug' => $albumSlug,
            'title' => $albumTitle,
            'cover' => $coverUrl,
            'url' => $url,
        ];
    }

    $collectionData = [
        'title' => $title,
        'slug' => $slug,
        'description' => $descriptionHtml,
        'cover' => $coverUrl,
    ];

    $data['title'] = $title;
    $data['slug'] = $slug;
    $data['description'] = $descriptionHtml;
    $data['cover'] = $coverUrl;
    $data['albums'] = $albumList;
    $data['collection'] = $collectionData;

    echo $twig->render('collection.twig', $data);
    exit;
}



if ($uri === 'home' || $uri === '') {
    // Lade Startseiten-Konfiguration aus home.yml
    $home = getHomeConfig(); // → sichert auch Standardwerte

    $style = $home['style'] ?? 'start';

    switch ($style) {
        case 'blog':
            header("Location: /blog");
            exit;

        case 'page':
            $slug = $home['startcontent'] ?? '';
            header("Location: /p/" . urlencode($slug));
            exit;
        case 'collection':
            exit;

        case 'album':
            $albumSlug = $home['startcontent'] ?? '';
            $album = readGalleryAlbum($albumSlug, $settings);
            $data['album'] = $album;
            $data['title'] = $album['title'] ?? 'Album';
            echo $twig->render('home.album.twig', $data);
            exit;

        case 'start':
        default:
            if($home['headline'] == '')
            {
                $home['headline'] = null;
            }
            $data['title'] = $home['headline'] ?? 'Home';
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
                $ymlFile = $imgDir . '/' . pathinfo($imgRef, PATHINFO_FILENAME) . '.yml';

                if (file_exists($ymlFile)) {
                    try {
                        $yaml = Symfony\Component\Yaml\Yaml::parseFile($ymlFile);
                        $imageMeta = $yaml['image'] ?? [];

                        if (!empty($imageMeta['guid'])) {
                            $guid = $imageMeta['guid'];
                            $data['home_image'] = '/cache/images/' . $guid . '_' . $size . '.jpg';
                        }
                    } catch (Exception $e) {
                        error_log("YAML-Fehler beim Parsen von $ymlFile:  " . $e->getMessage());
                    }
                }
            }

            echo $twig->render('home.twig', $data);
            exit;
    }
}
