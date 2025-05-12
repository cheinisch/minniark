<?php

function getTimelineImagesFromJson(string $mediaPath = 'userdata/content/images/', string $defaultSize = 'm'): array {

    $images = [];

    $imageFiles = glob($mediaPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

    foreach ($imageFiles as $imgPath) {
        $filename = basename($imgPath);
        $jsonPath = $mediaPath . pathinfo($filename, PATHINFO_FILENAME) . '.json';

        if (file_exists($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $guid = $json['guid'] ?? pathinfo($filename, PATHINFO_FILENAME);

                // EXIF-Datum übernehmen
                if (!isset($json['exif_date']) && isset($json['exif']['Date'])) {
                    $json['exif_date'] = date('Y-m-d H:i:s', strtotime(str_replace(':', '-', substr($json['exif']['Date'], 0, 10)) . substr($json['exif']['Date'], 10)));
                }

                // Bildpfade
                $json['thumb'] = [
                    's' => "cache/images/{$guid}_S.jpg",
                    'm' => "cache/images/{$guid}_M.jpg",
                    'l' => "cache/images/{$guid}_L.jpg",
                    'xl' => "cache/images/{$guid}_XL.jpg",
                ];

                $json['url'] = $json['thumb'][$defaultSize] ?? $imgPath;
                $json['description'] = nl2br(htmlspecialchars($json['description']));
                $images[] = $json;
            }
        }
    }

    // Sortieren nach exif_date > upload_date
    usort($images, function ($a, $b) {
        $dateA = strtotime($a['exif_date'] ?? $a['upload_date'] ?? '1970-01-01');
        $dateB = strtotime($b['exif_date'] ?? $b['upload_date'] ?? '1970-01-01');
        return $dateB <=> $dateA;
    });

    return $images;
}
