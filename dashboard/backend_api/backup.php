<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // File: change_password.php
    session_start();


    require_once __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__ . '/../../functions/function_backend.php';



    $result = generateBackup();

    if($result)
    {
        // do some shit
        header("Location: ../dashboard-export_import.php?success=true");
    }
