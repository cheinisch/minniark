<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../functions/function_api.php');

// Pfad zur settings.json
$settingsFile = __DIR__ . '/../userdata/config/settings.json';

// Sicherstellen, dass Request Methode POST ist
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Daten auslesen
$input = json_decode(file_get_contents('php://input'), true);

// Settings laden
if (!file_exists($settingsFile)) {
    echo json_encode(['success' => false, 'message' => 'Settings file not found']);
    exit;
}

$settings = json_decode(file_get_contents($settingsFile), true);

// Felder aktualisieren, wenn sie existieren
if (isset($input['site_name'])) {
    $settings['site_title'] = $input['site_name'];
}
if (isset($input['site_description'])) {
    $settings['site_description'] = $input['site_description'];
}
if (isset($input['language'])) {
    $settings['language'] = $input['language'];
}
if (isset($input['image_size'])) {
    $settings['default_image_size'] = $input['image_size'];
}

// Speichern
if (file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save settings']);
}
