<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../functions/function_backend.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // Für Yaml

use Symfony\Component\Yaml\Yaml;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $albumName = $_GET['album'] ?? '';
    $headImage = $_GET['filename'] ?? '';

    if (empty($albumName) || empty($headImage)) {
        die("Fehlende oder ungültige Daten.");
    }

    // Slug bereinigen
    $slug = generateSlug($albumName);
    $albumDir = __DIR__ . '/../../userdata/content/album/';
    $albumPath = $albumDir . $slug . '.yml';

    if (!file_exists($albumPath)) {
        die("Album nicht gefunden.");
    }

    // Bestehende YAML laden
    $yamlData = Yaml::parseFile($albumPath);
    $album = $yamlData['album'] ?? [];

    // Prüfen, ob Bild überhaupt im Album vorkommt
    $imageList = $album['images'] ?? [];
    if (!in_array($headImage, $imageList)) {
        die("Bild ist nicht Teil des Albums.");
    }

    // HeadImage setzen über updateAlbum
    $success = updateAlbum($slug, ['headImage' => $headImage], $slug);

    if (!$success) {
        die("Fehler beim Speichern.");
    }

    // Redirect
    header("Location: ../album-detail.php?album=$slug");
    exit;
} else {
    echo "Ungültige Anfrage.";
}
