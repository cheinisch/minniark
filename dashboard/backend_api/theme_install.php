<?php

    require_once(__DIR__ . "/../../functions/function_backend.php");
    security_checklogin();

    $package = $_GET['install'];

    $result = installTemplate($package);

    if($result)
    {
        header("Location: ../dashboard-theme.php");
    }