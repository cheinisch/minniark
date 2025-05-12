<?php

$templateDir = __DIR__ . '/../../userdata/template/' . $theme;
$navItems = buildNavigation($templateDir);
    
$data = [
    'title' => ucfirst($uri) ?: 'Home',
    'site_title' => $settings['site_title'] ?? 'Image Portfolio',
    'theme' => $theme,
    'themepath' => "/userdata/template/{$theme}",
    'settings' => $settings,
    'navItems' => $navItems,
    'current_path' => $current_path,
];

// Einzelne statische Seite per Slug: /p/<slug>
if (preg_match('#^p/([\w\-]+)$#', $uri, $matches)) {
    $slug = $matches[1]; // z. B. "impressum"
    $pageDir = realpath(__DIR__ . '/../../userdata/content/pages/' . $slug);

    if ($pageDir && file_exists($pageDir . '/data.json')) {
        $json = json_decode(file_get_contents($pageDir . '/data.json'), true);

        // use Parsedown;
        $Parsedown = new Parsedown();

        if (json_last_error() === JSON_ERROR_NONE) {
            $json['content'] = $Parsedown->text($json['content']);
            $data['page'] = $json;            
            $data['title'] = $json['title'];
            echo $twig->render('page.twig', $data);
            exit;
        }
    }
}


// Dynamischer Blogpost: /blog/<slug>
// Einzelner Blog-Post anhand des Slugs aus dem Verzeichnisnamen
if (preg_match('#^blog/([\w\-]+)$#', $uri, $matches)) {
    $slug = $matches[1]; // z. B. "lorem-ipsum"
    $jsonPath = realpath(__DIR__ . "/../../userdata/content/essays/$slug/data.json");

    if ($jsonPath && file_exists($jsonPath)) {
        $json = json_decode(file_get_contents($jsonPath), true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $parsedown = new Parsedown();
            $json['slug'] = $slug;
            $json['content_html'] = $parsedown->text($json['content'] ?? '');

            $data['post'] = $json;
            $data['title'] = $json['title'] ?? ucfirst($slug);

            echo $twig->render('post.twig', $data);
            exit;
        }
    }

    // Fehler: Datei nicht vorhanden oder ungültig
    http_response_code(404);
    echo $twig->render('404.twig', $data);
    exit;
}


