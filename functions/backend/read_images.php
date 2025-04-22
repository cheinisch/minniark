<?php


    function count_images()
    {
        $imageDir = '../content/images/';
        $counter = 0;
        foreach (scandir($imageDir) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $counter = $counter+1;
            }
        }
        echo $counter;
    }

    function get_imageyearlist($mobile)
    {
        $imageDir = '../content/images/';
        $yearCounts = [];

        // Alle Dateien im Verzeichnis durchgehen
        foreach (scandir($imageDir) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $filePath = $imageDir . $file;
                $jsonContent = file_get_contents($filePath);
                $data = json_decode($jsonContent, true);

                if (isset($data['exif']['Date'])) {
                    $date = $data['exif']['Date'];
                    $year = substr($date, 0, 4); // z.B. "2021:09:25 ..." → "2021"

                    if (!empty($year)) {
                        if (!isset($yearCounts[$year])) {
                            $yearCounts[$year] = 0;
                        }
                        $yearCounts[$year]++;
                    }
                }
            }
        }

        // Nach Jahr sortieren
        ksort($yearCounts);

        // Ausgabe
        foreach ($yearCounts as $year => $count) {
            if($mobile)
            {
                echo "<div class=\"pl-5\">
                    <a href=\"media.php?year=$year\" class=\"block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6\">$year ($count)</a>
                </div>\n";
            }else{
                echo "<li><a href=\"media.php?year=$year\" class=\"text-gray-400 hover:text-sky-400\">$year ($count)</a></li>\n";
            }
        }


    }

    function get_ratinglist($mobile)
{
    $imageDir = '../content/images/';
    $ratingCounts = [];

    foreach (scandir($imageDir) as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
            $filePath = $imageDir . $file;
            $data = json_decode(file_get_contents($filePath), true);
            $rating = isset($data['rating']) ? (int)$data['rating'] : 0;
            $ratingCounts[$rating] = ($ratingCounts[$rating] ?? 0) + 1;
        }
    }

    // Sicherstellen, dass 0–5 enthalten sind
    for ($i = 0; $i <= 5; $i++) {
        $ratingCounts[$i] = $ratingCounts[$i] ?? 0;
    }

    krsort($ratingCounts);

    foreach ($ratingCounts as $rating => $count) {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $colorClass = $i <= $rating ? 'text-sky-400' : 'text-gray-300';
            $stars .= '<svg class="w-4 h-4 inline-block ' . $colorClass . '" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.137 3.5h3.684
                          c.969 0 1.371 1.24.588 1.81l-2.984 2.17 1.138 3.5
                          c.3.921-.755 1.688-1.538 1.117L10 13.348l-2.976 2.176
                          c-.783.571-1.838-.196-1.538-1.117l1.138-3.5-2.984-2.17
                          c-.783-.57-.38-1.81.588-1.81h3.684l1.137-3.5z"/>
                      </svg>';
        }

        if ($mobile) {
            echo "<div class=\"pl-5\">
                    <a href=\"media.php?rating=$rating\" class=\"block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6\">
                        $stars ($count)
                    </a>
                  </div>\n";
        } else {
            echo "<li>
                    <a href=\"media.php?rating=$rating\" class=\"text-gray-400 hover:text-sky-400\">
                        $stars ($count)
                    </a>
                  </li>\n";
        }
    }
}


    

    function renderImageGallery($filterYear = null, $filterRating = null) {
        $imageDir = '../content/images/';
        $images = getImagesFromDirectory($imageDir);
    
        foreach ($images as $image) {
            $fileName = basename($image);
            $jsonFile = $imageDir . pathinfo($fileName, PATHINFO_FILENAME) . '.json';
            $metadata = [];
    
            // JSON-Daten auslesen und validieren
            if (file_exists($jsonFile)) {
                $jsonData = file_get_contents($jsonFile);
                $decodedData = json_decode($jsonData, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $metadata = $decodedData;
                } else {
                    error_log("Fehler beim Parsen von JSON: " . json_last_error_msg());
                    continue;
                }
            } else {
                continue; // JSON fehlt → Bild überspringen
            }
    
            // Aufnahmejahr auslesen
            $exifDate = $metadata['exif']['Date'] ?? null;
            $year = $exifDate ? substr($exifDate, 0, 4) : null;
            $rating = $metadata['rating'] ?? 0;
    
            // Wenn ein Filter gesetzt ist, aber das Jahr nicht passt → überspringen
            if ($filterYear !== null && $year !== $filterYear) {
                continue;
            }

            if ($filterRating !== null && (int)$rating !== (int)$filterRating) {
                continue;
            }
    
            $title = !empty($metadata['title']) ? htmlspecialchars($metadata['title']) : "Kein Titel";
            $description = !empty($metadata['description']) ? htmlspecialchars($metadata['description']) : "Keine Beschreibung verfügbar";
    
            // HTML für das Bild generieren
            echo "<div class=\"w-full aspect-video overflow-hidden border border-gray-300 hover:border-sky-400 rounded-xs dynamic-image-width transition-[max-width] duration-300 ease-in-out max-w-full md:max-w-none\" style=\"--img-max-width: 250px; max-width: var(--img-max-width);\">
                    <a href=\"media-detail.php?image=" . urlencode($fileName) . "\">
                        <img src='$image' class=\"w-full h-full object-cover\" alt=\"$title\" title=\"$description\" />
                    </a>
                </div>";
        }
    }
    

    function getImagesFromDirectory($directory = "../content/images/") {
        if (!is_dir($directory)) {
            return [];
        }
    
        // Alle Bilddateien abrufen (Rückgabe als Array von Strings)
        $images = glob($directory . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
    
        if (!is_array($images)) {
            error_log("Fehler: getImagesFromDirectory() gibt kein Array zurück.");
            return [];
        }
    
        return $images;
    }