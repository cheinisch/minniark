<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions/function_frontend.php';

is_installed();

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// use Parsedown;
$Parsedown = new Parsedown();


// JSON-Settings einlesen
$settingsPath = __DIR__ . '/userdata/config/settings.json';
$settings = file_exists($settingsPath)
    ? json_decode(file_get_contents($settingsPath), true)
    : [];


$theme = $settings['theme'] ?? 'basic';

// Twig Setup
$loader = new FilesystemLoader(__DIR__ . '/userdata/template/'.$theme);
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/cache/pages',
    'auto_reload' => true,
    'debug' => true,
]);

$twig->addExtension(new \Twig\Extension\DebugExtension());

// Routing per URL-Pfad
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri = str_replace('image_portfolio/', '', $uri); // falls über Unterordner aufgerufen
$current_path = '/' . $uri;

// Definiere statische Routen
$routes = [
    '' => 'home.twig',
    'home' => 'home.twig',
    'timeline' => 'timeline.twig',
    'map' => 'map.twig',
    'blog' => 'blog.twig',
    'gallery' => 'album.list.twig',
];


include ('functions/twig/twig.php');



// Standardrouten behandeln
if (array_key_exists($uri, $routes)) {
    switch ($uri) {
        case 'blog':
            $data['posts'] = getBlogPosts();
            break;
        case 'timeline':
            $data['timeline'] = getTimelineImagesFromJson();
            break;
        case 'map':
            $data['points'] = getGpsPoints();
            break;
        case 'gallery':
            $data['albums'] = getGalleryAlbums();
            break;
    }

    echo $twig->render($routes[$uri], $data);
    exit;
}

// Fallback 404
http_response_code(404);
echo $twig->render('404.twig', $data);
