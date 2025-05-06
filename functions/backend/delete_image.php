<?php

    function delete_image($filename)
    {
        $image = $filename;
        $json_temp = pathinfo($filename);
        $json = $json_temp['filename'].'json';
        $jsonfile = '../../userdata/content/images/'.$json;
        $jsonData = file_get_contents($jsonfile);
        $imagedata = json_decode($jsonData, true);
        $cacheguid = $imagedata['guid'];

        // Delete Cached files

        // Size S - XL

        $cache_s = "../../cache/images/".$cacheguid."_S.jpg";
        $cache_m = "../../cache/images/".$cacheguid."_M.jpg";
        $cache_l = "../../cache/images/".$cacheguid."_L.jpg";
        $cache_xl = "../../cache/images/".$cacheguid."_XL.jpg";

        // Image and JSON

        $imagefile = '../../userdata/content/images/'.$image;

        // delete Files

        unlink($jsonfile);
        unlink($imagefile);
        unlink($cache_s);
        unlink($cache_m);
        unlink($cache_l);
        unlink($cache_xl);

    }