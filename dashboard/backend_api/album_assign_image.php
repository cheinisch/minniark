<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . "/../../functions/function_backend.php");
security_checklogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';
    $albumName = isset($_POST['album']) ? trim($_POST['album']) : '';

    if (empty($image) || empty($albumName)) {
        exit(json_encode(['error' => 'Fehlende Bild- oder Albumangabe.', 'debug' => compact('image', 'albumName')]));
    }

    // Wenn der String 'media-detail.php?image=' enthält, bereinige ihn
    if (strpos($image, 'media-detail.php?image=') !== false) {
        parse_str(parse_url($image, PHP_URL_QUERY), $queryParams);
        if (isset($queryParams['image'])) {
            $image = $queryParams['image']; // Nur den Dateinamen übernehmen
        }
    }

    // Fix für Bildpfad: sicherstellen, dass nur der Dateiname verarbeitet wird
    // Nur den Dateinamen extrahieren – schützt auch vor Query-Strings
    $cleanImage = basename(parse_url($image, PHP_URL_PATH));
    $imageBase = pathinfo($cleanImage, PATHINFO_FILENAME);
    $imageJsonPath = __DIR__ . '/../../userdata/content/images/' . $imageBase . '.json';


    error_log("IMAGE INPUT: " . $image);
    error_log("CLEANED IMAGE: " . $cleanImage);
    error_log("JSON PATH: " . $imageJsonPath);

    if (!file_exists($imageJsonPath)) {
        exit(json_encode(['error' => 'Metadaten nicht gefunden.', 'debug' => ['expected_path' => $imageJsonPath, 'image_input' => $image]]));
    }

    $imageMeta = json_decode(file_get_contents($imageJsonPath), true);
    if (!isset($imageMeta['filename'])) {
        exit(json_encode(['error' => 'Kein Dateiname im Bild-Metadaten gefunden.', 'debug' => $imageMeta]));
    }
    $imageFile = $imageMeta['filename'];

    $albumFile = __DIR__ . '/../../userdata/content/albums/' . preg_replace('/[^a-z0-9]/', '_', strtolower($albumName)) . '.php';

    if (!file_exists($albumFile)) {
        exit(json_encode(['error' => 'Album nicht gefunden.', 'debug' => ['path' => $albumFile]]));
    }

    include $albumFile;

    // Sicherstellen, dass $Images ein Array ist
    if (!isset($Images) || !is_array($Images)) {
        $Images = [];
    }

    if (!in_array($imageFile, $Images)) {
        $Images[] = $imageFile;

        $content = "<?php\n";
        $content .= '$Name = ' . var_export($Name, true) . ";\n";
        $content .= '$Description = ' . var_export($Description, true) . ";\n";
        $content .= '$Password = ' . var_export($Password, true) . ";\n";
        $content .= '$Images = ' . var_export($Images, true) . ";\n";
        $content .= '$HeadImage = ' . var_export($HeadImage, true) . ";\n";

        if (file_put_contents($albumFile, $content) === false) {
            exit(json_encode(['error' => 'Album konnte nicht gespeichert werden.', 'debug' => ['file' => $albumFile]]));
        }

        echo json_encode(['success' => true, 'debug' => ['added' => $imageFile]]);
    } else {
        echo json_encode(['error' => 'Bild ist bereits im Album.', 'debug' => ['filename' => $imageFile, 'existing' => $Images]]);
    }
} else {
    echo json_encode(['error' => 'Ungültige Anfrage.']);
}
