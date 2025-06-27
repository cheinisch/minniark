<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../functions/function_backend.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // für Yaml

// POST-Daten
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$foldername = trim($_POST['foldername'] ?? '');
$original = trim($_POST['original_foldername'] ?? '');
$tags = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
$cover = trim($_POST['cover'] ?? '');
$published_at = trim($_POST['published_at'] ?? '');

$isPublished = false;

if($_POST['is_published'] == "true")
{
    $isPublished = true;
}else{
    $isPublished = false;
}

// Validierung
if (empty($title) || empty($foldername)) {
    die("Title and folder name must be provided.");
}

// Slugs normalisieren
$slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $foldername), '-'));
$originalSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $original), '-'));

$essayDir = __DIR__ . '/../../userdata/content/essay/';

// Plugin-Felder einsammeln
$pluginFields = [];
$pluginDirs = glob(__DIR__ . '/../../userdata/plugins/*', GLOB_ONLYDIR);

foreach ($pluginDirs as $pluginDir) {
    $settingsFile = $pluginDir . '/settings.json';
    $postJson = $pluginDir . '/admin/post.json';

    if (!file_exists($settingsFile) || !file_exists($postJson)) {
        continue;
    }

    $settings = json_decode(file_get_contents($settingsFile), true);
    if (empty($settings['enabled'])) {
        continue;
    }

    $fields = json_decode(file_get_contents($postJson), true)['fields'] ?? [];
    foreach ($fields as $field) {
        $key = $field['key'];
        $type = $field['type'] ?? 'text';

        if ($type === 'toggle') {
            $pluginFields[$key] = ($_POST[$key] ?? 'false') === 'true';
        } else {
            $pluginFields[$key] = trim($_POST[$key] ?? '');
        }
    }
}

// Gemeinsame Felder
$data = array_merge([
    'title' => $title,
    'content' => $content,
    'tags' => $tags,
    'cover' => $cover,
    'published_at' => $published_at,
    'is_published' => $isPublished,
], $pluginFields);

// Entscheidung: neu oder update
if (empty($original)) {
    $slug = saveNewEssay($title, $content, $tags, $isPublished, $cover, $pluginFields);
} else {
    updateEssay($slug, $data, $originalSlug);
}

header("Location: ../blog-detail.php?edit=$slug");
exit;
