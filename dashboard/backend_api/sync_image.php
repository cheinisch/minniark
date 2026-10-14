<?php



    require_once __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__ . '/../../functions/function_backend.php';

    security_checklogin();
    csrf_require();

    $result = syncImages();


    if($result)
    {
        header("Location: ../media.php");
    }