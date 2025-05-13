<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Zielpfad
$path = realpath(__DIR__ . '/../../userdata/config');
$file = $path . '/home.json';

if (!$path || !is_writable($path)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Storage path not writable.']);
    exit;
}

// Bestehende Daten laden
$existingData = [];
if (file_exists($file)) {
    $json = file_get_contents($file);
    $existingData = json_decode($json, true);
    if (!is_array($existingData)) {
        $existingData = [];
    }
}

// Neue Werte aus POST übernehmen
$existingData['headline']     = trim($_POST['headline'] ?? '');
$existingData['sub-headline'] = trim($_POST['sub-headline'] ?? '');
$existingData['content']      = trim($_POST['content'] ?? '');
$existingData['updated_at']   = date('Y-m-d H:i:s');


// Als JSON speichern
if (file_put_contents($file, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false) {
    // Erfolg: ggf. Weiterleitung oder JSON-Response
    header('Location: ../dashboard-welcomepage.php');
    exit;
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to save JSON file.']);
    exit;
}
