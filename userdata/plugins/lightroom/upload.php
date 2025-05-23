<?php

// Fehleranzeige deaktivieren (in Produktion!)
ini_set('display_errors', 0);
error_reporting(0);

// 1. Plugin-Passwort aus plugin/settings.json laden
$pluginSettingsPath = __DIR__ . '/settings.json';

if (!file_exists($pluginSettingsPath)) {
    http_response_code(500);
    echo "Missing plugin settings.";
    exit;
}

$pluginSettings = json_decode(file_get_contents($pluginSettingsPath), true);
$storedHash = $pluginSettings['passwordhash'] ?? null;

if (!$storedHash) {
    http_response_code(500);
    echo "Password hash not set.";
    exit;
}

// 2. Passwort aus HTTP-Header lesen
$clientPassword = $_SERVER['HTTP_X_PASSWORD'] ?? '';

if (!password_verify($clientPassword, $storedHash)) {
    http_response_code(403);
    echo "Unauthorized.";
    exit;
}

// 3. Dateiname und Extension prüfen
$filename = $_SERVER['HTTP_X_FILENAME'] ?? ('lightroom_' . time() . '.jpg');
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($extension, $allowedExtensions)) {
    http_response_code(400);
    echo "Invalid file type.";
    exit;
}

// 4. Bilddaten einlesen
$data = file_get_contents('php://input');

if (!$data || strlen($data) > 10 * 1024 * 1024) {
    http_response_code(400);
    echo "Invalid or too large file.";
    exit;
}

// 5. Dateiname entschärfen & Zielpfad bestimmen
$sanitizedFilename = preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', basename($filename));
$targetDir = realpath(__DIR__ . '/../../images');

if (!$targetDir) {
    http_response_code(500);
    echo "Upload directory not found.";
    exit;
}

$savePath = $targetDir . DIRECTORY_SEPARATOR . $sanitizedFilename;

// 6. Datei speichern
file_put_contents($savePath, $data);

// 7. Erfolgs-Response
http_response_code(200);
echo "Image uploaded successfully.";
