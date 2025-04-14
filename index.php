<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Definiere statische Routen
$routes = [
    '' => 'home.twig',
    'home' => 'home.twig',
    'timeline' => 'timeline.twig',
    'map' => 'map.twig',
    'blog' => 'blog.twig',  // Blog-Übersicht
];

// Dynamische Blogpost-Route erkennen: /blog/<folder>
if (preg_match('#^blog/([\w\-]+_\w+)$#', $uri, $matches)) {
    $folder = $matches[1]; // z. B. "2025-04-14_testeintrag"
    $jsonPath = "content/essays/$folder/$folder.json";

    if (file_exists($jsonPath)) {
        $post = json_decode(file_get_contents($jsonPath), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $post['date'] = substr($folder, 0, 10);
            $post['slug'] = substr($folder, 11);
            echo $twig->render('post.twig', ['post' => $post]);
            exit;
        }
    }

    http_response_code(404);
    echo "Beitrag nicht gefunden.";
    exit;
}

// Standardrouten laden
if (array_key_exists($uri, $routes)) {
    switch ($uri) {
        case 'blog':
            $posts = getBlogPosts();
            echo $twig->render($routes[$uri], ['posts' => $posts]);
            break;

        case 'timeline':
            $images = getTimelineImagesFromJson();
            echo $twig->render($routes[$uri], ['images' => $images]);
            break;

        default:
            echo $twig->render($routes[$uri]);
            break;
    }
    exit;
}

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

//echo $settings['theme'];

if ($template && file_exists(__DIR__ . "/template/basic/$template")) {
    echo $twig->render($template, array_merge([
        'title' => ucfirst($uri) ?: 'Home',
        'site_title' => $settings['site_title'] ?? 'Image Portfolio',
        'theme' => $settings['theme'] ?? 'classic',
        'themepath' => "/template/".($settings['theme'] ?? 'classic'),
    ], $data ?? []));
} else {
    http_response_code(404);
    echo $twig->render('404.twig', array_merge([
        'title' => ucfirst($uri) ?: 'Home',
        'site_title' => $settings['site_title'] ?? 'Image Portfolio',
        'theme' => $settings['theme'] ?? 'classic',
        'themepath' => "/template/".($settings['theme'] ?? 'classic'),
    ], $data ?? []));
}
