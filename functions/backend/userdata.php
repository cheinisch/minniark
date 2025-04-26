<?php

define('IMAGEPORTFOLIO', true);
$userConfigPath = __DIR__ . '/../../userdata/config/user_config.php';
$user = file_exists($userConfigPath) ? require $userConfigPath : [];

// Nutzerbild (Gravatar mit Fallback)
function get_userimage()
{
    global $user;
    $email = $user['EMAIL'] ?? '';
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $fallbackUrl = "$protocol://$host/dashboard/img/avatar.png";
    $size = 80;

    $gravatarUrl = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=$size&d=" . urlencode($fallbackUrl);
    return $gravatarUrl;
}

// Benutzername (aus Konfig)
function get_username()
{
    global $user;
    return $user['USERNAME'] ?? 'Unbekannt';
}

// E-Mail-Adresse
function get_usermail()
{
    global $user;
    return $user['EMAIL'] ?? '';
}

function get_displayname()
{
    global $user;
    return $user['DISPLAY_NAME'] ?? '';
}