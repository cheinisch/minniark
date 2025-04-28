<?php

    function get_sitename()
    {
        $settingsPath = __DIR__ . '/../../userdata/config/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $siteTitle = $settings['site_title'] ?? 'Standard Titel';

        return $siteTitle;
    }

    function get_sitedescription()
    {
        $settingsPath = __DIR__ . '/../../userdata/config/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $siteDescription = $settings['site_description'] ?? '';

        return $siteDescription;
    }

    function get_language()
    {
        $settingsPath = __DIR__ . '/../../userdata/config/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $siteTitle = $settings['language'] ?? 'en';

        return $siteTitle;
    }

    function get_imagesize()
    {
        $settingsPath = __DIR__ . '/../../userdata/config/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $imageSize = $settings['default_image_size'] ?? 'M';

        return $imageSize;
    }

    function is_map_enabled()
    {
        $settingsPath = __DIR__ . '/../../userdata/config/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $switch = $settings['map']['enable'] ?? false;
        if($switch)
        {
            $value = "true";
        }else{
            $value = "false";
        }
        return $value;
    }

    function is_timeline_enabled()
    {
        $settingsPath = __DIR__ . '/../../userdata/config/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $switch = $settings['timeline']['enable'] ?? false;
        if($switch)
        {
            $value = "true";
        }else{
            $value = "false";
        }
        return $value;
    }

    function is_timeline_grouped()
    {
        $settingsPath = __DIR__ . '/../../userdata/config/settings.json';
        $settings = json_decode(file_get_contents($settingsPath), true);
        $switch = $settings['timeline']['groupe_by_date'] ?? false;
        if($switch)
        {
            $value = "true";
        }else{
            $value = "false";
        }
        return $value;
    }