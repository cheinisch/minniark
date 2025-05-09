<?php

    
error_reporting(E_ALL);
ini_set('display_errors', 1);

error_log(print_r($_POST));



require_once __DIR__ . '/../../functions/function_backend.php';
security_checklogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $albumName = $_POST['album'] ?? '';
    $imagesToAdd = $_POST['image'] ?? [];

    if (empty($albumName) || (empty($imagesToAdd))) {
        die("Fehlende oder ungültige Daten.");
    }

    // Stelle sicher, dass es ein Array ist
    if (!is_array($imagesToAdd)) {
        $imagesToAdd = [$imagesToAdd];
    }


    $safeAlbumName = preg_replace('/[^a-z0-9]/i', '_', strtolower($albumName));
    $albumFile = __DIR__ . "/../../userdata/content/albums/$safeAlbumName.php";

    if (!file_exists($albumFile)) {
        die("Album nicht gefunden.");
    }

    // Album-Datei einbinden, um $Images zu erhalten
    include $albumFile;

    if (!isset($Images) || !is_array($Images)) {
        $Images = [];
    }

    // Neue Bilder hinzufügen, doppelte vermeiden
    foreach ($imagesToAdd as $img) {
        if (!in_array($img, $Images)) {
            $Images[] = $img;
        }
    }

    // Album-Datei neu schreiben
    $albumContent = "<?php\n";
    $albumContent .= '$Name = ' . var_export($Name, true) . ";\n";
    $albumContent .= '$Description = ' . var_export($Description, true) . ";\n";
    $albumContent .= '$Password = ' . var_export($Password, true) . ";\n";
    $albumContent .= '$Images = ' . var_export($Images, true) . ";\n";
    $albumContent .= '$HeadImage = ' . var_export($HeadImage ?? '', true) . ";\n";

    file_put_contents($albumFile, $albumContent);


    header("Location: ../media.php");
    exit;
} else {
    echo "Ungültige Anfrage.";
}
