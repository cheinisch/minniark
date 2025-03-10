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


function getVersion() {
    $filePath = __DIR__ . '/../VERSION'; // Pfad zur VERSION-Datei im Root-Verzeichnis

    if (!file_exists($filePath)) {
        error_log("Versionsdatei nicht gefunden: " . $filePath);
        return "Version nicht verfügbar"; // Rückgabe bei fehlender Datei
    }

    $version = trim(file_get_contents($filePath)); // Inhalt der Datei lesen und Leerzeichen entfernen

    if (empty($version)) {
        error_log("Versionsdatei ist leer: " . $filePath);
        return "Version nicht verfügbar"; // Rückgabe bei leerer Datei
    }

    return $version; // Versionsnummer zurückgeben
}

function getMaxFilesize()
{
    $max_upload = (int)(ini_get('upload_max_filesize'));
    $max_post = (int)(ini_get('post_max_size'));
    $memory_limit = (int)(ini_get('memory_limit'));
    $upload_mb = min($max_upload, $max_post, $memory_limit);

    return $upload_mb;
}

function getImagesFromDirectory($directory = "../content/images/") {
    // Sicherstellen, dass das Verzeichnis existiert
    if (!is_dir($directory)) {
        return [];
    }

    // Alle Bilddateien aus dem Verzeichnis abrufen
    $images = glob($directory . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
    $galleryItems = [];

    foreach ($images as $image) {
        $filename = basename($image);
        $jsonFile = $directory . pathinfo($filename, PATHINFO_FILENAME) . ".json";

        // Standardwerte für die Bildinfos
        $title = "Kein Titel";
        $description = "";
        
        // Falls JSON-Datei existiert, Metadaten auslesen
        if (file_exists($jsonFile)) {
            $jsonData = json_decode(file_get_contents($jsonFile), true);
            if ($jsonData && isset($jsonData["title"]) && !empty($jsonData["title"])) {
                $title = htmlspecialchars($jsonData["title"]);
            }
        }

        // Bild in Array speichern
        $galleryItems[] = [
            "src" => $image,
            "title" => $title
        ];
    }

    return $galleryItems;
}

// Galerie mit Tailwind HTML ausgeben
function renderImageGallery() {
    $images = getImagesFromDirectory();

    if (empty($images)) {
        echo "<p class='text-center text-gray-500'>Keine Bilder gefunden.</p>";
        return;
    }

    echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">';
    foreach ($images as $image) {
        echo '<div class="bg-white rounded-lg shadow-md overflow-hidden">';
        echo '    <img src="' . $image["src"] . '" alt="Bild" class="w-full h-48 object-cover">';
        echo '    <div class="p-4">';
        echo '        <h3 class="text-lg font-semibold text-gray-800 truncate">' . $image["title"] . '</h3>';
        echo '    </div>';
        echo '</div>';
    }
    echo '</div>';
}