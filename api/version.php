<?php

header('Content-Type: application/json');

$tempDir = __DIR__ . '/../temp';
$tempFile = $tempDir . '/version.json';

// Prüfen, ob die Datei existiert und jünger als 24h ist
$needUpdate = true;
if (file_exists($tempFile)) {
    $fileTime = filemtime($tempFile);
    if ((time() - $fileTime) < 86400) {
        $needUpdate = false;
    }
}

if ($needUpdate) {
    // GitHub API URL für den aktuellsten Release
    $url = "https://api.github.com/repos/cheinisch/Image-Portfolio/releases/latest";

    // GitHub benötigt einen User-Agent Header, sonst schlägt die Anfrage fehl
    $options = [
        "http" => [
            "header" => "User-Agent: PHP\r\n"
        ]
    ];

    // Kontext erstellen und JSON abrufen
    $context = stream_context_create($options);
    $json = file_get_contents($url, false, $context);

    // JSON-Daten in ein PHP-Array umwandeln
    $data = json_decode($json, true);

    if (isset($data['tag_name'])) {
        $tagName = $data['tag_name'];
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Fehler: Tag-Name nicht gefunden!"]);
        exit;
    }

    // lokale Version lesen
    $versionFile = __DIR__ . '/../VERSION';

    if (file_exists($versionFile)) {
        $currentVersion = trim(file_get_contents($versionFile));
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Datei VERSION nicht gefunden!"]);
        exit;
    }

    $gitVersion = str_replace("v", "", $tagName);

    $newVersionAvailable = version_compare($currentVersion, $gitVersion, "<");

    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    $versionData = [
        "new_version_available" => $newVersionAvailable,
        "new_version_number" => $gitVersion,
        "new_version_url" => "https://github.com/cheinisch/Image-Portfolio/archive/refs/tags/{$tagName}.zip",
        "last_check" => time()
    ];

    file_put_contents($tempFile, json_encode($versionData, JSON_PRETTY_PRINT));

    echo json_encode($versionData);

} else {
    // Falls keine Aktualisierung nötig ist, vorhandenes JSON ausgeben
    echo file_get_contents($tempFile);
}

?>
