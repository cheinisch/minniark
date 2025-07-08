<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once(__DIR__ . "/../../vendor/autoload.php");

    use Symfony\Component\Yaml\Yaml;

    

function getImage($imagename): ?array
{
    $basename = pathinfo($imagename, PATHINFO_FILENAME);
    $ymlFile = realpath(__DIR__ . '/../../userdata/content/images/' . $basename . '.yml');
    $mdFile  = realpath(__DIR__ . '/../../userdata/content/images/' . $basename . '.md');

    if (!$ymlFile || !file_exists($ymlFile)) {
        return null;
    }

    try {
        $yaml = Yaml::parseFile($ymlFile);
        $data = $yaml['image'] ?? [];
    } catch (Exception $e) {
        error_log("Fehler beim Parsen von YAML: " . $e->getMessage());
        return null;
    }

    $data['filename'] = $data['filename'] ?? ($basename . '.jpg'); // fallback
    $data['description'] = '';

    if ($mdFile && file_exists($mdFile)) {
        $data['description'] = trim(file_get_contents($mdFile));
    }

    return $data;
}

function count_images()
{
    $imageDir = '../userdata/content/images/';
    $files = glob($imageDir . '*.yml');
    echo count($files);
}

function get_imageyearlist($mobile)
{
    $imageDir = '../userdata/content/images/';
    $yearCounts = [];
    foreach (glob($imageDir . '*.yml') as $filePath) {
        $data = Yaml::parseFile($filePath);
        $date = $data['exif']['Date'] ?? null;
        if ($date) {
            $year = substr($date, 0, 4);
            if (ctype_digit($year)) {
                $yearCounts[$year] = ($yearCounts[$year] ?? 0) + 1;
            }
        }
    }
    ksort($yearCounts);
    foreach ($yearCounts as $year => $count) {
        $html = $mobile
            ? "<div class=\"pl-5\"><a href=\"media.php?year=$year\" class=\"block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6\">$year ($count)</a></div>"
            : "<li><a href=\"media.php?year=$year\" class=\"text-gray-400 hover:text-sky-400\">$year ($count)</a></li>";
        echo $html . "\n";
    }
}

function get_ratinglist($mobile)
{
    $imageDir = '../userdata/content/images/';
    $ratingCounts = [];
    foreach (glob($imageDir . '*.yml') as $filePath) {
        $data = Yaml::parseFile($filePath);
        $rating = (int)($data['image']['rating'] ?? 0);
        $ratingCounts[$rating] = ($ratingCounts[$rating] ?? 0) + 1;
    }
    for ($i = 0; $i <= 5; $i++) {
        $ratingCounts[$i] = $ratingCounts[$i] ?? 0;
    }
    krsort($ratingCounts);
    foreach ($ratingCounts as $rating => $count) {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $colorClass = $i <= $rating ? 'text-sky-400' : 'text-gray-300';
            $stars .= "<svg class='w-4 h-4 inline-block $colorClass' viewBox='0 0 20 20' fill='currentColor'><path d='M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.137 3.5h3.684c.969 0 1.371 1.24.588 1.81l-2.984 2.17 1.138 3.5c.3.921-.755 1.688-1.538 1.117L10 13.348l-2.976 2.176c-.783.571-1.838-.196-1.538-1.117l1.138-3.5-2.984-2.17c-.783-.57-.38-1.81.588-1.81h3.684l1.137-3.5z'/></svg>";
        }
        $html = $mobile
            ? "<div class=\"pl-5\"><a href=\"media.php?rating=$rating\" class=\"block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6\">$stars ($count)</a></div>"
            : "<li><a href=\"media.php?rating=$rating\" class=\"text-gray-400 hover:text-sky-400\">$stars ($count)</a></li>";
        echo $html . "\n";
    }
}

function getImagesFromDirectory($directory = "../userdata/content/images/") {
    return is_dir($directory)
        ? glob($directory . '*.{jpg,jpeg,png,gif}', GLOB_BRACE)
        : [];
}

