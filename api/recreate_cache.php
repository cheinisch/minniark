<?php

require_once(__DIR__ . '/../functions/function_api.php');
require_once(__DIR__ . '/../vendor/autoload.php');

use Symfony\Component\Yaml\Yaml;

header('Content-Type: application/json');

$cacheDir = __DIR__ . '/../cache/images/';
$contentDir = __DIR__ . '/../userdata/content/images/';
$sizes = [
    'S' => 150,
    'M' => 500,
    'L' => 1024,
    'XL' => 1920,
    'XXL' => 3440
];

// Ensure cache directory exists
if (!is_dir($cacheDir) && !mkdir($cacheDir, 0755, true)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not create cache directory.']);
    exit;
}

// Check content directory
if (!is_dir($contentDir)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Content directory not found.']);
    exit;
}

// 1. Clear existing thumbnails
foreach (glob($cacheDir . '*') as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}
error_log('Cache cleared.');

// 2. Process each image
$images = glob($contentDir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

foreach ($images as $imagePath) {
    $info = pathinfo($imagePath);
    $basename = $info['filename'];
    $extension = strtolower($info['extension']);

    $ymlPath = $contentDir . $basename . '.yml';

    if (!file_exists($ymlPath)) {
        error_log("Missing YAML for image: $basename");
        continue;
    }

    try {
        $yaml = Yaml::parseFile($ymlPath);
        $guid = $yaml['image']['guid'] ?? null;

        if (!$guid) {
            error_log("GUID missing in YAML: $ymlPath");
            continue;
        }

        foreach ($sizes as $sizeKey => $maxWidth) {
            $targetPath = $cacheDir . "{$guid}_{$sizeKey}.{$extension}";

            if (!file_exists($imagePath)) {
                error_log("Source image missing: $imagePath");
                continue;
            }

            $success = createThumbnail($imagePath, $targetPath, $maxWidth);

            if ($success) {
                error_log("Thumbnail created: $targetPath");
            } else {
                error_log("Failed to create thumbnail: $targetPath");
            }
        }

    } catch (Exception $e) {
        error_log("YAML error for file $ymlPath: " . $e->getMessage());
        continue;
    }
}

echo json_encode(['success' => true]);
