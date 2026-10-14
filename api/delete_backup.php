<?php
require_once(__DIR__ . '/../functions/function_api.php');
header('Content-Type: application/json');
secure_API_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    $filename = basename($_POST['filename']);
    $filepath = __DIR__ . '/../backup/' . $filename;

    if (file_exists($filepath)) {
        if (unlink($filepath)) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Konnte Datei nicht löschen']);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'message' => 'Datei nicht gefunden']);