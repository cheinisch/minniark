<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../functions/function_api.php');

// Pfad zur settings.json
$settingsFile = __DIR__ . '/../userdata/config/settings.json';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Eingehende Daten lesen
$input = json_decode(file_get_contents('php://input'), true);

// Settings laden
if (!file_exists($settingsFile)) {
    echo json_encode(['success' => false, 'message' => 'Settings file not found']);
    exit;
}

$settings = json_decode(file_get_contents($settingsFile), true);

// vorhandene Felder aktualisieren
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
if (isset($input['timeline_enable'])) {
    $settings['timeline']['enable'] = $input['timeline_enable'];
}
if (isset($input['timeline_group_by_date'])) {
    $settings['timeline']['groupe_by_date'] = $input['timeline_group_by_date'];
}
if (isset($input['map_enable'])) {
    $settings['map']['enable'] = $input['map_enable'];
}

// Datei speichern
if (file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save settings']);
}
?>
