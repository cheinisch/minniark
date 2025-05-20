<?php
require_once(__DIR__ . "/../../functions/function_backend.php");
security_checklogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Albuminformationen von POST-Daten übernehmen
    $albumName = isset($_POST['album-title']) ? trim($_POST['album-title']) : '';


    $result = saveNewAlbum($albumName);

    $slug = generateSlug($albumName);

    if($result)
    {
        header("Location: ../album-detail.php?album=".$slug);
    }

}