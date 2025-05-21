<?php

require_once(__DIR__ . '/../vendor/autoload.php');
use Symfony\Component\Yaml\Yaml;

// Daten aus POST holen
$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$sitename = $_POST['sitename'] ?? 'Minniark';

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
$settingsPath = __DIR__ . '/../userdata/config/settings.yml';

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

// YAML erzeugen
try {
    $yaml = Yaml::dump($settings, 4, 2);
    file_put_contents($settingsPath, $yaml, LOCK_EX);
} catch (Exception $e) {
    die('Fehler beim Speichern der Einstellungen: ' . $e->getMessage());
}

// Weiterleitung nach erfolgreicher Installation
header('Location: ../');
exit;
