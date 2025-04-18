<?php

    function security_checklogin(){

        session_start();
        // Prüfen, ob der Benutzer eingeloggt ist
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {

        } else {
            // Benutzer ist nicht eingeloggt, Login-Seite anzeigen
            // Pfad zur Index: Relativ: ../../dashboard_v2/index.php
           header("Location: index.php");
        }

    }