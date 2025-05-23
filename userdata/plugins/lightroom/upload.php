<?php

    $filename = $_SERVER['HTTP_X_FILENAME'] ?? ('lightroom_' . time() . '.jpg');
    $data = file_get_contents('php://input');

    if (!$data) {
        http_response_code(400);
        echo "No data received.";
        exit;
    }

    $savePath = __DIR__ . '/../../images/' . basename($filename);
    file_put_contents($savePath, $data);

    // Optional: DB oder Logging hier

    http_response_code(200);
    echo "Image uploaded successfully.";