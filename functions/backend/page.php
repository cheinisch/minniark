<?php

    require_once(__DIR__ . "/../../vendor/autoload.php");

    use Symfony\Component\Yaml\Yaml;

    function saveNewPage(
        string $title,
        string $content,
        bool $isPublished = false,
        string $cover = ''
    ): string {
        $baseDir = __DIR__ . '/../../userdata/content/page/';
        $baseSlug = generateSlug($title);
        $slug = $baseSlug;
        $i = 1;

        // Slug eindeutig machen
        while (is_dir($baseDir . $slug)) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }

        $pageDir = $baseDir . $slug . '/';
        mkdir($pageDir, 0755, true);

        $yamlPath = $pageDir . $slug . '.yml';
        $mdPath = $pageDir . $slug . '.md';
        $now = date('Y-m-d');

        $data = [
            'page' => [
                'title' => $title,
                'slug' => $slug,
                'created_at' => $now,
                'is_published' => $isPublished,
                'cover' => $cover,
            ]
        ];

        file_put_contents($yamlPath, Yaml::dump($data, 2, 4));
        file_put_contents($mdPath, $content);

        return $slug;
    }

    function updatepage(string $slug, array $data, string $oldSlug): bool
    {
        $baseDir = __DIR__ . '/../../userdata/content/page/';
        $oldPath = $baseDir . $oldSlug . '/';
        $newPath = $baseDir . $slug . '/';

        // Slug-Wechsel → Ordner umbenennen
        if ($slug !== $oldSlug) {
            if (is_dir($oldPath)) {
                rename($oldPath, $newPath);
            } else {
                mkdir($newPath, 0755, true);
            }

            // Alte YAML/MD im neuen Ordner löschen, falls durch rename übernommen
            @unlink($newPath . $oldSlug . '.yml');
            @unlink($newPath . $oldSlug . '.md');
        } elseif (!is_dir($newPath)) {
            mkdir($newPath, 0755, true);
        }

        $yamlPath = $newPath . $slug . '.yml';
        $mdPath = $newPath . $slug . '.md';

        // Bestehende YAML laden (egal ob neuer oder alter Slug)
        $existingYaml = file_exists($yamlPath)
            ? Yaml::parseFile($yamlPath)
            : (file_exists($oldPath . $oldSlug . '.yml')
                ? Yaml::parseFile($oldPath . $oldSlug . '.yml')
                : []);

        $page = $existingYaml['page'] ?? [];

        // ✨ created_at erhalten oder setzen
        $page['created_at'] = $page['created_at'] ?? date('Y-m-d');

        // Felder aktualisieren
        $page['title'] = $data['title'] ?? $page['title'] ?? '';
        $page['cover'] = $data['cover'] ?? $page['cover'] ?? '';
        $page['slug'] = $slug;
        $page['is_published'] = $data['is_published'];

        // YAML speichern
        $yamlOK = file_put_contents($yamlPath, Yaml::dump(['page' => $page], 2, 4)) !== false;

        // Markdown speichern
        $mdOK = true;
        if (isset($data['content'])) {
            $mdOK = file_put_contents($mdPath, $data['content']) !== false;
        }

        if (!$yamlOK || !$mdOK) {
            error_log("updatepage fehlgeschlagen – YAML OK: " . ($yamlOK ? 'ja' : 'nein') . ", MD OK: " . ($mdOK ? 'ja' : 'nein'));
        }

        return $yamlOK && $mdOK;
    }



    function removepage(string $slug): bool
    {
        $pageDir = __DIR__ . '/../../userdata/content/page/';
        $yamlPath = $pageDir . $slug . '.yml';
        $mdPath = $pageDir . $slug . '.md';

        $success = true;

        if (file_exists($yamlPath)) {
            $success &= unlink($yamlPath);
        }

        if (file_exists($mdPath)) {
            $success &= unlink($mdPath);
        }

        return $success;
    }


    function getpageData(string $slug): array
    {
        $pageDir = __DIR__ . '/../../userdata/content/page/' . $slug . '/';
        $yamlPath = $pageDir . $slug . '.yml';
        $mdPath = $pageDir . $slug . '.md';

        if (!file_exists($yamlPath)) {
            error_log("page nicht gefunden: $slug");
            return [];
        }

        // YAML laden
        $yaml = Yaml::parseFile($yamlPath);
        $data = $yaml['page'] ?? [];

        // Markdown laden
        $data['content'] = file_exists($mdPath) ? file_get_contents($mdPath) : '';

        // Slug ergänzen zur Sicherheit
        $data['slug'] = $slug;

        // Standardwerte für fehlende Felder
        $defaults = [
            'title' => '',
            'created_at' => '',
            'cover' => '',
            'is_published' => false,
            'content' => '',
        ];

        return array_merge($defaults, $data);
    }



    function count_pages()
    {
        $pageDir = __DIR__ . '/../../userdata/content/page/';

        $folderCount = count_subfolders($pageDir);

        error_log("Anzahl erkannter Ordner: " . $folderCount); // Zum Testen

        return $folderCount;
    }


    function get_pageyearlist(bool $mobile): void
    {
        $baseDir = realpath(__DIR__ . '/../../userdata/content/page/');
        $yearCounts = [];
        $debug = false; // Debugging aktivieren bei Bedarf

        if (!$baseDir || !is_dir($baseDir)) {
            error_log("Verzeichnis nicht gefunden: $baseDir");
            return;
        }

        $folders = scandir($baseDir);
        if ($debug) error_log("Inhalt von $baseDir: " . print_r($folders, true));

        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $folderPath = $baseDir . DIRECTORY_SEPARATOR . $folder;
            if (!is_dir($folderPath)) continue;

            $yamlFile = $folderPath . DIRECTORY_SEPARATOR . $folder . '.yml';
            if (!file_exists($yamlFile)) {
                if ($debug) error_log("YAML fehlt: $yamlFile");
                continue;
            }

            $yaml = Yaml::parseFile($yamlFile);
            $page = $yaml['page'] ?? [];

            $createdAt = $page['created_at'] ?? null;
            if (!$createdAt || strlen($createdAt) < 4) {
                if ($debug) error_log("created_at fehlt oder ungültig: $yamlFile");
                continue;
            }

            $year = substr($createdAt, 0, 4);
            if (!ctype_digit($year)) continue;

            if (!isset($yearCounts[$year])) {
                $yearCounts[$year] = 0;
            }

            $yearCounts[$year]++;
        }

        ksort($yearCounts);

        foreach ($yearCounts as $year => $count) {
            if ($mobile) {
                echo "<div class=\"pl-5\">
                    <a href=\"blog.php?year=$year\" class=\"block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6\">$year ($count)</a>
                </div>\n";
            } else {
                echo "<li><a href=\"blog.php?year=$year\" class=\"text-gray-400 hover:text-sky-400\">$year ($count)</a></li>\n";
            }
        }

        if ($debug) error_log("Finale Jahresverteilung: " . print_r($yearCounts, true));
    }

        
    function get_pages($year = null): array
    {
        error_log("Starte Funktion");
        $baseDir = realpath(__DIR__ . '/../../userdata/content/page/');
        $pages = [];

        if (!$baseDir || !is_dir($baseDir)) {
            error_log("Verzeichnis nicht gefunden: $baseDir");
            return [];
        }

        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $folderPath = $baseDir . DIRECTORY_SEPARATOR . $folder;
            if (!is_dir($folderPath)) continue;

            $yamlPath = $folderPath . DIRECTORY_SEPARATOR . $folder . '.yml';
            $mdPath = $folderPath . DIRECTORY_SEPARATOR . $folder . '.md';

            if (!file_exists($yamlPath)) {
                error_log("Fehlende YAML: $yamlPath");
                continue;
            }

            $yaml = Yaml::parseFile($yamlPath);
            $data = $yaml['page'] ?? [];

            if (!isset($data['title'], $data['created_at'])) {
                error_log("Ungültige Struktur in: $yamlPath");
                continue;
            }

            $pageYear = substr($data['created_at'], 0, 4);


            if (($year !== null && $pageYear !== $year)) {
                continue;
            }

            $content = file_exists($mdPath) ? file_get_contents($mdPath) : '';
            $excerpt = mb_substr(strip_tags($content), 0, 500) . '...';

            $data['slug'] = $folder;
            $data['content'] = $excerpt;
            $pages[] = $data;
        }

        usort($pages, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));
        error_log("Ende Funktion");
        return $pages;
    }