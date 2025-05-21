<?php

require_once(__DIR__ . "/../functions/function_api.php");
require_once(__DIR__ . "/../vendor/autoload.php");

use Symfony\Component\Yaml\Yaml;

secure_API();

header('Content-Type: application/json');

// Nur POST zulassen
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Nur POST erlaubt']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['filename']) || !isset($data['rating'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Fehlender Dateiname oder Rating']);
    exit;
}

$filename = basename($data['filename']); // z. B. img_01.jpg
$rating = (int) $data['rating'];

// Pfad zur YML-Datei erzeugen
$slug = pathinfo($filename, PATHINFO_FILENAME);
$ymlFile = __DIR__ . '/../userdata/content/images/' . $slug . '.yml';

if (!file_exists($ymlFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'YAML-Datei nicht gefunden', 'file' => $ymlFile]);
    exit;
}

// YAML laden
try {
    $yamlData = Yaml::parseFile($ymlFile);
    $imageData = $yamlData['image'] ?? [];
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'YAML konnte nicht gelesen werden', 'message' => $e->getMessage()]);
    exit;
}

// Rating aktualisieren
$imageData['rating'] = $rating;

// YAML speichern
$yamlData['image'] = $imageData;
try {
    file_put_contents($ymlFile, Yaml::dump($yamlData, 2, 4));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Fehler beim Schreiben der YAML', 'message' => $e->getMessage()]);
    exit;
}

echo json_encode([
    'success' => true,
    'file' => $ymlFile,
    'rating' => $rating
]);