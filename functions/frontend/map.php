<?php

function getGpsPoints(string $mediaPath = 'userdata/content/images/'): array {
    $points = [];

    $files = glob($mediaPath . '*.json');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $json = json_decode($content, true); // ← HIER: jetzt ist $json ein Array!

        if (
            json_last_error() === JSON_ERROR_NONE &&
            isset($json['exif']['GPS']['latitude'], $json['exif']['GPS']['longitude'])
        ) {
            $points[] = [
                'lat' => $json['exif']['GPS']['latitude'],
                'lng' => $json['exif']['GPS']['longitude'],
                'title' => $json['title'] ?? '',
                'description' => $json['description'] ?? '',
                'image' => "/cache/images/{$json['guid']}_M.jpg",
            ];
        }
    }

    return $points;
}