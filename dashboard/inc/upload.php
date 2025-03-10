<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $uploadDir = __DIR__ . "/../../content/images/"; // Zielverzeichnis

    // Falls das Verzeichnis nicht existiert, erstelle es
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            echo json_encode(["error" => "Upload-Verzeichnis konnte nicht erstellt werden."]);
            exit;
        }
    }

    $fileName = basename($_FILES["file"]["name"]);
    $filePath = $uploadDir . $fileName;
    $jsonPath = $uploadDir . pathinfo($fileName, PATHINFO_FILENAME) . ".json";

    $allowedTypes = ['image/png', 'image/jpeg', 'image/gif'];
    $fileType = mime_content_type($_FILES["file"]["tmp_name"]);

    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(["error" => "Nur PNG, JPG und GIF-Dateien sind erlaubt."]);
        exit;
    }

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
        // EXIF-Daten extrahieren (nur bei JPEG-Bildern)
        $exifData = [];
        if ($fileType == 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($filePath, 'EXIF');

            if ($exif) {
                $exifData = [
                    'camera' => $exif['Model'] ?? '',
                    'lens' => $exif['UndefinedTag:0xA434'] ?? '',
                    'aperture' => isset($exif['COMPUTED']['ApertureFNumber']) ? $exif['COMPUTED']['ApertureFNumber'] : '',
                    'shutter_speed' => isset($exif['ExposureTime']) ? $exif['ExposureTime'] . ' sec' : '',
                    'iso' => $exif['ISOSpeedRatings'] ?? '',
                    'date' => isset($exif['DateTimeOriginal']) ? date('Y-m-d H:i:s', strtotime($exif['DateTimeOriginal'])) : '',
                    'gps' => $gpsData
                ];
            }
        }

        // JSON-Daten vorbereiten
        $jsonData = [
            "title" => $fileName,
            "description" => '',
            "tags" => [],
            "date_uploaded" => date("Y-m-d H:i:s"),
            "exif" => $exifData
        ];

        // JSON-Datei speichern
        file_put_contents($jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT));

        echo json_encode(["success" => "✅ Datei hochgeladen und JSON erstellt: " . $fileName]);
    } else {
        echo json_encode(["error" => "Fehler beim Hochladen."]);
    }
} else {
    echo json_encode(["error" => "Keine Datei erhalten."]);
}

/**
 * Extrahiert GPS-Koordinaten aus den EXIF-Daten.
 */
function extractGpsData($exif) {
    if (!isset($exif["GPSLatitude"]) || !isset($exif["GPSLongitude"]) || 
        !isset($exif["GPSLatitudeRef"]) || !isset($exif["GPSLongitudeRef"])) {
        return null; // Keine GPS-Daten vorhanden
    }

    $lat = convertGpsToDecimal($exif["GPSLatitude"], $exif["GPSLatitudeRef"]);
    $lon = convertGpsToDecimal($exif["GPSLongitude"], $exif["GPSLongitudeRef"]);

    return [
        "latitude" => $lat,
        "longitude" => $lon
    ];
}

/**
 * Konvertiert GPS-Koordinaten ins Dezimalformat.
 */
function convertGpsToDecimal($gpsCoord, $hemisphere) {
    $degrees = count($gpsCoord) > 0 ? gpsToFloat($gpsCoord[0]) : 0;
    $minutes = count($gpsCoord) > 1 ? gpsToFloat($gpsCoord[1]) : 0;
    $seconds = count($gpsCoord) > 2 ? gpsToFloat($gpsCoord[2]) : 0;

    $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

    return ($hemisphere == 'S' || $hemisphere == 'W') ? -$decimal : $decimal;
}

/**
 * Wandelt eine GPS-Zahl in eine Float-Zahl um.
 */
function gpsToFloat($gpsPart) {
    $parts = explode('/', $gpsPart);
    if (count($parts) == 2) {
        return floatval($parts[0]) / floatval($parts[1]);
    }
    return floatval($gpsPart);
}
?>
