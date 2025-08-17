<?php

    require_once( __DIR__ . "/../../functions/function_backend.php");
    security_checklogin();

    $file = $_GET['file'];

    $imageData = getImage($file);



    $contentlength = 250;   // Ziel-Wortzahl (ändern falls Zeichen gewünscht)
    $language      = "en";  // "de", "en", ...

    // GPS-Daten für OpenStreetMap
    $latitude = $imageData['exif']['GPS']['latitude'] ?? 0;
    $longitude = $imageData['exif']['GPS']['longitude'] ?? 0;
  
    $hasGPS = !is_null($latitude) && !is_null($longitude);

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

    // 1) OpenAI-Text generieren
$generated = generateOpenAIImageText($meta, $contentlength, $language);

// Fehlerbehandlung OpenAI
if (is_string($generated) && str_starts_with($generated, 'Error:')) {
  // optional: Fehlermeldung loggen/weiterreichen
  error_log("AI generation failed: " . $generated);
  header("Location: ../media-detail.php?image=" . urlencode($file) . "&gen=fail");
  exit;
}

    // 2) YAML speichern (updateImage erwartet Daten + Typ)
        $saveData = [
        'filename'    => $imageData['filename'],          // WICHTIG!
        'title'       => $imageData['title'] ?? null,     // optional
        'description' => $generated,                      // neue Beschreibung
        ];

        $ok = updateImage($saveData, 'description');

        // 3) Redirect zurück zur Detailseite
        if ($ok) {
        header("Location: ../media-detail.php?image=" . urlencode($file) . "&gen=ok");
        exit;
        } else {
        error_log("updateImage failed for {$imageData['filename']}");
        header("Location: ../media-detail.php?image=" . urlencode($file) . "&gen=savefail");
        exit;
        }

?>