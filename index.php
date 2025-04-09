<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/functions.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Twig Setup
$loader = new FilesystemLoader(__DIR__ . '/../templates/basic');
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/../cache/pages',
    'auto_reload' => true,
    'debug' => true,
]);

// Routing
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Route -> Template Mapping
$routes = [
    '' => 'home.twig',
    'home' => 'home.twig',
    'timeline' => 'timeline.twig',
    'map' => 'map.twig',
];

$template = $routes[$uri] ?? null;

if ($template && file_exists(__DIR__ . "/../templates/basic/$template")) {
    echo $twig->render($template, [
        'title' => ucfirst($uri) ?: 'Home',
        'data' => getData($uri), // Beispiel-Funktion
    ]);
} else {
    http_response_code(404);
    echo $twig->render('404.twig', ['title' => 'Seite nicht gefunden']);
}
