<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions/functions.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Twig Setup
$loader = new FilesystemLoader(__DIR__ . '/template/basic');
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/cache/pages',
    'auto_reload' => true,
    'debug' => true,
]);

// Routing per URL-Pfad
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri = str_replace('image_portfolio/', '', $uri); // falls über Unterordner aufgerufen

$routes = [
    '' => 'home.twig',
    'home' => 'home.twig',
    'timeline' => 'timeline.twig',
    'map' => 'map.twig',
];

if ($uri === 'timeline') {
    $data['timeline'] = getTimelineImagesFromJson('content/images/');
}

// Template auswählen
$template = $routes[$uri] ?? null;

$settingsPath = __DIR__ . '/userdata/settings.json';
$settings = [];

if (file_exists($settingsPath)) {
    $settingsJson = file_get_contents($settingsPath);
    $settings = json_decode($settingsJson, true);
}

if ($template && file_exists(__DIR__ . "/template/basic/$template")) {
    echo $twig->render($template, array_merge([
        'title' => ucfirst($uri) ?: 'Home',
        'site_title' => $settings['site_title'] ?? 'Image Portfolio',
        'theme' => $settings['theme'] ?? 'classic',
    ], $data ?? []));
} else {
    http_response_code(404);
    echo $twig->render('404.twig', ['title' => 'Seite nicht gefunden']);
}
