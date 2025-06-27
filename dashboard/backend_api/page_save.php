<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../functions/function_backend.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // für Yaml

use Symfony\Component\Yaml\Yaml;

// POST-Daten abholen
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$foldername = trim($_POST['foldername'] ?? '');
$original = trim($_POST['original_foldername'] ?? '');
$cover = trim($_POST['cover'] ?? '');
$published_at = trim($_POST['published_at'] ?? '');

// Validierung
if (empty($title) || empty($foldername)) {
    die("Titel und Zielordner (foldername) müssen übergeben werden.");
}

$isPublished = false;

if($_POST['is_published'] == "true")
{
    $isPublished = true;
}else{
    $isPublished = false;
}

// Slug normalisieren
$slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $foldername), '-'));
$originalSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $original), '-'));

$pageDir = __DIR__ . '/../../userdata/content/page/';
$targetPath = $pageDir . $slug;

// Neuer Eintrag?
if (empty($original)) {
    // Sicherstellen, dass der Slug eindeutig ist
    $uniqueSlug = $slug;
    $i = 1;
    while (is_dir($pageDir . $uniqueSlug)) {
        $uniqueSlug = $slug . '-' . $i;
        $i++;
    }
    $slug = $uniqueSlug;

    saveNewPage($title, $content, $isPublished, $cover);

    header("Location: ../page-detail.php?edit=$slug");
    exit;
} else {
    // Update bestehenden Eintrag
    $data = [
        'title' => $title,
        'content' => $content,
        'cover' => $cover,
        'published_at' => $published_at,
        'is_published' => $isPublished
    ];

    $success = updatepage($slug, $data, $originalSlug);

    if ($success) {
        header("Location: ../page-detail.php?edit=$slug");
        exit;
    } else {
        echo "Fehler beim Speichern des pages.";
    }
}
