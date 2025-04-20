<?php

    function secure_API()
    {
        session_start();
        // Prüfen, ob der Benutzer eingeloggt ist
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {

        } else {
            
            http_response_code(403);
            echo "403 Verboten – Zugriff verweigert.";
            exit;
        }
    }