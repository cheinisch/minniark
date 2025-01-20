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
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            return true; // Login erfolgreich
        }
    }

    return false; // Kein passender Benutzer gefunden
}


function getUserData($username) {
    $filePath = __DIR__ . '/../userdata/users.json';
    if (!file_exists($filePath)) {
        return null;
    }

    $users = json_decode(file_get_contents($filePath), true);
    return $users[$username] ?? null;
}


function updateUserData($username, $data) {
    $filePath = __DIR__ . '/../userdata/users.json';
    if (!file_exists($filePath)) {
        return false;
    }

    $users = json_decode(file_get_contents($filePath), true);
    if (!isset($users[$username])) {
        return false;
    }

    // Benutzer aktualisieren
    $users[$username] = array_merge($users[$username], $data);

    // Änderungen speichern
    return file_put_contents($filePath, json_encode($users, JSON_PRETTY_PRINT));
}
