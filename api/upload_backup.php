<?php

    header('Content-Type: application/json');

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    date_default_timezone_set('Europe/Berlin');
    require_once( __DIR__ . "/../functions/function_api.php");
    secure_API();



    $uploadDir = __DIR__ . '/../backup';

    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['backup_file'])) {
        $file = $_FILES['backup_file'];

        // Nur ZIP-Dateien erlauben
        if ($file['error'] === UPLOAD_ERR_OK && pathinfo($file['name'], PATHINFO_EXTENSION) === 'zip') {
            $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', basename($file['name']));
            $targetPath = $uploadDir . '/' . $safeName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                echo json_encode(['success' => true]);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Konnte Datei nicht speichern.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Ungültige Datei.']);
            exit;
        }
    }

    // Falls kein Upload stattgefunden hat
    echo json_encode(['success' => false, 'message' => 'Kein Upload empfangen.']);
