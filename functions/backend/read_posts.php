<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    function count_posts()
    {
        $postDir = __DIR__ . '/../../userdata/content/essays/';

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

    function get_postyearlist($mobile)
    {
        $baseDir = __DIR__ . '/../../userdata/content/essays/';
        $yearCounts = [];
        $debug = true; // Logs ein-/ausschalten

        if (!is_dir($baseDir)) {
            error_log("Verzeichnis nicht gefunden: $baseDir");
            return;
        }

        $folders = scandir($baseDir);
        if ($debug) error_log("Inhalt von $baseDir: " . print_r($folders, true));

        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..') {
                continue;
            }

            $folderPath = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $folder;

            // Nur wenn es ein Verzeichnis ist
            if (!is_dir($folderPath)) {
                if ($debug) error_log("Übersprungen (kein Ordner): $folderPath");
                continue;
            }

            if ($debug) error_log("Durchsuche Ordner: $folderPath");

            $jsonFiles = glob($folderPath . DIRECTORY_SEPARATOR . '*.json');
            if ($debug) error_log("JSON-Dateien in $folderPath: " . print_r($jsonFiles, true));

            foreach ($jsonFiles as $filePath) {
                if (!file_exists($filePath)) {
                    if ($debug) error_log("Datei nicht gefunden: $filePath");
                    continue;
                }

                if ($debug) error_log("Lese Datei: $filePath");
                $jsonContent = file_get_contents($filePath);

                if (!$jsonContent) {
                    if ($debug) error_log("Datei leer oder nicht lesbar: $filePath");
                    continue;
                }

                $data = json_decode($jsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("JSON-Fehler in $filePath: " . json_last_error_msg());
                    continue;
                }

                if (!isset($data['created_at'])) {
                    if ($debug) error_log("'created_at' fehlt in $filePath");
                    continue;
                }

                $year = substr($data['created_at'], 0, 4);

                if (!empty($year) && ctype_digit($year)) {
                    if ($debug) error_log("Jahr extrahiert: $year aus $filePath");

                    if (!isset($yearCounts[$year])) {
                        $yearCounts[$year] = 0;
                    }
                    $yearCounts[$year]++;
                } else {
                    if ($debug) error_log("Ungültiges Jahr in $filePath → '$year'");
                }
            }
        }

        ksort($yearCounts);
        if ($debug) error_log("Finales Jahr-Ergebnis: " . print_r($yearCounts, true));

        foreach ($yearCounts as $year => $count) {
            if ($mobile) {
                echo "<div class=\"pl-5\">
                    <a href=\"blog.php?year=$year\" class=\"block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6\">$year ($count)</a>
                </div>\n";
            } else {
                echo "<li><a href=\"blog.php?year=$year\" class=\"text-gray-400 hover:text-sky-400\">$year ($count)</a></li>\n";
            }
        }
    }

    function get_posts($year = null, $tag = null)
    {
        $baseDir = __DIR__ . '/../../userdata/content/essays/';
        $posts = [];
        $debug = false;

        if (!is_dir($baseDir)) {
            error_log("Verzeichnis nicht gefunden: $baseDir");
            return [];
        }

        $folders = scandir($baseDir);

        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $folderPath = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $folder;
            if (!is_dir($folderPath)) continue;

            $jsonFiles = glob($folderPath . DIRECTORY_SEPARATOR . '*.json');

            foreach ($jsonFiles as $filePath) {
                if (!file_exists($filePath)) continue;

                $jsonContent = file_get_contents($filePath);
                if (!$jsonContent) continue;

                $data = json_decode($jsonContent, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("JSON-Fehler in $filePath: " . json_last_error_msg());
                    continue;
                }

                // Basisprüfung
                if (!isset($data['title'], $data['content'], $data['created_at'])) {
                    continue;
                }

                $postYear = substr($data['created_at'], 0, 4);
                $hasTag = true;

                if ($tag !== null) {
                    $hasTag = false;
                    if (isset($data['tags']) && is_array($data['tags'])) {
                        foreach ($data['tags'] as $t) {
                            if (strtolower(trim($t)) === strtolower($tag)) {
                                $hasTag = true;
                                break;
                            }
                        }
                    }
                }

                if (($year !== null && $postYear !== $year) || !$hasTag) {
                    continue;
                }

                // Kürze den String
                $data['content'] = mb_substr($data['content'], 0, 500) . '...';

                $data['source_path'] = basename(dirname($filePath));
                $posts[] = $data;
            }
        }

        usort($posts, function ($a, $b) {
            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });

        if ($debug) {
            error_log("Gefilterte Posts: " . print_r($posts, true));
        }

        return $posts;
    }


    function get_posttaglist($mobile)
    {
        $baseDir = __DIR__ . '/../../userdata/content/essays/';
        $tagCounts = [];
        $debug = false; // Debug-Ausgaben in error_log

        if (!is_dir($baseDir)) {
            error_log("Verzeichnis nicht gefunden: $baseDir");
            return;
        }

        $folders = scandir($baseDir);

        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $folderPath = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $folder;
            if (!is_dir($folderPath)) continue;

            $jsonFiles = glob($folderPath . DIRECTORY_SEPARATOR . '*.json');
            foreach ($jsonFiles as $filePath) {
                if (!file_exists($filePath)) continue;

                $jsonContent = file_get_contents($filePath);
                if (!$jsonContent) continue;

                $data = json_decode($jsonContent, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("JSON-Fehler in $filePath: " . json_last_error_msg());
                    continue;
                }

                if (isset($data['tags']) && is_array($data['tags'])) {
                    foreach ($data['tags'] as $tag) {
                        $tag = trim(strtolower($tag)); // Normalisieren
                        if ($tag === '') continue;

                        if (!isset($tagCounts[$tag])) {
                            $tagCounts[$tag] = 0;
                        }
                        $tagCounts[$tag]++;
                    }
                }
            }
        }

        ksort($tagCounts);

        if ($debug) error_log("Tag-Zusammenfassung: " . print_r($tagCounts, true));

        // Ausgabe
        foreach ($tagCounts as $tag => $count) {
            $safeTag = urlencode($tag); // für URL-Parameter
            if ($mobile) {
                echo "<div class=\"pl-5\">
                    <a href=\"blog.php?tag=$safeTag\" class=\"block px-4 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 sm:px-6\">#$tag ($count)</a>
                </div>\n";
            } else {
                echo "<li><a href=\"blog.php?tag=$safeTag\" class=\"text-gray-400 hover:text-sky-400\">#$tag ($count)</a></li>\n";
            }
        }
    }


    function read_post($foldername) {
        $baseDir = realpath(__DIR__ . '/../../userdata/content/essays');
    
        // Sicherheitscheck auf erlaubte Zeichen im Ordnernamen
        $folder = preg_replace('/[^a-zA-Z0-9\-_]/', '', $foldername);
        $postDir = $baseDir . DIRECTORY_SEPARATOR . $folder;
        $jsonPath = $postDir . DIRECTORY_SEPARATOR . 'data.json';
    
        if (!is_dir($postDir) || !file_exists($jsonPath)) {
            return null;
        }
    
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        $data['source_path'] = basename(dirname($jsonPath));
        if($data['is_published'])
        {
            $data['is_published'] = "true";
        }else{
            $data['is_published'] = "false";
        }
        
    
        return $data;
    }


