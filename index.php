<?php

    require('bin/function/function.php');

    if(userfile_exist())
    {
        // If USer Exist
    }else{
        require_once('bin/install/install.php');
    }

?>