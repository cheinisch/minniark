<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();


    require_once __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__ . '/../../functions/function_backend.php';

    security_checklogin();

    $result = syncImages();


    if($result)
    {
        header("Location: ../media.php");
    }