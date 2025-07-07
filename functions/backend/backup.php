<?php

    function generateBackup(): bool
    {
        error_log("Generate Backup started");
        $prefix = read_prefix(); // z. B. 'projectname'
        error_log("Backup Prefix: " . $prefix);
        $timestamp = date('Y-m-d_H-i');
        $filename = "{$prefix}_{$timestamp}.zip";
        $backupDir = __DIR__ . '/../../backup';
        $backupPath = "$backupDir/$filename";

        error_log("Filepath: " . $backupPath);

        // Sicherstellen, dass das Verzeichnis existiert
        if (!is_dir($backupDir)) {
            error_log("Create Backup Folder");
            if (!mkdir($backupDir, 0775, true)) {
                error_log("Backup folder could not be created.");
                return false;
            }
        }

        // ZIP erstellen (z. B. von /userdata)
        $sourceDir = realpath(__DIR__ . '/../../userdata');

        $zip = new ZipArchive();
        if ($zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            error_log("ZIP konnte nicht erstellt werden.");
            return false;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($sourceDir) + 1);
            if (!$zip->addFile($filePath, $relativePath)) {
                error_log("Datei konnte nicht zum ZIP hinzugefügt werden: " . $filePath);
                $zip->close();
                return false;
            }
        }

        $zip->close();
        error_log("Backup erfolgreich erstellt.");
        return true;
    }


    function restoreBackup($filename)
    {
        $backupPath = __DIR__ . '/../../backup/' . $filename;
        $restorePath = __DIR__ . '/../../userdata';

        if (!file_exists($backupPath)) {
            return false;
            exit;
        }

        $zip = new ZipArchive();
        if ($zip->open($backupPath) === TRUE) {
            // Vorher ggf. aufräumen oder sichern?
            $zip->extractTo($restorePath);
            $zip->close();

            return true;
            exit;
        } else {
            return false;
            exit;
        }
    }
