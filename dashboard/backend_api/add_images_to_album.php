<?php


require_once __DIR__ . '/../../functions/function_backend.php';
security_checklogin();
require_once __DIR__ . '/../../vendor/autoload.php'; // Symfony YAML laden

use Symfony\Component\Yaml\Yaml;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $albumName = $_POST['album'] ?? '';
    $imagesToAdd = $_POST['images'] ?? [];

    if (empty($albumName) || !is_array($imagesToAdd)) {
        die("Fehlende oder ungültige Daten.");
    }

    // Slug generieren
    $slug = generateSlug($albumName);
    $albumDir = __DIR__ . '/../../userdata/content/album/';
    $albumPath = $albumDir . $slug . '.yml';

    if (!file_exists($albumPath)) {
        die("Album nicht gefunden.");
    }

    // Bestehende Daten laden
    $yamlData = Yaml::parseFile($albumPath);
    $albumData = $yamlData['album'] ?? [];
    $existingImages = $albumData['images'] ?? [];

    // Neue Bilder hinzufügen (Duplikate vermeiden)
    $mergedImages = array_unique(array_merge($existingImages, $imagesToAdd));

    // Nur das Feld 'images' übergeben und mit sich selbst als $oldSlug speichern
    $success = updateAlbum($slug, ['images' => $mergedImages], $slug);

    if ($success) {
        header("Location: ../album-detail.php?album=$slug");
        exit;
    } else {
        echo "Fehler beim Speichern.";
    }
} else {
    echo "Ungültige Anfrage.";
}
