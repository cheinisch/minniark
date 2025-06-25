<?php

header('Content-Type: application/json');

try {
    $baseDir = __DIR__;
    $tempDir = dirname($baseDir) . '/temp';
    $dashboardDir = $baseDir;
    $tempUpdateScript = $tempDir . '/update.php';
    $lockFile = $tempDir . '/update.lock';

    // Beim ersten Aufruf: nach /temp/update.php umleiten
    if (strpos(str_replace('\\', '/', __DIR__), '/temp') === false) {
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Vorherige update.php in /temp löschen (falls vorhanden)
        if (file_exists($tempUpdateScript)) {
            unlink($tempUpdateScript);
        }

        // Aktuelle Datei nach /temp kopieren
        copy(__FILE__, $tempUpdateScript);

        // Redirect-URL berechnen
        $redirectPath = dirname(dirname($_SERVER['PHP_SELF'])) . '/temp/update.php';
        echo json_encode(['success' => true, 'redirect' => $redirectPath]);
        exit;
    }

    // Schon aktualisiert?
    if (file_exists($lockFile)) {
        echo json_encode(['success' => true]);
        exit;
    }

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

    // Backup relevanter Verzeichnisse
    $backupDirs = ['userdata', 'cache', 'backup'];
    foreach ($backupDirs as $dir) {
        $src = dirname($baseDir) . "/$dir";
        if (is_dir($src)) {
            recurse_copy($src, $tempDir . "/$dir");
        }
    }

    // Alle alten Dateien löschen, außer /temp
    foreach (glob(dirname($baseDir) . '/*') as $file) {
        if (basename($file) !== 'temp') {
            deleteFileOrDir($file);
        }
    }

    // Neue Version herunterladen und entpacken
    file_put_contents($zipFile, file_get_contents($downloadUrl));

    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo(dirname($baseDir));
        $zip->close();
    } else {
        throw new Exception('Fehler beim Entpacken der ZIP-Datei.');
    }

    // Inhalte aus entpacktem Ordner verschieben
    foreach (glob(dirname($baseDir) . '/*') as $folder) {
        if (is_dir($folder) && preg_match('/minniark-/', basename($folder))) {
            foreach (glob($folder . '/*') as $item) {
                $dest = dirname($baseDir) . '/' . basename($item);
                if (!rename($item, $dest)) {
                    throw new Exception("Fehler beim Verschieben von $item nach $dest");
                }
            }
            deleteFileOrDir($folder);
        }
    }

    // Backups wiederherstellen
    foreach ($backupDirs as $dir) {
        $src = $tempDir . "/$dir";
        if (is_dir($src)) {
            recurse_copy($src, dirname($baseDir) . "/$dir");
        }
    }

    // Lock setzen (Update abgeschlossen)
    file_put_contents($lockFile, 'done');

    // Aufräumen: gezielte Dateien & Ordner in /temp löschen
    $toDelete = [
        $tempDir . '/cache',
        $tempDir . '/userdata',
        $tempDir . '/backup',
        $tempDir . '/update.zip',
        $tempDir . '/version.json',
        $tempDir . '/update.lock' // ← Ja, Lock kann gelöscht werden, weil Update abgeschlossen
    ];

    foreach ($toDelete as $path) {
        deleteFileOrDir($path);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Lock entfernen bei Fehler
    if (file_exists($lockFile)) {
        @unlink($lockFile);
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

// --- Hilfsfunktionen ---

function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    while (false !== ($file = readdir($dir))) {
        if ($file !== '.' && $file !== '..') {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) {
                recurse_copy($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
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
        @rmdir($target);
    } elseif (file_exists($target)) {
        @unlink($target);
    }
}
