<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(__DIR__ . "/../../functions/function_backend.php");
security_checklogin();

header('Content-Type: application/json');

$albumName = $_POST['album'] ?? '';
$newName = trim($_POST['name'] ?? '');
$newDesc = trim($_POST['description'] ?? '');

if (!$albumName || !$newName) {
    echo json_encode(['error' => 'Ungültige Eingaben.']);
    exit;
}

error_log("Update Album");
error_log("Eingehende Beschreibung: " . $newDesc);

$oldFilename = __DIR__ . '/../../userdata/content/albums/' . preg_replace('/[^a-z0-9]/i', '_', strtolower($albumName)) . '.php';
$newFilename = __DIR__ . '/../../userdata/content/albums/' . preg_replace('/[^a-z0-9]/i', '_', strtolower($newName)) . '.php';

if (!file_exists($oldFilename)) {
    error_log("Ursprüngliche Albumdatei nicht gefunden");
    echo json_encode(['error' => 'Ursprüngliche Albumdatei nicht gefunden.', 'debug' => ['path' => $oldFilename]]);
    exit;
}

// Prüfen: Existiert die neue Datei bereits (außer wenn es dieselbe ist)?
if (strtolower($albumName) !== strtolower($newName) && file_exists($newFilename)) {
    error_log("Ein Album mit dem gewünschten neuen Titel existiert bereits. Bitte wähle einen anderen Namen.");
    echo json_encode([        
        'error' => 'Ein Album mit dem gewünschten neuen Titel existiert bereits. Bitte wähle einen anderen Namen.',
        'debug' => ['newFileExists' => true, 'newFilename' => $newFilename]
    ]);
    exit;
}

// Bestehende Datei laden
include $oldFilename;

// Werte aktualisieren
$Name = $newName;
$Description = $newDesc;

$content = "<?php\n";
$content .= '$Name = ' . var_export($Name, true) . ";\n";
$content .= '$Description = ' . var_export($Description, true) . ";\n";
$content .= '$Password = ' . var_export($Password ?? '', true) . ";\n";
$content .= '$Images = ' . var_export($Images ?? [], true) . ";\n";
$content .= '$HeadImage = ' . var_export($HeadImage ?? '', true) . ";\n";

// In neue Datei schreiben
$result = file_put_contents($newFilename, $content);

if ($result === false) {
    error_log("Fehler beim Schreiben der Datei an Pfad: $newFilename");
    echo json_encode(['error' => 'Fehler beim Schreiben der neuen Albumdatei.', 'debug' => ['path' => $newFilename]]);
    exit;
} else {
    error_log("Datei erfolgreich geschrieben: $newFilename ($result Bytes)");
}


// Alte Datei löschen, wenn Name geändert wurde
if ($oldFilename !== $newFilename) {
    error_log("lösche alte datei");
    unlink($oldFilename);
}

echo json_encode(['success' => true, 'newAlbum' => $newName]);
