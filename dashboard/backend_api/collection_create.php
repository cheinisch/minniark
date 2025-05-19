<?php

    print_r($_POST);

    $title = $_POST['title'] ?? null;
    $content = $_POST['content'] ?? null;


    $collectionDir = __DIR__.'/../../userdata/content/collection/';

    if (!is_dir($collectionDir)) {
        // Versuche, das Verzeichnis anzulegen (inkl. Unterverzeichnisse)
        if (!mkdir($collectionDir, 0755, true)) {
            die("Konnte das Verzeichnis nicht erstellen: $collectionDir");
        }
    }




?>