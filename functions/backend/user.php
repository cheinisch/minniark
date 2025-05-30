<?php

    require_once __DIR__ . '/../../vendor/autoload.php'; // für Yaml
    use Symfony\Component\Yaml\Yaml;

    function saveNewUser($username, $mail, $password):bool
    {
    
        $slug = generateSlug($username);

        $ymlDir = __DIR__.'/../../userdata/config/user/';
        $ymlFile = __DIR__.'/../../userdata/config/user/'.$slug.'.yml';

        // Check if user dir exist
        if (!is_dir($ymlDir))
        {
            mkdir($ymlDir, 0755, true);
        }

        return false;
    }