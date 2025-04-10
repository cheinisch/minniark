<?php

function getTimelineImagesFromJson(string $mediaPath = 'content/images/', string $defaultSize = 'm'): array {
    $images = [];

    $imageFiles = glob($mediaPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

    foreach ($imageFiles as $imgPath) {
        $filename = basename($imgPath);
        $jsonPath = $mediaPath . pathinfo($filename, PATHINFO_FILENAME) . '.json';

        if (file_exists($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $guid = $json['guid'] ?? null;

                if ($guid) {
                    // 🔽 HIER: alle Bildgrößen hinzufügen
                    $json['thumb'] = [
                        's' => "cache/images/{$guid}_S.jpg",
                        'm' => "cache/images/{$guid}_M.jpg",
                        'l' => "cache/images/{$guid}_L.jpg",
                        'xl' => "cache/images/{$guid}_XL.jpg",
                    ];

                    // Standardgröße (optional)
                    $json['url'] = $json['thumb'][$defaultSize] ?? $imgPath;

                    $images[] = $json;
                }
            }
        }
    }

    // Nach Upload-Datum sortieren (neueste oben)
    usort($images, function ($a, $b) {
        $dateA = strtotime($a['exif_date'] ?? $a['upload_date'] ?? '1970-01-01');
        $dateB = strtotime($b['exif_date'] ?? $b['upload_date'] ?? '1970-01-01');
        return $dateB <=> $dateA;
    });

    return $images;
}




// Übergabe an Twig:
echo $twig->render($template, array_merge([
    'title' => ucfirst($uri) ?: 'Home',
    'site_title' => $settings['site_title'] ?? 'Image Portfolio',
    'theme' => $settings['theme'] ?? 'classic',
], $data ?? []));
