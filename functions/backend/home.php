<?php

    require_once __DIR__ . '/../../vendor/autoload.php'; // für Yaml

    use Symfony\Component\Yaml\Yaml;

    function homeSaveNew(array $data): bool
    {
        $path = __DIR__ . '/../../userdata/config/home.yml';

        // Standarddaten + eingehende überschreiben
        $home = array_merge([
            'style' => 'start',
            'startcontent' => '',
            'headline' => '',
            'sub-headline' => '',
            'content' => '',
            'default_image_style' => 'image',
            'default_image' => '',
            'cover' => '',
            'updated_at' => date('Y-m-d H:i:s'),
        ], $data);

        $yamlData = ['home' => $home];

        return file_put_contents($path, Yaml::dump($yamlData, 2, 4)) !== false;
    }


    function homeUpdate(array $data): bool
    {
        $path = __DIR__ . '/../../userdata/config/home.yml';

        $existing = file_exists($path) ? Yaml::parseFile($path) : [];
        $home = $existing['home'] ?? [];

        // Nur bekannte Felder überschreiben
        $fields = [
            'style',
            'startcontent',
            'headline',
            'sub-headline',
            'content',
            'default_image_style',
            'default_image',
            'cover'
        ];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $home[$field] = $data[$field];
            }
        }

        $home['updated_at'] = date('Y-m-d H:i:s');

        $yamlData = ['home' => $home];

        return file_put_contents($path, Yaml::dump($yamlData, 2, 4)) !== false;
    }



    function getHomeConfig(): array
    {
        $path = __DIR__ . '/../../userdata/config/home.yml';

        // Standardwerte
        $defaults = [
            'style' => 'start',
            'startcontent' => '',
            'headline' => '',
            'sub-headline' => '',
            'content' => '',
            'default_image_style' => 'image',
            'default_image' => '',
            'cover' => '',
            'updated_at' => '',
        ];

        if (!file_exists($path)) {
            return $defaults;
        }

        $yaml = Yaml::parseFile($path);
        $home = $yaml['home'] ?? [];

        return array_merge($defaults, $home);
    }