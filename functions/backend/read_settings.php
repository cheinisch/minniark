<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

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

function get_sitename()
{
    $settings = get_settings_array();
    return $settings['site_title'] ?? 'Standard Titel';
}

function get_sitedescription()
{
    $settings = get_settings_array();
    return $settings['site_description'] ?? '';
}

function get_language()
{
    $settings = get_settings_array();
    return $settings['language'] ?? 'en';
}

function get_imagesize()
{
    $settings = get_settings_array();
    return $settings['default_image_size'] ?? 'M';
}

function is_map_enabled()
{
    $settings = get_settings_array();
    return !empty($settings['map']['enable']) ? "true" : "false";
}

function is_timeline_enabled()
{
    $settings = get_settings_array();
    return !empty($settings['timeline']['enable']) ? "true" : "false";
}

function is_timeline_grouped()
{
    $settings = get_settings_array();
    return !empty($settings['timeline']['groupe_by_date']) ? "true" : "false";
}

function get_theme()
{
    $settings = get_settings_array();
    return $settings['theme'] ?? 'basic';
}

function get_themelist()
{
    $themefolder = realpath(__DIR__ . '/../../userdata/template/');
    $themelist = [];

    if (!$themefolder || !is_dir($themefolder)) {
        return $themelist;
    }

    foreach (scandir($themefolder) as $folder) {
        if ($folder === '.' || $folder === '..') continue;

        $themePath = $themefolder . DIRECTORY_SEPARATOR . $folder;

        if (!is_dir($themePath)) continue;

        $jsonFile = $themePath . DIRECTORY_SEPARATOR . 'theme.json';
        if (!file_exists($jsonFile)) continue;

        $jsonContent = file_get_contents($jsonFile);
        $themeData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($themeData)) continue;

        foreach ($themeData as $theme) {
            if (isset($theme['name'])) {
                $themelist[] = $theme;
            }
        }
    }

    return $themelist;
}

function get_license()
{
    $settings = get_settings_array();
    return $settings['license'] ?? '';
}

function is_nav_enabled()
{
    $settings = get_settings_array();
    return $settings['custom_nav'] ?? false;
}