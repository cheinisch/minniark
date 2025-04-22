<?php

require_once( __DIR__ . "/../functions/function_api.php");
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

// JSON-Dateiname erzeugen
$jsonFile = __DIR__ . '/../content/images/' . preg_replace('/\.[^.]+$/', '.json', $filename);

if (!file_exists($jsonFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'JSON-Datei nicht gefunden', 'file' => $jsonFile]);
    exit;
}

// JSON laden
$jsonData = json_decode(file_get_contents($jsonFile), true);

// Rating einfügen
$jsonData['rating'] = $rating;

// JSON speichern
file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo json_encode(['success' => true, 'file' => $jsonFile, 'rating' => $rating]);
