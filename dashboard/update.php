<?php

header('Content-Type: application/json');

try {
    $baseDir = __DIR__;
    $tempDir = dirname($baseDir) . '/temp';
    $dashboardDir = $baseDir;
    $tempUpdateScript = $tempDir . '/update.php';

    // Wenn das Skript noch nicht aus /temp gestartet wurde
    if (strpos(__DIR__, '/temp') === false) {
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Kopiere diese Datei nach /temp
        copy(__FILE__, $tempUpdateScript);

        // Ausgabe für JS: Weiterleitung an neues Skript
        echo json_encode(['success' => true, 'redirect' => '/temp/update.php']);
        exit;
    }

    // Weiter mit Update, da wir in /temp sind
    $versionFile = $tempDir . '/version.json';

    if (!file_exists($versionFile)) {
        throw new Exception('Version-Datei nicht gefunden.');
    }

    $versionData = json_decode(file_get_contents($versionFile), true);

    if (empty($versionData['new_version_url'])) {
        throw new Exception('Download-URL nicht gefunden.');
    }

    $downloadUrl = $versionData['new_version_url'];
    $zipFile = $tempDir . '/update.zip';

    // Backup wichtiger Verzeichnisse
    $backupDirs = ['content', 'userdata', 'cache'];
    foreach ($backupDirs as $dir) {
        if (is_dir(dirname($baseDir) . "/$dir")) {
            recurse_copy(dirname($baseDir) . "/$dir", $tempDir . "/$dir");
        }
    }

    // Alle Dateien und Ordner außer /temp löschen
    foreach (glob(dirname($baseDir) . '/*') as $file) {
        if (basename($file) !== 'temp') {
            deleteFileOrDir($file);
        }
    }

    // ZIP herunterladen
    file_put_contents($zipFile, file_get_contents($downloadUrl));

    // Entpacken
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo(dirname($baseDir));
        $zip->close();
    } else {
        throw new Exception('Fehler beim Entpacken der ZIP-Datei.');
    }

    // Backup-Verzeichnisse wiederherstellen
    foreach ($backupDirs as $dir) {
        if (is_dir($tempDir . "/$dir")) {
            recurse_copy($tempDir . "/$dir", dirname($baseDir) . "/$dir");
        }
    }

    // Temp-Verzeichnis leeren
    deleteFileOrDir($tempDir);
    mkdir($tempDir, 0755, true);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Hilfsfunktionen
function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function deleteFileOrDir($target) {
    if (is_dir($target)) {
        $files = array_diff(scandir($target), ['.', '..']);
        foreach ($files as $file) {
            deleteFileOrDir($target . '/' . $file);
        }
        rmdir($target);
    } elseif (file_exists($target)) {
        unlink($target);
    }
}

?>
