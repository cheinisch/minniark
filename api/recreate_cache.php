<?php
require_once(__DIR__ . '/../functions/function_api.php');

header('Content-Type: application/json');

$cacheDir = __DIR__ . '/../cache/images/';
$contentDir = __DIR__ . '/../userdata/content/images/';
$sizes = [
    'S' => 150,
    'M' => 500,
    'L' => 1024,
    'XL' => 1920
];

// Cache-Verzeichnis prüfen
if (!is_dir($cacheDir)) {
    error_log('Cache directory missing: ' . $cacheDir);
    echo json_encode(['success' => false, 'message' => 'Cache directory does not exist']);
    exit;
}

// Content-Verzeichnis prüfen
if (!is_dir($contentDir)) {
    error_log('Content directory missing: ' . $contentDir);
    echo json_encode(['success' => false, 'message' => 'Content directory does not exist']);
    exit;
}

// 1. Cache löschen
foreach (glob($cacheDir . '*') as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}
error_log('Cache cleared.');

// 2. Bilder durchgehen
$images = glob($contentDir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

foreach ($images as $imagePath) {
    $pathInfo = pathinfo($imagePath);
    $filenameWithoutExt = $pathInfo['filename'];
    $extension = strtolower($pathInfo['extension']);

    $jsonFile = $contentDir . $filenameWithoutExt . '.json';
    if (!file_exists($jsonFile)) {
        error_log('JSON missing for image: ' . $filenameWithoutExt);
        continue;
    }

    $jsonData = json_decode(file_get_contents($jsonFile), true);
    if (!isset($jsonData['guid'])) {
        error_log('GUID missing in JSON: ' . $jsonFile);
        continue;
    }

    $guid = $jsonData['guid'];

    foreach ($sizes as $sizeKey => $sizeValue) {
        $thumbnailPath = $cacheDir . $guid . "_$sizeKey." . $extension;

        // Prüfen ob Quelle existiert
        if (!file_exists($imagePath)) {
            error_log('Source file missing: ' . $imagePath);
            continue;
        }

        // Thumbnail erzeugen
        $success = createThumbnail($imagePath, $thumbnailPath, $sizeValue);

        if ($success) {
            error_log('Thumbnail created: ' . $thumbnailPath);
        } else {
            error_log('Failed to create thumbnail: ' . $thumbnailPath);
        }
    }
}

echo json_encode(['success' => true]);
