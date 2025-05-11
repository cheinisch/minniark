<?php


    function is_map_enabled()
    {
        $settingsPath = __DIR__ . '/../../userdata/config/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $switch = $settings['map']['enable'] ?? false;

        return $switch;
    }

    function is_timeline_enabled()
    {
        $settingsPath = __DIR__ . '/../../userdata/config/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $switch = $settings['timeline']['enable'] ?? false;

        return $switch;
    }