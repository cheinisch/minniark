<?php

/**
 * Authentifiziert einen Benutzer.
 * 
 * @param string $username Benutzername.
 * @param string $password Passwort.
 * @return bool Gibt true zurück, wenn die Authentifizierung erfolgreich war, ansonsten false.
 */
function authenticateUser($username, $password) {
    $userFile = __DIR__ . '/../userdata/config/users.json';
    
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
    $filePath = __DIR__ . '/../userdata/config/users.json';
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
    $filePath = __DIR__ . '/../userdata/config/users.json';
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

function getImagesFromDirectory($directory = "../userdata/content/images/") {
    if (!is_dir($directory)) {
        return [];
    }

    // Alle Bilddateien abrufen (Rückgabe als Array von Strings)
    $images = glob($directory . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);

    if (!is_array($images)) {
        error_log("Fehler: getImagesFromDirectory() gibt kein Array zurück.");
        return [];
    }

    return $images;
}


// Galerie mit Tailwind HTML ausgeben()
/*function renderImageGallery() {
    $images = getImagesFromDirectory();
    $imageDir = '../userdata/content/images/';

    if (empty($images)) {
        echo "<p class='text-center text-gray-500'>Keine Bilder gefunden.</p>";
        return;
    }

    echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">';
    foreach ($images as $image) {
        $fileName = basename($image);
        $jsonFile = $imageDir . pathinfo($fileName, PATHINFO_FILENAME) . '.json';
        $metadata = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
        
        $title = !empty($metadata['title']) ? $metadata['title'] : "Untitled";
        echo "<div class='relative group cursor-pointer' onclick='openImageDetails(\"$fileName\", \"$title\")'>
                <img src='$image' class='w-full h-40 object-cover rounded-md shadow-md hover:shadow-lg transition duration-300'>
                <div class='absolute bottom-2 left-2 bg-white bg-opacity-75 px-3 py-1 rounded-md text-sm font-medium'>$title</div>
              </div>";
    }
    echo '</div>';
}*/

/**
 * Erstellt eine Bildergalerie mit Tailwind CSS.
 * Diese Funktion lädt Bilder aus einem Verzeichnis, liest optionale Metadaten aus JSON-Dateien
 * und zeigt die Bilder in einem Grid-Layout an.
 */
function renderImageGallery() {
    $imageDir = '../userdata/content/images/';
    $images = getImagesFromDirectory($imageDir);

    // Falls keine Bilder gefunden wurden, eine Meldung ausgeben
    if (empty($images)) {
        echo "<p class='text-center text-gray-500'>Keine Bilder gefunden.</p>";
        return;
    }

    echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">';

    foreach ($images as $image) {
        $fileName = basename($image);
        $jsonFile = $imageDir . pathinfo($fileName, PATHINFO_FILENAME) . '.json';
        $metadata = [];

        // JSON-Daten auslesen und validieren
        if (file_exists($jsonFile)) {
            $jsonData = file_get_contents($jsonFile);
            $decodedData = json_decode($jsonData, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $metadata = $decodedData;
            } else {
                error_log("Fehler beim Parsen von JSON: " . json_last_error_msg());
            }
        }

        $title = !empty($metadata['title']) ? htmlspecialchars($metadata['title']) : "Kein Titel";
        $description = !empty($metadata['description']) ? htmlspecialchars($metadata['description']) : "Keine Beschreibung verfügbar";

        // HTML für das Bild generieren
        echo "<a href='media-detail.php?image=" . urlencode($fileName) . "' class='block'>
        <div class='relative group cursor-pointer' data-open-panel data-src='$image' data-title='$title'>
            <img src='$image' class='w-full h-40 object-cover rounded-md shadow-md hover:shadow-lg transition duration-300'>
            <div class='absolute bottom-2 left-2 bg-white bg-opacity-75 px-3 py-1 rounded-md text-sm font-medium'>$title</div>
        </div>
      </a>";

    }

    echo '</div>';
}

function getImage($imagename)
{

    
    
    $fileName = $imagename;
    $jsonFiles = glob("../userdata/content/images/*.json");
    
    $imageData = null;
    
    // Passende JSON-Datei anhand des Dateinamens suchen
    foreach ($jsonFiles as $file) {
        $jsonData = file_get_contents($file);
        $image = json_decode($jsonData, true);
        if ($image && $image['filename'] === $fileName) {
            $imageData = $image;
            return $imageData;
            break;
        }
    }
}

