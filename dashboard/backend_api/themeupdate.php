<?php

    require_once(__DIR__ . "/../../functions/function_backend.php");
    security_checklogin();

    if($_GET['update'] != "all_themes")
    {
        $result = updateSingleTheme($_GET['update']);

        if($result)
        {
            header("Location: ../dashboard-theme.php");
        }
    }