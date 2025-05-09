<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require_once(__DIR__ . "/../../functions/function_backend.php");
    security_checklogin();


    $filename = $_GET['filename'] ?? null;
    $type = $_GET['type'] ?? null;

    switch($type) {
        case "album":
            delete_image($filename);
            header("Location: ../media.php");
            break;
        case "essay":
            delete_post($filename);
            header("Location: ../blog.php");
            break;
    }