<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    function is_installed()
    {
        $userconfig = __DIR__ . '/../../userdata/config/user/';
        $systemsettings = __DIR__ . '/../../userdata/config/settings.yml';


        $systemsettingsfile = file_exists($systemsettings);
        $accountFiles = glob($userconfig . '/*.yml');

        if($accountFiles)
        {
            error_log("Account Files exist");
        }else{
            error_log("Account Files not exist");
        }

        if($systemsettingsfile)
        {
            error_log("Settingsfile Files exist");
        }else{
            error_log("Settingsfile Files not exist");
        }

        if($systemsettingsfile && $accountFiles)
        {
            // do nothing
        }else{
            $projectRoot = explode('/', $_SERVER['SCRIPT_NAME'])[1]; // "minniark"
            header('Location: dashboard/install.php');
            exit(); // Wichtig: Script hier beenden!
        }

        

    }