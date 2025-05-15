<?php

    function get_cacheimage($filename)
    {
        $settingsPath = __DIR__ . '/../../userdata/config/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $size = $settings['default_image_size'] ?? "";

        if($size == "Original")
        {
            return '/userdata/content/images/'.$filename;
        }

        // Dateiendung entfernen, falls vorhanden (z. B. ".jpg", ".png")
        $filename = pathinfo($filename, PATHINFO_FILENAME);

        // Pfad zum Verzeichnis und JSON-Datei
        $imageDir = __DIR__ . '/../../userdata/content/images/';
        $jsonPath = $imageDir . $filename . '.json';

        // Prüfen, ob JSON-Datei existiert
        if (!file_exists($jsonPath)) {
            return null;
        }

        // JSON lesen
        $jsonContent = file_get_contents($jsonPath);
        if ($jsonContent === false) {
            return null;
        }

        // JSON dekodieren
        $meta = json_decode($jsonContent, true);
        if (!is_array($meta) || !isset($meta['guid'])) {
            return null;
        }


        $newSize = strtoupper($size);

        $image = $meta['guid']."_".$newSize.".jpg";

        $image = '/cache/images/'.$image;

        return $image;
    }