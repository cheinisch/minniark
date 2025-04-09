<?php 

function getTimelineImages(string $path = 'media/'): array {
    $images = [];
    $files = glob($path . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

    foreach ($files as $file) {
        $images[] = [
            'url' => $file,
            'date' => date('Y-m-d', filemtime($file)), // oder EXIF-Datum bei Bedarf
        ];
    }

    // Optional nach Datum sortieren (neueste zuerst)
    usort($images, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

    return $images;
}