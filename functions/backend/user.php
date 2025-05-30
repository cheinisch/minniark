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

        // check if file exist
        if(file_exists($ymlFile))
        {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'user' => [
                'username' => $username,
                'mail' => $mail,
                'password' => $passwordHash,
                'auth_type' => 'password', // default login for new users
            ]
        ];

        $yamlOK = file_put_contents($yamlFile, Yaml::dump($data, 2, 4)) !== false;

        return $yamlOK;
    }