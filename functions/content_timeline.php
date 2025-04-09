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
                        's' => "cache/images/{$guid}_s.jpg",
                        'm' => "cache/images/{$guid}_m.jpg",
                        'l' => "cache/images/{$guid}_l.jpg",
                        'xl' => "cache/images/{$guid}_xl.jpg",
                    ];

                    // Standardgröße (optional)
                    $json['url'] = $json['thumb'][$defaultSize] ?? $imgPath;

                    $images[] = $json;
                }
            }
        }
    }

    // Nach Upload-Datum sortieren (neueste oben)
    usort($images, fn($a, $b) => strtotime($b['upload_date']) <=> strtotime($a['upload_date']));

    return $images;
}

