<?php

    $file = isset($_GET['file']) ? $_GET['file'] : null;
    $rotate = isset($_GET['rotate']) ? $_GET['rotate'] : null;

    if($rotate != null)
    {
        $result = rotateImage($file, $rotate);
        if($result)
        {
            header("Location: ../media-detail.php?image=".$file);
        }
    }