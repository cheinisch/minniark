<?php

header('Content-Type: application/json');

require_once(__DIR__ . '/../functions/function_api.php');
require_once(__DIR__ . '/../functions/backend/sitemap.php');
require_once(__DIR__ . '/../vendor/autoload.php');

use Symfony\Component\Yaml\Yaml;

$settingsFile = __DIR__ . '/../userdata/config/settings.yml';

// POST prüfen
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Eingabedaten lesen
$input = json_decode(file_get_contents('php://input'), true);

// Default-Werte, falls Datei neu angelegt werden muss
$defaultSettings = [
    'site_title' => 'Minniark',
    'site_description' => '',
    'theme' => 'basic',
    'language' => 'en',
    'default_image_size' => 'M',
    'default_page' => 'home',
    'timeline' => [
        'enable' => false,
        'groupe_by_date' => false
    ],
    'map' => [
        'enable' => false
    ],
    'sitemap' => [
        'active' => false,
        'images' => false
    ]
];

// YAML laden oder initialisieren
if (!file_exists($settingsFile)) {
    // Ordner anlegen, falls notwendig
    @mkdir(dirname($settingsFile), 0777, true);
    $settings = $defaultSettings;
} else {
    try {
        $settings = Yaml::parseFile($settingsFile);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Fehler beim Parsen der Einstellungen: ' . $e->getMessage()]);
        exit;
    }
}

// Einstellungen aktualisieren
if (isset($input['site_name'])) {
    $settings['site_title'] = $input['site_name'];
}
if (isset($input['site_description'])) {
    $settings['site_description'] = $input['site_description'];
}
if (isset($input['language'])) {
    $settings['language'] = $input['language'];
}
if (isset($input['image_size'])) {
    $settings['default_image_size'] = $input['image_size'];
}
if (isset($input['timeline_enable'])) {
    $settings['timeline']['enable'] = (bool)$input['timeline_enable'];
}
if (isset($input['timeline_group_by_date'])) {
    $settings['timeline']['groupe_by_date'] = (bool)$input['timeline_group_by_date'];
}
if (isset($input['map_enable'])) {
    $settings['map']['enable'] = (bool)$input['map_enable'];
}
if (isset($input['sitemap_enable'])) {
    //sitemap();
    $settings['sitemap']['active'] = (bool)$input['sitemap_enable'];
}
if (isset($input['sitemap_images_enable'])) {
    $settings['sitemap']['images'] = (bool)$input['sitemap_images_enable'];
}
if (isset($input['theme'])) {
    $settings['theme'] = $input['theme'];
}

// Speichern
try {
    file_put_contents($settingsFile, Yaml::dump($settings, 4, 2));
    if (isset($input['sitemap_enable'])) {
        sitemap();
    }
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern: ' . $e->getMessage()]);
}
