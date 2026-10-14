<?php


    require_once __DIR__ . '/../../functions/function_backend.php';

    security_checklogin();
    csrf_require();

    // Clean Image Cache Folder
    // clearImageCache();

    // Rebuild Cache
    // generate_image_cache();

    // Clean Twig Cache
    clearTwigCache();


    header("Location: ../dashboard.php");