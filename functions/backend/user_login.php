<?php

$userConfigPath = __DIR__ . '/../../userdata/config/user_config.php';
$user = file_exists($userConfigPath) ? require $userConfigPath : [];


    function check_username($username)
    {
        global $user;
        $value = false;

        if($user['USERNAME'] == $username)
        {
            $value = true;
        }
    
        return $value;
    }


    function get_logintype()
    {
        global $user;
        $returnValue = "password";

        $returnValue = $user['AUTH_TYPE'];

        return $returnValue;

    }

    function send_otp_mail()
    {
        
    }