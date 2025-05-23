<?php

    require_once(__DIR__ . "/../../vendor/autoload.php");

    use Symfony\Component\Yaml\Yaml;

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
                $image = $data['album']['headImage'] ?? '';
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


    function getAlbumData(string $slug): array
    {
        // Verzeichnisse & Dateinamen
        $albumDir = __DIR__ . '/../../userdata/content/album/';
        $albumPath = $albumDir . $slug . '.yml';
        $albumMDPath = $albumDir . $slug . '.md';

        // Prüfen, ob die YAML-Datei existiert
        if (!file_exists($albumPath)) {
            error_log("Album not found: " . $slug);
            return []; // oder: throw new \Exception("Album not found");
        }

        // YAML-Daten einlesen
        try {
            $yamlData = Yaml::parseFile($albumPath);
            $album = $yamlData['album'] ?? [];
        } catch (\Exception $e) {
            error_log("Fehler beim Parsen von $albumPath: " . $e->getMessage());
            return [];
        }

        // Markdown-Text einlesen (falls vorhanden)
        $markdown = file_exists($albumMDPath) ? file_get_contents($albumMDPath) : '';

        // Rückgabe strukturieren
        return [
            'slug' => $slug,
            'name' => $album['name'] ?? null,
            'description' => $markdown,
            'password' => $album['password'] ?? null,
            'images' => $album['images'] ?? [],
            'headImage' => $album['headImage'] ?? null,
        ];
    }


    function saveNewAlbum(string $title): string
    {
        $slug = generateSlug($title);
        $albumDir = __DIR__ . '/../../userdata/content/album/';
        
        // Verzeichnisse anlegen, falls nötig
        if (!is_dir($albumDir)) {
            mkdir($albumDir, 0755, true);
        }

        $albumPath = $albumDir . $slug . '.yml';
        $albumMDPath = $albumDir . $slug . '.md';

        // YAML-Daten vorbereiten
        $data = [
            'album' => [
                'name' => $title,
                'description' => '',
                'password' => '',
                'images' => [],
                'headImage' => '',
            ]
        ];

        // Speichern
        file_put_contents($albumPath, Yaml::dump($data, 2, 4));
        file_put_contents($albumMDPath, '');

        return $slug;
    }

    // Update Album Data
    function updateAlbum(string $slug, array $data, string $oldSlug): bool
    {
        $albumDir = __DIR__ . '/../../userdata/content/album/';
        $albumPath = $albumDir . $slug . '.yml';
        $albumMDPath = $albumDir . $slug . '.md';

        // Alte YAML laden, falls vorhanden
        $existing = file_exists($albumPath) ? Yaml::parseFile($albumPath) : [];
        $album = $existing['album'] ?? [];

        // Nur vorhandene Daten überschreiben
        if (isset($data['name']) || isset($data['album-title-edit'])) {
            $album['name'] = $data['name'] ?? $data['album-title-edit'];
        }

        if (isset($data['password'])) {
            $album['password'] = $data['password'];
        }

        if (isset($data['images'])) {
            $album['images'] = $data['images'];
        }

        if (isset($data['headImage'])) {
            $album['headImage'] = $data['headImage'];
        }

        // Markdown-Content (aus "description")
        $useExistingMarkdown = !isset($data['album-description']);
        $markdown = $useExistingMarkdown
            ? (file_exists($albumMDPath) ? file_get_contents($albumMDPath) : '')
            : $data['album-description'];


        // Slug geändert → alte Dateien löschen
        if ($slug !== $oldSlug) {
            $oldYamlPath = $albumDir . $oldSlug . '.yml';
            $oldMDPath = $albumDir . $oldSlug . '.md';
            if (file_exists($oldYamlPath)) unlink($oldYamlPath);
            if (file_exists($oldMDPath)) unlink($oldMDPath);
        }

        // Speichern
        $yamlOK = file_put_contents($albumPath, Yaml::dump(['album' => $album], 2, 4)) !== false;
        $mdOK = $useExistingMarkdown || file_put_contents($albumMDPath, $markdown) !== false;

        return $yamlOK && $mdOK;
    }


    function remove_img_from_album(string $filename, string $albumname): bool
    {
        $slug = $albumname;
        $albumDir = __DIR__ . '/../../userdata/content/album/';
        $albumPath = $albumDir . $slug . '.yml';

        if (!file_exists($albumPath)) {
            error_log("Album '$slug' nicht gefunden.");
            return false;
        }

        // YAML laden
        $yamlData = Yaml::parseFile($albumPath);
        $album = $yamlData['album'] ?? [];

        // Bildliste prüfen
        if (!isset($album['images']) || !is_array($album['images'])) {
            error_log("Keine gültige Bildliste im Album '$slug'.");
            return false;
        }

        // Bild entfernen
        $newImages = array_filter($album['images'], fn($img) => $img !== $filename);

        // Änderungen speichern
        return updateAlbum($slug, ['images' => array_values($newImages)], $slug);
    }



    function removeAlbum($slug)
    {
        $albumnDir = __DIR__.'/../../userdata/content/album/';

        $filename = $slug.".yml";
        $filenameMD = $slug.".md";

        $filePath = $albumDir.$filename;

        $result = unlink($filePath);

        if($result)
        {
            $filePathMD = $albumDir.$filenameMD;

            unlink($filePathMD);
        }

        return $result;
    }