<?php

/**
 * Authentifiziert einen Benutzer.
 * 
 * @param string $username Benutzername.
 * @param string $password Passwort.
 * @return bool Gibt true zurück, wenn die Authentifizierung erfolgreich war, ansonsten false.
 */
function authenticateUser($username, $password) {
    $userFile = __DIR__ . '/../userdata/users.json';
    
    // Benutzerdatei einlesen
    if (!file_exists($userFile)) {
        return false; // Keine Benutzerdatei vorhanden
    }

    $users = json_decode(file_get_contents($userFile), true);

    foreach ($users as $user) {
        if ($user['login_name'] === $username && password_verify($password, $user['password'])) {
            return true; // Login erfolgreich
        }
    }

    return false; // Kein passender Benutzer gefunden
}


function getUserData($username) {
    $filePath = __DIR__ . '/../userdata/users.json';
    if (!file_exists($filePath)) {
        error_log("Datei nicht gefunden: " . $filePath);
        return null;
    }

    $users = json_decode(file_get_contents($filePath), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON-Fehler: " . json_last_error_msg());
        return null;
    }

    // Durchsuche das Array nach dem Benutzer
    foreach ($users as $user) {
        if (isset($user['login_name']) && strtolower($user['login_name']) === strtolower($username)) {
            return $user;
        }
    }

    error_log("Benutzer '$username' nicht in der Datei gefunden.");
    return null;
}


function updateUserData($username, $data) {
    $filePath = __DIR__ . '/../userdata/users.json';
    if (!file_exists($filePath)) {
        error_log("Datei nicht gefunden: " . $filePath);
        return false;
    }

    $users = json_decode(file_get_contents($filePath), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON-Fehler beim Lesen: " . json_last_error_msg());
        return false;
    }

    // Benutzer aktualisieren
    foreach ($users as &$user) {
        if (isset($user['login_name']) && strtolower($user['login_name']) === strtolower($username)) {
            $user = array_merge($user, $data);
            break;
        }
    }

    // Datei speichern
    $jsonData = json_encode($users, JSON_PRETTY_PRINT);
    if ($jsonData === false) {
        error_log("JSON-Fehler beim Kodieren: " . json_last_error_msg());
        return false;
    }

    if (file_put_contents($filePath, $jsonData) === false) {
        error_log("Fehler beim Schreiben der Datei: " . $filePath);
        return false;
    }

    return true;
}

