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

        echo json_encode(['success' => true, 'redirect' => '/temp/update.php']);
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

    // Nach Entpacken: Ordner finden und Inhalte verschieben
    $extractedFolder = null;
    foreach (glob(dirname($baseDir) . '/*') as $folder) {
        if (is_dir($folder) && preg_match('/minniark-/', basename($folder))) {
            foreach (glob($folder . '/*') as $item) {
                $dest = dirname($baseDir) . '/' . basename($item);
                if (!rename($item, $dest)) {
                    throw new Exception("Fehler beim Verschieben von $item nach $dest");
                }
            }
            deleteFileOrDir($folder); // Stelle sicher, dass der Ordner vollständig gelöscht wird
        }
    }

    if ($extractedFolder) {
        foreach (glob($extractedFolder . '/*') as $item) {
            $dest = dirname($baseDir) . '/' . basename($item);
            rename($item, $dest);
        }
        rmdir($extractedFolder);
    }

    foreach ($backupDirs as $dir) {
        if (is_dir($tempDir . "/$dir")) {
            recurse_copy($tempDir . "/$dir", dirname($baseDir) . "/$dir");
        }
    }

    deleteFileOrDir($tempDir);
    mkdir($tempDir, 0755, true);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

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
