<?php

    function get_userimage()
    {

        $usersPath = __DIR__ . '/../../userdata/users.json';
        $users = json_decode(file_get_contents($usersPath), true);

        // Nur einen User annehmen
        $user = $users[0];
        $email = $user['email'];
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI']; // Oder verwende einen spezifischeren Pfad, falls nötig
        $fallbackUrl = "$protocol://$host/dashboard_v2/img/avatar.png"; // Fallback-URL
        $size = 80;

        //$gravatarUrl = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=$size&d=" . urlencode($default);
        $gravatarUrl = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=$size&d=" . urlencode($fallbackUrl);
        return $gravatarUrl;

    }

    function get_username()
    {
        $usersPath = __DIR__ . '/../../userdata/users.json';
        $users = json_decode(file_get_contents($usersPath), true);

        // Nur einen User annehmen
        $user = $users[0];
        $name = $user['name'];

        return $name;
    }

    function get_usermail()
    {
        $usersPath = __DIR__ . '/../../userdata/users.json';
        $users = json_decode(file_get_contents($usersPath), true);

        // Nur einen User annehmen
        $user = $users[0];
        $email = $user['email'];

        return $email;
    }