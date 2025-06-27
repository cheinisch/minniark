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
        $exifDateRaw = $data['exif']['Date'] ?? null;
        
        $createdAt = $data['created_at'] ?? null;

        // Datum formatieren (EXIF bevorzugt, sonst created_at)

        // Fallback: EXIF-Datum nicht vorhanden oder ungültig
        if (empty($exifDateRaw) || strtolower(trim($exifDateRaw)) === 'unknown' || strtotime($exifDateRaw) === false) {
            // Wenn vorhanden: created_at ins exif-Feld eintragen
            if (!empty($createdAt)) {
                // Format: 'YYYY:MM:DD HH:MM:SS' (wie typisches EXIF-Datum)
                $data['exif']['Date'] = str_replace('-', ':', substr($createdAt, 0, 10)) . substr($createdAt, 10);
                $exifDateRaw = $data['exif']['Date'];
            }
        }

        // Formatieren fürs Sorting (oder Twig)
        $parsedDate = null;
        if (!empty($exifDateRaw) && strtotime($exifDateRaw) !== false) {
            $parsedDate = date('Y-m-d H:i:s', strtotime(str_replace(':', '-', substr($exifDateRaw, 0, 10)) . substr($exifDateRaw, 10)));
        }

        // Wenn kein gültiges EXIF-Datum → fallback auf created_at
        if (!$parsedDate && !empty($data['created_at'])) {
            $parsedDate = $data['created_at'];
        }

        

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
            'file' => "cache/images/{$guid}_" . strtoupper($defaultSize) . ".jpg",
            'url' => "/i/" . $filename,
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
