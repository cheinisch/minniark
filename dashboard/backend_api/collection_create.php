<?php

    require_once(__DIR__ . "/../../functions/function_backend.php");
    require_once(__DIR__ . "/../../vendor/autoload.php");

    use Symfony\Component\Yaml\Yaml;

    print_r($_POST);

    $title = $_POST['collection-title'] ?? null;
    $content = $_POST['content'] ?? null;


    $collectionDir = __DIR__.'/../../userdata/content/collection/';

    if (!is_dir($collectionDir)) {
        // Versuche, das Verzeichnis anzulegen (inkl. Unterverzeichnisse)
        if (!mkdir($collectionDir, 0755, true)) {
            die("Konnte das Verzeichnis nicht erstellen: $collectionDir");
        }
    }

    $filename = generateSlug($title).'.yml';

    $fullPath = $collectionDir.''.$filename;

    echo("Full Path: ".$collectionDir.''.$filename);


    $data = [
        'title' => $title,
        'description' => $content,
        'albums' => [],
        'image' => '',
    ];

    $yaml = Yaml::dump($data, 2, 4); // 2 = Tiefe, 4 = Einrückung
    $result = file_put_contents($fullPath, $yaml);

    if($result)
    {
        header("Location: ../media.php");
    }else{
        echo "error writing file";
    }


?>