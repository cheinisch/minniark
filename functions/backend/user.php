<?php

    require_once __DIR__ . '/../../vendor/autoload.php'; // für Yaml
    use Symfony\Component\Yaml\Yaml;

    function saveNewUser($username, $mail, $password):bool
    {
    
        $slug = generateSlug($username);

        $ymlDir = __DIR__.'/../../userdata/config/user/';

        // Check if user dir exist
        if (!is_dir($ymlDir))
        {
            mkdir($ymlDir, 0755, true);
        }

        // generate user id for file
        $id = generateUserID();
        $ymlFile = __DIR__.'/../../userdata/config/user/'.$id.'-'.$slug.'.yml';

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

    function updateUserData($username, $data, $newUsername): bool
    {
        $ymlDir = __DIR__ . '/../../userdata/config/user/';
        if (!is_dir($ymlDir)) {
            return false;
        }

        $files = scandir($ymlDir);
        $slug = generateSlug($username);
        $userFile = null;

        // Bestehende Datei zum aktuellen Benutzer finden
        foreach ($files as $file) {
            if (preg_match('/^(\d+)-' . preg_quote($slug, '/') . '\.yml$/', $file)) {
                $userFile = $file;
                break;
            }
        }

        if (!$userFile) {
            return false; // Benutzer nicht gefunden
        }

        $oldFilePath = $ymlDir . $userFile;
        $userYaml = Yaml::parseFile($oldFilePath);

        if (!isset($userYaml['user'])) {
            return false;
        }

        // Prüfen, ob der neue Benutzername bereits verwendet wird (nur wenn er sich ändert)
        if ($newUsername && $newUsername !== $username) {
            $newSlug = generateSlug($newUsername);
            foreach ($files as $file) {
                if (preg_match('/^\d+-' . preg_quote($newSlug, '/') . '\.yml$/', $file)) {
                    return false; // Neuer Benutzername existiert bereits
                }
            }
            $userYaml['user']['username'] = $newUsername;
        }

        // Daten aktualisieren
        foreach ($data as $key => $value) {
            $userYaml['user'][$key] = $value;
        }

        // Neues oder gleiches File schreiben
        if (isset($newSlug)) {
            $newFilePath = $ymlDir . $userYaml['user']['id'] . '-' . $newSlug . '.yml';
            if (file_put_contents($newFilePath, Yaml::dump($userYaml, 2, 4)) === false) {
                return false;
            }
            unlink($oldFilePath); // Alte Datei löschen
        } else {
            if (file_put_contents($oldFilePath, Yaml::dump($userYaml, 2, 4)) === false) {
                return false;
            }
        }

        return true;
    }

    function getUserDataFromID($id)
    {
        $ymlDir = __DIR__ . '/../../userdata/config/user/';
        if (!is_dir($ymlDir)) {
            return null;
        }

        $files = scandir($ymlDir);

        foreach ($files as $file) {
            if (preg_match('/^' . preg_quote((string)$id, '/') . '-.*\.yml$/', $file)) {
                $filePath = $ymlDir . $file;
                $data = Yaml::parseFile($filePath);
                return $data['user'] ?? null;
            }
        }

        return null;
    }

    function getUserDataFromUsername($username)
    {
        $slug = generateSlug($username);
        $ymlDir = __DIR__ . '/../../userdata/config/user/';
        if (!is_dir($ymlDir)) {
            return null;
        }

        $files = scandir($ymlDir);

        foreach ($files as $file) {
            if (preg_match('/^\d+-' . preg_quote($slug, '/') . '\.yml$/', $file)) {
                $filePath = $ymlDir . $file;
                $data = Yaml::parseFile($filePath);
                return $data['user'] ?? null;
            }
        }

        return null;
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