function getAllUploadedImages()
{
    $imageDir = __DIR__.'/../../userdata/content/images/';
    $images = getImagesFromDirectory($imageDir);
    $imageData = [];
    foreach ($images as $image) {
        $fileName = basename($image);
        $yamlFile = $imageDir . pathinfo($fileName, PATHINFO_FILENAME) . '.yml';
        if (!file_exists($yamlFile)) continue;
        $metadata = Yaml::parseFile($yamlFile);
        $title = $metadata['image']['title'];
        $description = htmlspecialchars($metadata['image']['description'] ?? 'Keine Beschreibung verfügbar');
        $exifDate = $metadata['image']['exif']['Date'] ?? null;
        $year = $exifDate ? substr($exifDate, 0, 4) : null;
        $rating = $metadata['image']['rating'] ?? 0;
        $imageData[] = [
            'filename' => $fileName,
            'title' => $title,
            'description' => $description,
            'year' => $year,
            'rating' => $rating,
        ];
    }
    return $imageData;
}



function renderImageGallery($filterYear = null, $filterRating = null, $sort = null, $sort_direction = 'ASC')
{
    $imageDir = '../userdata/content/images/';
    $files = glob($imageDir . '*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE);

    $images = [];

    foreach ($files as $imagePath) {
        $fileName = basename($imagePath);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $ymlFile = $imageDir . $baseName . '.yml';
        $mdFile  = $imageDir . $baseName . '.md';

        if (!file_exists($ymlFile)) {
            continue;
        }

        $metadata = Yaml::parseFile($ymlFile)['image'] ?? [];

        $description = 'Keine Beschreibung verfügbar';
        if (file_exists($mdFile)) {
            $description = trim(file_get_contents($mdFile));
        }

        $exifDate = $metadata['exif']['Date'] ?? null;
        $year = $exifDate ? substr($exifDate, 0, 4) : null;
        $rating = $metadata['rating'] ?? 0;

        if ($filterYear !== null && $year !== $filterYear) {
            continue;
        }

        if ($filterRating !== null && (int)$rating !== (int)$filterRating) {
            continue;
        }

        $images[] = [
            'fileName' => $fileName,
            'title' => $metadata['title'] ?? '',
            'description' => $description,
            'exifDate' => $exifDate,
            'rating' => $rating
        ];
    }

    // Sortierung
    if ($sort) {
        usort($images, function ($a, $b) use ($sort, $sort_direction) {
            $valueA = $a[$sort] ?? '';
            $valueB = $b[$sort] ?? '';

            if ($sort === 'exifDate') {
                $valueA = strtotime($valueA);
                $valueB = strtotime($valueB);
            } else {
                $valueA = strtolower($valueA);
                $valueB = strtolower($valueB);
            }

            $result = $valueA <=> $valueB;

            return strtoupper($sort_direction) === 'DESC' ? -$result : $result;
        });
    }

    // HTML-Ausgabe
    foreach ($images as $image) {
        $fileName = $image['fileName'];
        $title = !empty($image['title']) ? htmlspecialchars($image['title']) : 'Kein Titel';
        $description = htmlspecialchars($image['description']);
        $cachedImage = get_cacheimage_dashboard($fileName, 'M');

        $shorttitle = getShortTitle($title);

        echo "
        <div>
        <div class=\"w-full aspect-video overflow-hidden border border-gray-300 hover:border-sky-400 rounded-xs dynamic-image-width transition-[max-width] duration-300 ease-in-out max-w-full md:max-w-none\" style=\"--img-max-width: 250px; max-width: var(--img-max-width);\">
                <a href=\"media-detail.php?image=" . urlencode($fileName) . "\">
                    <img src='$cachedImage' class=\"w-full h-full object-cover\" alt=\"$title\" data-filename=\"$fileName\" title=\"$description\" draggable=\"true\"/>
                </a>
            </div>
            <div class=\"w-full flex justify-between items-center text-sm pt-1 dark:text-gray-400\">
                <span class=\"text-sm dark:text-gray-400\">$shorttitle</span>
                <div class=\"relative inline-block\">
                    <button id=\"$fileName\" class=\"p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-400\" data-filename=\"$fileName\">
                        <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"3\" stroke=\"currentColor\" class=\"w-5 h-5\">
                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M6 12h.01M12 12h.01M18 12h.01\" />
                        </svg>
                    </button>                    
                    <div class=\"dropdown hidden absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none\">
                        <a href=\"#\" class=\"block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 assign-to-album-btn\" data-filename=\"$fileName\">Add to Album</a>
                        <a href=\"backend_api/delete.php?type=img&filename=$fileName\" class=\"confirm-link block px-4 py-2 text-sm text-red-600 hover:bg-red-100\">Delete</a>
                    </div>
                </div>
            </div>
        </div>";
    }
}


    

    function renderImageGalleryAlbum($album) {
        $albumFile = __DIR__ . "/../../userdata/content/album/".$album . ".yml";

        if (!file_exists($albumFile)) {
            echo "<p class='text-red-500'>Album nicht gefunden.</p>";
            return;
        }

            
        if (!file_exists($albumFile)) {
            echo "<p class='text-red-500'>Album nicht gefunden.</p>";
            return;
        }

        try {
            $yaml = Yaml::parseFile($albumFile);
            $albumData = $yaml['album'] ?? [];
            $Images = $albumData['images'] ?? [];
        } catch (Exception $e) {
            echo "<p class='text-red-500'>Fehler beim Laden des Albums: " . htmlspecialchars($e->getMessage()) . "</p>";
            return;
        }

        $imageDir = __DIR__ .'/../../userdata/content/images/';
        $imageData = [];

        foreach ($Images as $fileName) {
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            $ymlFile = $imageDir . $baseName . '.yml';
            $mdFile  = $imageDir . $baseName . '.md';

            if (!file_exists($ymlFile)) {
                continue;
            }

            try {
                $meta = Yaml::parseFile($ymlFile)['image'] ?? [];
            } catch (Exception $e) {
                error_log("YAML-Fehler in $ymlFile: " . $e->getMessage());
                continue;
            }

            $description = '';
            if (file_exists($mdFile)) {
                $description = trim(file_get_contents($mdFile));
            }

            $date = $meta['exif']['Date'] ?? '0000-00-00 00:00:00';

            $imageData[] = [
                'file' => $fileName,
                'date' => $date,
                'title' => $meta['title'] ?? '',
                'description' => $description,
            ];
        }

        // Nach Datum sortieren
        usort($imageData, function($a, $b) {
            return strtotime($b['date']) <=> strtotime($a['date']);
        });

        // Bilder anzeigen
        foreach ($imageData as $img) {
            $title = htmlspecialchars($img['title'] ?: 'Kein Titel');
            $description = htmlspecialchars($img['description'] ?: 'Keine Beschreibung verfügbar');
            $fileName = $img['file'];
            $smallimg = get_cacheimage_dashboard($fileName, "M");
            $imagePath = $smallimg;

            $shorttitle = getShortTitle($title);

            echo "
            <div class=\"\">
                <div class=\"w-full aspect-video overflow-hidden border border-gray-300 hover:border-sky-400 rounded-xs dynamic-image-width transition-[max-width] duration-300 ease-in-out max-w-full md:max-w-none\" style=\"--img-max-width: 200px; max-width: var(--img-max-width);\">
                    <a href=\"media-detail.php?image=" . urlencode($fileName) . "\">
                        <img src=\"$imagePath\" class=\"w-full h-full object-cover\" alt=\"$title\" data-filename=\"$fileName\" title=\"$description\" draggable=\"true\"/>
                    </a>
                </div>
                <div class=\"w-full flex justify-between items-center text-sm pt-1 dark:text-gray-400\">
                    <span class=\"text-sm dark:text-gray-400\">$shorttitle</span>
                    <div class=\"relative inline-block\">
                        <button id=\"$fileName\" class=\"p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-400\" data-filename=\"$fileName\">
                            <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"3\" stroke=\"currentColor\" class=\"w-5 h-5\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M6 12h.01M12 12h.01M18 12h.01\" />
                            </svg>
                        </button>
                        <div class=\"dropdown hidden absolute right-0 z-10 bottom-full mb-2 w-40 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none\">
                            <a href=\"backend_api/album_set_hero.php?album=$album&filename=$fileName\" class=\"block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100\">Set as Hero Image</a>
                            <a href=\"backend_api/delete.php?type=album_img&filename=$fileName&albumname=$album\" class=\"confirm-link block px-4 py-2 text-sm text-red-600 hover:bg-red-100\">remove from album</a>
                        </div>
                    </div>
                </div>
            </div>";
        }
    }


    function syncImages()
    {
        $imageDir = realpath(__DIR__ . '/../../userdata/content/images/');
        if (!$imageDir || !is_dir($imageDir)) {
            error_log("Image directory not found.");
            return;
        }

        $images = glob($imageDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        $metaFiles = glob($imageDir . '/*.yml');
        $usedBaseNames = [];

        foreach ($images as $imagePath) {
            $basename = pathinfo($imagePath, PATHINFO_FILENAME);
            $usedBaseNames[] = $basename;

            $ymlPath = $imageDir . '/' . $basename . '.yml';
            $jsonPath = $imageDir . '/' . $basename . '.json';
            $mdPath = $imageDir . '/' . $basename . '.md';

            if (!file_exists($ymlPath)) {
                $meta = [];

                
                    // Keine JSON → Metadaten aus EXIF
                    error_log("keine json, lade Exif DAta");
                    $exif = @exif_read_data($imagePath, 0, true);
                    $exifData = extractExifData($imagePath);
                    error_log(print_r($exif, true));
                    print_r($exifData);
                    $yaml = [
                        'image' => [
                            'filename'    => basename($imagePath),
                            'guid'        => uniqid(),
                            'title'       => '',
                            'description' => '',
                            'tags'        => [],
                            'rating'      => 0,
                            'exif'        => $exifData ?: [],
                            'created_at'  => date('Y-m-d H:i:s'),
                        ]
                    ];

                    file_put_contents($ymlPath, Yaml::dump($yaml, 2, 4));
                
            }
        }

        // Entferne verwaiste YMLs ohne zugehöriges Bild
        foreach ($metaFiles as $ymlPath) {
            $basename = pathinfo($ymlPath, PATHINFO_FILENAME);
            $imgFound = false;

            foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
                if (file_exists($imageDir . '/' . $basename . '.' . $ext)) {
                    $imgFound = true;
                    break;
                }
            }

            if (!$imgFound) {
                unlink($ymlPath);
                $mdPath = $imageDir . '/' . $basename . '.md';
                if (file_exists($mdPath)) {
                    unlink($mdPath);
                }
            }
        }

        // Cache neu erzeugen
        if (function_exists('generate_image_cache')) {
            generate_image_cache();
        } else {
            error_log("generate_image_cache() ist nicht definiert.");
            return false;
            exit;
        }

        return true;
    }


    function get_cacheimage_dashboard($filename, $size)
    {

        if($size == 'Original')
        {
            return $filename;
        }

        // Nur den Basisnamen (ohne .jpg usw.)
        $basename = pathinfo($filename, PATHINFO_FILENAME);

        // Pfad zur .yml-Datei
        $imageDir = __DIR__ . '/../../userdata/content/images/';
        $ymlPath = $imageDir . $basename . '.yml';

        $image = "../userdata/content/images/".$filename;

        // Prüfen ob YML existiert
        if (!file_exists($ymlPath)) {
            return $image;
        }

        // YML lesen
        try {
            $data = Yaml::parseFile($ymlPath);
        } catch (Exception $e) {
            error_log("YAML Parse Error: " . $e->getMessage());
            return $image;
        }

        $guid = $data['image']['guid'] ?? null;
        if (!$guid) {
            return $image;
        }

        // Ziel-Dateiname erzeugen
        $newSize = strtoupper($size);
        $cachedFile = "../cache/images/".$guid . "_" . $newSize . ".jpg";

        return $cachedFile;
        
    }

    function get_cacheimage($filename, $size)
    {

        if($size == 'Original')
        {
            return $filename;
        }

        // Nur den Basisnamen (ohne .jpg usw.)
        $basename = pathinfo($filename, PATHINFO_FILENAME);

        // Pfad zur .yml-Datei
        $imageDir = __DIR__ . '/../../userdata/content/images/';
        $ymlPath = $imageDir . $basename . '.yml';

        // Prüfen ob YML existiert
        if (!file_exists($ymlPath)) {
            return $filename;
        }

        // YML lesen
        try {
            $data = Yaml::parseFile($ymlPath);
        } catch (Exception $e) {
            error_log("YAML Parse Error: " . $e->getMessage());
            return $filename;
        }

        $guid = $data['image']['guid'] ?? null;
        if (!$guid) {
            return $filename;
        }

        // Ziel-Dateiname erzeugen
        $newSize = strtoupper($size);
        $cachedFile = $guid . "_" . $newSize . ".jpg";

        return $cachedFile;
    }

    function updateImage(array $data = [], string $type = 'description'): bool
    {
        echo '<br />';
        print_r($data);
        echo '<br />';
        if (empty($data['filename'])) {
            error_log("Dateiname fehlt.");
            return false;
        }

        $filename = basename($data['filename']);
        $slug     = pathinfo($filename, PATHINFO_FILENAME);
        $file     = __DIR__ . "/../../userdata/content/images/{$slug}.yml";

        // YAML laden oder leere Struktur
        $yaml = [];
        if (file_exists($file)) {
            try {
                $yaml = Yaml::parseFile($file);
            } catch (Exception $e) {
                error_log("Fehler beim Laden der YAML: " . $e->getMessage());
                return false;
            }
        }

        $yaml['image'] = $yaml['image'] ?? [];

        if ($type === 'description') {
            if (!empty($data['title'])) {
                $yaml['image']['title'] = trim($data['title']);
            }

            if (!empty($data['description'])) {
                $yaml['image']['description'] = trim($data['description']);
            }

        } elseif ($type === 'exif') {
            // Exif initialisieren, falls nötig
            if (!isset($yaml['image']['exif']) || !is_array($yaml['image']['exif'])) {
                $yaml['image']['exif'] = [];
            }

            // Exif-Felder direkt übernehmen (z. B. Camera, Date, etc.)
            if (isset($data['exif']) && is_array($data['exif'])) {
                foreach ($data['exif'] as $key => $value) {
                    $key = trim($key);
                    if ($key !== '' && trim((string)$value) !== '') {
                        $yaml['image']['exif'][$key] = trim($value);
                    }
                }
            }

            // Debug (falls benötigt)
            error_log("Typ von gps: " . gettype($data['gps'] ?? null));
            error_log("Inhalt gps: " . print_r($data['gps'] ?? [], true));

            // GPS-Daten ergänzen, wenn gültig
            if (isset($data['gps']) && is_array($data['gps'])) {
                // Initialisiere GPS nur, wenn es Werte gibt
                $gps = [];

                if (isset($data['gps']['latitude']) && $data['gps']['latitude'] !== '') {
                    $gps['latitude'] = (float)$data['gps']['latitude'];
                }

                if (isset($data['gps']['longitude']) && $data['gps']['longitude'] !== '') {
                    $gps['longitude'] = (float)$data['gps']['longitude'];
                }

                if (!empty($gps)) {
                    $yaml['image']['exif']['GPS'] = $gps;
                }
            }

        }

        // updated_at immer setzen
        $yaml['image']['updated_at'] = date('Y-m-d H:i:s');
        print_r($yaml);
        // Speichern
        try {
            $dump = Yaml::dump($yaml, 2, 4);
            file_put_contents($file, $dump);
            return true;
        } catch (Exception $e) {
            error_log("Fehler beim Speichern: " . $e->getMessage());
            return false;
        }
    }



    function generate_image_cache()
    {
        

        $sourceDir = realpath(__DIR__ . '/../../userdata/content/images/');
        $cacheDir = realpath(__DIR__ . '/../../cache/images/');

        if (!$sourceDir || !$cacheDir) {
            error_log("Quell- oder Cache-Verzeichnis nicht gefunden.");
            return;
        }

        $sizes = ['S' => 150, 'M' => 500, 'L' => 1024, 'XL' => 1920, 'XXL' => 3440];
        $images = glob($sourceDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

        foreach ($images as $imgPath) {
            $filename = basename($imgPath);
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $ymlPath = $sourceDir . '/' . $basename . '.yml';

            if (!file_exists($ymlPath)) {
                continue;
            }

            try {
                $yml = Yaml::parseFile($ymlPath);
                $guid = $yml['image']['guid'] ?? null;
            } catch (Exception $e) {
                error_log("Fehler beim Parsen von $ymlPath: " . $e->getMessage());
                continue;
            }

            if (!$guid) {
                error_log("GUID fehlt in YAML für $filename");
                continue;
            }

            $targetFile = $imgPath;

            foreach ($sizes as $sizeKey => $sizeValue) {
                $thumbPath = $cacheDir . '/' . $guid . "_$sizeKey.jpg";
                if (!file_exists($thumbPath)) {
                    createThumbnail($targetFile, $thumbPath, $sizeValue);
                    error_log("Thumbnail: $thumbPath");
                }
            }
        }

        error_log("Bildcache wurde erfolgreich generiert.");
    }


    function generate_single_image_cache($filename)
    {
        $sourceDir = realpath(__DIR__ . '/../../userdata/content/images/');
        $cacheDir = realpath(__DIR__ . '/../../cache/images/');

        if (!$sourceDir || !$cacheDir) {
            error_log("Quell- oder Cache-Verzeichnis nicht gefunden.");
            return;
        }

        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $imgPath = $sourceDir . '/' . $filename;
        $ymlPath = $sourceDir . '/' . $basename . '.yml';

        if (!file_exists($imgPath)) {
            error_log("Bild nicht gefunden: $imgPath");
            return;
        }

        if (!file_exists($ymlPath)) {
            error_log("YAML nicht gefunden: $ymlPath");
            return;
        }

        try {
            $yml = Yaml::parseFile($ymlPath);
            $guid = $yml['image']['guid'] ?? null;
        } catch (Exception $e) {
            error_log("Fehler beim Parsen von $ymlPath: " . $e->getMessage());
            return;
        }

        if (!$guid) {
            error_log("GUID fehlt in YAML für $filename");
            return;
        }

        // 1. Vorherige Thumbnails mit gleichem GUID löschen
        foreach (glob($cacheDir . '/' . $guid . '_*.jpg') as $oldThumb) {
            if (is_file($oldThumb)) {
                unlink($oldThumb);
                error_log("Alter Cache gelöscht: $oldThumb");
            }
        }

        // 2. Neue Thumbnails erstellen
        $sizes = ['S' => 150, 'M' => 500, 'L' => 1024, 'XL' => 1920, 'XXL' => 3440];

        foreach ($sizes as $sizeKey => $sizeValue) {
            $thumbPath = $cacheDir . '/' . $guid . "_$sizeKey.jpg";
            createThumbnail($imgPath, $thumbPath, $sizeValue);
            error_log("Thumbnail erstellt: $thumbPath");
        }

        error_log("Bildcache für $filename erfolgreich generiert.");
    }



    function getShortTitle(string $title): string
    {
        $maxInputLength = 26; // 28 (Referenzlänge) - 3
        $maxLength = 24; // 28 (Referenzlänge) - 3

        if (mb_strlen($title) <= $maxLength) {
            return $title;
        }

        return mb_substr($title, 0, $maxLength) . ' ...';
    }


    function modifyImage(string $filename, int $rotate = 0, int $flipX = 1, int $flipY = 1): bool
    {
        $imageDir = realpath(__DIR__ . '/../../userdata/content/images/');
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $imagePath = $imageDir . '/' . basename($filename);
        $exifPath  = $imageDir . '/' . $basename . '.exif';

        if (!file_exists($imagePath)) {
            error_log("Bild nicht gefunden: $imagePath");
            return false;
        }

        // EXIF-Daten sichern
        $exifData = @exif_read_data($imagePath, null, true);
        if ($exifData && !file_exists($exifPath)) {
            file_put_contents($exifPath, json_encode($exifData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        // Bild öffnen
        $image = match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($imagePath),
            'png'         => imagecreatefrompng($imagePath),
            'gif'         => imagecreatefromgif($imagePath),
            default       => null
        };

        if (!$image) {
            error_log("Bildformat nicht unterstützt: $imagePath");
            return false;
        }

        // Bild rotieren
        if ($rotate !== 0) {
            error_log("Rotation: $rotate");
            $image = imagerotate($image, 360 - $rotate, 0);
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Bild spiegeln falls nötig
        if ($flipX === -1 || $flipY === -1) {
            $flipped = imagecreatetruecolor($width, $height);

            // PNG-Transparenz beibehalten
            if ($extension === 'png') {
                imagealphablending($flipped, false);
                imagesavealpha($flipped, true);
                $transparent = imagecolorallocatealpha($flipped, 0, 0, 0, 127);
                imagefill($flipped, 0, 0, $transparent);
            }

            imagecopyresampled(
                $flipped,
                $image,
                0, 0,
                $flipX === -1 ? $width - 1 : 0,
                $flipY === -1 ? $height - 1 : 0,
                $width, $height,
                $flipX === -1 ? -$width : $width,
                $flipY === -1 ? -$height : $height
            );

            imagedestroy($image);
            $image = $flipped;
        }

        // Bild speichern
        $saveSuccess = match ($extension) {
            'jpg', 'jpeg' => imagejpeg($image, $imagePath, 90),
            'png'         => imagepng($image, $imagePath),
            'gif'         => imagegif($image, $imagePath),
            default       => false
        };

        imagedestroy($image);
        return $saveSuccess;
    }



    function delete_image(string $filename): bool
    {
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $imageDir = realpath(__DIR__ . '/../../userdata/content/images/');
        $cacheDir = realpath(__DIR__ . '/../../cache/images/');
        $albumDir = realpath(__DIR__ . '/../../userdata/content/album/');

        $imagePath = $imageDir . '/' . $filename;
        $ymlPath   = $imageDir . '/' . $baseName . '.yml';
        $mdPath    = $imageDir . '/' . $baseName . '.md';
        $exifPath  = $imageDir . '/' . $baseName . '.exif';

        // GUID ermitteln für Cache
        $guid = null;
        if (file_exists($ymlPath)) {
            try {
                $yml = Yaml::parseFile($ymlPath);
                $guid = $yml['image']['guid'] ?? null;
            } catch (Exception $e) {
                error_log("YAML-Fehler: " . $e->getMessage());
            }
        }

        // Bild löschen
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // YML, MD, EXIF löschen
        foreach ([$ymlPath, $mdPath, $exifPath] as $metaFile) {
            if (file_exists($metaFile)) {
                unlink($metaFile);
            }
        }

        // Cache-Bilder löschen
        if ($guid && $cacheDir) {
            $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
            foreach ($sizes as $size) {
                $cacheFile = $cacheDir . '/' . $guid . "_$size.jpg";
                if (file_exists($cacheFile)) {
                    unlink($cacheFile);
                }
            }
        }

        // Aus allen Alben entfernen
        if ($albumDir && is_dir($albumDir)) {
            foreach (glob($albumDir . '/*.yml') as $albumFile) {
                try {
                    $yaml = Yaml::parseFile($albumFile);
                    if (!isset($yaml['album']['images']) || !is_array($yaml['album']['images'])) {
                        continue;
                    }

                    $before = count($yaml['album']['images']);
                    $yaml['album']['images'] = array_values(array_filter($yaml['album']['images'], function ($img) use ($filename) {
                        return $img !== $filename;
                    }));

                    if (count($yaml['album']['images']) !== $before) {
                        file_put_contents($albumFile, Yaml::dump($yaml, 2, 4));
                    }

                } catch (Exception $e) {
                    error_log("Fehler beim Bearbeiten von Album: $albumFile – " . $e->getMessage());
                }
            }
        }

        return true;
    }
