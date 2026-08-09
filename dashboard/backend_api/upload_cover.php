<?php


require_once(__DIR__ . '/../../functions/function_backend.php');
security_checklogin();

header('Content-Type: application/json');

error_log("Uploadphp geladen");

// Zielverzeichnis
$tempDir = realpath(__DIR__ . '/../../temp');
if (!$tempDir || !is_writable($tempDir)) {
    echo json_encode(['success' => false, 'message' => 'Temporäres Verzeichnis nicht verfügbar oder nicht beschreibbar.']);
    exit;
}

// Datei vorhanden?
if (!isset($_FILES['coverFile']) || $_FILES['coverFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Fehler beim Datei-Upload.']);
    exit;
}

// Erweiterung prüfen
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$filename = basename($_FILES['coverFile']['name']);
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'Ungültiger Dateityp.']);
    exit;
}

// Neuen Dateinamen erzeugen
$newName = uniqid('cover_', true) . '.' . $extension;
$targetPath = $tempDir . DIRECTORY_SEPARATOR . $newName;

// Datei verschieben
if (move_uploaded_file($_FILES['coverFile']['tmp_name'], $targetPath)) {
    $relativePath = '/temp/' . $newName;
    error_log("Pfad: " .$relativePath);
    echo json_encode(['success' => true, 'path' => $relativePath]);
} else {
    echo json_encode(['success' => false, 'message' => 'Datei konnte nicht gespeichert werden.']);
}
