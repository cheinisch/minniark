<?php

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
        $rating = (int)($data['rating'] ?? 0);
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
        $title = $metadata['title'];
        $description = htmlspecialchars($metadata['description'] ?? 'Keine Beschreibung verfügbar');
        $exifDate = $metadata['exif']['Date'] ?? null;
        $year = $exifDate ? substr($exifDate, 0, 4) : null;
        $rating = $metadata['rating'] ?? 0;
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



function renderImageGallery($filterYear = null, $filterRating = null)
{
    $imageDir = '../userdata/content/images/';
    $files = glob($imageDir . '*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE);

    foreach ($files as $imagePath) {
        $fileName = basename($imagePath);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $ymlFile = $imageDir . $baseName . '.yml';
        $mdFile  = $imageDir . $baseName . '.md';

        if (!file_exists($ymlFile)) {
            continue; // Kein YML → überspringen
        }

        $metadata = Yaml::parseFile($ymlFile)['image'] ?? [];

        // Beschreibung aus .md übernehmen
        $description = 'Keine Beschreibung verfügbar';
        if (file_exists($mdFile)) {
            $description = trim(file_get_contents($mdFile));
        }

        // EXIF-Datum auslesen
        $exifDate = $metadata['exif']['Date'] ?? null;
        $year = $exifDate ? substr($exifDate, 0, 4) : null;

        $rating = $metadata['rating'] ?? 0;

        if ($filterYear !== null && $year !== $filterYear) {
            continue;
        }

        if ($filterRating !== null && (int)$rating !== (int)$filterRating) {
            continue;
        }

        $title = !empty($metadata['title']) ? htmlspecialchars($metadata['title']) : 'Kein Titel';
        $description = htmlspecialchars($description);
        $cachedImage = get_cached_image_dashboard($fileName, 'M');

        echo "
        <div>
        <div class=\"w-full aspect-video overflow-hidden border border-gray-300 hover:border-sky-400 rounded-xs dynamic-image-width transition-[max-width] duration-300 ease-in-out max-w-full md:max-w-none\" style=\"--img-max-width: 250px; max-width: var(--img-max-width);\">
                <a href=\"media-detail.php?image=" . urlencode($fileName) . "\">
                    <img src='$cachedImage' class=\"w-full h-full object-cover\" alt=\"$title\" data-filename=\"$fileName\" title=\"$description\" draggable=\"true\"/>
                </a>
            </div>
            <div class=\"w-full flex justify-between items-center text-sm pt-1 dark:text-gray-600\">
                <span class=\"text-sm dark:text-gray-600\">$title</span>
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
        $smallimg = get_cacheimage($fileName, "m");
        $imagePath = "../cache/images/" . $smallimg;

        echo "
        <div class=\"\">
            <div class=\"w-full md:w-3xs aspect-video overflow-hidden border border-gray-300 hover:border-sky-400 duration-300 ease-in-out\">
                <a href=\"media-detail.php?image=" . urlencode($fileName) . "\">
                    <img src=\"$imagePath\" class=\"w-full h-full object-cover\" alt=\"$title\" data-filename=\"$fileName\" title=\"$description\" draggable=\"true\"/>
                </a>
            </div>
            <div class=\"w-full flex justify-between items-center text-sm pt-1 dark:text-gray-600\">
                <span class=\"text-sm dark:text-gray-600\">$title</span>
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

                if (file_exists($jsonPath)) {
                    $json = file_get_contents($jsonPath);
                    $meta = json_decode($json, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        error_log("Fehler beim Parsen von $jsonPath: " . json_last_error_msg());
                        continue;
                    }

                    // YAML erzeugen basierend auf deiner JSON-Struktur
                    $yaml = [
                        'image' => [
                            'filename'    => $meta['filename'] ?? ($basename . '.jpg'),
                            'guid'        => $meta['guid'] ?? uniqid(),
                            'title'       => $meta['title'] ?? '',
                            'description' => '', // wandert in .md
                            'tags'        => $meta['tags'] ?? [],
                            'rating'      => $meta['rating'] ?? 0,
                            'exif'        => $meta['exif'] ?? [],
                            'created_at'  => $meta['upload_date'] ?? date('Y-m-d H:i:s'),
                        ]
                    ];

                    file_put_contents($ymlPath, Yaml::dump($yaml, 2, 4));

                    if (!empty($meta['description'])) {
                        file_put_contents($mdPath, $meta['description']);
                    }

                    unlink($jsonPath); // Alte JSON löschen

                } else {
                    // Keine JSON → Metadaten aus EXIF
                    $exif = @exif_read_data($imagePath, 0, true);
                    $yaml = [
                        'image' => [
                            'filename'    => basename($imagePath),
                            'guid'        => uniqid(),
                            'title'       => '',
                            'description' => '',
                            'tags'        => [],
                            'rating'      => 0,
                            'exif'        => $exif ?: [],
                            'created_at'  => date('Y-m-d H:i:s'),
                        ]
                    ];

                    file_put_contents($ymlPath, Yaml::dump($yaml, 2, 4));
                }
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
        }
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
            return null;
        }

        // YML lesen
        try {
            $data = Yaml::parseFile($ymlPath);
        } catch (Exception $e) {
            error_log("YAML Parse Error: " . $e->getMessage());
            return null;
        }

        $guid = $data['image']['guid'] ?? null;
        if (!$guid) {
            return null;
        }

        // Ziel-Dateiname erzeugen
        $newSize = strtoupper($size);
        $cachedFile = $guid . "_" . $newSize . ".jpg";

        return $cachedFile;
    }
