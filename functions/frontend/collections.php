<?php

    function hasCollections()
    {
        $hasPosts = false;

        $postDir = __DIR__ . '/../../userdata/content/collection/';

        $folderCount = count_subfolders($postDir);

        if($folderCount > 0)
        {
            $hasPosts = true;
        }

        return $hasPosts;
    }