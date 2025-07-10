<?php

    require_once(__DIR__ . "/../../functions/function_backend.php");
    security_checklogin();

    $folder = $_GET['name'];


    $result = saveSettings(['theme' => $folder]);

    if($result)
    {
        header("Location: ../dashboard-theme.php");
        exit;
    }else{
        echo "Something gone wrong";
    }
