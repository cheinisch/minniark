<?php

/*
 * Check if the accountfile exist
 */

function userfile_exist()
{

    if(file_exists('conf/account.php'))
    {
        return true;
    }else{
        return false;
    }

    return false;
}


?>