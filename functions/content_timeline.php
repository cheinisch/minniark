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

function getBlogPosts(string $essaysPath = 'content/essays/'): array {
    $posts = [];

    foreach (glob($essaysPath . '*/') as $dir) {
        $folderName = basename($dir); // z. B. "2025-04-14_testeintrag"
        $jsonFile = $dir . $folderName . '.json';

        if (file_exists($jsonFile)) {
            $json = json_decode(file_get_contents($jsonFile), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                // Datum und Slug aus dem Ordnernamen extrahieren
                if (preg_match('/^(\d{4}-\d{2}-\d{2})_(.+)$/', $folderName, $matches)) {
                    $json['date'] = $matches[1];           // "2025-04-14"
                    $json['slug'] = $matches[2];           // "testeintrag"
                    $json['folder'] = $folderName;         // für Linkaufbau
                    $posts[] = $json;
                }
            }
        }
    }

    // Nach Datum sortieren (neueste zuerst)
    usort($posts, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));

    return $posts;
}
