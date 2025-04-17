<?php

    function get_sitename()
    {
        $settingsPath = __DIR__ . '/../../userdata/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $siteTitle = $settings['site_title'] ?? 'Standard Titel';

        return $siteTitle;
    }

    function get_language()
    {
        $settingsPath = __DIR__ . '/../../userdata/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $siteTitle = $settings['language'] ?? 'en';

        return $siteTitle;
    }