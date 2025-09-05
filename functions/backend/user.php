<?php

    require_once __DIR__ . '/../../vendor/autoload.php'; // für Yaml
    use Symfony\Component\Yaml\Yaml;

    function saveNewUser($username, $mail, $password,$userrole):bool
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
                'userrole' => $userrole,
            ]
        ];

        $yamlOK = file_put_contents($ymlFile, Yaml::dump($data, 2, 4)) !== false;

        return $yamlOK;
    }

    function updateUserData($username, $data, $newUsername): bool
    {
        error_log("TEST");
        error_log(print_r($data, true));

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

        // Wenn Passwort im $data-Array enthalten ist, verschlüsseln
        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
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

    function getIDfromUsername($username):int
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
                $data = Symfony\Component\Yaml\Yaml::parseFile($filePath);

                if (isset($data['user']['id'])) {
                    return (int)$data['user']['id'];
                }
            }
        }

        return 0;
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

    function get_userimage($username)
    {
        $user = getUserDataFromUsername($username);
        $email = $user['mail'] ?? '';
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $fallbackUrl = "$protocol://$host/dashboard/img/avatar.png";
        $size = 160;
        $gravatarUrl = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=$size&d=" . urlencode($fallbackUrl);
        return $gravatarUrl;
    }

    function get_username($username): string
    {
        return $username;
    }

    function get_usermail($username): string
    {
        $user = getUserDataFromUsername($username);
        return $user['mail'] ?? '';
    }

    function get_displayname($username): string
    {
        $user = getUserDataFromUsername($username);
        return $user['display_name'] ?? $user['username'] ?? '';
    }

    function get_logintype_select($username): string
    {
        $user = getUserDataFromUsername($username);
        return $user['auth_type'] ?? '';
    }

    function getAllUser(): string
    {
        $ymlDir = __DIR__ . '/../../userdata/config/user/';
        $output = '';

        if (!is_dir($ymlDir)) {
            return '<tr><td colspan="4">Keine Benutzer gefunden.</td></tr>';
        }

        $files = scandir($ymlDir);

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'yml') {
                continue;
            }

            $filePath = $ymlDir . $file;
            $data = Symfony\Component\Yaml\Yaml::parseFile($filePath);

            if (!isset($data['user'])) {
                continue;
            }

            $username = htmlspecialchars($data['user']['username'] ?? '');
            $mail     = htmlspecialchars($data['user']['mail'] ?? '');
            $role     = htmlspecialchars($data['user']['userrole'] ?? 'user');

            // Optional: Button/Link in letzter Spalte
            $action = '<a href="?edit=' . urlencode($username) . '" class="text-sky-600 hover:underline">'.languageString('general.edit').'</a> - <a href="?delete=' . urlencode($username) . '" class="text-rose-600 hover:underline">'.languageString('generel.delete').'</a>';

            $output .= "<tr>
                        <td class=\"py-2\">$username</td>
                        <td>$mail</td>
                        <td>$role</td>
                        <td>$action</td>
                        </tr>";
        }

        return $output;
    }

    function isAdmin(string $username): bool
    {
        $user = getUserDataFromUsername($username);

        if (!$user) {
            return false;
        }

        // Falls 'userrole' nicht vorhanden ist, standardmäßig 'user' annehmen
        $role = strtolower($user['userrole'] ?? 'user');

        return $role === 'admin';
    }

    function removeUser($username): bool
    {
        $slug = generateSlug($username);
        $ymlDir = __DIR__ . '/../../userdata/config/user/';
        if (!is_dir($ymlDir)) {
            return false;
        }

        $files = scandir($ymlDir);

        foreach ($files as $file) {
            if (preg_match('/^\d+-' . preg_quote($slug, '/') . '\.yml$/', $file)) {
                $filePath = $ymlDir . $file;
                unlink($filePath);
                return true;
            }
        }

        return false;
    }
