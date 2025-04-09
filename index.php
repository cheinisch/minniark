<?php
require_once __DIR__ . './vendor/autoload.php'; // Composer Autoload

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Twig initialisieren
$loader = new FilesystemLoader(__DIR__ . './template/basic');
$twig = new Environment($loader, [
    'cache' => __DIR__ . './cache/pages',
    'auto_reload' => true,
    'debug' => true,
]);

// Routing
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$routes = [
    '' => 'home.twig',
    'home' => 'home.twig',
    'timeline' => 'timeline.twig',
    'map' => 'map.twig',
];

$template = $routes[$uri] ?? null;

if ($template && file_exists(__DIR__ . "./template/basic/$template")) {
    echo $twig->render($template, ['title' => ucfirst($uri) ?: 'Home']);
} else {
    http_response_code(404);
    echo $twig->render('404.twig', ['title' => 'Seite nicht gefunden']);
}
