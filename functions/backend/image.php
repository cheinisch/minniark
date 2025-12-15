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
    $imageDir = __DIR__.'/../../userdata/content/images/';
    $yearCounts = [];
    foreach (glob($imageDir . '*.yml') as $filePath) {
        $data = Yaml::parseFile($filePath);
        $date = $data['image']['exif']['Date'] ?? null;
        if ($date) {
            $year = substr($date, 0, 4);
            if (ctype_digit($year)) {
                $yearCounts[$year] = ($yearCounts[$year] ?? 0) + 1;
            }
        }
    }
    ksort($yearCounts);
    foreach ($yearCounts as $year => $count) {
        $html = 
        "<li>
            <a href=\"media.php?year=$year\"
                class=\"group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white\">
                $year ($count)
            </a>
            </li>";
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
            $colorClass = $i <= $rating ? 'text-cyan-400' : 'text-gray-300';
            $stars .= "<svg class='w-4 h-4 inline-block $colorClass' viewBox='0 0 20 20' fill='currentColor'><path d='M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.137 3.5h3.684c.969 0 1.371 1.24.588 1.81l-2.984 2.17 1.138 3.5c.3.921-.755 1.688-1.538 1.117L10 13.348l-2.976 2.176c-.783.571-1.838-.196-1.538-1.117l1.138-3.5-2.984-2.17c-.783-.57-.38-1.81.588-1.81h3.684l1.137-3.5z'/></svg>";
        }
        $html = "
                <li>
            <a href=\"media.php?rating=$rating\"
                class=\"group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white\">
                $stars ($count)
            </a>
            </li>";
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



