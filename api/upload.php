<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Berlin');
require_once( __DIR__ . "/../functions/function_api.php");
    secure_API();


// Debugging-Funktion für Logs
function logMessage($message) {
    file_put_contents(__DIR__ . '/upload_log.txt', date("[Y-m-d H:i:s]") . " " . $message . PHP_EOL, FILE_APPEND);
}

// Basisverzeichnisse
$uploadDir = __DIR__ . '/../userdata/content/images/';
$cacheDir = __DIR__ . '/../cache/images/';

// Stelle sicher, dass die Verzeichnisse existieren
foreach ([$uploadDir, $cacheDir] as $dir) {
    if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
        die(json_encode(["error" => "Failed to create directory: $dir"]));
    }
    chmod($dir, 0777);
}

// Prüfe, ob eine Datei hochgeladen wurde
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    die(json_encode(["error" => "No file uploaded."]));
}

$file = $_FILES['file'];
$fileName = basename($file['name']);  // Originaldateiname beibehalten
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$allowedTypes = ['jpg', 'jpeg', 'png'];

if (!in_array($fileExt, $allowedTypes)) {
    die(json_encode(["error" => "Invalid file type! Allowed: JPG, PNG."]));
}

// Definiere den Speicherpfad für das Originalbild
$targetFile = $uploadDir . $fileName;

// Falls eine Datei mit dem gleichen Namen existiert, hänge eine Zahl an
$counter = 1;
while (file_exists($targetFile)) {
    $fileName = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME) . "_$counter." . $fileExt;
    $targetFile = $uploadDir . $fileName;
    $counter++;
}

// Datei speichern
if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
    die(json_encode(["error" => "Error moving uploaded file."]));
}

logMessage("File uploaded successfully: $targetFile");

// Generiere eine GUID für die Thumbnails
$guid = uniqid();

error_log("GUID: ".$guid);

// EXIF-Daten auslesen
$exifData = extractExifData($targetFile);

error_log("Exif Data: ". $exifData['Camera']);



// Erstelle Thumbnails mit der GUID im /cache/ Verzeichnis
$sizes = ['S' => 150, 'M' => 500, 'L' => 1024, 'XL' => 1920];
error_log("After Sizes Array");
foreach ($sizes as $sizeKey => $sizeValue) {
    error_log("Thumb Foreach");
    
    $thumbnailPath = $cacheDir . $guid . "_$sizeKey." . $fileExt;
    error_log("Filepath: ".$thumbnailPath);
    createThumbnail($targetFile, $thumbnailPath, $sizeValue);
    logMessage("Thumbnail created: $thumbnailPath");
}

// JSON-Datei mit Metadaten speichern
$jsonData = [
    "filename" => $fileName,
    "guid" => $guid,
    "title" => "",
    "description" => "",
    "tags" => [],
    "rating" => 0,
    "upload_date" => date("Y-m-d H:i:s"),
    "exif" => $exifData
];
$jsonFile = $uploadDir . pathinfo($fileName, PATHINFO_FILENAME) . '.json';

// Versuche die JSON-Datei zu schreiben
if (file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT)) === false) {
    $errorMessage = "Failed to save JSON metadata: $jsonFile";
    logMessage($errorMessage);                // in upload_log.txt schreiben
    error_log($errorMessage);                  // zusätzlich ins PHP-Error-Log schreiben
    die(json_encode(["error" => "Failed to save JSON metadata."]));
} else {
    logMessage("JSON metadata saved: $jsonFile");
    error_log("JSON erfolgreich geschrieben");
    error_log(json_encode(["success" => "File uploaded successfully!", "filename" => $fileName]));
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => "File uploaded successfully!", "filename" => $fileName]);
}



// --------- FUNKTIONEN ---------


?>
