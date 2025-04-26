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

// EXIF-Daten auslesen
$exifData = extractExifData($targetFile);

error_log("Exif Data: ".$exifData);

// Erstelle Thumbnails mit der GUID im /cache/ Verzeichnis
$sizes = ['S' => 150, 'M' => 500, 'L' => 1024, 'XL' => 1920];
foreach ($sizes as $sizeKey => $sizeValue) {
    $thumbnailPath = $cacheDir . $guid . "_$sizeKey." . $fileExt;
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
file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));
logMessage("JSON metadata saved: $jsonFile");

echo json_encode(["success" => "File uploaded successfully!", "filename" => $fileName]);


// --------- FUNKTIONEN ---------

// Erstelle ein Thumbnail in der gewünschten Größe
function createThumbnail($source, $destination, $newWidth) {
    error_log("Start Thumbnail");
    list($width, $height, $type) = getimagesize($source);
    $newHeight = intval(($height / $width) * $newWidth);

    switch ($type) {
        case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($source); break;
        case IMAGETYPE_PNG:  $image = imagecreatefrompng($source); break;
        case IMAGETYPE_GIF:  $image = imagecreatefromgif($source); break;
        default: return false;
    }

    $thumb = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    switch ($type) {
        case IMAGETYPE_JPEG: imagejpeg($thumb, $destination, 85); break;
        case IMAGETYPE_PNG:  imagepng($thumb, $destination, 8); break;
        case IMAGETYPE_GIF:  imagegif($thumb, $destination); break;
    }

    imagedestroy($thumb);
    imagedestroy($image);
}
?>