function renderImageGallery($filterYear = null, $filterRating = null, $filterTag = null, $filterCountry = null, $sort = null, $sort_direction = 'ASC')
{
    $delete = languageString('general.delete');
    $addToAlbum = languageString('album.addToAlbum');


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

        $tagsRaw = $metadata['tags'] ?? [];
        if (is_string($tagsRaw)) {
            $tags = array_map('trim', explode(',', $tagsRaw));
        } elseif (is_array($tagsRaw)) {
            $tags = $tagsRaw;
        } else {
            $tags = [];
        }
        // normalisiert (case-insensitiv, leere raus)
        $tagsNorm = array_values(array_filter(array_map(function($t){
            if (!is_string($t)) return '';
            $t = trim(preg_replace('/\s+/', ' ', $t));
            if ($t !== '' && $t[0] === '#') $t = ltrim($t, '#');
            return mb_strtolower($t);
        }, $tags), fn($t) => $t !== ''));

        $countryCode = $metadata['exif']['CountryCode'] ?? '';
        $countryCode = is_string($countryCode) ? strtoupper(trim($countryCode)) : '';

        if ($filterTag !== null) {
            $needles = is_array($filterTag) ? $filterTag : [$filterTag];
            $needles = array_values(array_filter(array_map(function($t){
                return mb_strtolower(trim((string)$t));
            }, $needles), fn($v)=>$v!==''));

            if (!empty($needles)) {
                $match = false;
                foreach ($needles as $needle) {
                    if (in_array($needle, $tagsNorm, true)) { $match = true; break; }
                }
                if (!$match) continue;
            }
        }

        if ($filterCountry !== null) {
            $fc = strtoupper(trim((string)$filterCountry));
            $hasValidCode = (strlen($countryCode) === 2 && ctype_alpha($countryCode));

            if ($fc === 'UN') {
                // nur Bilder ohne gültigen ISO-2 Code
                if ($hasValidCode) continue;
            } else {
                // exakte Übereinstimmung (ISO-2)
                if (!$hasValidCode || $countryCode !== $fc) continue;
            }
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
        <div class=\"relative w-full md:w-72 rounded-sm border border-black dark:border-white/10 overflow-hidden\">
            <a href=\"media-detail.php?image=" . urlencode($fileName) . "\">
                <img src=\"$cachedImage\" alt=\"$title\" class=\"w-full  aspect-[3/2] object-cover\">
            </a>
            <div class=\"p-3\">
                <h3 class=\"text-sm font-medium text-black dark:text-white\">$shorttitle</h3>
            </div>
            <div class=\"absolute bottom-2 right-2\">
                <button type=\"button\" aria-expanded=\"false\" data-collapse-target=\"next\"
                        class=\"p-2 rounded hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20\">
                <span class=\"sr-only\">Optionen</span>
                <svg viewBox=\"0 0 20 20\" fill=\"currentColor\" class=\"size-5 text-black/80 dark:text-white/80\">
                    <path d=\"M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z\"/>
                </svg>
                </button>
                <!-- Menü: öffnet nach oben -->
                <div class=\"absolute bottom-10 right-0 z-10 hidden min-w-36 rounded-md border border-black dark:border-white/10 bg-white dark:bg-black/90 shadow-lg backdrop-blur-md p-1\">
                    <a href=\"#\" class=\"block w-full text-left px-3 py-2 text-sm/6 text-black dark:text-white hover:bg-white/10 rounded assign-to-album-btn\" data-filename=\"$fileName\">$addToAlbum</a>
                    <a href=\"backend_api/delete.php?type=img&filename=$fileName\" class=\"block w-full text-left px-3 py-2 text-sm/6 text-red-400 hover:bg-white/10 rounded\">$delete</a>
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
                <div class=\"w-full aspect-video overflow-hidden border border-gray-300 hover:border-cyan-400 rounded-xs dynamic-image-width transition-[max-width] duration-300 ease-in-out max-w-full md:max-w-none\" style=\"--img-max-width: 200px; max-width: var(--img-max-width);\">
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
                            <a href=\"backend_api/album_set_hero.php?album=$album&filename=$fileName\" class=\"block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100\">".languageString('album.setHero')."</a>
                            <a href=\"backend_api/delete.php?type=album_img&filename=$fileName&albumname=$album\" class=\"image-delete-link block px-4 py-2 text-sm text-red-600 hover:bg-red-100\">".languageString('album.removeFromAlbum')."</a>
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
            $originalFilename = basename($imagePath);
            $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
            $basename = pathinfo($originalFilename, PATHINFO_FILENAME);

            // Prüfen auf Leerzeichen → Umbenennung falls nötig
            if (str_contains($originalFilename, ' ')) {
                $newBase = generateslug($basename);
                $newFilename = $newBase . '.' . $extension;
                $i = 2;

                while (file_exists($imageDir . '/' . $newFilename)) {
                    $newFilename = $newBase . '-' . $i . '.' . $extension;
                    $i++;
                }

                $newPath = $imageDir . '/' . $newFilename;

                if (rename($imagePath, $newPath)) {
                    error_log("Datei umbenannt: $originalFilename → $newFilename");
                    $imagePath = $newPath;
                    $basename = pathinfo($newFilename, PATHINFO_FILENAME);
                } else {
                    error_log("Fehler beim Umbenennen von $originalFilename");
                    continue;
                }
            }

            $usedBaseNames[] = $basename;

            $ymlPath = $imageDir . '/' . $basename . '.yml';
            $mdPath = $imageDir . '/' . $basename . '.md';
            $exifPath = $imageDir . '/' . $basename . '.exif';

            $exif = @exif_read_data($imagePath, 0, true);
            $exifData = extractExifData($imagePath);

            if ($exifData && !file_exists($exifPath)) {
                file_put_contents($exifPath, json_encode($exifData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }

            if (!file_exists($ymlPath)) {
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

        // Verwaiste YAML-Dateien entfernen
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
        if (empty($data['filename'])) {
            error_log("Dateiname fehlt.");
            return false;
        }

        $filename = basename($data['filename']);
        $slug     = pathinfo($filename, PATHINFO_FILENAME);

        $yamlFile = __DIR__ . "/../../userdata/content/images/{$slug}.yml";
        $mdFile   = __DIR__ . "/../../userdata/content/images/{$slug}.md";

        // YAML laden oder leere Struktur
        $yaml = [];
        if (file_exists($yamlFile)) {
            try {
                $yaml = Yaml::parseFile($yamlFile);
            } catch (Exception $e) {
                error_log("Fehler beim Laden der YAML: " . $e->getMessage());
                return false;
            }
        }

        $yaml['image'] = $yaml['image'] ?? [];

        if ($type === 'description') {
            // Titel weiterhin in YAML pflegen (optional)
            if (!empty($data['title'])) {
                $yaml['image']['title'] = trim($data['title']);
            }

            // Beschreibung NICHT in YAML speichern, sondern in .md
            // Falls vorher mal description in YAML existierte, entfernen:
            if (isset($yaml['image']['description'])) {
                unset($yaml['image']['description']);
            }

            // .md schreiben (leer ist erlaubt → löscht/überschreibt Inhalt)
            $desc = isset($data['description']) ? (string)$data['description'] : '';
            try {
                // Du kannst hier weitere Normalisierung machen (z. B. \r\n → \n)
                file_put_contents($mdFile, $desc);
            } catch (Exception $e) {
                error_log("Fehler beim Schreiben der MD-Datei: " . $e->getMessage());
                return false;
            }

        } elseif ($type === 'exif') {
            // Exif initialisieren, falls nötig
            if (!isset($yaml['image']['exif']) || !is_array($yaml['image']['exif'])) {
                $yaml['image']['exif'] = [];
            }

            // Exif-Felder direkt übernehmen (z. B. Camera, Date, etc.)
            if (isset($data['exif']) && is_array($data['exif'])) {
                foreach ($data['exif'] as $key => $value) {
                    $key = trim($key);
                    if ($key !== '' && trim((string)$value) !== '') {
                        $yaml['image']['exif'][$key] = trim($value);
                    }
                }
            }

            // GPS-Daten ergänzen, wenn gültig
            if (isset($data['gps']) && is_array($data['gps'])) {
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
        } elseif ($type === 'tags') {
            // Tags-Array im YAML sicherstellen
            if (!isset($yaml['image']['tags']) || !is_array($yaml['image']['tags'])) {
                $yaml['image']['tags'] = [];
            }

            // --- Entfernen (unverändert) ---
            if (isset($data['remove_tag']) || isset($data['remove_tags'])) {
                $toRemove = [];
                if (isset($data['remove_tag'])) {
                    $toRemove = is_array($data['remove_tag']) ? $data['remove_tag'] : [$data['remove_tag']];
                }
                if (isset($data['remove_tags'])) {
                    $more = is_array($data['remove_tags']) ? $data['remove_tags'] : [$data['remove_tags']];
                    $toRemove = array_merge($toRemove, $more);
                }

                // kommagetrennte Eingaben aufsplitten
                $flat = [];
                foreach ($toRemove as $r) {
                    if (is_string($r) && str_contains($r, ',')) {
                        $flat = array_merge($flat, array_map('trim', explode(',', $r)));
                    } else {
                        $flat[] = is_string($r) ? trim($r) : $r;
                    }
                }

                // leere raus + case-insensitiv vergleichen
                $needles = array_values(array_filter(array_map('mb_strtolower', $flat), fn($v) => $v !== ''));

                if (!empty($needles)) {
                    $yaml['image']['tags'] = array_values(array_filter(
                        $yaml['image']['tags'],
                        fn($t) => !in_array(mb_strtolower(trim((string)$t)), $needles, true)
                    ));
                }
            }

            // --- Hinzufügen/Anhängen (bestehende NICHT löschen) ---
            if (isset($data['tags'])) {
                // String "a, b, c" oder Array -> normalisieren
                $incoming = is_array($data['tags'])
                    ? $data['tags']
                    : array_map('trim', explode(',', (string)$data['tags']));

                // bereinigen
                $incoming = array_values(array_filter(array_map('trim', $incoming), fn($t) => $t !== ''));

                // Duplikate (case-insensitiv) vermeiden und anhängen
                $existing    = $yaml['image']['tags'];
                $existingSet = array_map(fn($t) => mb_strtolower(trim((string)$t)), $existing);

                foreach ($incoming as $tagToAdd) {
                    $k = mb_strtolower($tagToAdd);
                    if (!in_array($k, $existingSet, true)) {
                        $existing[]    = $tagToAdd;  // Original-Case der Eingabe beibehalten
                        $existingSet[] = $k;
                    }
                }

                $yaml['image']['tags'] = array_values($existing);
            }
        }



        // updated_at immer setzen
        $yaml['image']['updated_at'] = date('Y-m-d H:i:s');

        // YAML speichern
        try {
            $dump = Yaml::dump($yaml, 2, 4);
            file_put_contents($yamlFile, $dump);
            return true;
        } catch (Exception $e) {
            error_log("Fehler beim Speichern der YAML: " . $e->getMessage());
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

/**
 * Länder-Liste rendern:
 * - Liest alle *.yml im Bilderverzeichnis
 * - Falls GPS vorhanden und kein CountryCode: via Nominatim ermitteln und in YAML (uppercased) speichern
 * - Zählt pro ISO2-Code (DE, FR, US, ...) – „UN“ für unbekannt
 * - Rendert Liste; Label per Sprachdatei (Fallback: Code)
 *
 * @param bool $mobile Steuert das HTML (mobile vs. Desktop)
 * @return void
 */
function getCountries(bool $mobile): void
{
    $imageDir = __DIR__ . '/../../userdata/content/images/';
    $countryCounts = [];

    if (!is_dir($imageDir)) {
        echo $mobile
            ? "<div class=\"pl-5 text-gray-500\">Keine Daten</div>\n"
            : "<li class=\"text-gray-400\">Keine Daten</li>\n";
        return;
    }

    foreach (glob($imageDir . '*.yml') as $filePath) {
        try {
            $yaml = Yaml::parseFile($filePath);
        } catch (Exception $e) {
            error_log("YAML-Fehler in $filePath: " . $e->getMessage());
            continue;
        }

        $yaml = is_array($yaml) ? $yaml : [];
        $img  = $yaml['image'] ?? [];
        $exif = $img['exif'] ?? [];

        // Aktuellen CountryCode lesen (erwartet 2 Buchstaben)
        $countryCode = $exif['CountryCode'] ?? null;

        // GPS-Koordinaten vorhanden?
        $lat = isset($exif['GPS']['latitude'])  ? (float)$exif['GPS']['latitude']  : null;
        $lon = isset($exif['GPS']['longitude']) ? (float)$exif['GPS']['longitude'] : null;

        // Nur wenn kein valider Code vorhanden und GPS sinnvoll ist -> reverse geocoding
        $needsLookup = !($countryCode && is_string($countryCode) && strlen(trim($countryCode)) === 2 && ctype_alpha($countryCode));
        $hasCoords   = ($lat !== null && $lon !== null && ($lat !== 0.0 || $lon !== 0.0));

        if ($needsLookup && $hasCoords) {
            $code = nominatim_country_code($lat, $lon); // lowercase oder null
            if ($code) {
                // In YAML NUR den Ländercode (uppercase) persistieren
                $yaml['image'] = $yaml['image'] ?? [];
                $yaml['image']['exif'] = $yaml['image']['exif'] ?? [];
                $yaml['image']['exif']['CountryCode'] = strtoupper($code);
                $yaml['image']['updated_at'] = date('Y-m-d H:i:s');

                try {
                    file_put_contents($filePath, Yaml::dump($yaml, 2, 4), LOCK_EX);
                    $countryCode = $yaml['image']['exif']['CountryCode'];
                } catch (Exception $e) {
                    error_log("Fehler beim Speichern CountryCode in $filePath: " . $e->getMessage());
                }
            }
        }

        // Zählen (nur gültige ISO2); sonst „UN“
        if (is_string($countryCode)) {
            $cc = strtoupper(trim($countryCode));
            if (strlen($cc) === 2 && ctype_alpha($cc)) {
                $countryCounts[$cc] = ($countryCounts[$cc] ?? 0) + 1;
                continue;
            }
        }
        $countryCounts['UN'] = ($countryCounts['UN'] ?? 0) + 1; // Unknown
    }

    if (empty($countryCounts)) {
        echo $mobile
            ? "<div class=\"pl-5 text-gray-500\">Keine Daten</div>\n"
            : "<li class=\"text-gray-400\">Keine Daten</li>\n";
        return;
    }

    // Sortierung: UN nach hinten; sonst Anzahl DESC, Code ASC
    uksort($countryCounts, function ($a, $b) use ($countryCounts) {
        if ($a === 'UN' && $b !== 'UN') return 1;
        if ($b === 'UN' && $a !== 'UN') return -1;
        $ca = $countryCounts[$a] ?? 0;
        $cb = $countryCounts[$b] ?? 0;
        if ($ca === $cb) return strcmp($a, $b);
        return $cb <=> $ca;
    });

    // Sprachlabels laden (z. B. aus Session; Fallback 'de')
    $lang = get_language();
    $translations = getCountryTranslations($lang);

    foreach ($countryCounts as $code => $count) {
        $label = $translations[$code] ?? ($code === 'UN' ? ($translations['UN'] ?? 'Unbekannt') : $code);
        $url   = "media.php?country=" . urlencode($code);
        $safe  = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

        $html =
            "<li>
            <a href=\"$url\"
                class=\"group flex items-center rounded-md px-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white\">
                $safe ($count)
            </a>
            </li>";
        echo $html . "\n";
    }
}


/**
 * Reverse-Geocoding via OSM Nominatim → ISO2-Ländercode (lowercase) oder null.
 * - JSON-Disk-Cache (cache/geocode_country.json)
 * - in-memory Cache (static)
 * - sanftes Rate-Limit (1 req/s)
 * - ein einfacher Retry bei HTTP 429
 */
function nominatim_country_code(float $lat, float $lon): ?string
{
    // In-Memory Cache (pro Request-Lebensdauer)
    static $memCache = [];
    $latKey = number_format($lat, 4, '.', ''); // ~11 m
    $lonKey = number_format($lon, 4, '.', '');
    $key    = $latKey . ',' . $lonKey;
    if (isset($memCache[$key])) {
        return $memCache[$key];
    }

    // Cache-Verzeichnis sicherstellen
    $cacheDir = __DIR__ . '/../../cache';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0775, true);
    }
    $cacheFile = $cacheDir . '/geocode_country.json';

    // Disk-Cache laden
    $diskCache = [];
    if (is_file($cacheFile)) {
        $raw = @file_get_contents($cacheFile);
        if ($raw !== false) {
            $dec = json_decode($raw, true);
            if (is_array($dec)) $diskCache = $dec;
        }
    }

    if (!empty($diskCache[$key])) {
        $code = is_string($diskCache[$key]) ? $diskCache[$key] : null;
        $memCache[$key] = $code;
        return $code;
    }

    // Rate-Limit (1 req/s)
    static $lastCall = 0.0;
    $now = microtime(true);
    if ($now - $lastCall < 1.05) {
        usleep((int)((1.05 - ($now - $lastCall)) * 1_000_000));
    }

    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latKey}&lon={$lonKey}&zoom=3&addressdetails=1";
    $ua  = 'Minniark-Media/1.0 (+contact@example.com)'; // <--- bitte echte Kontaktadresse setzen
    $opts = [
        'http' => [
            'header'  => "User-Agent: $ua\r\nAccept: application/json\r\n",
            'timeout' => 8,
        ],
    ];
    $ctx = stream_context_create($opts);

    $json = @file_get_contents($url, false, $ctx);
    $lastCall = microtime(true);

    // einfacher Retry bei 429 (Too Many Requests)
    $status = null;
    if (isset($http_response_header) && is_array($http_response_header)) {
        foreach ($http_response_header as $h) {
            if (preg_match('#^HTTP/\d\.\d\s+(\d{3})#', $h, $m)) {
                $status = (int)$m[1];
                break;
            }
        }
    }
    if ($json === false && $status === 429) {
        usleep(1_500_000); // 1.5s warten
        $json = @file_get_contents($url, false, $ctx);
    }

    if ($json === false) {
        error_log("Nominatim: kein Response für $key");
        return null;
    }

    $data = json_decode($json, true);
    if (!is_array($data)) {
        return null;
    }

    $code = $data['address']['country_code'] ?? null;
    if (!is_string($code) || strlen($code) !== 2) {
        return null;
    }
    $code = strtolower($code);

    // In-Memory + Disk-Cache aktualisieren
    $memCache[$key] = $code;
    $diskCache[$key] = $code;
    @file_put_contents($cacheFile, json_encode($diskCache, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

    return $code;
}


/**
 * Lädt die Übersetzungen der Ländernamen für die Anzeige.
 * Erwartet Datei: __DIR__/../../lang/<lang>.yml mit Struktur:
 *
 *  countries:
 *    UN: Unbekannt
 *    DE: Deutschland
 *    FR: Frankreich
 *    ...
 *
 * @param string|null $lang Sprachcode (z. B. 'de', 'en'); default: $_SESSION['lang'] oder 'de'
 * @return array ['DE' => 'Deutschland', ...]
 */
function getCountryTranslations(?string $lang = null): array
{
    if ($lang === null) {
        $lang = get_language();
    }
    error_log("Language: " . $lang);
    $lang = preg_replace('/[^a-zA-Z_-]/', '', $lang) ?: 'en';

    $langFile = __DIR__ . "/../../language/countries/$lang.yml";
    if (!file_exists($langFile)) {
        // Minimaler Fallback
        return ['UN' => 'Unbekannt'];
    }
    try {
        $yaml = Yaml::parseFile($langFile);
        $map  = $yaml['countries'] ?? [];
        if (!isset($map['UN'])) {
            $map['UN'] = ($lang === 'en') ? 'Unknown' : 'Unbekannt';
        }
        return $map;
    } catch (Exception $e) {
        error_log("Fehler beim Laden der Sprachdatei $langFile: " . $e->getMessage());
        return ['UN' => 'Unbekannt'];
    }
}

/**
 * Zählt alle Tags über alle Bild-YAMLs.
 * - Tags können als Array oder als Komma-String vorliegen
 * - Case-insensitiv zusammenführen, erste Schreibweise behalten
 * - Führendes '#' wird entfernt, Whitespace normalisiert
 *
 * @return array ['counts'=>['tagkey'=>int,...], 'labels'=>['tagkey'=>'Anzeige',...]]
 */
function computeTagCounts(): array
{
    $imageDir = __DIR__ . '/../../userdata/content/images/';
    $counts = [];
    $labels = [];

    if (!is_dir($imageDir)) {
        return ['counts' => [], 'labels' => []];
    }

    foreach (glob($imageDir . '*.yml') as $filePath) {
        try {
            $yaml = Yaml::parseFile($filePath);
        } catch (Exception $e) {
            error_log("YAML-Fehler in $filePath: " . $e->getMessage());
            continue;
        }

        $image = $yaml['image'] ?? [];
        $tags  = $image['tags'] ?? [];

        // Tags können als Array ODER String (kommagetrennt) vorliegen
        if (is_string($tags)) {
            $tags = array_map('trim', explode(',', $tags));
        } elseif (!is_array($tags)) {
            $tags = [];
        }

        foreach ($tags as $tag) {
            if (!is_string($tag)) continue;

            // Normalisieren
            $t = trim(preg_replace('/\s+/', ' ', $tag));
            if ($t === '') continue;
            if ($t[0] === '#') $t = ltrim($t, '#'); // führendes '#' entfernen
            if ($t === '') continue;

            // Case-insensitive Key
            $key = mb_strtolower($t);

            // Erste Schreibweise merken
            if (!isset($labels[$key])) {
                $labels[$key] = $t;
            }

            // Zählen
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }
    }

    return ['counts' => $counts, 'labels' => $labels];
}

/**
 * Rendert eine Tag-Liste (ähnlich Jahr-/Rating-Liste).
 *
 * @param bool   $mobile       true = mobiles HTML, false = Desktop-<li>
 * @param string $sort         'alpha' (A→Z) oder 'count' (häufigste zuerst)
 * @param int    $minCount     nur Tags mit mindestens so vielen Vorkommen
 * @param int    $limit        0 = alle; sonst Top-N nach Sortierung
 * @return void
 */
function getTagsList(bool $mobile, string $sort='alpha', int $minCount=1, int $limit=0): void
{
    $res = computeTagCounts();
    $counts = $res['counts'];
    $labels = $res['labels'];

    if (empty($counts)) {
        echo $mobile
            ? "<div class=\"pl-5 text-gray-500\">Keine Tags</div>\n"
            : "<li class=\"text-gray-400\">Keine Tags</li>\n";
        return;
    }

    // Filtern nach minCount
    $counts = array_filter($counts, fn($c) => $c >= $minCount);

    if (empty($counts)) {
        echo $mobile
            ? "<div class=\"pl-5 text-gray-500\">Keine Tags</div>\n"
            : "<li class=\"text-gray-400\">Keine Tags</li>\n";
        return;
    }

    // Sortierung
    if ($sort === 'count') {
        // Häufigkeit DESC, Tiebreaker Label ASC
        uksort($counts, function($a, $b) use ($counts, $labels) {
            $ca = $counts[$a]; $cb = $counts[$b];
            if ($ca === $cb) {
                return strcasecmp($labels[$a] ?? $a, $labels[$b] ?? $b);
            }
            return $cb <=> $ca;
        });
    } else {
        // Alphabetisch nach Label
        uksort($counts, function($a, $b) use ($labels) {
            return strcasecmp($labels[$a] ?? $a, $labels[$b] ?? $b);
        });
    }

    // Limit anwenden
    if ($limit > 0) {
        $sliceKeys = array_slice(array_keys($counts), 0, $limit);
        $counts = array_intersect_key($counts, array_flip($sliceKeys));
    }

    // Ausgabe
    foreach ($counts as $key => $count) {
        $label = $labels[$key] ?? $key;
        $safe  = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
        $url   = "media.php?tag=" . urlencode($label);

        $html = "
            <li>
            <a href=\"$url\"
                class=\"group flex items-center rounded-md p-1 pl-11 text-sm/6 text-gray-400 hover:bg-white/5 hover:text-white\">
                $safe ($count)
            </a>
            </li>";

        echo $html . "\n";
    }
}
