<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

require_once(__DIR__ . "/../functions/function_api.php");
secure_API();

// JSON einlesen und prüfen
$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    echo json_encode(["success" => false, "error" => "Ungültige JSON-Daten."]);
    exit;
}

// Dateiname validieren und Pfad sichern
$filename = basename($data['filename'] ?? '');
$imagePath = realpath("../userdata/content/images/$filename");

if (!$filename || !$imagePath || !file_exists($imagePath)) {
    echo json_encode(["success" => false, "error" => "Bilddatei nicht gefunden."]);
    exit;
}

// JSON-Datei laden
$jsonPath = realpath("../userdata/content/images/") . "/" . pathinfo($filename, PATHINFO_FILENAME) . ".json";
if (!file_exists($jsonPath)) {
    echo json_encode(["success" => false, "error" => "JSON-Datei nicht gefunden."]);
    exit;
}

$json = json_decode(file_get_contents($jsonPath), true);

// Titel & Beschreibung aktualisieren
if (isset($data['title'])) {
    $json['title'] = trim($data['title']);
}
if (isset($data['description'])) {
    $json['description'] = trim($data['description']);
}

// EXIF-Daten aktualisieren
$exifUpdates = $data['exif'] ?? [];
$allowedExifKeys = ['Camera', 'Lens', 'Aperture', 'Shutter Speed', 'ISO', 'Focal Length', 'Date'];

if (!isset($json['exif'])) {
    $json['exif'] = [];
}

foreach ($allowedExifKeys as $expectedKey) {
    foreach ($exifUpdates as $givenKey => $value) {
        if (strtolower($givenKey) === strtolower($expectedKey)) {
            if (!empty($value) && strtolower($value) !== 'unknown') {
                $json['exif'][$expectedKey] = trim($value);
            }
        }
    }
}

// GPS-Daten aktualisieren
if (isset($data['gps'])) {
    if (!isset($json['exif']['GPS'])) {
        $json['exif']['GPS'] = [];
    }

    if (isset($data['gps']['latitude'])) {
        $json['exif']['GPS']['latitude'] = (float) $data['gps']['latitude'];
    }

    if (isset($data['gps']['longitude'])) {
        $json['exif']['GPS']['longitude'] = (float) $data['gps']['longitude'];
    }
}

// Debug Logging
error_log("Eingehende Daten: " . json_encode($data));
error_log("Neue Metadaten: " . json_encode($exifUpdates));
error_log("JSON vor Schreiben: " . json_encode($json, JSON_PRETTY_PRINT));

// Datei speichern
$result = file_put_contents($jsonPath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

if ($result === false) {
    echo json_encode(["success" => false, "error" => "Fehler beim Speichern der Datei."]);
    error_log("Schreibfehler bei: $jsonPath");
    exit;
}

echo json_encode(["success" => true]);
