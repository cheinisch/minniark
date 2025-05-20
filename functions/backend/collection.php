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
                $title = $data['collection']['name'] ?? 'Empty title';
                $image = $data['collection']['image'] ?? '';
            } catch (\Exception $e) {
                $title = '(Fehler beim Parsen)';
            }

            $list[] = [
                'slug' => $slug,
                'title' => $title,
                'image' => $image,
            ];
        }

        return $list;
    }

    function getCollectionData($slug):array
    {

        $collectionDir = __DIR__ . '/../../userdata/content/collection/';
        $file = $slug.'.yml';
        $filePath = $collectionDir.$file;

        if (!file_exists($filePath)) {
            return []; // Datei nicht gefunden
        }

        
        $data = Yaml::parseFile($filePath);

        // Zugriff auf die einzelnen Werte
        $collection = $data['collection'] ?? [];

        $name = $collection['name'] ?? '';
        $description = $collection['description'] ?? '';
        $albums = $collection['albums'] ?? [];
        $image = $collection['image'] ?? '';

        return $collection;

    }

    function saveNewCollection($title, $description)
    {

        $collectionDir = __DIR__.'/../../userdata/content/collection/';

        if (!is_dir($collectionDir)) {
            // Versuche, das Verzeichnis anzulegen (inkl. Unterverzeichnisse)
            if (!mkdir($collectionDir, 0755, true)) {
                die("Konnte das Verzeichnis nicht erstellen: $collectionDir");
            }
        }

        $slug = generateSlug($title);

        $filename = generateSlug($title).'.yml';

        $fullPath = $collectionDir.''.$filename;

        echo("Full Path: ".$collectionDir.''.$filename);


        $data = [
            'collection' => [
                'name' => $title,
                'description' => $description,
                'albums' => [],
                'image' => '',
            ]
        ];

        $yaml = Yaml::dump($data, 2, 4); // 2 = Tiefe, 4 = Einrückung
        $result = file_put_contents($fullPath, $yaml);

        return $result;
    }


    function removeCollection($slug)
    {
        $collectionDir = __DIR__.'/../../userdata/content/collection/';

        $filename = $slug.".yml";

        $filePath = $collectionDir.$filename;

        $result = unlink($filePath);

        return $result;
    }