<?php

    require_once( __DIR__ . "/../../functions/function_backend.php");

    $result = syncImages();


    if($result)
    {
        header("Location: ../media.php");
    }