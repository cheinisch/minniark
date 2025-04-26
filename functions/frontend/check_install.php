<?php

    function is_installed()
    {
        $userconfig = __DIR__ . '/../../userdata/config/user_config.php';
        $systemsettings = __DIR__ . '/../../userdata/config/system';


        $accountfile = file_exists($userconfig);
        //$systemFiles = glob($systemsettings . '/*.json');

        if(!$accountfile)
        {
            header('Location: ./../../dashboard/install.php');
            exit(); // Wichtig: Script hier beenden!
        }

        

    }