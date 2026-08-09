<?php

    function security_checklogin(){

        session_start();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            // logged in — continue
        } else {
            header("Location: index.php");
            exit;
        }

    }

    // onlyAdmin is to check for Admin Only features (dashboard at this moment)
    function onlyAdmin()
    {
        if (!$_SESSION['admin']) {
            header("Location: ../dashboard.php");
            exit;
        }
    }
