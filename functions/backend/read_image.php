<?php

function getImage($imagename)
{

    
    
    $fileName = $imagename;
    $jsonFiles = glob("../userdata/content/images/*.json");
    
    $imageData = null;
    
    // Passende JSON-Datei anhand des Dateinamens suchen
    foreach ($jsonFiles as $file) {
        $jsonData = file_get_contents($file);
        $image = json_decode($jsonData, true);
        if ($image && $image['filename'] === $fileName) {
            $imageData = $image;
            return $imageData;
            break;
        }
    }
}
