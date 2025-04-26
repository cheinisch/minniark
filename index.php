<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions/function_frontend.php';



use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Parsedown;

is_installed();

// Twig Setup
$loader = new FilesystemLoader(__DIR__ . '/template/basic');
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/cache/pages',
    'auto_reload' => true,
    'debug' => true,
]);

$twig->addExtension(new \Twig\Extension\DebugExtension());

// Routing per URL-Pfad
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri = str_replace('image_portfolio/', '', $uri); // falls über Unterordner aufgerufen

// Definiere statische Routen
$routes = [
    '' => 'home.twig',
    'home' => 'home.twig',
    'timeline' => 'timeline.twig',
    'map' => 'map.twig',
    'blog' => 'blog.twig',
];

// JSON-Settings einlesen
$settingsPath = __DIR__ . '/userdata/config/settings.json';
$settings = file_exists($settingsPath)
    ? json_decode(file_get_contents($settingsPath), true)
    : [];

$theme = $settings['theme'] ?? 'basic';

$data = [
    'title' => ucfirst($uri) ?: 'Home',
    'site_title' => $settings['site_title'] ?? 'Image Portfolio',
    'theme' => $theme,
    'themepath' => "/template/{$theme}",
    'settings' => $settings,
];

// Dynamischer Blogpost: /blog/<slug>
// Einzelner Blog-Post anhand des Slugs aus dem Verzeichnisnamen
if (preg_match('#^blog/([\w\-]+)$#', $uri, $matches)) {
    $slug = $matches[1]; // z. B. "testeintrag"
    $essaysPath = 'content/essays/';
    $post = null;

    foreach (glob($essaysPath . '*/') as $dir) {
        $folder = basename($dir); // z. B. "2025-04-14_testeintrag"

        if (str_ends_with($folder, "_$slug")) {
            $jsonFiles = glob($dir . '*.json');
            if (!empty($jsonFiles)) {
                $json = json_decode(file_get_contents($jsonFiles[0]), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (preg_match('/^(\d{4}-\d{2}-\d{2})_(.+)$/', $folder, $m)) {
                        $json['date'] = $m[1];
                        $json['slug'] = $m[2];
                        $json['folder'] = $folder;
                        $post = $json;
                        break;
                    }
                }
            }
        }
    }

    if ($post) {
        $parsedown = new Parsedown();
        $post['content_html'] = $parsedown->text($post['content'] ?? '');
        $data['post'] = $post;
        echo $twig->render('post.twig', $data);
        exit;
    } else {
        http_response_code(404);
        echo $twig->render('404.twig', $data);
        exit;
    }
}

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
    }

    echo $twig->render($routes[$uri], $data);
    exit;
}

// Fallback 404
http_response_code(404);
echo $twig->render('404.twig', $data);
