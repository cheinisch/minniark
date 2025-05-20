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
            'description' => $album['description'] ?? null,
            'password' => $album['password'] ?? null,
            'images' => $album['images'] ?? [],
            'headImage' => $album['headImage'] ?? null,
            'content' => $markdown,
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


    function updateAlbum(string $slug, array $data): bool
    {
        $albumDir = __DIR__ . '/../../userdata/content/album/';
        $albumPath = $albumDir . $slug . '.yml';
        $albumMDPath = $albumDir . $slug . '.md';

        if (!file_exists($albumPath)) {
            error_log("Kann Album nicht aktualisieren – Datei nicht gefunden: $slug");
            return false;
        }

        // Nur bekannte Felder übernehmen
        $yamlData = [
            'album' => [
                'name' => $data['name'] ?? '',
                'description' => $data['description'] ?? '',
                'password' => $data['password'] ?? '',
                'images' => $data['images'] ?? [],
                'headImage' => $data['headImage'] ?? '',
            ]
        ];

        // YAML & Markdown speichern
        $yamlOK = file_put_contents($albumPath, Yaml::dump($yamlData, 2, 4)) !== false;
        $mdOK = file_put_contents($albumMDPath, $data['content'] ?? '') !== false;

        return $yamlOK && $mdOK;
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