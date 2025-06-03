<?php

    function security_checklogin(){

        session_start();
        // Prüfen, ob der Benutzer eingeloggt ist
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {

        } else {
            // Benutzer ist nicht eingeloggt, Login-Seite anzeigen
            // Pfad zur Index: Relativ: ../../dashboard/index.php
           header("Location: index.php");
        }

    }

    // onlyAdmin is to check for Admin Only features (dasboard at this moment)
    function onlyAdmin()
    {
    
        if(!$_SESSION['admin'])
        {
            // redirect to dashboard overview
            header("Location: ../dashboard.php");
        }

    }