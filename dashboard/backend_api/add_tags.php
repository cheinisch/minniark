<?php

    require_once( __DIR__ . "/../../functions/function_backend.php");
    security_checklogin();

    $type = $_GET['type'];
    $file = $_GET['file'];    


    switch($type) {
        case "image":
            if (!empty($_POST['tag']) && !empty($file)) {
                $data = [
                    'filename' => $file,
                    'tags'     => $_POST['tag']   // ein einzelner Tag aus Input
                ];

                if (updateImage($data, 'tags')) {
                    header("Location: ../media-detail.php?image=$file");
                } else {
                    echo "Fehler beim Speichern.";
                }
            } else {
                echo "Kein Tag oder Datei angegeben.";
            }
            break;
    }
