<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../functions/function_backend.php';

use Symfony\Component\Yaml\Yaml;

// Zielpfad
$path = realpath(__DIR__ . '/../../userdata/config');
$file = $path . '/home.yml';

if (!$path || !is_writable($path)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Storage path not writable.']);
    exit;
}

// Bestehende Daten laden
$existing = [];
if (file_exists($file)) {
    $yaml = Yaml::parseFile($file);
    $existing = $yaml['home'] ?? [];
}

// POST-Daten auslesen
$allowedStyleValues = ['album', 'page', 'start'];
$allowedImageStyles = ['album', 'image', ''];

$incoming = [];

// Bereich 1: Allgemeine Inhalte
if (isset($_POST['headline']) || isset($_POST['content'])) {
    $incoming['headline'] = trim($_POST['headline'] ?? '');
    $incoming['sub-headline'] = trim($_POST['sub-headline'] ?? '');
    $incoming['content'] = trim($_POST['content'] ?? '');
}

// Bereich 2: Startinhalt
if (isset($_POST['welcome_type']) || isset($_POST['welcome_content'])) {
    $style = trim($_POST['welcome_type'] ?? '');
    $startcontent = trim($_POST['welcome_content'] ?? '');

    if (!in_array($style, $allowedStyleValues, true)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid welcome_type']);
        exit;
    }

    $incoming['style'] = $style;
    $incoming['startcontent'] = $startcontent;
}

// Bereich 3: Coverdaten (Bild oder Album)
if (isset($_POST['cover']) || isset($_POST['default_image_style'])) {
    $cover = trim($_POST['cover'] ?? '');
    $style = trim($_POST['default_image_style'] ?? '');

    if (!in_array($style, $allowedImageStyles, true)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid default_image_style']);
        exit;
    }

    $incoming['default_image'] = $cover;
    $incoming['default_image_style'] = $style;

    // 💡 Cover auch explizit mitschreiben
    $incoming['cover'] = $cover;
}

// Immer aktualisieren
$incoming['updated_at'] = date('Y-m-d H:i:s');

// Zusammenführen
$merged = array_merge($existing, $incoming);
$yamlData = ['home' => $merged];

// Speichern
if (file_put_contents($file, Yaml::dump($yamlData, 2, 4)) !== false) {
    header('Location: ../dashboard-welcomepage.php');
    exit;
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to write YAML.']);
    exit;
}
