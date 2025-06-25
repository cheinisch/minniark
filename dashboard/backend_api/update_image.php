<?php

    require_once(__DIR__ . "/../../functions/function_backend.php");
    security_checklogin();

    if(isset($_GET['type']) && $_GET['type'] == 'exif')
    {
        $type = $_GET['type'];
        $decoded = json_decode($_POST['data'], true);
        $success = updateImage($decoded, $type);

        $image = $decoded['filename'] ?? '';
        // Optional: einfache Weiterleitung oder plain-Text-Ausgabe
        if ($success) {
            header("Location: ../media-detail.php?image={$image}");
        } else {
            //header("Location: ../media-detail.php?image={$image}");
            exit;
        }
        exit;
    }