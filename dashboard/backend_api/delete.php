<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once(__DIR__ . '/../../functions/function_backend.php');
    security_checklogin();

    $type = $_GET['type'] ?? null;
    $filename = $_GET['filename'] ?? null;
    $albumname = $_GET['albumname'] ?? null;
    $collection = $_GET['collection'] ?? null;

    switch($type) {
        case "img":
            $value = delete_image($filename);
            if($value)
            {
                header("Location: ../media.php");
            }
            break;
        case "essay":
            delete_post($filename);
            header("Location: ../blog.php");
            break;
        case "album_img":
            $result = remove_img_from_album($filename, $albumname);
            if($result)
            {
                header("Location: ../album-detail.php?album=".$albumname);
            }            
            break;
        case "album":
            $result = removeAlbum($albumname);
            if($result)
            {
                header("Location: ../media.php");
            }
            break;
        case "collection":
            removeCollection($filename);
            header("Location: ../media.php");
            break;
        case "removealbumfromcollection":
            removealbumfromcollection($collection, $albumname);
            header("Location: ../collection-detail.php?collection=".$collection);
            break;
    }