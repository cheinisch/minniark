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

    function get_imageyearlist()
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
            echo "<li><a href=\"?year=$year\" class=\"text-gray-400 hover:text-sky-400\">$year ($count)</a></li>\n";
        }


    }

    function renderImageGallery($filterYear = null) {
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
    
            // Wenn ein Filter gesetzt ist, aber das Jahr nicht passt → überspringen
            if ($filterYear !== null && $year !== $filterYear) {
                continue;
            }
    
            $title = !empty($metadata['title']) ? htmlspecialchars($metadata['title']) : "Kein Titel";
            $description = !empty($metadata['description']) ? htmlspecialchars($metadata['description']) : "Keine Beschreibung verfügbar";
    
            // HTML für das Bild generieren
            echo "<div class=\"w-full aspect-video overflow-hidden hover:border hover:border-sky-400 hover:rounded-xs dynamic-image-width transition-[max-width] duration-300 ease-in-out max-w-full md:max-w-none\" style=\"--img-max-width: 250px; max-width: var(--img-max-width);\">
                    <a href=\"#\">
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