<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

use Symfony\Component\Yaml\Yaml;

function get_cacheimage($filename)
{
    $settingsPath = __DIR__ . '/../../userdata/config/settings.yml';

    if (!file_exists($settingsPath)) {
        error_log("Settings file not found: $settingsPath");
        return null;
    }

    try {
        $settings = Yaml::parseFile($settingsPath);
        $size = $settings['default_image_size'] ?? 'M';

        if (strtolower($size) === "original") {
            return '/userdata/content/images/' . $filename;
        }

        $slug = pathinfo($filename, PATHINFO_FILENAME);
        $ymlPath = __DIR__ . "/../../userdata/content/images/{$slug}.yml";

        if (!file_exists($ymlPath)) {
            return null;
        }

        $yaml = Yaml::parseFile($ymlPath);
        $guid = $yaml['image']['guid'] ?? null;

        if (!$guid) {
            return null;
        }

        $newSize = strtoupper($size);
        return "/cache/images/{$guid}_{$newSize}.jpg";

    } catch (Exception $e) {
        error_log("YAML Error in get_cacheimage: " . $e->getMessage());
        return null;
    }
}
