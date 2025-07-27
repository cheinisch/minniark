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
            $newKey = trim($data['site-license']);
            $oldKey = $settings['license'] ?? null;

            // Aktivierung/Deaktivierung nur wenn sich etwas ändert
            if (!empty($newKey) && empty($oldKey)) {
                activateKey($newKey); // Neuer Key, bisher keiner
                error_log("Key aktiviert: ".$newKey);
            } elseif (!empty($oldKey) && $oldKey !== $newKey) {
                deactivateKey($oldKey); // Alter Key deaktivieren
                error_log("Key deaktiviert: ".$oldKey);
                if (!empty($newKey)) {
                    activateKey($newKey); // Neuer Key aktivieren
                    error_log("anderer Key aktiviert: ".$newKey);
                }
            }

            // Neuen Key speichern (auch wenn leer = löschen)
            $settings['license'] = $newKey;
        }

        if (isset($data['custom_nav'])) {
            $settings['custom_nav'] = $data['custom_nav'];
        }

        if (isset($data['theme'])) {
            $settings['theme'] = $data['theme'];
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