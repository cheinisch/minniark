<?php
// File: change_password.php
session_start();
define('IMAGEPORTFOLIO', true);

$userConfigPath = __DIR__ . '/../userdata/config/user_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = trim($_POST['current_password'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (!file_exists($userConfigPath)) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Benutzerdatei nicht gefunden.']);
        exit;
    }

    $user = require $userConfigPath;

    // Passwort prüfen
    if (!password_verify($currentPassword, $user['PASSWORD_HASH'])) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Aktuelles Passwort ist falsch.']);
        exit;
    }

    // Neue Passwörter vergleichen
    if ($newPassword !== $confirmPassword) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Neue Passwörter stimmen nicht überein.']);
        exit;
    }

    // Neues Passwort hashen und Datei neu schreiben
    $user['PASSWORD_HASH'] = password_hash($newPassword, PASSWORD_DEFAULT);

    $configContent = "<?php\ndefine('IMAGEPORTFOLIO', true);\nreturn " . var_export($user, true) . ";\n";
    file_put_contents($userConfigPath, $configContent, LOCK_EX);

    echo json_encode(['status' => 'success', 'message' => 'Passwort erfolgreich geändert.']);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Nur POST-Anfragen erlaubt.']);
