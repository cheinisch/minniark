<?php

    require_once( __DIR__ . "/../../functions/function_backend.php");
    security_checklogin();

    $file = $_GET['file'];

    $imageData = getImage($file);



    $contentlength = 250;   // Ziel-Wortzahl (ändern falls Zeichen gewünscht)
    $language      = "en";  // "de", "en", ...

    $meta = [
    'title'        => $imageData['title']                 ?? null,
    'camera'       => $imageData['exif']['Camera']        ?? null,
    'lens'         => $imageData['exif']['Lens']          ?? null,
    'aperture'     => $imageData['exif']['Aperture']      ?? null,
    'shutter'      => $imageData['exif']['Shutter Speed'] ?? null,
    'iso'          => $imageData['exif']['ISO']           ?? null,
    'focal_length' => $imageData['exif']['Focal Length']  ?? null,
    'date_taken'   => $imageData['exif']['Date']          ?? null,
    'gps'          => [
        'has' => !is_null($latitude) && !is_null($longitude),
        'lat' => $latitude,
        'lon' => $longitude
    ],
    ];

    $imageText = generateOpenAIImageText($meta, $contentlength, $language);


    echo $imageText;

?>