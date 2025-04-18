<?php

    function renderImageGallery() {


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
                }
            }

            $title = !empty($metadata['title']) ? htmlspecialchars($metadata['title']) : "Kein Titel";
            $description = !empty($metadata['description']) ? htmlspecialchars($metadata['description']) : "Keine Beschreibung verfügbar";

            // HTML für das Bild generieren
            echo "<div class=\"w-full aspect-video overflow-hidden hover:border hover:border-sky-400 hover:rounded-xs dynamic-image-width transition-[max-width] duration-300 ease-in-out max-w-full md:max-w-none\" style=\"--img-max-width: 250px; max-width: var(--img-max-width);\">
                    <a href=\"#\">
                        <img src='$image' class=\"w-full h-full object-cover\" />
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