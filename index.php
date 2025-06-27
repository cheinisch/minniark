<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions/function_frontend.php';

is_installed();

// LAden von Plugins
$pluginDirs = glob(__DIR__ . '/userdata/plugins/*', GLOB_ONLYDIR);

foreach ($pluginDirs as $pluginDir) {
    $configFile = $pluginDir . '/plugin.json';
    $routeFile  = $pluginDir . '/routes.php';

    if (file_exists($configFile)) {
        $config = json_decode(file_get_contents($configFile), true);
        if (!empty($config['enabled']) && file_exists($routeFile)) {
            require_once $routeFile;
        }
    }
}

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// use Parsedown;
$Parsedown = new Parsedown();

// Import Yaml
use Symfony\Component\Yaml\Yaml;


$settingsPath = __DIR__ . '/userdata/config/settings.yml';
$settings = [];

if (file_exists($settingsPath)) {
    error_log("File exist!!!");
    try {
        $settings = Yaml::parseFile($settingsPath);
        error_log(print_r($settings, true));
    } catch (Exception $e) {
        error_log("YAML-Fehler beim Einlesen der Einstellungen: " . $e->getMessage());
    }
}else{
    erorr_log("File not exist");
}


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
$uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '', '/');
$uri = str_replace('image_portfolio/', '', $uri); // falls über Unterordner aufgerufen
$current_path = '/' . $uri;

// Definiere statische Routen
$routes = [
    '' => 'home.twig',
    'home' => 'home.twig',
    'timeline' => 'timeline.twig',
    'map' => 'map.twig',
    'blog' => 'blog.twig',
    'albums' => 'album.list.twig',
];


include ('functions/twig/twig.php');



// Standardrouten behandeln
if (array_key_exists($uri, $routes)) {
    switch ($uri) {
        case 'blog':
            $data['posts'] = getBlogPosts();
            break;
        case 'timeline':
            $data['timeline'] = getTimelineImagesFromYaml();
            break;
        case 'map':
            $data['points'] = getGpsPoints();
            break;
        case 'albums':
            $data['albums'] = getGalleryAlbums();
            break;
    }

    echo $twig->render($routes[$uri], $data);
    exit;
}

// Fallback 404
http_response_code(404);
echo $twig->render('404.twig', $data);
