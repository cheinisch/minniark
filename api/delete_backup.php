<?php
header('Content-Type: application/json');

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