<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Berlin');

require_once(__DIR__ . "/../functions/function_api.php");
require_once(__DIR__ . "/../vendor/autoload.php"); // für Symfony YAML

use Symfony\Component\Yaml\Yaml;

secure_API();

// Debug-Logger
function logMessage($message) {
    file_put_contents(__DIR__ . '/upload_log.txt', date("[Y-m-d H:i:s] ") . $message . PHP_EOL, FILE_APPEND);
}

// Pfade
$uploadDir = __DIR__ . '/../userdata/content/images/';
$cacheDir  = __DIR__ . '/../cache/images/';

// Verzeichnisse sicherstellen
foreach ([$uploadDir, $cacheDir] as $dir) {
    if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
        die(json_encode(["error" => "Verzeichnis konnte nicht erstellt werden: $dir"]));
    }
    chmod($dir, 0777);
}

// Datei prüfen
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    die(json_encode(["error" => "Keine Datei übergeben."]));
}

$file = $_FILES['file'];
$fileName = basename($file['name']);
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($fileExt, $allowedTypes)) {
    die(json_encode(["error" => "Ungültiger Dateityp. Erlaubt: JPG, PNG, WEBP."]));
}

// Doppelte Namen vermeiden
$baseName = pathinfo($fileName, PATHINFO_FILENAME);
$counter = 1;
while (file_exists($uploadDir . $fileName)) {
    $fileName = $baseName . "_$counter." . $fileExt;
    $counter++;
}
$targetFile = $uploadDir . $fileName;

// Datei speichern
if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
    die(json_encode(["error" => "Fehler beim Speichern der Datei."]));
}
logMessage("Hochgeladen: $fileName");

// GUID erzeugen
$guid = uniqid();
logMessage("GUID: $guid");

// EXIF lesen
$exifData = extractExifData($targetFile);

// Vorschaubilder erstellen
$sizes = ['S' => 150, 'M' => 500, 'L' => 1024, 'XL' => 1920, 'XXL' => 3440];
foreach ($sizes as $sizeKey => $sizeValue) {
    $thumbPath = $cacheDir . $guid . "_$sizeKey." . $fileExt;
    createThumbnail($targetFile, $thumbPath, $sizeValue);
    logMessage("Thumbnail: $thumbPath");
}

// YAML-Datenstruktur
$yamlData = [
    'image' => [
        'filename'    => $fileName,
        'guid'        => $guid,
        'title'       => '',
        'description' => '', // optional in .md
        'tags'        => [],
        'rating'      => 0,
        'exif'        => $exifData,
        'created_at'  => date('Y-m-d H:i:s'),
        'uploaded_at' => date('Y-m-d H:i:s'),
    ]
];

    // Speichern als YML
    $slug = pathinfo($fileName, PATHINFO_FILENAME);
    $ymlFile = $uploadDir . $slug . '.yml';

    // Save Exif Data

    $exifPath  = $uploadDir . '/' . $slug . '.exif';

    if ($exifData) {
        file_put_contents($exifPath, json_encode($exifData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        error_log("EXIF konnte nicht gelesen werden oder fehlt.");
    }

try {
    file_put_contents($ymlFile, Yaml::dump($yamlData, 2, 4));
    logMessage("Metadaten gespeichert: $ymlFile");
} catch (Exception $e) {
    logMessage("Fehler beim Schreiben der YAML: " . $e->getMessage());
    die(json_encode(["error" => "YAML konnte nicht gespeichert werden."]));
}

// Optional .md anlegen für Beschreibung
$mdPath = $uploadDir . $slug . '.md';
file_put_contents($mdPath, '');

// Rückgabe
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "success" => true,
    "filename" => $fileName,
    "guid" => $guid
]);
exit;
