<?php

require_once(__DIR__ . "/../functions/function_api.php");
require_once(__DIR__ . "/../vendor/autoload.php");

use Symfony\Component\Yaml\Yaml;

secure_API();
header('Content-Type: application/json');

// Daten einlesen
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['filename'], $data['title'], $data['description'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing data']);
    exit;
}

$filename = basename($data['filename']); // z. B. img_01.jpg
$slug = pathinfo($filename, PATHINFO_FILENAME);
$ymlFile = __DIR__ . '/../userdata/content/images/' . $slug . '.yml';
$mdFile  = __DIR__ . '/../userdata/content/images/' . $slug . '.md';

// Prüfen ob YML existiert
if (!file_exists($ymlFile)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'YAML-Datei nicht gefunden']);
    exit;
}

// YAML laden
try {
    $yaml = Yaml::parseFile($ymlFile);
    $image = $yaml['image'] ?? [];
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Fehler beim Lesen der YAML-Datei', 'message' => $e->getMessage()]);
    exit;
}

// Werte aktualisieren
$image['title'] = trim($data['title']);
$image['updated_at'] = date('Y-m-d H:i:s');
$yaml['image'] = $image;

// Speichern
try {
    file_put_contents($ymlFile, Yaml::dump($yaml, 2, 4));
    file_put_contents($mdFile, trim($data['description']));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Fehler beim Speichern', 'message' => $e->getMessage()]);
    exit;
}

echo json_encode(['success' => true]);
