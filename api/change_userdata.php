<?php
// File: change_userdata.php
session_start();
define('IMAGEPORTFOLIO', true);

header('Content-Type: application/json');

$userConfigPath = __DIR__ . '/../userdata/config/user_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Nur POST-Anfragen erlaubt.']);
    exit;
}

if (!file_exists($userConfigPath)) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Benutzerdatei nicht gefunden.']);
    exit;
}

$user = require $userConfigPath;

// Eingaben sichern
$username     = trim($_POST['username'] ?? '');
$email        = trim($_POST['email'] ?? '');
$display_name = trim($_POST['display-name'] ?? '');

// Validierung (einfach gehalten)
if (!$username || !$email) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Benutzername und E-Mail sind erforderlich.']);
    exit;
}

// Daten aktualisieren
$user['USERNAME'] = $username;
$user['EMAIL']    = $email;
if ($display_name) {
    $user['DISPLAY_NAME'] = $display_name;
}

// Datei neu schreiben
$configContent = "<?php\ndefine('IMAGEPORTFOLIO', true);\nreturn " . var_export($user, true) . ";\n";
file_put_contents($userConfigPath, $configContent, LOCK_EX);

// Erfolg melden
echo json_encode(['status' => 'success', 'message' => 'Benutzerdaten gespeichert.']);
