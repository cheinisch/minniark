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

    function removealbumfromcollection($collection, $album): bool
    {
        $collectionDir = __DIR__ . '/../../userdata/content/collection/';
        $ymlPath = $collectionDir . $collection . '.yml';
        if (!file_exists($ymlPath)) {
            return false;
        }

        try {
            $data = Symfony\Component\Yaml\Yaml::parseFile($ymlPath);
            $collectionData = $data['collection'] ?? [];

            if (!isset($collectionData['albums']) || !is_array($collectionData['albums'])) {
                return false;
            }

            // Entferne das Album aus der Liste
            $collectionData['albums'] = array_values(
                array_filter($collectionData['albums'], fn($a) => $a !== $album)
            );

            // Speichere die aktualisierte YAML
            $newData = ['collection' => $collectionData];
            $yaml = Symfony\Component\Yaml\Yaml::dump($newData, 2, 4);
            return file_put_contents($ymlPath, $yaml) !== false;

        } catch (Exception $e) {
            error_log("YAML error in removeAlbumFromCollection: " . $e->getMessage());
            return false;
        }
    }

    function updateCollection(string $newSlug, array $data, string $oldSlug): bool
    {
        $collectionDir = __DIR__ . '/../../userdata/content/collection/';
        $ymlOldPath = $collectionDir . $oldSlug . '.yml';
        $ymlNewPath = $collectionDir . $newSlug . '.yml';

        $mdOldPath = $collectionDir . $oldSlug . '.md';
        $mdNewPath = $collectionDir . $newSlug . '.md';

        // Bestehende Daten laden
        $existing = [];
        if (file_exists($ymlOldPath)) {
            try {
                $yaml = Symfony\Component\Yaml\Yaml::parseFile($ymlOldPath);
                $existing = $yaml['collection'] ?? [];
            } catch (Exception $e) {
                error_log("YAML parse error: " . $e->getMessage());
            }
        }

        // Neue Beschreibung aus dem neuen Datensatz extrahieren
        $newDescription = $data['description'] ?? null;
        unset($data['description']);

        // Vorhandene Alben und Bild übernehmen, wenn im neuen Datensatz nicht enthalten
        if (!isset($data['albums']) && isset($existing['albums'])) {
            $data['albums'] = $existing['albums'];
        }

        if (!isset($data['image']) && isset($existing['image'])) {
            $data['image'] = $existing['image'];
        }

        // Daten zusammenführen
        $merged = array_merge($existing, $data);
        $yamlContent = Symfony\Component\Yaml\Yaml::dump(['collection' => $merged], 2, 4);

        // Umbenennen, falls Slug sich geändert hat
        if ($newSlug !== $oldSlug) {
            if (file_exists($ymlOldPath)) rename($ymlOldPath, $ymlNewPath);
            if (file_exists($mdOldPath)) rename($mdOldPath, $mdNewPath);
        }

        // YAML-Datei speichern
        $ymlSaved = file_put_contents($ymlNewPath, $yamlContent) !== false;

        // Markdown speichern, falls neue Beschreibung vorhanden ist
        $mdSaved = true;
        if ($newDescription !== null) {
            $mdSaved = file_put_contents($mdNewPath, trim($newDescription)) !== false;
        }

        return $ymlSaved && $mdSaved;
    }


    function renameAlbumInCollection(string $oldSlug, string $newSlug): void
    {
        $collectionDir = __DIR__ . '/../../userdata/content/collection/';
        $collectionFiles = glob($collectionDir . '*.yml');

        foreach ($collectionFiles as $filePath) {
            try {
                $yaml = Symfony\Component\Yaml\Yaml::parseFile($filePath);
                $collection = $yaml['collection'] ?? [];

                // Prüfen, ob das Album existiert
                if (!isset($collection['albums']) || !is_array($collection['albums'])) {
                    continue;
                }

                $updated = false;
                foreach ($collection['albums'] as &$albumSlug) {
                    if ($albumSlug === $oldSlug) {
                        $albumSlug = $newSlug;
                        $updated = true;
                    }
                }

                if ($updated) {
                    $collection['albums'] = array_values($collection['albums']); // Reindex für sauberes YAML
                    $newYaml = Symfony\Component\Yaml\Yaml::dump(['collection' => $collection], 2, 4);
                    file_put_contents($filePath, $newYaml);
                }
            } catch (Exception $e) {
                error_log("Fehler beim Bearbeiten der Collection $filePath: " . $e->getMessage());
            }
        }
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
                $albumslug = generateSlug($title);

                if ($headImage) {
                    $imageSlug = pathinfo($headImage, PATHINFO_FILENAME);
                    $ymlPath = $imageDir . $imageSlug . '.yml';

                    if (file_exists($ymlPath)) {
                        try {
                            $meta = Symfony\Component\Yaml\Yaml::parseFile($ymlPath);
                            $guid = $meta['image']['guid'] ?? null;

                            if (!empty($guid)) {
                                $cachedImage = $cacheDir . $guid . '_M.jpg';
                                $fileName = basename($headImage);
                            }
                        } catch (Exception $e) {
                            error_log("YAML parse error in image metadata: " . $e->getMessage());
                        }
                    }
                }

                if ($cachedImage) {
                    echo "
                    <div>
                        <div class=\"w-full aspect-video overflow-hidden border border-gray-300 hover:border-cyan-400 rounded-xs dynamic-image-width transition-[max-width] duration-300 ease-in-out max-w-full md:max-w-none\" style=\"--img-max-width: 200px; max-width: var(--img-max-width);\">
                            <a href=\"media-detail.php?image=" . urlencode($fileName) . "\">
                                <img src='$cachedImage' class=\"w-full h-full object-cover\" alt=\"$title\" data-filename=\"$fileName\" title=\"$description\" draggable=\"true\"/>
                            </a>
                        </div>
                        <div class=\"w-full flex justify-between items-center text-sm pt-1 dark:text-gray-400\">
                            <span class=\"text-sm dark:text-gray-400\">$title</span>
                            <div class=\"relative inline-block\">
                                <button id=\"$fileName\" class=\"p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-400\" data-filename=\"$fileName\">
                                    <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"3\" stroke=\"currentColor\" class=\"w-5 h-5\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M6 12h.01M12 12h.01M18 12h.01\" />
                                    </svg>
                                </button>                    
                                <div class=\"dropdown hidden absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none\">
                                    <a href=\"backend_api/delete.php?type=removealbumfromcollection&albumname=$albumslug&collection=$collectionSlug\" class=\"confirm-link block px-4 py-2 text-sm text-red-600 hover:bg-red-100\">".languageString('collection.removeFromCollection')."</a>
                                </div>
                            </div>
                        </div>
                    </div>";
                }else if($headImage){
                    echo "
                    <div>
                        <div class=\"w-full aspect-video overflow-hidden border border-gray-300 hover:border-cyan-400 rounded-xs dynamic-image-width transition-[max-width] duration-300 ease-in-out max-w-full md:max-w-none\" style=\"--img-max-width: 200px; max-width: var(--img-max-width);\">
                            <a href=\"media-detail.php?image=" . urlencode($fileName) . "\">
                                <img src=\"../userdata/content/images/".$headImage."/\" class=\"w-full h-full object-cover\" alt=\"$title\" data-filename=\"$fileName\" title=\"$description\" draggable=\"true\"/>
                            </a>
                        </div>
                        <div class=\"w-full flex justify-between items-center text-sm pt-1 dark:text-gray-400\">
                            <span class=\"text-sm dark:text-gray-400\">$title</span>
                            <div class=\"relative inline-block\">
                                <button id=\"$fileName\" class=\"p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-400\" data-filename=\"$fileName\">
                                    <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"3\" stroke=\"currentColor\" class=\"w-5 h-5\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M6 12h.01M12 12h.01M18 12h.01\" />
                                    </svg>
                                </button>                    
                                <div class=\"dropdown hidden absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none\">
                                    <a href=\"backend_api/delete.php?type=removealbumfromcollection&albumname=$albumslug&collection=$collectionSlug\" class=\"confirm-link block px-4 py-2 text-sm text-red-600 hover:bg-red-100\">".languageString('collection.removeFromCollection')."</a>
                                </div>
                            </div>
                        </div>
                    </div>";
                }else{
                    echo "
                    <div>
                        <div class=\"w-full aspect-video overflow-hidden border border-gray-300 hover:border-cyan-400 rounded-xs dynamic-image-width transition-[max-width] duration-300 ease-in-out max-w-full md:max-w-none\" style=\"--img-max-width: 200px; max-width: var(--img-max-width);\">
                            <a href=\"media-detail.php?image=" . urlencode($fileName) . "\">
                                <img src=\"img/placeholder.png\" class=\"w-full h-full object-cover\" alt=\"$title\" data-filename=\"$fileName\" title=\"$description\" draggable=\"true\"/>
                            </a>
                        </div>
                        <div class=\"w-full flex justify-between items-center text-sm pt-1 dark:text-gray-400\">
                            <span class=\"text-sm dark:text-gray-400\">$title</span>
                            <div class=\"relative inline-block\">
                                <button id=\"$fileName\" class=\"p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-400\" data-filename=\"$fileName\">
                                    <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"3\" stroke=\"currentColor\" class=\"w-5 h-5\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M6 12h.01M12 12h.01M18 12h.01\" />
                                    </svg>
                                </button>                    
                                <div class=\"dropdown hidden absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none\">
                                    <a href=\"backend_api/delete.php?type=removealbumfromcollection&albumname=$albumslug&collection=$collectionSlug\" class=\"confirm-link block px-4 py-2 text-sm text-red-600 hover:bg-red-100\">".languageString('collection.removeFromCollection')."</a>
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

