<?php

    require_once __DIR__ . '/../../functions/function_backend.php';
    security_checklogin();

    print_r($_POST);

    $rotation = $_POST['rotation'];
    $flipX = $_POST['flipX'];
    $flipY = $_POST['flipY'];
    $filename = $_POST['filename'];


    $result = modifyImage($filename, $rotation, $flipX, $flipY);

    if($result)
    {
        generate_single_image_cache($filename);
        header("Location: ../media-detail.php?image=".$filename);
    }