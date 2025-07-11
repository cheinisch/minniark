<?php

    require_once(__DIR__ . "/../../functions/function_backend.php");
    security_checklogin();

    if(isset($_GET['update']))
    {
        if($_GET['update'] != "all_themes")
        {
            $result = updateSingleTheme($_GET['update']);

            if($result)
            {
                header("Location: ../dashboard-theme.php");
            }
        }
    }else if(isset($_GET['remove']))
    {
        $result = removeTheme($_GET['remove']);

        if($result)
        {
            header("Location: ../dashboard-theme.php");
        }
    }
    