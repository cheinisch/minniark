<?php

    require_once(__DIR__ . "/../../vendor/autoload.php");

    use Symfony\Component\Yaml\Yaml;

    function getCollectionList(): array
    {        

        $collectionDir = __DIR__ . '/../../userdata/content/collection/';
        $list = [];

        if (!is_dir($collectionDir)) {
            return $list; // Verzeichnis existiert nicht – leere Liste zurückgeben
        }

        $files = glob($collectionDir . '*.yml');

        foreach ($files as $filePath) {
            $slug = basename($filePath, '.yml');

            try {
                $data = Yaml::parseFile($filePath);
                $title = $data['title'] ?? 'Empty title';
            } catch (\Exception $e) {
                $title = '(Fehler beim Parsen)';
            }

            $list[] = [
                'slug' => $slug,
                'title' => $title,
            ];
        }

        return $list;
    }

    function getCollectionData($slug):array
    {

        $collectionDir = __DIR__ . '/../../userdata/content/collection/';
        $file = $slug.'.yml';

        if (!file_exists($filePath)) {
            return []; // Datei nicht gefunden
        }

        try {
            $data = Yaml::parseFile($filePath);
            return is_array($data) ? $data : [];
        } catch (\Exception $e) {
            // Optional: Fehler loggen
            return [];
        }

    }