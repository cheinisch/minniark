<?php

    function delete_album($filename)
    {
        $album = preg_replace('/[^a-z0-9]/', '_', strtolower($filename)) . '.php';
        
        $albumpath = "../../userdata/content/albums/".$album;

        unlink($albumpath);
    }