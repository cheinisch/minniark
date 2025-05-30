<?php

    require_once __DIR__ . '/../../vendor/autoload.php'; // für Yaml
    use Symfony\Component\Yaml\Yaml;

    function saveNewUser($username, $mail, $password):bool
    {
    
        $slug = generateSlug($username);

        $ymlDir = __DIR__.'/../../userdata/config/user/';
        $ymlFile = __DIR__.'/../../userdata/config/user/'.$id.'-'.$slug.'.yml';

        // Check if user dir exist
        if (!is_dir($ymlDir))
        {
            mkdir($ymlDir, 0755, true);
        }

        // generate user id
        $id = generateUserID();

        // check if file exist
        if(file_exists($ymlFile))
        {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'user' => [
                'id' => $id,
                'username' => $username,
                'mail' => $mail,
                'password' => $passwordHash,
                'auth_type' => 'password', // default login for new users
            ]
        ];

        $yamlOK = file_put_contents($ymlFile, Yaml::dump($data, 2, 4)) !== false;

        return $yamlOK;
    }


    function getUserFromMail($mail)
    {
        $ymlDir = __DIR__.'/../../userdata/config/user/';
        
        if (!is_dir($ymlDir)) {
            return null;
        }

        $files = scandir($ymlDir);

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'yml') {
                continue;
            }

            $filePath = $ymlDir . $file;
            $data = Yaml::parseFile($filePath);

            if (isset($data['user']['mail']) && strtolower($data['user']['mail']) === strtolower($mail)) {
                return $data['user'];
            }
        }

        return null;
    }

    
    function generateUserID(): int
    {
        $ymlDir = __DIR__.'/../../userdata/config/user/';
        
        if (!is_dir($ymlDir)) {
            return 1;
        }

        $files = scandir($ymlDir);
        $maxId = 0;

        foreach ($files as $file) {
            if (preg_match('/^(\d+)-/', $file, $matches)) {
                $id = (int)$matches[1];
                if ($id > $maxId) {
                    $maxId = $id;
                }
            }
        }

        return $maxId + 1;
    }