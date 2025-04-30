<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    $filename = basename($_POST['filename']); // schützt vor Pfadangriffen
    $backupPath = __DIR__ . '/../backup/' . $filename;
    $restorePath = __DIR__ . '/../userdata';

    if (!file_exists($backupPath)) {
        echo json_encode(['success' => false, 'message' => 'Backup-Datei existiert nicht.']);
        exit;
    }

    $zip = new ZipArchive();
    if ($zip->open($backupPath) === TRUE) {
        // Vorher ggf. aufräumen oder sichern?
        $zip->extractTo($restorePath);
        $zip->close();

        echo json_encode(['success' => true]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'ZIP konnte nicht geöffnet werden.']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Ungültiger Request.']);
