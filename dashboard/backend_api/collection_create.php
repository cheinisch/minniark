<?php

    require_once(__DIR__ . "/../../functions/function_backend.php");
    

    print_r($_POST);

    $title = $_POST['collection-title'] ?? null;
    $content = $_POST['content'] ?? null;


    $result = saveNewCollection($title, $content);

    $slug = generateSlug($title);

    if($result)
    {
        header("Location: ../collection-detail.php?collection=".$slug);
    }else{
        echo "error writing file";
    }


?>