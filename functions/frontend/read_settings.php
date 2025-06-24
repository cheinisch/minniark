<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
use Symfony\Component\Yaml\Yaml;

function is_map_enabled(): bool
{
    $settingsPath = __DIR__ . '/../../userdata/config/settings.yml';

    if (!file_exists($settingsPath)) {
        return false;
    }

    try {
        $settings = Yaml::parseFile($settingsPath);
        return (bool)($settings['map']['enable'] ?? false);
    } catch (Exception $e) {
        error_log("Fehler beim Parsen der settings.yml: " . $e->getMessage());
        return false;
    }
}

function is_timeline_enabled(): bool
{
    $settingsPath = __DIR__ . '/../../userdata/config/settings.yml';

    if (!file_exists($settingsPath)) {
        return false;
    }

    try {
        $settings = Yaml::parseFile($settingsPath);
        return (bool)($settings['timeline']['enable'] ?? false);
    } catch (Exception $e) {
        error_log("Fehler beim Parsen der settings.yml: " . $e->getMessage());
        return false;
    }
}

function get_settings_array()
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

function is_nav_enabled()
{
    $settings = get_settings_array();
    return $settings['custom_nav'] ?? false;
}
