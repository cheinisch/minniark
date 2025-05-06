<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once(__DIR__ . '/../../functions/function_backend.php');
    security_checklogin();

    $type = $_GET['type'] ?? null;
    $filename = $_GET['filename'] ?? null;

    switch($type) {
        case "img":
            delete_image($filename);
            header("Location: ../media.php");
            break;
        case "essay":
            delete_post($filename);
            header("Location: ../blog.php");
            break;
    }