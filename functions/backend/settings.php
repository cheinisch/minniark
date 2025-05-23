<?php

    require_once(__DIR__ . '/../../vendor/autoload.php');

    use Symfony\Component\Yaml\Yaml;

    function saveSettings(array $data = []): bool
    {
        $file = __DIR__ . '/../../userdata/config/settings.yml';

        // Bestehende Einstellungen laden oder mit leer starten
        $settings = [];
        if (file_exists($file)) {
            try {
                $settings = Yaml::parseFile($file);
            } catch (Exception $e) {
                error_log("Failed to parse YAML: " . $e->getMessage());
                return false;
            }
        }

        // Neue Werte aus dem $data-Array setzen
        if (isset($data['site-license'])) {
            $settings['license'] = trim($data['site-license']);
        }

        // Weitere Felder hier ergänzen, falls nötig

        // Speichern
        try {
            $yaml = Yaml::dump($settings, 2, 4);
            file_put_contents($file, $yaml);
            return true;
        } catch (Exception $e) {
            error_log("Failed to save YAML: " . $e->getMessage());
            return false;
        }
    }