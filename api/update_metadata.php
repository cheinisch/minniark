<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

require_once(__DIR__ . "/../functions/function_api.php");
require_once(__DIR__ . "/../vendor/autoload.php");

use Symfony\Component\Yaml\Yaml;

secure_API();

// JSON lesen und prüfen
$data = json_decode(file_get_contents("php://input"), true);
error_log("Data Array: ".print_r($data, true));
if (!is_array($data) || empty($data['filename'])) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Eingabe oder fehlender Dateiname.']);
    exit;
}

$filename = basename($data['filename']);
$slug = pathinfo($filename, PATHINFO_FILENAME);
$ymlPath = __DIR__ . "/../userdata/content/images/{$slug}.yml";
$mdPath = __DIR__ . "/../userdata/content/images/{$slug}.md";

// Existiert die Bild-YML?
if (!file_exists($ymlPath)) {
    echo json_encode(['success' => false, 'error' => 'YAML-Datei nicht gefunden.']);
    exit;
}

// Bestehende Daten laden
try {
    $yaml = Yaml::parseFile($ymlPath);
    $image = $yaml['image'] ?? [];
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Parsen der YAML.', 'message' => $e->getMessage()]);
    exit;
}

// 🔄 EXIF-Mapping
$exifUpdates = $data['exif'] ?? [];
$allowedExifKeys = [
    'camera' => 'Camera',
    'lens' => 'Lens',
    'aperture' => 'Aperture',
    'shutter speed' => 'Shutter Speed',
    'iso' => 'ISO',
    'focal length' => 'Focal Length',
    'date' => 'Date',
];

$image['exif'] = $image['exif'] ?? [];

foreach ($exifUpdates as $key => $value) {
    $keyLower = strtolower(trim($key));
    if (!empty($value)) {
        $image['exif'][$mappedKey] = trim($value);
    }
}

// 📍 GPS
if (isset($data['gps']) && is_array($data['gps'])) {
    $image['exif']['GPS'] = $image['exif']['GPS'] ?? [];

    if (isset($data['gps']['latitude'])) {
        $image['exif']['GPS']['latitude'] = (float) $data['gps']['latitude'];
    }
    if (isset($data['gps']['longitude'])) {
        $image['exif']['GPS']['longitude'] = (float) $data['gps']['longitude'];
    }
}

// ✏️ Title
if (isset($data['title'])) {
    $image['title'] = trim($data['title']);
}

// 📄 Description (.md)
if (isset($data['description'])) {
    file_put_contents($mdPath, trim($data['description']));
}

// 🕓 updated_at
$image['updated_at'] = date('Y-m-d H:i:s');

// Speichern
$yaml['image'] = $image;

try {
    if (file_put_contents($ymlPath, Yaml::dump($yaml, 2, 4)) === false) {
        throw new Exception("file_put_contents fehlgeschlagen");
    }

    // Optional: OPCache invalidieren
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($ymlPath, true);
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Speichern', 'message' => $e->getMessage()]);
    exit;
}
