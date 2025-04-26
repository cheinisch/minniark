<?php

    require_once( __DIR__ . "/../functions/function_api.php");
    secure_API();

    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['filename'], $data['title'], $data['description'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing data']);
        exit;
    }

    $filename = basename($data['filename']);
    $jsonFile = __DIR__ . '/../userdata/content/images/' . preg_replace('/\.[^.]+$/', '.json', $filename);

    if (!file_exists($jsonFile)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'File not found']);
        exit;
    }

    $jsonData = json_decode(file_get_contents($jsonFile), true);
    $jsonData['title'] = $data['title'];
    $jsonData['description'] = $data['description'];

    file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    echo json_encode(['success' => true]);

?>