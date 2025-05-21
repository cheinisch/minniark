<?php

require_once(__DIR__ . "/../../vendor/autoload.php");
use Symfony\Component\Yaml\Yaml;

function getTimelineImagesFromYaml(string $mediaPath = 'userdata/content/images/', string $defaultSize = 'm'): array
{
    $images = [];

    $imageFiles = glob($mediaPath . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

    foreach ($imageFiles as $imgPath) {
        $filename = basename($imgPath);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $ymlPath = $mediaPath . $basename . '.yml';
        $mdPath  = $mediaPath . $basename . '.md';

        if (!file_exists($ymlPath)) {
            continue;
        }

        try {
            $yaml = Yaml::parseFile($ymlPath);
            $data = $yaml['image'] ?? [];
        } catch (Exception $e) {
            error_log("YAML-Fehler bei $ymlPath: " . $e->getMessage());
            continue;
        }

        $guid = $data['guid'] ?? $basename;
        $exifDate = $data['exif']['Date'] ?? null;
        $createdAt = $data['created_at'] ?? null;

        // Datum formatieren (EXIF bevorzugt, sonst created_at)
        $parsedDate = null;
        if ($exifDate) {
            $parsedDate = date('Y-m-d H:i:s', strtotime(str_replace(':', '-', substr($exifDate, 0, 10)) . substr($exifDate, 10)));
        }

        // Beschreibung aus Markdown oder leer
        $description = '';
        if (file_exists($mdPath)) {
            $description = trim(file_get_contents($mdPath));
        }

        $images[] = [
            'filename'    => $filename,
            'guid'        => $guid,
            'title'       => $data['title'] ?? '',
            'description' => nl2br(htmlspecialchars($description)),
            'tags'        => $data['tags'] ?? [],
            'rating'      => $data['rating'] ?? 0,
            'exif'        => $data['exif'] ?? [],
            'exif_date'   => $parsedDate,
            'created_at'  => $createdAt,
            'thumb'       => [
                's'  => "cache/images/{$guid}_S.jpg",
                'm'  => "cache/images/{$guid}_M.jpg",
                'l'  => "cache/images/{$guid}_L.jpg",
                'xl' => "cache/images/{$guid}_XL.jpg",
            ],
            'url' => "cache/images/{$guid}_" . strtoupper($defaultSize) . ".jpg"
        ];
    }

    // Sortieren nach EXIF-Datum > created_at
    usort($images, function ($a, $b) {
        $dateA = strtotime($a['exif_date'] ?? $a['created_at'] ?? '1970-01-01');
        $dateB = strtotime($b['exif_date'] ?? $b['created_at'] ?? '1970-01-01');
        return $dateB <=> $dateA;
    });

    return $images;
}
