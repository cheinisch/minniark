<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Zielpfad
$path = realpath(__DIR__ . '/../../userdata/config');
$file = $path . '/home.json';

error_log("Save Home Cover");

print_r($_POST);

if (!$path || !is_writable($path)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Storage path not writable.']);
    exit;
}

// Vorhandene Daten laden
$existingData = [];
if (file_exists($file)) {
    $json = file_get_contents($file);
    $existingData = json_decode($json, true);
    if (!is_array($existingData)) {
        $existingData = [];
    }
}

// POST-Daten validieren
$cover = trim($_POST['cover'] ?? '');
$style = trim($_POST['default_image_style'] ?? '');

$allowedStyles = ['album', 'image', '']; // nur erlaubte Werte

if (!in_array($style, $allowedStyles, true)) {
    http_response_code(400);
    exit;
}

// Nur relevante Felder überschreiben
$existingData['default_image'] = $cover;
$existingData['default_image_style'] = $style;
$existingData['updated_at'] = date('Y-m-d H:i:s');

// Speichern
if (file_put_contents($file, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false) {
    header('Location: ../dashboard-welcomepage.php');
    exit;
} else {
    http_response_code(500);
    exit;
}
