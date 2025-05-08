<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(__DIR__ . "/../../functions/function_backend.php");
security_checklogin();

$albumName = $_POST['album-current-title'] ?? '';
$newName = trim($_POST['album-title-edit'] ?? '');
$newDesc = trim($_POST['album-description'] ?? '');

error_log("Update File");
error_log("Album old:".$albumName);
error_log("Album new:".$newName);
error_log("Album Description:".$newDesc);

if (!$albumName || !$newName) {
    http_response_code(400);
    echo "Ungültige Eingaben.";
    exit;
}

$oldFilename = __DIR__ . '/../../userdata/content/albums/' . preg_replace('/[^a-z0-9]/i', '_', strtolower($albumName)) . '.php';
$newFilename = __DIR__ . '/../../userdata/content/albums/' . preg_replace('/[^a-z0-9]/i', '_', strtolower($newName)) . '.php';

if (!file_exists($oldFilename)) {
    http_response_code(404);
    echo "Ursprüngliche Albumdatei nicht gefunden.";
    exit;
}

if (strtolower($albumName) !== strtolower($newName) && file_exists($newFilename)) {
    http_response_code(409);
    echo "Album mit diesem Titel existiert bereits.";
    exit;
}

include $oldFilename;

$Name = $newName;
$Description = $newDesc;

$content = "<?php\n";
$content .= '$Name = ' . var_export($Name, true) . ";\n";
$content .= '$Description = ' . var_export($Description, true) . ";\n";
$content .= '$Password = ' . var_export($Password ?? '', true) . ";\n";
$content .= '$Images = ' . var_export($Images ?? [], true) . ";\n";
$content .= '$HeadImage = ' . var_export($HeadImage ?? '', true) . ";\n";

if (file_put_contents($newFilename, $content) === false) {
    http_response_code(500);
    echo "Fehler beim Schreiben der Datei.";
    exit;
}

if ($oldFilename !== $newFilename) {
    unlink($oldFilename);
}
sleep(5);
// Erfolg: kein Output (Status 200)
header("Location: ../album-detail.php?album=$newName");
exit;

?>
