<?php

    function saveNewUser($username, $mail, $password):bool
    {
    
        $slug = generateSlug($username);

        $ymlDir = __DIR__.'/../../userdata/config/user/';
        $ymlFile = __DIR__.'/../../userdata/config/user/'.$slug.'.yml';
    return false;
    }