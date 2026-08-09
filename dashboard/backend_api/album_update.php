<?php

require_once(__DIR__ . "/../../functions/function_backend.php");
security_checklogin();

$albumName = $_POST['album-current-title'] ?? '';
$newName = trim($_POST['album-title-edit'] ?? '');
$newDesc = trim($_POST['album-description'] ?? '');

$slug = generateSlug($newName);
$result = updateAlbum(generateSlug($newName),$_POST,generateSlug($albumName));

    if($result)
    {
        header("Location: ../album-detail.php?album=$slug");
    }
exit;

?>
