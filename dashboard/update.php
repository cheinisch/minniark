<?php

header('Content-Type: application/json');

try {
    $baseDir = __DIR__;
    $tempDir = dirname($baseDir) . '/temp';
    $dashboardDir = $baseDir;
    $tempUpdateScript = $tempDir . '/update.php';
    $lockFile = $tempDir . '/update.lock';

    // Wenn das Skript noch nicht aus /temp gestartet wurde
    if (strpos(__DIR__, '/temp') === false) {
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Skript nach /temp kopieren
        copy(__FILE__, $tempUpdateScript);

        echo json_encode(['success' => true, 'redirect' => '/temp/update.php']);
        exit;
    }

    // Wenn das Lockfile existiert, wurde Update bereits durchgeführt
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

    $backupDirs = ['userdata', 'cache', 'backup'];
    foreach ($backupDirs as $dir) {
        if (is_dir(dirname($baseDir) . "/$dir")) {
            recurse_copy(dirname($baseDir) . "/$dir", $tempDir . "/$dir");
        }
    }

    // Bestehende Dateien löschen, außer /temp
    foreach (glob(dirname($baseDir) . '/*') as $file) {
        if (basename($file) !== 'temp') {
            deleteFileOrDir($file);
        }
    }

    file_put_contents($zipFile, file_get_contents($downloadUrl));

    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo(dirname($baseDir));
        $zip->close();
    } else {
        throw new Exception('Fehler beim Entpacken der ZIP-Datei.');
    }

    // Inhalte aus dem entpackten Ordner in das Zielverzeichnis verschieben
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

    // Backups zurückkopieren
    foreach ($backupDirs as $dir) {
        if (is_dir($tempDir . "/$dir")) {
            recurse_copy($tempDir . "/$dir", dirname($baseDir) . "/$dir");
        }
    }

    // Aufräumen
    deleteFileOrDir($tempDir);
    mkdir($tempDir, 0755, true); // Wiederherstellen für Lockfile

    // Lockfile erstellen (jetzt, wo temp wieder da ist)
    file_put_contents($lockFile, 'done');

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Lock entfernen bei Fehlern, um Neustart zu ermöglichen
    if (file_exists($lockFile)) {
        unlink($lockFile);
    }

    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    while (false !== ($file = readdir($dir))) {
        if ($file != '.' && $file != '..') {
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
        rmdir($target);
    } elseif (file_exists($target)) {
        unlink($target);
    }
}
