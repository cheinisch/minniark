<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();


    require_once __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__ . '/../../functions/function_backend.php';

    security_checklogin();


    $filename = $_GET['file'];


    $result = restoreBackup($filename);

    if($result)
    {
        header("Location: ../dashboard-export_import.php?restore=success");
        exit;
    }else{
        header("Location: ../dashboard-export_import.php?restore=error");
        exit;
    }