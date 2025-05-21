<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

require_once(__DIR__ . "/../functions/function_api.php");
require_once(__DIR__ . "/../vendor/autoload.php");

use Symfony\Component\Yaml\Yaml;

secure_API(); // Absichern

// JSON einlesen
$data = json_decode(file_get_contents("php://input"), true);
error_log("Eingehende Daten: " . print_r($data, true));

if (!is_array($data) || empty($data['filename'])) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Eingabe oder fehlender Dateiname.']);
    exit;
}

// Pfade bestimmen
$filename = basename($data['filename']);
$slug     = pathinfo($filename, PATHINFO_FILENAME);
$ymlPath  = __DIR__ . "/../userdata/content/images/{$slug}.yml";

// YAML-Datei prüfen
if (!file_exists($ymlPath)) {
    echo json_encode(['success' => false, 'error' => 'YAML-Datei nicht gefunden.']);
    exit;
}

// Bestehende YAML-Daten laden
try {
    $yaml  = Yaml::parseFile($ymlPath);
    $image = $yaml['image'] ?? [];
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Parsen der YAML.', 'message' => $e->getMessage()]);
    exit;
}

// =======================
// 🔄 EXIF aktualisieren
// =======================

$exifUpdates = $data['exif'] ?? [];
$allowedExifKeys = [
    'camera'        => 'Camera',
    'lens'          => 'Lens',
    'aperture'      => 'Aperture',
    'shutter speed' => 'Shutter Speed',
    'iso'           => 'ISO',
    'focal length'  => 'Focal Length',
    'date'          => 'Date',
];

$image['exif'] = $image['exif'] ?? [];

foreach ($exifUpdates as $key => $value) {
    $keyLower = strtolower(trim($key));
    if (isset($allowedExifKeys[$keyLower])) {
        $mappedKey = $allowedExifKeys[$keyLower];
        $image['exif'][$mappedKey] = trim($value);
        error_log("EXIF gesetzt: $mappedKey = $value");
    }
}

// =======================
// 📍 GPS aktualisieren
// =======================

if (isset($data['gps']) && is_array($data['gps'])) {
    $image['exif']['GPS'] = $image['exif']['GPS'] ?? [];

    if (isset($data['gps']['latitude'])) {
        $image['exif']['GPS']['latitude'] = (float) $data['gps']['latitude'];
    }
    if (isset($data['gps']['longitude'])) {
        $image['exif']['GPS']['longitude'] = (float) $data['gps']['longitude'];
    }
}

// =======================
// 🕓 updated_at setzen
// =======================

$image['updated_at'] = date('Y-m-d H:i:s');

// YAML schreiben
$yaml['image'] = $image;

try {
    $dump = Yaml::dump($yaml, 2, 4);
    if (file_put_contents($ymlPath, $dump) === false) {
        throw new Exception("file_put_contents fehlgeschlagen");
    }

    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($ymlPath, true);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Fehler beim Speichern: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Fehler beim Speichern', 'message' => $e->getMessage()]);
    exit;
}
