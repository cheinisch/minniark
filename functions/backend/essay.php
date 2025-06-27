<?php

    require_once(__DIR__ . "/../../vendor/autoload.php");

    use Symfony\Component\Yaml\Yaml;

    function saveNewEssay(
    string $title,
    string $content,
    array $tags = [],
    bool $isPublished = false,
    string $cover = ''
): string {
    $baseDir = __DIR__ . '/../../userdata/content/essay/';
    $baseSlug = generateSlug($title);
    $slug = $baseSlug;
    $i = 1;

    // Slug eindeutig machen
    while (is_dir($baseDir . $slug)) {
        $slug = $baseSlug . '-' . $i;
        $i++;
    }

    $essayDir = $baseDir . $slug . '/';
    mkdir($essayDir, 0755, true);

    $yamlPath = $essayDir . $slug . '.yml';
    $mdPath = $essayDir . $slug . '.md';
    $now = date('Y-m-d');

    // Basisdaten
    $data = [
        'title' => $title,
        'slug' => $slug,
        'created_at' => $now,
        'updated_at' => $now,
        'published_at' => $isPublished ? $now : null,
        'is_published' => $isPublished,
        'tags' => $tags,
        'cover' => $cover,
    ];

    // Plugin-Felder automatisch einlesen (aus $_POST)
    $pluginDirs = glob(__DIR__ . '/../../plugins/*', GLOB_ONLYDIR);
    foreach ($pluginDirs as $pluginDir) {
        $postJson = $pluginDir . '/post.json';
        if (!file_exists($postJson)) continue;

        $fields = json_decode(file_get_contents($postJson), true)['fields'] ?? [];
        foreach ($fields as $field) {
            $key = $field['key'];
            $type = $field['type'] ?? 'text';

            if ($type === 'toggle') {
                $data[$key] = ($_POST[$key] ?? 'false') === 'true';
            } else {
                $data[$key] = trim($_POST[$key] ?? '');
            }
        }
    }

    // YAML schreiben
    file_put_contents($yamlPath, Yaml::dump(['essay' => $data], 2, 4));
    file_put_contents($mdPath, $content);

    return $slug;
}


    function updateEssay(string $slug, array $data, string $oldSlug): bool
    {


        $baseDir = __DIR__ . '/../../userdata/content/essay/';
        $oldPath = $baseDir . $oldSlug . '/';
        $newPath = $baseDir . $slug . '/';

        if ($slug !== $oldSlug) {
            if (is_dir($oldPath)) {
                rename($oldPath, $newPath);
            } else {
                mkdir($newPath, 0755, true);
            }

            @unlink($newPath . $oldSlug . '.yml');
            @unlink($newPath . $oldSlug . '.md');
        } elseif (!is_dir($newPath)) {
            mkdir($newPath, 0755, true);
        }

        $yamlPath = $newPath . $slug . '.yml';
        $mdPath = $newPath . $slug . '.md';

        $existingYaml = file_exists($yamlPath)
            ? Yaml::parseFile($yamlPath)
            : [];

        $essay = $existingYaml['essay'] ?? [];

        // Markdown speichern
        $mdOK = true;
        if (isset($data['content'])) {
            $mdOK = file_put_contents($mdPath, $data['content']) !== false;
        }

        // ✨ Zusammenführen aller übergebenen Felder inkl. Plugins
        unset($data['content']); 
        $essay = array_merge($essay, $data);

        // ✨ Pflichtfelder überschreiben
        $essay['slug'] = $slug;
        $essay['updated_at'] = date('Y-m-d');
        $essay['created_at'] = $essay['created_at'] ?? date('Y-m-d');

        // YAML speichern
        $yamlOK = file_put_contents($yamlPath, Yaml::dump(['essay' => $essay], 2, 4)) !== false;

        

        return $yamlOK && $mdOK;
    }





    function removeEssay(string $slug): bool
    {
        $essayDir = __DIR__ . '/../../userdata/content/essay/';
        $yamlPath = $essayDir . $slug . '.yml';
        $mdPath = $essayDir . $slug . '.md';

        $success = true;

        if (file_exists($yamlPath)) {
            $success &= unlink($yamlPath);
        }

        if (file_exists($mdPath)) {
            $success &= unlink($mdPath);
        }

        return $success;
    }


    function getEssayData(string $slug): array
    {
        $essayDir = __DIR__ . '/../../userdata/content/essay/' . $slug . '/';
        $yamlPath = $essayDir . $slug . '.yml';
        $mdPath = $essayDir . $slug . '.md';

        if (!file_exists($yamlPath)) {
            error_log("Essay nicht gefunden: $slug");
            return [];
        }

        // YAML laden
        $yaml = Yaml::parseFile($yamlPath);
        $data = $yaml['essay'] ?? [];

        // Markdown laden
        $data['content'] = file_exists($mdPath) ? file_get_contents($mdPath) : '';

        // Slug ergänzen zur Sicherheit
        $data['slug'] = $slug;

        // Standardwerte für fehlende Felder
        $defaults = [
            'title' => '',
            'created_at' => '',
            'updated_at' => '',
            'published_at' => '',
            'tags' => [],
            'cover' => '',
            'is_published' => false,
            'content' => '',
        ];

        return array_merge($defaults, $data);
    }



    function count_posts()
    {
        $postDir = __DIR__ . '/../../userdata/content/essay/';

        $folderCount = count_subfolders($postDir);

        error_log("Anzahl erkannter Ordner: " . $folderCount); // Zum Testen

        return $folderCount;
    }


    function count_subfolders($postDir)
    {
        if (!is_dir($postDir)) {
            error_log("Verzeichnis nicht gefunden: $postDir");
            return 0;
        }
    
        $items = scandir($postDir);
    
        $folders = array_filter($items, function ($item) use ($postDir) {
            if (!is_string($item) || trim($item) === '.' || trim($item) === '..') {
                error_log("Übersprungen: >" . var_export($item, true) . "<");
                return false; // nur OK im Filter-Callback
            }
        
            $path = rtrim($postDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $item;
        
            $isDir = is_dir($path);
            error_log("Pfad geprüft: $path → " . ($isDir ? 'Ordner' : 'kein Ordner'));
        
            return $isDir;
        });
    
        return count($folders);
    }

    function get_postyearlist(bool $mobile): void
{
    $baseDir = realpath(__DIR__ . '/../../userdata/content/essay/');
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
        $essay = $yaml['essay'] ?? [];

        $createdAt = $essay['created_at'] ?? null;
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

    
    function get_posts($year = null, $tag = null): array
    {
        error_log("Starte Funktion");
        $baseDir = realpath(__DIR__ . '/../../userdata/content/essay/');
        $posts = [];

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
            $data = $yaml['essay'] ?? [];

            if (!isset($data['title'], $data['created_at'])) {
                error_log("Ungültige Struktur in: $yamlPath");
                continue;
            }

            $postYear = substr($data['created_at'], 0, 4);

            // Tags filtern
            $hasTag = true;
            if ($tag !== null) {
                $hasTag = false;
                foreach (($data['tags'] ?? []) as $t) {
                    if (strtolower(trim($t)) === strtolower($tag)) {
                        $hasTag = true;
                        break;
                    }
                }
            }

            if (($year !== null && $postYear !== $year) || !$hasTag) {
                continue;
            }

            $content = file_exists($mdPath) ? file_get_contents($mdPath) : '';
            $excerpt = mb_substr(strip_tags($content), 0, 500) . '...';

            $data['slug'] = $folder;
            $data['content'] = $excerpt;
            $posts[] = $data;
        }

        usort($posts, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));
        error_log("Ende Funktion");
        return $posts;
    }



    function get_posttaglist(bool $mobile): void
{
    $baseDir = realpath(__DIR__ . '/../../userdata/content/essay/');
    $tagCounts = [];
    $debug = false;

    if (!$baseDir || !is_dir($baseDir)) {
        error_log("Verzeichnis nicht gefunden: $baseDir");
        return;
    }

    $folders = scandir($baseDir);

    foreach ($folders as $folder) {
        if ($folder === '.' || $folder === '..') continue;

        $folderPath = $baseDir . DIRECTORY_SEPARATOR . $folder;
        if (!is_dir($folderPath)) continue;

        $yamlPath = $folderPath . DIRECTORY_SEPARATOR . $folder . '.yml';
        if (!file_exists($yamlPath)) continue;

        $yaml = Yaml::parseFile($yamlPath);
        $essay = $yaml['essay'] ?? [];

        if (!isset($essay['tags']) || !is_array($essay['tags'])) continue;

        foreach ($essay['tags'] as $tag) {
            $tag = strtolower(trim($tag));
            if ($tag === '') continue;

            if (!isset($tagCounts[$tag])) {
                $tagCounts[$tag] = 0;
            }
            $tagCounts[$tag]++;
        }
    }

    ksort($tagCounts);

    if ($debug) {
        error_log("Tag-Zusammenfassung: " . print_r($tagCounts, true));
    }

    foreach ($tagCounts as $tag => $count) {
        $safeTag = urlencode($tag);
        if ($mobile) {
            echo "<div class=\"pl-5\">
                <a href=\"blog.php?tag=$safeTag\" class=\"block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6\">#$tag ($count)</a>
            </div>\n";
        } else {
            echo "<li><a href=\"blog.php?tag=$safeTag\" class=\"text-gray-400 hover:text-sky-400\">#$tag ($count)</a></li>\n";
        }
    }
}