<?php

    require_once __DIR__ . '/../../vendor/autoload.php';

    use Symfony\Component\Yaml\Yaml;

    function get_settings_array_data()
    {
        $settingsPath = __DIR__ . '/../../userdata/config/settings.yml';

        if (!file_exists($settingsPath)) {
            return [];
        }

        try {
            return Yaml::parseFile($settingsPath);
        } catch (Exception $e) {
            error_log("YAML Parse Error: " . $e->getMessage());
            return [];
        }
    }

    function sitemap_enabled()
    {
        $settings = get_settings_array_data();
        return !empty($settings['sitemap']['active']) ? true : false;
    }

    function sitemap_images_enabled()
    {
        $settings = get_settings_array_data();
        return !empty($settings['sitemap']['images']) ? true : false;
    }


    function map_enabled()
    {
        $settings = get_settings_array_data();
        return !empty($settings['map']['enable']) ? true : false;
    }

    function timeline_enabled()
    {
        $settings = get_settings_array_data();
        return !empty($settings['timeline']['enable']) ? true : false;
    }


    function sitemap()
    {
        if(sitemap_enabled())
        {
            sitemap_delete();
            sitemap_generate();
        }else{
            sitemap_delete();
        }

    }


    function sitemap_delete()
    {

        error_log("SITEMAP: run delete");

        // Path => ../../sitemap.xml

        $path = __DIR__.'/../../sitemap.xml';

        if(unlink($path))
        {
            return true;
        }else{
            return false;
        }

        return false;

    }


    function sitemap_generate()
    {
        $path = __DIR__ . '/../../sitemap.xml';
        $baseUrl = get_base_url();

        $urls = [
            "$baseUrl/",
        ];



        if(existAlbum())
        {
            $urls[] = "$baseUrl/albums";
            foreach (albumSlugs() as $slug) {
                $urls[] = "$baseUrl/album/" . urlencode($slug);
            }
        }

        if(existCollections())
        {
            $urls[] = "$baseUrl/collections";
            foreach (collectionSlugs() as $slug) {
                $urls[] = "$baseUrl/collection/" . urlencode($slug);
            }
        }

        if (timeline_enabled()) {
            $urls[] = "$baseUrl/timeline";
        }

        if (map_enabled()) {
            $urls[] = "$baseUrl/map";
        }        

        if (existPages()) {
            foreach (getPageSlugs() as $slug) {
                $urls[] = "$baseUrl/p/" . urlencode($slug);
            }
        }

        if (existEssays()) {
            $urls[] = "$baseUrl/blog"; // ggf. dein Index-Pfad
            foreach (getEssaySlugs() as $slug) {
                $urls[] = "$baseUrl/blog/" . urlencode($slug);
            }
        }

        if (existImages() && sitemap_images_enabled()) {
            foreach (imageFilename() as $filename) {
                $urls[] = "$baseUrl/i/" . urlencode($filename);
            }
        }

        // Sitemap schreiben
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($urls as $url) {
            $xml .= "  <url><loc>$url</loc></url>" . PHP_EOL;
        }
        $xml .= '</urlset>' . PHP_EOL;

        file_put_contents($path, $xml);
    }



    function get_base_url(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Kompletten URI-Pfad abschneiden, sodass nur Domain bleibt
        return $protocol . '://' . $host;
    }


    function existAlbum(): bool
    {
        $albumDir = __DIR__ . '/../../userdata/content/album/';
        return is_dir($albumDir) && count(glob($albumDir . '*.yml')) > 0;
    }

    function albumSlugs(): array
    {
        $albumDir = __DIR__ . '/../../userdata/content/album/';
        $slugs = [];

        if (!is_dir($albumDir)) {
            return $slugs;
        }

        $files = glob($albumDir . '*.yml');

        foreach ($files as $filePath) {
            $slug = basename($filePath, '.yml');

            try {
                $data = Symfony\Component\Yaml\Yaml::parseFile($filePath);
                if (!empty($data['album'])) {
                    $slugs[] = $slug;
                }
            } catch (\Exception $e) {
                error_log("YAML parse error in album '$slug': " . $e->getMessage());
            }
        }

        return $slugs;
    }

    function existPages(): bool
    {
        $baseDir = __DIR__ . '/../../userdata/content/page/';

        if (!is_dir($baseDir)) {
            return false;
        }

        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..' || str_starts_with($folder, '.')) continue;

            $yamlPath = $baseDir . $folder . '/' . $folder . '.yml';
            if (file_exists($yamlPath)) {
                return true;
            }
        }

        return false;
    }

    function getPageSlugs(): array
    {
        $baseDir = __DIR__ . '/../../userdata/content/page/';
        $slugs = [];

        if (!is_dir($baseDir)) {
            return $slugs;
        }

        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..' || str_starts_with($folder, '.')) continue;

            $yamlPath = $baseDir . $folder . '/' . $folder . '.yml';
            if (file_exists($yamlPath)) {
                $slugs[] = $folder;
            }
        }

        return $slugs;
    }




    function existCollections(): bool
    {
        $path = __DIR__ . '/../../userdata/content/collection/';

        return is_dir($path) && count(array_diff(scandir($path), ['.', '..'])) > 0;
    }

    function collectionSlugs(): array
    {
        $collectionPath = __DIR__ . '/../../userdata/content/collection/';
        $collectionFiles = glob($collectionPath . '*.yml');
        $slugs = [];

        foreach ($collectionFiles as $file) {
            $slug = basename($file, '.yml');
            try {
                $data = Symfony\Component\Yaml\Yaml::parseFile($file);
                if (!empty($data['collection'])) {
                    $slugs[] = $slug;
                }
            } catch (Exception $e) {
                error_log("Fehler beim Parsen von Collection $slug: " . $e->getMessage());
            }
        }

        return $slugs;
    }


