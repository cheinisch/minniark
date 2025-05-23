<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$pluginKey = $_POST['plugin'] ?? '';
$pluginDir = __DIR__ . '/../../userdata/plugins/' . basename($pluginKey);
$pluginJson = $pluginDir . '/plugin.json';
$settingsPath = $pluginDir . '/settings.json';

if (!is_dir($pluginDir) || !file_exists($pluginJson)) {
    http_response_code(400);
    exit('Ungültiges Plugin.');
}

// Lade Plugin-Definition
$pluginData = json_decode(file_get_contents($pluginJson), true);
if (!is_array($pluginData)) {
    http_response_code(500);
    exit('Fehlerhafte plugin.json');
}

// Lade bisherige Einstellungen (falls vorhanden)
$existingSettings = file_exists($settingsPath)
    ? json_decode(file_get_contents($settingsPath), true)
    : [];

$newSettings = [];

// Aktivierung aus verstecktem Input speichern
$newSettings['enabled'] = ($_POST['enabled'] ?? '') === 'true';

// Felder iterieren
foreach (($pluginData['settings']['fields'] ?? []) as $field) {
    $key = $field['key'];
    $type = $field['type'] ?? 'text';

    switch ($type) {
        case 'password':
            if (!empty($_POST[$key])) {
                $newSettings[$key . 'hash'] = password_hash($_POST[$key], PASSWORD_DEFAULT);
            } elseif (isset($existingSettings[$key . 'hash'])) {
                // Passwort leer → alten Hash behalten
                $newSettings[$key . 'hash'] = $existingSettings[$key . 'hash'];
            }
            break;

        case 'toggle':
            $newSettings[$key] = isset($_POST[$key]) && $_POST[$key] === '1';
            break;

        default:
            $newSettings[$key] = $_POST[$key] ?? ($field['default'] ?? '');
            break;
    }
}

// Speichern
file_put_contents($settingsPath, json_encode($newSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Weiterleitung zurück zum Adminbereich
header('Location: ../dashboard-plugin.php?plugin=' . urlencode($pluginKey) . '&saved=1');
exit;
