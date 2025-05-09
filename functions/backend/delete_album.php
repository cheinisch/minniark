<?php

    function delete_album($filename)
    {
        $album = strtolower($filename)."php";

        $albumpath = "../../userdata/content/albums".$album;

        unlink($albumpath);
    }