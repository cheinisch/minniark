<?php


    session_start();


    require_once __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__ . '/../../functions/function_backend.php';

    security_checklogin();



    $result = generateBackup();

    if($result)
    {
        // do some shit
        header("Location: ../dashboard-export_import.php?success=true");
    }
