<?php


require_once( __DIR__ . "/../functions/function_api.php");
secure_API();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prefix = trim($_POST['prefix'] ?? '');

    // Nur erlaubte Zeichen (alphanumerisch, - und _)
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $prefix)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ungültiger Prefix']);
        exit;
    }

    // Pfad zur PHP-Konfigurationsdatei
    $configPath = __DIR__ . '/../userdata/config/backup_config.php';

    // Inhalt schreiben
    $content = "<?php\n\$backup_prefix = '" . addslashes($prefix) . "';\n";
    if (file_put_contents($configPath, $content)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Konnte Prefix nicht speichern']);
    }
}