<?php

/**
 * Erstellt ein neues Page.
 *
 * @param string $title Titel des Pages.
 * @param string $content Inhalt des Pages.
 * @return bool|string Erfolgsnachricht oder false bei Fehler.
 */
function createPage($title, $content) {
    $baseDir = __DIR__ . '/../content/pages/';
    $date = date('Y-m-d');
    $slug = generateSlug($title);
    $PageDir = $baseDir . "{$slug}/";

    // Verzeichnis erstellen, wenn es nicht existiert
    if (!is_dir($PageDir) && !mkdir($PageDir, 0777, true)) {
        error_log("Konnte Verzeichnis nicht erstellen: $PageDir");
        return false;
    }

    $filePath = $PageDir . "{$slug}.json";

    // Page-Daten
    $PageData = [
        'title' => $title,
        'content' => $content,
        'created_at' => $date,
        'updated_at' => $date
    ];

    // Page speichern
    if (file_put_contents($filePath, json_encode($PageData, JSON_PRETTY_PRINT)) === false) {
        error_log("Konnte Page nicht speichern: $filePath");
        return false;
    }

    return "Seite erfolgreich erstellt";
}

/**
 * Liest ein Page.
 *
 * @param string $date Datum des Pages (YYYY-MM-DD).
 * @param string $title Titel des Pages.
 * @return array|false Page-Daten oder false bei Fehler.
 */
function readPage($date, $title) {
    $baseDir = __DIR__ . '/../content/pages/';
    $slug = generateSlug($title);
    $filePath = $baseDir . "{$slug}/{$slug}.json";

    if (!file_exists($filePath)) {
        error_log("Page nicht gefunden: $filePath");
        return false;
    }

    $data = json_decode(file_get_contents($filePath), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Fehler beim Lesen des Pages: " . json_last_error_msg());
        return false;
    }

    return $data;
}

/**
 * Aktualisiert ein Page.
 *
 * @param string $date Datum des Pages (YYYY-MM-DD).
 * @param string $title Titel des Pages.
 * @param string $newContent Neuer Inhalt des Pages.
 * @return bool|string Erfolgsnachricht oder false bei Fehler.
 */
function updatePage($date, $title, $newContent) {
    $baseDir = __DIR__ . '/../content/pages/';
    $slug = generateSlug($title);
    $filePath = $baseDir . "{$slug}/{$slug}.json";

    if (!file_exists($filePath)) {
        error_log("Page nicht gefunden: $filePath");
        return false;
    }

    $data = json_decode(file_get_contents($filePath), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Fehler beim Lesen des Pages: " . json_last_error_msg());
        return false;
    }

    // Page aktualisieren
    $data['content'] = $newContent;
    $data['updated_at'] = date('Y-m-d H:i:s');

    // Änderungen speichern
    if (file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT)) === false) {
        error_log("Konnte Page nicht aktualisieren: $filePath");
        return false;
    }

    return "Seite erfolgreich aktualisiert";
}

