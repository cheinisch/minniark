<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Berlin');

require_once(__DIR__ . "/../functions/function_api.php");
require_once(__DIR__ . "/../vendor/autoload.php"); // Symfony YAML

use Symfony\Component\Yaml\Yaml;

secure_API();

// Log-Funktion
function logMessage($msg) {
    file_put_contents(__DIR__ . '/upload_log.txt', date("[Y-m-d H:i:s] ") . $msg . PHP_EOL, FILE_APPEND);
}

// Pfade
$uploadDir = realpath(__DIR__ . '/../userdata/content/images/') . '/';
$cacheDir  = realpath(__DIR__ . '/../cache/images/') . '/';

foreach ([$uploadDir, $cacheDir] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    chmod($dir, 0777);
}

// Prüfe Datei
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    die(json_encode(["error" => "Keine Datei hochgeladen."]));
}

$file = $_FILES['file'];
$originalName = basename($file['name']);
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
$allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($extension, $allowedTypes)) {
    die(json_encode(["error" => "Ungültiger Dateityp. Erlaubt: JPG, PNG, WEBP."]));
}

// Slug-Name erzeugen (ohne Extension)
$baseName = pathinfo($originalName, PATHINFO_FILENAME);
$slug = generateSlug($baseName);

// Dateinamen prüfen und ggf. erhöhen (für Datei, YAML, MD)
$finalSlug = $slug;
$counter = 1;
while (
    file_exists($uploadDir . $finalSlug . '.' . $extension) ||
    file_exists($uploadDir . $finalSlug . '.yml') ||
    file_exists($uploadDir . $finalSlug . '.md')
) {
    $finalSlug = $slug . '-' . $counter;
    $counter++;
}

$finalFileName = $finalSlug . '.' . $extension;
$finalPath     = $uploadDir . $finalFileName;

// Datei speichern
if (!move_uploaded_file($file['tmp_name'], $finalPath)) {
    die(json_encode(["error" => "Fehler beim Speichern der Datei."]));
}
logMessage("Bild gespeichert: $finalFileName");

// GUID generieren
$guid = uniqid();
logMessage("GUID: $guid");

// EXIF lesen
$exifData = extractExifData($finalPath);

// Thumbnails
$sizes = ['S' => 150, 'M' => 500, 'L' => 1024, 'XL' => 1920, 'XXL' => 3440];
foreach ($sizes as $key => $size) {
    $thumbPath = $cacheDir . $guid . "_$key." . $extension;
    createThumbnail($finalPath, $thumbPath, $size);
    logMessage("Thumbnail erzeugt: $thumbPath");
}

// YAML-Daten
$yamlData = [
    'image' => [
        'filename'    => $finalFileName,
        'guid'        => $guid,
        'title'       => '',
        'description' => '',
        'tags'        => [],
        'rating'      => 0,
        'exif'        => $exifData ?: [],
        'created_at'  => date('Y-m-d H:i:s'),
        'uploaded_at' => date('Y-m-d H:i:s'),
    ]
];

// YAML + .md speichern
$ymlPath = $uploadDir . $finalSlug . '.yml';
$mdPath  = $uploadDir . $finalSlug . '.md';

try {
    file_put_contents($ymlPath, Yaml::dump($yamlData, 2, 4));
    file_put_contents($mdPath, '');
    logMessage("Metadaten gespeichert: $ymlPath");
} catch (Exception $e) {
    logMessage("YAML-Fehler: " . $e->getMessage());
    die(json_encode(["error" => "Fehler beim Speichern der Metadaten."]));
}

// Optional EXIF auch separat speichern
$exifJsonPath = $uploadDir . $finalSlug . '.exif';
if (!empty($exifData)) {
    file_put_contents($exifJsonPath, json_encode($exifData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// JSON Antwort
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "success"  => true,
    "filename" => $finalFileName,
    "guid"     => $guid,
    "slug"     => $finalSlug
]);
exit;
