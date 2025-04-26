<?php

// Daten aus POST holen
$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$sitename = $_POST['sitename'] ?? '';

$authtype = 'password';

// Passwort hashen
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Auth-Token generieren
$authToken = bin2hex(random_bytes(32));

// Optional: debug POST
// print_r($_POST);

// Pfad zur PHP-Konfigurationsdatei
$configPath = __DIR__ . '/../userdata/config/user_config.php';

// Inhalt der Datei erstellen
$configContent = "<?php\n";
$configContent .= "defined('IMAGEPORTFOLIO') or die('Access denied');\n\n";
$configContent .= "return [\n";
$configContent .= "    'USERNAME'      => '" . addslashes($username) . "',\n";
$configContent .= "    'EMAIL'         => '" . addslashes($email) . "',\n";
$configContent .= "    'AUTH_TYPE'     => '" . $authtype . "',\n";
$configContent .= "    'PASSWORD_HASH' => '" . $passwordHash . "',\n";
$configContent .= "    'AUTH_TOKEN'    => '" . $authToken . "'\n";
$configContent .= "];\n";

// Datei schreiben
file_put_contents($configPath, $configContent, LOCK_EX);

// Pfad zur settings.json
$settingsPath = __DIR__ . '/../userdata/config/settings.json';

// Einstellungsdaten als Array
$settings = [
    'site_title' => $sitename,
    'theme' => 'basic',
    'language' => 'en',
    'show_upload_dates' => false,
    'default_image_size' => 'm',
    'default_page' => 'home',
    'timeline' => [
        'enable' => true,
        'groupe_by_date' => false
    ],
    'map' => [
        'enable' => true
    ]
];

// In JSON umwandeln und speichern
$settingsJson = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
file_put_contents($settingsPath, $settingsJson, LOCK_EX);

header('Location: ../');

