<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once(__DIR__ . '/../../functions/function_backend.php');
    security_checklogin();

    $type = $_GET['type'] ?? null;
    $filename = $_GET['filename'] ?? null;
    $albumname = $_GET['albumname'] ?? null;

    switch($type) {
        case "img":
            delete_image($filename);
            header("Location: ../media.php");
            break;
        case "essay":
            delete_post($filename);
            header("Location: ../blog.php");
            break;
        case "album_img":
            remove_img_from_album($filename, $albumname);
            header("Location: ../album-detail.php?album=".$albumname);
            break;
        case "album":
            removeAlbum($filename);
            header("Location: ../media.php");
            break;
    }