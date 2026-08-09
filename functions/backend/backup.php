<?php

    function generateBackup(): bool
    {
        error_log("Generate Backup started");
        $prefix = read_prefix();
        error_log("Backup Prefix: " . $prefix);
        $timestamp = date('Y-m-d_H-i');
        $filename = "{$prefix}_{$timestamp}.zip";
        $backupDir = __DIR__ . '/../../backup';
        $backupPath = "$backupDir/$filename";

        error_log("Filepath: " . $backupPath);

        if (!is_dir($backupDir)) {
            error_log("Create Backup Folder");
            if (!mkdir($backupDir, 0775, true)) {
                error_log("Backup folder could not be created.");
                return false;
            }
        }

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


    function restoreBackup(string $filename): bool
    {
        // ── Security: validate filename ────────────────────────────────────
        // Allow only safe characters — no path separators, no null bytes, no dots at start
        if (
            !preg_match('/^[a-zA-Z0-9_\-\.]+\.zip$/', $filename) ||
            str_contains($filename, '..')  ||
            str_contains($filename, '/')   ||
            str_contains($filename, '\\')  ||
            str_contains($filename, "\0")
        ) {
            error_log("restoreBackup: rejected unsafe filename: " . $filename);
            return false;
        }

        $backupDir  = realpath(__DIR__ . '/../../backup');
        $restorePath = realpath(__DIR__ . '/../../userdata');

        if (!$backupDir || !$restorePath) {
            error_log("restoreBackup: could not resolve backup or restore path.");
            return false;
        }

        $backupPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        // Confirm resolved path is still inside backup dir
        if (!str_starts_with(realpath($backupPath) ?: '', $backupDir . DIRECTORY_SEPARATOR)) {
            error_log("restoreBackup: path traversal detected for: " . $filename);
            return false;
        }

        if (!file_exists($backupPath)) {
            error_log("restoreBackup: file not found: " . $backupPath);
            return false;
        }

        $zip = new ZipArchive();
        if ($zip->open($backupPath) !== TRUE) {
            error_log("restoreBackup: could not open ZIP: " . $backupPath);
            return false;
        }

        // ── Zip-Slip protection: validate all entry paths ──────────────────
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (
                str_starts_with($entry, '/') ||
                str_contains($entry, '..') ||
                str_contains($entry, "\0")
            ) {
                $zip->close();
                error_log("restoreBackup: ZIP contains unsafe path: " . $entry);
                return false;
            }

            // Normalise and verify resolved path stays inside restorePath
            $resolved = $restorePath . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $entry);
            if (!str_starts_with($resolved, $restorePath . DIRECTORY_SEPARATOR)) {
                $zip->close();
                error_log("restoreBackup: path traversal in ZIP entry: " . $entry);
                return false;
            }
        }

        $zip->extractTo($restorePath);
        $zip->close();

        error_log("restoreBackup: restored successfully from " . $filename);
        return true;
    }
