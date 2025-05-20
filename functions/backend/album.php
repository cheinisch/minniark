<?php

    function getAlbumList(): array
    {
        $albumDir = __DIR__ . '/../../userdata/content/album/';
        $list = [];

        if (!is_dir($albumDir)) {
            return $list; // Verzeichnis existiert nicht – leere Liste zurückgeben
        }

        $files = glob($albumDir . '*.yml');

        foreach ($files as $filePath) {
            $slug = basename($filePath, '.yml');

            try {
                $data = Yaml::parseFile($filePath);
                $title = $data['album']['name'] ?? 'Empty title';
                $image = $data['album']['image'] ?? '';
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