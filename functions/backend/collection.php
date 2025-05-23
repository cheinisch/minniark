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

    function getCollectionData($slug): array
    {
        $collectionDir = __DIR__ . '/../../userdata/content/collection/';
        $ymlPath = $collectionDir . $slug . '.yml';
        $mdPath  = $collectionDir . $slug . '.md';

        if (!file_exists($ymlPath)) {
            return []; // YAML-Datei nicht vorhanden
        }

        $data = Yaml::parseFile($ymlPath);
        $collection = $data['collection'] ?? [];

        // Beschreibung aus .md-Datei lesen, wenn vorhanden
        $description = '';
        if (file_exists($mdPath)) {
            $description = trim(file_get_contents($mdPath));
        }

        // In den Collection-Daten einfügen
        $collection['description'] = $description;

        return $collection;
    }


    function saveNewCollection($title, $description)
    {
        $collectionDir = __DIR__ . '/../../userdata/content/collection/';

        // Verzeichnis sicherstellen
        if (!is_dir($collectionDir)) {
            if (!mkdir($collectionDir, 0755, true)) {
                die("Konnte das Verzeichnis nicht erstellen: $collectionDir");
            }
        }

        $slug = generateSlug($title);
        $filename = $slug . '.yml';
        $fullYmlPath = $collectionDir . $filename;
        $fullMdPath  = $collectionDir . $slug . '.md';

        $data = [
            'collection' => [
                'name'   => $title,
                'albums' => [],
                'image'  => '',
            ]
        ];

        // YAML speichern
        $yaml = Yaml::dump($data, 2, 4);
        $ymlSaved = file_put_contents($fullYmlPath, $yaml);

        // Markdown-Datei mit Beschreibung speichern
        $mdSaved = file_put_contents($fullMdPath, trim($description));

        return $ymlSaved !== false && $mdSaved !== false;
    }


    function updateCollection($slug, $data, $oldslug) {
        $collectionDir = __DIR__ . '/../../userdata/content/collection/';
        $ymlOldPath = $collectionDir . $oldslug . '.yml';
        $ymlNewPath = $collectionDir . $slug . '.yml';

        $mdOldPath = $collectionDir . $oldslug . '.md';
        $mdNewPath = $collectionDir . $slug . '.md';

        // Optional: alte YAML laden (nur wenn existiert)
        $existing = [];
        if (file_exists($ymlOldPath)) {
            try {
                $yaml = Symfony\Component\Yaml\Yaml::parseFile($ymlOldPath);
                $existing = $yaml['collection'] ?? [];
            } catch (Exception $e) {
                error_log("YAML parse error: " . $e->getMessage());
            }
        }

        // Description aus dem POST trennen
        $description = $data['collection']['description'] ?? null;
        unset($data['collection']['description']);

        // Daten zusammenführen
        $merged = array_merge($existing, $data['collection']);
        $yamlData = ['collection' => $merged];
        $yamlString = Symfony\Component\Yaml\Yaml::dump($yamlData, 2, 4);

        // Falls Slug geändert wurde → Dateien umbenennen
        if ($slug !== $oldslug) {
            if (file_exists($ymlOldPath)) {
                rename($ymlOldPath, $ymlNewPath);
            }

            if (file_exists($mdOldPath)) {
                rename($mdOldPath, $mdNewPath);
            }
        }

        // YAML speichern (neu oder überschreiben)
        $ymlSaved = file_put_contents($ymlNewPath, $yamlString) !== false;

        // Beschreibung speichern oder beibehalten
        $mdSaved = true;
        if ($description !== null) {
            $mdSaved = file_put_contents($mdNewPath, trim($description)) !== false;
        }

        return $ymlSaved && $mdSaved;
    }



    function removeCollection($slug)
    {
        $collectionDir = __DIR__.'/../../userdata/content/collection/';

        $filename = $slug.".yml";
        $description = $slug.".md";

        $filePath = $collectionDir.$filename;
        $descriptionPath = $collectionDir.$description;

        $result = unlink($filePath);
        $result = unlink($descriptionPath);

        return $result;
    }

    function renderImageGalleryCollection(string $collectionSlug): void
{
    $collection = getCollectionData($collectionSlug);
    $albumSlugs = $collection['albums'] ?? [];

    $albumDir = __DIR__ . '/../../userdata/content/album/';
    $imageDir = __DIR__ . '/../../userdata/content/images/';
    $cacheDir = '/cache/images/';

    foreach ($albumSlugs as $slug) {
        $albumYml = $albumDir . $slug . '.yml';
        if (!file_exists($albumYml)) {
            continue;
        }

        try {
            $albumData = Yaml::parseFile($albumYml);
            $album = $albumData['album'] ?? [];
            $headImage = $album['headImage'] ?? null;
            $title = htmlspecialchars($album['name'] ?? $slug);
            $description = htmlspecialchars($album['description'] ?? '');

            $cachedImage = '';
            $fileName = $headImage;

            if ($headImage) {
                $imageSlug = pathinfo($headImage, PATHINFO_FILENAME);
                $jsonPath = $imageDir . $imageSlug . '.json';

                if (file_exists($jsonPath)) {
                    $meta = json_decode(file_get_contents($jsonPath), true);
                    if (!empty($meta['guid'])) {
                        $guid = $meta['guid'];
                        $cachedImage = $cacheDir . $guid . '_M.jpg';
                        $fileName = basename($headImage);
                    }
                }
            }

            if ($cachedImage) {
                echo "
                <div>
                    <div class=\"w-full aspect-video overflow-hidden border border-gray-300 hover:border-sky-400 rounded-xs dynamic-image-width transition-[max-width] duration-300 ease-in-out max-w-full md:max-w-none\" style=\"--img-max-width: 250px; max-width: var(--img-max-width);\">
                        <a href=\"media-detail.php?image=" . urlencode($fileName) . "\">
                            <img src='$cachedImage' class=\"w-full h-full object-cover\" alt=\"$title\" data-filename=\"$fileName\" title=\"$description\" draggable=\"true\"/>
                        </a>
                    </div>
                    <div class=\"w-full flex justify-between items-center text-sm pt-1 dark:text-gray-600\">
                        <span class=\"text-sm dark:text-gray-600\">$title</span>
                        <div class=\"relative inline-block\">
                            <button id=\"$fileName\" class=\"p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-400\" data-filename=\"$fileName\">
                                <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"3\" stroke=\"currentColor\" class=\"w-5 h-5\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M6 12h.01M12 12h.01M18 12h.01\" />
                                </svg>
                            </button>                    
                            <div class=\"dropdown hidden absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none\">
                                <a href=\"#\" class=\"block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 assign-to-album-btn\" data-filename=\"$fileName\">Add to Album</a>
                                <a href=\"backend_api/delete.php?type=img&filename=$fileName\" class=\"confirm-link block px-4 py-2 text-sm text-red-600 hover:bg-red-100\">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>";
            }
        } catch (\Exception $e) {
            error_log("Fehler beim Laden von Album {$slug}: " . $e->getMessage());
        }
    }
}

