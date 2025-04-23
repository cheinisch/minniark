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
$configPath = __DIR__ . '/../userdata/user_config.php';

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

// Rückmeldung (optional)
echo "Benutzerkonfiguration gespeichert.";
