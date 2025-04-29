<?php
require_once( __DIR__ . "/../functions/function_api.php");
require_once( __DIR__ . "/../functions/function_backend.php");
secure_API();
$prefix = read_prefix(); // z. B. 'projectname'

$timestamp = date('Y-m-d_H-i');
$filename = "{$prefix}_{$timestamp}.zip";
$backupDir = __DIR__ . '/../backup';
$backupPath = "$backupDir/$filename";

// Sicherstellen, dass das Verzeichnis existiert
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0775, true);
}

// ZIP erstellen (z. B. von /userdata)
$sourceDir = realpath(__DIR__ . '/../userdata');

$zip = new ZipArchive();
if ($zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'ZIP konnte nicht erstellt werden.']);
    exit;
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $file) {
    $filePath = $file->getRealPath();
    $relativePath = substr($filePath, strlen($sourceDir) + 1);
    $zip->addFile($filePath, $relativePath);
}
$zip->close();

// Rückgabe
echo json_encode([
    'success' => true,
    'filename' => $filename,
    'download_url' => '../../backup/' . rawurlencode($filename)
]);
