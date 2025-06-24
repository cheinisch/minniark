<?php

    require_once(__DIR__ . '/../../vendor/autoload.php');

    use Symfony\Component\Yaml\Yaml;

    
    function save_navigation(array $nav): bool
    {
        $file = __DIR__ . '/../../userdata/config/navigation.yml';

        // Nur Arrays akzeptieren (zur Sicherheit)
        if (!is_array($nav)) {
            error_log("Navigation data must be an array.");
            return false;
        }

        try {
            // YAML sauber formatieren (2 Ebenen, 4 Leerzeichen Einrückung)
            $yaml = Yaml::dump($nav, 2, 4);

            // Datei schreiben
            file_put_contents($file, $yaml);
            return true;
        } catch (Exception $e) {
            error_log("Failed to save YAML: " . $e->getMessage());
            return false;
        }
    }

    function read_navigation()
    {
        $file = __DIR__ . '/../../userdata/config/navigation.yml';

        if (!file_exists($file)) {
            return []; // Datei existiert nicht → leeres Menü
        }

        try {
            $data = Yaml::parseFile($file);
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            error_log("Failed to read YAML: " . $e->getMessage());
            return [];
        }
    }
