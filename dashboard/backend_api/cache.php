<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();

    require_once __DIR__ . '/../../functions/function_backend.php';

    security_checklogin();

    // Clean Image Cache Folder
    // clearImageCache();

    // Rebuild Cache
    // generate_image_cache();

    // Clean Twig Cache
    clearTwigCache();


    header("Location: ../dashboard.php");