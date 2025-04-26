<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log("Hallo Welt");

require_once(__DIR__ . "/../functions/function_api.php");
header("Content-Type: application/json");

secure_API();

// 🔽 JSON-Daten auslesen
$data = json_decode(file_get_contents("php://input"), true);
$filename = $data['filename'] ?? '';

$imagePath = "../userdata/content/images/" . $filename;
$jsonPath = "../userdata/content/images/" . pathinfo($filename, PATHINFO_FILENAME) . ".json";

// 🔎 Datei vorhanden?
if (!$filename || !file_exists($imagePath)) {
    echo json_encode(["success" => false, "error" => "Datei nicht gefunden."]);
    exit;
}
if (!file_exists($jsonPath)) {
    echo json_encode(["success" => false, "error" => "JSON-Datei nicht gefunden."]);
    exit;
}

error_log("Verwende Bilddatei: " . realpath($imagePath));

// EXIF-Daten auslesen
$formattedExif = extractExifData($imagePath);
if (empty($formattedExif)) {
    echo json_encode(["success" => false, "error" => "Keine EXIF-Daten gefunden."]);
    exit;
}

//error_log(print_r($formattedExif));

//  Vorhandene JSON-Daten laden
$existing = json_decode(file_get_contents($jsonPath), true);

// 🛠️ Zusammenführen: Nur neue/existierende, sinnvolle EXIF-Werte übernehmen
foreach ($formattedExif as $key => $value) {
    $isValid = !empty($value) && (!is_string($value) || strtolower($value) !== 'unknown');

    if ($isValid) {
        $existing['exif'][$key] = $value;
        error_log("Aktualisiere $key: " . (is_array($value) ? json_encode($value) : $value));
    } else {
        error_log("Behalte $key (bisher: " . (isset($existing['exif'][$key]) ? json_encode($existing['exif'][$key]) : 'nicht gesetzt') . ")");
    }
}
//error_log("JSON vor Schreiben: " . json_encode($json, JSON_PRETTY_PRINT));

//  Speichern
file_put_contents($jsonPath, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo json_encode(["success" => true]);
