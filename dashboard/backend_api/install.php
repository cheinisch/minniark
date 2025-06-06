<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../functions/function_backend.php';

use Symfony\Component\Yaml\Yaml;

// POST-Daten absichern
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$sitename = trim($_POST['sitename'] ?? 'Minniark');
$userrole = 'admin';

if (empty($username) || empty($email) || empty($password)) {
    die('Bitte Benutzername, E-Mail und Passwort angeben.');
}

// Authentifizierungsinfos
$authtype = 'password';
$authToken = bin2hex(random_bytes(32));

// Benutzer anlegen (inkl. YAML-Datei im user-Verzeichnis)
if (!saveNewUser($username, $email, $password,$userrole)) {
    die('Benutzer konnte nicht erstellt werden. Möglicherweise existiert er bereits.');
}


// settings.yml schreiben
$settingsPath = __DIR__ . '/../../userdata/config/settings.yml';
$settings = [
    'site_title' => $sitename,
    'theme' => 'basic',
    'language' => 'en',
    'show_upload_dates' => false,
    'default_image_size' => 'M',
    'default_page' => 'home',
    'timeline' => [
        'enable' => true,
        'groupe_by_date' => false
    ],
    'map' => [
        'enable' => true
    ]
];

try {
    $yaml = Yaml::dump($settings, 4, 2);
    file_put_contents($settingsPath, $yaml, LOCK_EX);
} catch (Exception $e) {
    die('Fehler beim Speichern der Einstellungen: ' . $e->getMessage());
}

// Erfolgreich
header('Location: ../../');
exit;
