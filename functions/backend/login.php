<?php

$userConfigPath = __DIR__ . '/../../userdata/config/user_config.php';
$user = file_exists($userConfigPath) ? require $userConfigPath : [];


    function check_username($username)
    {
        return true;
    }