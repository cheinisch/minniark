<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../functions/function_backend.php');
security_checklogin();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

error_log("Save PAge");


if (!$input || !isset($input['title'], $input['foldername'])) {
    echo json_encode(['success' => false, 'message' => 'Fehlende Pflichtfelder.']);
    exit;
}
error_log("Titel: " . $input['title']);
error_log("Input: " . $input['content']);
error_log("Array: " . $input['original_foldername']);

$baseDir = realpath(__DIR__ . '/../../userdata/content/pages');
$originalFolder = preg_replace('/[^a-zA-Z0-9\-_]/', '', $input['original_foldername']);
$originalPath = $baseDir . DIRECTORY_SEPARATOR . $originalFolder;

// Slugify-Titel
function slugify($text) {
    $map = ['ä'=>'ae','ö'=>'oe','ü'=>'ue','ß'=>'ss'];
    $text = strtolower(strtr($text, $map));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

$newFolderBase = slugify($input['title']);
$newFolder = $newFolderBase;
$newPath = $baseDir . DIRECTORY_SEPARATOR . $newFolder;

// Prüfen, ob der Slug existiert und ggf. erhöhen (_1, _2, ...), außer es ist derselbe Ordner
$counter = 1;
while (is_dir($newPath) && realpath($newPath) !== realpath($originalPath)) {
    $newFolder = $newFolderBase . "_" . $counter;
    $newPath = $baseDir . DIRECTORY_SEPARATOR . $newFolder;
    $counter++;
}

// Wenn Verzeichnis existiert, aber Titel sich geändert hat → umbenennen
if (is_dir($originalPath) && realpath($originalPath) !== realpath($newPath)) {
    error_log("ORdnername aendert sich");
    if (!rename($originalPath, $newPath)) {
        echo json_encode(['success' => false, 'message' => 'Ordner konnte nicht umbenannt werden.']);
        exit;
    }
}

// Zielordner verwenden (neu oder umbenannt)
$targetDir = is_dir($newPath) ? $newPath : $originalPath;
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0775, true)) {
        echo json_encode(['success' => false, 'message' => 'Ordner konnte nicht erstellt werden.']);
        exit;
    }
}

$jsonPath = $targetDir . DIRECTORY_SEPARATOR . 'data.json';

error_log("JSON PAth: ".$jsonPath);

$dateNow = date('Y-m-d');
$createdAt = $dateNow;

$coverPath = $input['cover'] ?? '';
$finalCoverPath = '';
if ($coverPath && str_starts_with($coverPath, '/temp/')) {
    $tempFilePath = realpath(__DIR__ . '/../../' . ltrim($coverPath, '/'));
    $newFileName = basename($coverPath);
    $newFilePath = $targetDir . DIRECTORY_SEPARATOR . $newFileName;
    if (is_file($tempFilePath) && rename($tempFilePath, $newFilePath)) {
        $finalCoverPath = '/userdata/content/pages/' . $newFolder . '/' . $newFileName;
    } else {
        $finalCoverPath = $coverPath; // fallback
    }
} else {
    $finalCoverPath = $coverPath;
}

// Wenn die Datei bereits existiert, erhalte das ursprüngliche created_at
if (file_exists($jsonPath)) {
    error_log("Datei existiert");
    $existing = json_decode(file_get_contents($jsonPath), true);
    if (isset($existing['created_at'])) {
        $createdAt = $existing['created_at'];
    }
}

$data = [
    'title' => $input['title'],
    'content' => $input['content'],
    'slug' => slugify($input['title']),
    'created_at' => $createdAt,
    'updated_at' => $dateNow,
    'cover' => $finalCoverPath,
    'is_published' => $input['is_published'] ?? false
];

if (file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode(['success' => true, 'folder' => $newFolder]);
} else {
    echo json_encode(['success' => false, 'message' => 'Speichern fehlgeschlagen.']);
}
