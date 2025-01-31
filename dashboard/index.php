<?php
session_start();

// Prüfen, ob der Benutzer eingeloggt ist
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Benutzer ist eingeloggt, Dashboard anzeigen
    include 'media.php';
} else {
    // Benutzer ist nicht eingeloggt, Login-Seite anzeigen
    include 'login.php';
}