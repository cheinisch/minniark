<?php

function read_backupfiles(): array
{
    $backupdir = __DIR__ . '/../../backup';
    $files = [];

    if (!is_dir($backupdir)) {
        return [];
    }

    // Alle .zip-Dateien auslesen
    foreach (glob($backupdir . '/*.zip') as $filePath) {
        $filename = basename($filePath);

        // Beispiel-Format: prefix_2024-04-29_12:30.zip
        if (preg_match('/_(\d{4}-\d{2}-\d{2})_(\d{2}-\d{2})\.zip$/', $filename, $matches)) {
            $dateStr = $matches[1] . ' ' . str_replace('-', ':', $matches[2]); // z. B. "2024-04-29 12:30"
            $timestamp = strtotime($dateStr);

            $files[] = [
                'name' => $filename,
                'path' => $filePath,
                'timestamp' => $timestamp
            ];
        }
    }

    // Nach Timestamp DESC sortieren (neueste zuerst)
    usort($files, function ($a, $b) {
        return $b['timestamp'] <=> $a['timestamp'];
    });

    return $files;
}
