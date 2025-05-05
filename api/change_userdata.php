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

// Eingabe sichern
$auth_type = trim($_POST['auth_type'] ?? '');

if (!$auth_type) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'AUTH_TYPE ist erforderlich.']);
    exit;
}

// AUTH_TYPE setzen
$user['AUTH_TYPE'] = $auth_type;

// Datei neu schreiben
$configContent = "<?php\n//define('IMAGEPORTFOLIO', true);\nreturn " . var_export($user, true) . ";\n";
file_put_contents($userConfigPath, $configContent, LOCK_EX);

// Erfolg melden
echo json_encode(['status' => 'success', 'message' => 'AUTH_TYPE wurde gespeichert.']);
