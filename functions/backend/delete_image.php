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

    function remove_img_from_album($filename, $album)
    {
        $albumFile = __DIR__ . "/../../userdata/content/albums/" . preg_replace('/[^a-z0-9]/i', '_', strtolower($album)) . ".php";

        if (!file_exists($albumFile)) {
            echo "<p class='text-red-500'>Album nicht gefunden.</p>";
            return;
        }

        // Album-Datei einlesen und Variablen extrahieren
        include($albumFile);

        if (!isset($Images) || !is_array($Images)) {
            echo "<p class='text-red-500'>Kein gültiges Bilderarray gefunden.</p>";
            return;
        }

        // Bild entfernen
        $Images = array_filter($Images, function ($img) use ($filename) {
            return $img !== $filename;
        });

        // Neue Albumdatei als PHP-String erzeugen
        $albumContent = "<?php\n"
            . "\$Name = " . var_export($Name, true) . ";\n"
            . "\$Description = " . var_export($Description, true) . ";\n"
            . "\$Password = " . var_export($Password, true) . ";\n"
            . "\$Images = " . var_export(array_values($Images), true) . ";\n"
            . "\$HeadImage = " . var_export($HeadImage, true) . ";\n";

        // Datei überschreiben
        file_put_contents($albumFile, $albumContent);

        // Weiterleitung
        sleep(5);
        header("Location: ../../dashboard/album-detail.php?album=$Name");
        exit;
    }
