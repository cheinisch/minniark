<?php

    require_once( __DIR__ . "/../../functions/function_backend.php");
    security_checklogin();

    $file = $_GET['file'];


    // Protokoll erkennen (auch hinter Proxy berücksichtigen)
    $scheme = 'http';
    if (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    ) {
        $scheme = 'https';
    }

    // Host bestimmen (X-Forwarded-Host falls vorhanden, sonst HTTP_HOST)
    $host = !empty($_SERVER['HTTP_X_FORWARDED_HOST'])
        ? $_SERVER['HTTP_X_FORWARDED_HOST']
        : ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost');

    // Datei aus GET absichern (Pfad-Traversal verhindern)
    $file = isset($_GET['file']) ? basename($_GET['file']) : '';
    // Ggf. nur bestimmte Endungen erlauben
    // if (!preg_match('/\.(png|jpe?g|gif|webp)$/i', $file)) { die('Ungültige Datei'); }

    $imageData = getImage($file);

    // Absolute URL bauen
    $url = $scheme . '://' . $host . '/userdata/content/images/' . rawurlencode($file);



    $contentlength = 250;   // Ziel-Wortzahl (ändern falls Zeichen gewünscht)
    $language      = get_language();  // "de", "en", ...

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

    $tags = $imageData['tags'] ?? [];

    // 1)AI-Text generieren
    $generated = generateOpenRouterImageText($meta, $tags, $contentlength, $language, $url);

    // Fehlerbehandlung AI
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