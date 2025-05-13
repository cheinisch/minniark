<?php
require_once(__DIR__ . "/../../functions/function_backend.php");
security_checklogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Albuminformationen von POST-Daten übernehmen
    $albumName = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $images = isset($_POST['images']) && is_array($_POST['images']) ? $_POST['images'] : [];
    $headImage = isset($_POST['headImage']) ? trim($_POST['headImage']) : '';

    // Validierung
    if (empty($albumName)) {
        exit(json_encode(['error' => 'Album name is required.']));
    }

    // Dateiname erstellen (in Kleinbuchstaben und ohne Sonderzeichen)
    $fileName = preg_replace('/[^a-z0-9]/', '_', strtolower($albumName)) . '.php';
    $newName =  preg_replace('/[^a-z0-9]/', '_', strtolower($albumName));

    // Zielverzeichnis
    $albumDir = __DIR__ . '/../../userdata/content/albums';
    if (!is_dir($albumDir)) {
        mkdir($albumDir, 0755, true);
    }

    // Dateiinhalt vorbereiten
    $content = "<?php\n";
    $content .= '$Name = ' . var_export($albumName, true) . ";\n";
    $content .= '$Description = ' . var_export($description, true) . ";\n";
    $content .= '$Password = ' . var_export($password, true) . ";\n";
    $content .= '$Images = ' . var_export($images, true) . ";\n";
    $content .= '$HeadImage = ' . var_export($headImage, true) . ";\n";
    $content .= '$Slug = ' . var_export($newName, true) . ";\n";

    // Datei erstellen
    $filePath = $albumDir . '/' . $fileName;
    if (file_put_contents($filePath, $content) === false) {
        exit(json_encode(['error' => 'Failed to create album file.']));
    }

    // Erfolgsmeldung zurückgeben
    echo json_encode(['success' => 'Album successfully created.', 'path' => $filePath]);
} else {
    exit(json_encode(['error' => 'Invalid request method.']));
}
