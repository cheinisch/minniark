<?php

    require('bin/function.php');

    if(!user_exist())
    {
        require_once('bin/install/install.php');
    }else{
        
    }

?>