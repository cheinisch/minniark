<?php

    function is_installed()
    {
        $userconfig = __DIR__ . '/../../userdata/users/accounts';
        $systemsettings = __DIR__ . '/../../userdata/system';


        $accountFiles = glob($userconfig . '/*.php');
        $systemFiles = glob($systemsettings . '/*.json');

        if(empty($accountFiles))
        {
            header('Location: ./../../dashboard/install.php');
        }

        

    }