<?php


require_once __DIR__ . '/../../functions/function_backend.php';
security_checklogin();
require_once __DIR__ . '/../../vendor/autoload.php'; // Symfony YAML laden

use Symfony\Component\Yaml\Yaml;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    print_r($_POST);

    $collectionName = $_POST['collection'] ?? '';
    $albumsToAdd = $_POST['albums'] ?? [];

    if (empty($collectionName) || !is_array($albumsToAdd)) {
        die("Fehlende oder ungültige Daten.");
    }

    // Slug generieren
    $slug = generateSlug($collectionName);
    $collectionDir = __DIR__ . '/../../userdata/content/collection/';
    $collectionPath = $collectionDir . $slug . '.yml';

    if (!file_exists($collectionPath)) {
        die("collection nicht gefunden.");
    }

    // Bestehende Daten laden
    $yamlData = Yaml::parseFile($collectionPath);
    $collectionData = $yamlData['collection'] ?? [];
    $existingAlbums = $collectionData['albums'] ?? [];

    // Neue Bilder hinzufügen (Duplikate vermeiden)
    $mergedalbums = array_unique(array_merge($existingAlbums, $albumsToAdd));

    // Nur das Feld 'albums' übergeben und mit sich selbst als $oldSlug speichern
    $success = updateCollection($slug, ['albums' => $mergedalbums], $slug);

    if ($success) {
        header("Location: ../collection-detail.php?collection=$slug");
        exit;
    } else {
        echo "Fehler beim Speichern.";
    }
} else {
    echo "Ungültige Anfrage.";
}