function existImages(): bool
{
    $dir = __DIR__ . '/../../userdata/content/images/';
    if (!is_dir($dir)) {
        return false;
    }

    $yamlFiles = glob($dir . '*.yml');
    foreach ($yamlFiles as $yamlFile) {
        $basename = pathinfo($yamlFile, PATHINFO_FILENAME);

        foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
            if (file_exists($dir . $basename . '.' . $ext)) {
                return true;
            }
        }
    }

    return false;
}

function imageFilename(): array
{
    $dir = __DIR__ . '/../../userdata/content/images/';
    $filenames = [];

    if (!is_dir($dir)) {
        return $filenames;
    }

    $yamlFiles = glob($dir . '*.yml');
    foreach ($yamlFiles as $yamlFile) {
        $basename = pathinfo($yamlFile, PATHINFO_FILENAME);

        foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
            $imageFile = $dir . $basename . '.' . $ext;
            if (file_exists($imageFile)) {
                $filenames[] = basename($imageFile);
                break; // Nur ein Treffer pro Datei
            }
        }
    }

    return $filenames;
}

function existEssays(): bool
{
    $baseDir = __DIR__ . '/../../userdata/content/essay/';

    if (!is_dir($baseDir)) {
        return false;
    }

    foreach (scandir($baseDir) as $folder) {
        if ($folder === '.' || $folder === '..' || str_starts_with($folder, '.')) continue;

        $yamlPath = $baseDir . $folder . '/' . $folder . '.yml';
        if (file_exists($yamlPath)) {
            return true;
        }
    }

    return false;
}

function getEssaySlugs(): array
{
    $baseDir = __DIR__ . '/../../userdata/content/essay/';
    $slugs = [];

    if (!is_dir($baseDir)) {
        return $slugs;
    }

    foreach (scandir($baseDir) as $folder) {
        if ($folder === '.' || $folder === '..' || str_starts_with($folder, '.')) continue;

        $yamlPath = $baseDir . $folder . '/' . $folder . '.yml';
        if (file_exists($yamlPath)) {
            $slugs[] = $folder;
        }
    }

    return $slugs;
}

