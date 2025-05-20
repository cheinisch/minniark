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
$tags = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
$cover = trim($_POST['cover'] ?? '');
$published_at = trim($_POST['published_at'] ?? '');

print_r($_POST);
// Validierung
if (empty($title) || empty($foldername)) {
    die("Titel und Zielordner (foldername) müssen übergeben werden.");
}

$isPublished = !empty($published_at);

// Slug normalisieren
$slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $foldername), '-'));
$originalSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $original), '-'));

$essayDir = __DIR__ . '/../../userdata/content/essay/';
$targetPath = $essayDir . $slug;

// Neuer Eintrag?
if (empty($original)) {
    // Sicherstellen, dass der Slug eindeutig ist
    $uniqueSlug = $slug;
    $i = 1;
    while (is_dir($essayDir . $uniqueSlug)) {
        $uniqueSlug = $slug . '-' . $i;
        $i++;
    }
    $slug = $uniqueSlug;

    saveNewEssay($title, $content, $tags, $isPublished, $cover);

    header("Location: ../blog-detail.php?edit=$slug");
    exit;
} else {
    // Update bestehenden Eintrag
    $data = [
        'title' => $title,
        'content' => $content,
        'tags' => $tags,
        'cover' => $cover,
        'published_at' => $published_at,
        'is_published' => $isPublished
    ];

    $success = updateEssay($slug, $data, $originalSlug);

    if ($success) {
        header("Location: ../blog-detail.php?edit=$slug");
        exit;
    } else {
        echo "Fehler beim Speichern des Essays.";
    }
}
