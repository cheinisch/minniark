<?php

    function get_cached_image_dashboard($imagefile, $size)
    {

        if($imagefile == '')
        {
            return "img/placeholder.png";
        }

        if($size == 'Original')
        {
            return "../userdata/content/images/".$imagefile;
        }

        $jsonFile = __DIR__.'/../../userdata/content/images/'.pathinfo($imagefile, PATHINFO_FILENAME) . '.json';

        if (file_exists($jsonFile)) {
            $meta = json_decode(file_get_contents($jsonFile), true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($meta['guid'])) {
                $guid = $meta['guid'];
                $cacheDir = '../cache/images/';
                $cachedImagePath = $cacheDir . $guid . '_' . $size . '.jpg';
                return $cachedImagePath;
            }
        }

        return "../userdata/content/images/".$imagefile;
        
    }