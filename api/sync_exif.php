<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log("Starte EXIF-Update");

require_once(__DIR__ . "/../functions/function_api.php");
require_once(__DIR__ . "/../functions/backend/exifdata.php");
require_once(__DIR__ . "/../vendor/autoload.php");

use Symfony\Component\Yaml\Yaml;

header("Content-Type: application/json");
secure_API();

// 🔽 JSON-Daten auslesen
$data = json_decode(file_get_contents("php://input"), true);
$filename = $data['filename'] ?? '';

$imagePath = realpath(__DIR__ . "/../userdata/content/images/" . $filename);
$basename = pathinfo($filename, PATHINFO_FILENAME);
$ymlPath  = __DIR__ . "/../userdata/content/images/" . $basename . ".yml";

// 🔎 Datei vorhanden?
if (!$filename || !$imagePath || !file_exists($imagePath)) {
    echo json_encode(["success" => false, "error" => "Bilddatei nicht gefunden."]);
    exit;
}
if (!file_exists($ymlPath)) {
    echo json_encode(["success" => false, "error" => "YAML-Datei nicht gefunden."]);
    exit;
}

error_log("Verwende Bilddatei: " . $imagePath);

// Neue EXIF-Daten extrahieren
$formattedExif = extractExifData($imagePath);
if (empty($formattedExif)) {
    echo json_encode(["success" => false, "error" => "Keine EXIF-Daten gefunden."]);
    exit;
}

// YAML laden
try {
    $yaml = Yaml::parseFile($ymlPath);
    $image = $yaml['image'] ?? [];
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "Fehler beim Lesen der YAML-Datei.", "message" => $e->getMessage()]);
    exit;
}

// EXIF mergen (nur gültige Werte übernehmen)
foreach ($formattedExif as $key => $value) {
    $isValid = !empty($value) && (!is_string($value) || strtolower($value) !== 'unknown');

    if ($isValid) {
        $image['exif'][$key] = $value;
        error_log("Aktualisiere $key: " . (is_array($value) ? json_encode($value) : $value));
    } else {
        error_log("Behalte $key (bisher: " . (isset($image['exif'][$key]) ? json_encode($image['exif'][$key]) : 'nicht gesetzt') . ")");
    }
}

// YAML wieder schreiben
try {
    $yaml['image'] = $image;
    file_put_contents($ymlPath, Yaml::dump($yaml, 2, 4));
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "Fehler beim Schreiben der YAML-Datei.", "message" => $e->getMessage()]);
    exit;
}

echo json_encode(["success" => true]);
