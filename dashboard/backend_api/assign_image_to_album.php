<?php

    
error_reporting(E_ALL);
ini_set('display_errors', 1);

error_log(print_r($_POST));



require_once __DIR__ . '/../../functions/function_backend.php';
security_checklogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $albumName = $_POST['album'] ?? '';
    $imagesToAddRaw = $_POST['image'] ?? [];
    $imagesToAdd = is_array($imagesToAddRaw) ? $imagesToAddRaw : [$imagesToAddRaw];

    if (empty($albumName) || (empty($imagesToAdd))) {
        die("Fehlende oder ungültige Daten.");
    }

    $result = addImageToAlbum($albumName, $imagesToAdd);

    if($result)
    {
        header("Location: ../media.php");
    }

} else {
    echo "Ungültige Anfrage.";
}
