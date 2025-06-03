<?php

    function is_installed()
    {
        $userconfig = __DIR__ . '/../../userdata/config/user/';
        $systemsettings = __DIR__ . '/../../userdata/config/settings.yml';


        $systemsettingsfile = file_exists($systemsettingsfile);
        $accountFiles = glob($userconfig . '/*.yml');

        if($systemsettingsfile && $accountFiles)
        {
            // do nothing
        }else{
            header('Location: ./../../dashboard/install.php');
            exit(); // Wichtig: Script hier beenden!
        }

        

    }