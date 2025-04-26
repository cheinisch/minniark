<?php

/**
 * Erstellt ein neues Essay.
 *
 * @param string $title Titel des Essays.
 * @param string $content Inhalt des Essays.
 * @return bool|string Erfolgsnachricht oder false bei Fehler.
 */
function createEssay($title, $content) {
    $baseDir = __DIR__ . '/../userdata/content/essays/';
    $date = date('Y-m-d');
    $slug = generateSlug($title);
    $essayDir = $baseDir . "{$date}_{$slug}/";

    // Verzeichnis erstellen, wenn es nicht existiert
    if (!is_dir($essayDir) && !mkdir($essayDir, 0777, true)) {
        error_log("Konnte Verzeichnis nicht erstellen: $essayDir");
        return false;
    }

    $filePath = $essayDir . "{$slug}.json";

    // Essay-Daten
    $essayData = [
        'title' => $title,
        'content' => $content,
        'created_at' => $date,
        'updated_at' => $date
    ];

    // Essay speichern
    if (file_put_contents($filePath, json_encode($essayData, JSON_PRETTY_PRINT)) === false) {
        error_log("Konnte Essay nicht speichern: $filePath");
        return false;
    }

    return "Essay erfolgreich erstellt";
}

/**
 * Liest ein Essay.
 *
 * @param string $date Datum des Essays (YYYY-MM-DD).
 * @param string $title Titel des Essays.
 * @return array|false Essay-Daten oder false bei Fehler.
 */
function readEssay($date, $title) {
    $baseDir = __DIR__ . '/../userdata/content/essays/';
    $slug = generateSlug($title);
    $filePath = $baseDir . "{$date}_{$slug}/{$slug}.json";

    if (!file_exists($filePath)) {
        error_log("Essay nicht gefunden: $filePath");
        return false;
    }

    $data = json_decode(file_get_contents($filePath), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Fehler beim Lesen des Essays: " . json_last_error_msg());
        return false;
    }

    return $data;
}

/**
 * Liest alle Essays aus dem Verzeichnis.
 *
 * @param string $essaysDir Pfad zum Essays-Verzeichnis.
 * @return array Liste der Essays (Titel, Inhalt, Metadaten).
 */
function readAllEssays($essaysDir) {
    $essays = [];

    if (!is_dir($essaysDir)) {
        error_log("Verzeichnis nicht gefunden: $essaysDir");
        return $essays;
    }

    $folders = scandir($essaysDir);

    foreach ($folders as $folder) {
        if ($folder === '.' || $folder === '..') {
            continue; // "." und ".." ignorieren
        }

        $essayPath = $essaysDir . DIRECTORY_SEPARATOR . $folder;
        if (is_dir($essayPath)) {
            $files = glob($essayPath . DIRECTORY_SEPARATOR . '*.json');
            foreach ($files as $file) {
                $essayData = readEssayFromFile($file);
                if ($essayData !== false) {
                    $essayData['folder'] = $folder; // Ordnername hinzufügen
                    $essayData['filename'] = basename($file); // Dateiname hinzufügen
                    $essays[] = $essayData;
                }
            }
        }
    }

    return $essays;
}


/**
 * Liest ein einzelnes Essay aus einer Datei.
 *
 * @param string $filePath Pfad zur Essay-Datei.
 * @return array|false Array mit Essay-Daten oder false bei Fehler.
 */
function readEssayFromFile($filePath) {
    if (!file_exists($filePath)) {
        error_log("Datei nicht gefunden: $filePath");
        return false;
    }

    $essayData = json_decode(file_get_contents($filePath), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Fehler beim Lesen der JSON-Daten: " . json_last_error_msg());
        return false;
    }

    return $essayData;
}

/**
 * Aktualisiert ein Essay.
 *
 * @param string $date Datum des Essays (YYYY-MM-DD).
 * @param string $title Titel des Essays.
 * @param string $newContent Neuer Inhalt des Essays.
 * @return bool|string Erfolgsnachricht oder false bei Fehler.
 */
function updateEssay($date, $title, $newContent) {
    $baseDir = __DIR__ . '/../userdata/content/essays/';
    $slug = generateSlug($title);
    $filePath = $baseDir . "{$date}_{$slug}/{$slug}.json";

    if (!file_exists($filePath)) {
        error_log("Essay nicht gefunden: $filePath");
        return false;
    }

    $data = json_decode(file_get_contents($filePath), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Fehler beim Lesen des Essays: " . json_last_error_msg());
        return false;
    }

    // Essay aktualisieren
    $data['content'] = $newContent;
    $data['updated_at'] = date('Y-m-d H:i:s');

    // Änderungen speichern
    if (file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT)) === false) {
        error_log("Konnte Essay nicht aktualisieren: $filePath");
        return false;
    }

    return "Essay erfolgreich aktualisiert: $filePath";
}
