<?php

    
    

    function secure_API()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        error_log("Secure API");
        session_start();
        // Prüfen, ob der Benutzer eingeloggt ist
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            error_log("Access Granted");
        } else {
            error_log("Access Denied");
            http_response_code(403);
            exit;
        }
    }