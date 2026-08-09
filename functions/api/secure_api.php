<?php

    function secure_API()
    {
        error_log("Secure API");
        session_start();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            error_log("Access Granted");
        } else {
            error_log("Access Denied");
            http_response_code(403);
            exit;
        }
    }
