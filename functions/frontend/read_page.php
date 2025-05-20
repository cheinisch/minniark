<?php

use Symfony\Component\Yaml\Yaml;

function getPageList(): array
{
    $baseDir = realpath(__DIR__ . '/../../userdata/content/page/');
    $pages = [];
    $debug = false;

    if (!$baseDir || !is_dir($baseDir)) {
        error_log("Verzeichnis nicht gefunden: $baseDir");
        return [];
    }

    $parsedown = new Parsedown();

    foreach (scandir($baseDir) as $folder) {
        if ($folder === '.' || $folder === '..') continue;

        $folderPath = $baseDir . '/' . $folder;
        if (!is_dir($folderPath)) continue;

        $yamlPath = $folderPath . '/' . $folder . '.yml';
        $mdPath = $folderPath . '/' . $folder . '.md';

        if (!file_exists($yamlPath) || !file_exists($mdPath)) {
            if ($debug) error_log("Fehlende Datei in $folderPath");
            continue;
        }

        $yaml = Yaml::parseFile($yamlPath);
        $page = $yaml['page'] ?? [];

        // Pflichtfelder prüfen
        if (!isset($page['title'])) continue;

        $contentRaw = file_get_contents($mdPath);
        $excerpt = mb_substr(strip_tags($contentRaw), 0, 500) . '...';

        $pages[] = [
            'slug' => $folder,
            'title' => $page['title'],
            'created_at' => $page['created_at'] ?? '1970-01-01',
            'content' => $excerpt,
            'is_published' => $page['is_published'] ?? false,
        ];
    }

    usort($pages, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

    return $pages;
}
