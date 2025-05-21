<?php
require_once(__DIR__ . "/../../vendor/autoload.php");
use Symfony\Component\Yaml\Yaml;

function getGpsPoints(string $mediaPath = 'userdata/content/images/'): array {
    $points = [];

    // Suche nach allen .yml-Dateien im Bilderordner
    $ymlFiles = glob($mediaPath . '*.yml');

    foreach ($ymlFiles as $file) {
        try {
            $yaml = Yaml::parseFile($file);
            $data = $yaml['image'] ?? [];
        } catch (Exception $e) {
            error_log("Fehler beim Parsen von $file: " . $e->getMessage());
            continue;
        }

        // GPS muss vollständig sein
        if (
            isset($data['exif']['GPS']['latitude'], $data['exif']['GPS']['longitude'])
        ) {
            $basename = pathinfo($file, PATHINFO_FILENAME);
            $mdFile = $mediaPath . $basename . '.md';
            $description = '';

            if (file_exists($mdFile)) {
                $description = trim(file_get_contents($mdFile));
            }

            $guid = $data['guid'] ?? $basename;

            $points[] = [
                'lat' => $data['exif']['GPS']['latitude'],
                'lng' => $data['exif']['GPS']['longitude'],
                'title' => $data['title'] ?? '',
                'description' => $description,
                'image' => "/cache/images/{$guid}_M.jpg",
            ];
        }
    }

    return $points;
}
