<?php

require_once __DIR__ . '/../../functions/function_backend.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = saveSettings($_POST);

    if ($result) {
        header("Location: ../dashboard-system.php");
        exit;
    } else {
        header("Location: ../dashboard-system.php");
        exit;
    }
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}