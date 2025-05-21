<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../functions/function_backend.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // für Yaml


use Symfony\Component\Yaml\Yaml;

$path = realpath(__DIR__ . '/../../userdata/config');
$file = $path . '/home.yml';

if (!$path || !is_writable($path)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Storage path not writable.']);
    exit;
}

// Vorhandene Daten laden
$existing = [];
if (file_exists($file)) {
    $yaml = Yaml::parseFile($file);
    $existing = $yaml['home'] ?? [];
}

// Felder vorbereiten
$allowedStyleValues = ['album', 'page', 'start'];
$allowedImageStyles = ['album', 'image', ''];

$incoming = [];

// Bereich 1: Texteingaben (z. B. aus Hauptformular)
if (isset($_POST['headline']) || isset($_POST['content'])) {
    $incoming['headline'] = trim($_POST['headline'] ?? '');
    $incoming['sub-headline'] = trim($_POST['sub-headline'] ?? '');
    $incoming['content'] = trim($_POST['content'] ?? '');
}

// Bereich 2: Willkommensstil und Startinhalt
if (isset($_POST['welcome_type']) || isset($_POST['welcome_content'])) {
    $style = trim($_POST['welcome_type'] ?? '');
    $startcontent = trim($_POST['welcome_content'] ?? '');

    if (!in_array($style, $allowedStyleValues, true)) {
        http_response_code(400);
        exit;
    }

    $incoming['style'] = $style;
    $incoming['startcontent'] = $startcontent;
}

// Bereich 3: Standardbild und Stil (z. B. aus „Cover“-Aktion)
if (isset($_POST['cover']) || isset($_POST['default_image_style'])) {
    $cover = trim($_POST['cover'] ?? '');
    $imgStyle = trim($_POST['default_image_style'] ?? '');

    if (!in_array($imgStyle, $allowedImageStyles, true)) {
        http_response_code(400);
        exit;
    }

    $incoming['default_image'] = $cover;
    $incoming['default_image_style'] = $imgStyle;
}

// Immer aktualisieren
$incoming['updated_at'] = date('Y-m-d H:i:s');

// Zusammenführen & speichern
$merged = array_merge($existing, $incoming);
$yamlData = ['home' => $merged];

if (file_put_contents($file, Yaml::dump($yamlData, 2, 4)) !== false) {
    header('Location: ../dashboard-welcomepage.php');
    exit;
} else {
    http_response_code(500);
    exit;
}