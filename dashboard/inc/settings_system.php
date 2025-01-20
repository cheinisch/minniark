<?php
// Systeminformationen dynamisch ermitteln
$systemInfo = [
    "server_software" => $_SERVER['SERVER_SOFTWARE'] ?? 'Unbekannt',
    "ssl_connection" => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    "php_version" => PHP_VERSION,
    "php_extensions" => get_loaded_extensions(),
    "php_image_processing_libraryies" => [],
    "php_memory_limit" => ini_get('memory_limit'),
    "php_max_upload_size" => ini_get('upload_max_filesize'),
    "php_file_uploads" => ini_get('file_uploads') == '1'
];

// PHP-Bildverarbeitungsbibliotheken prüfen
if (extension_loaded('imagick')) {
    $imagick = new Imagick();
    $systemInfo["php_image_processing_libraryies"][] = [
        "name" => "Imagick",
        "version" => $imagick->getVersion()['versionString'] ?? 'Unbekannt',
        "formats" => $imagick->queryFormats()
    ];
}
if (extension_loaded('gd')) {
    $gdInfo = gd_info();
    $systemInfo["php_image_processing_libraryies"][] = [
        "name" => "GD",
        "version" => $gdInfo['GD Version'] ?? 'Unbekannt',
        "formats" => array_keys(array_filter($gdInfo, fn($key) => str_contains($key, 'Support')))
    ];
}
?>

<div class="table-container">
    <table class="uk-table uk-table-divider uk-table-small">
        <tbody>
            <tr>
                <td>Server Software</td>
                <td><?= htmlspecialchars($systemInfo['server_software']) ?></td>
                <td><span class="check-badge">check</span></td>
            </tr>
            <tr>
                <td>SSL Connection</td>
                <td><?= $systemInfo['ssl_connection'] ? 'active' : 'inactive' ?></td>
                <td><span class="check-badge">check</span></td>
            </tr>
            <tr>
                <td>PHP Version</td>
                <td><?= htmlspecialchars($systemInfo['php_version']) ?> <a href="#" class="uk-link-muted">(show extensions)</a></td>
                <td><span class="check-badge">check</span></td>
            </tr>
            <tr>
                <td>Image Processing Libraries</td>
                <td>
                    <?php foreach ($systemInfo['php_image_processing_libraryies'] as $library): ?>
                        <?= htmlspecialchars($library['name']) ?> <?= htmlspecialchars($library['version']) ?> 
                        (<?= implode(', ', array_map('htmlspecialchars', $library['formats'])) ?>)<br>
                    <?php endforeach; ?>
                </td>
                <td><span class="check-badge">check</span></td>
            </tr>
            <tr>
                <td>Memory Limit</td>
                <td><?= htmlspecialchars($systemInfo['php_memory_limit']) ?></td>
                <td>
                    <span class="<?= (int)str_replace('M', '', $systemInfo['php_memory_limit']) >= 64 ? 'check-badge' : 'warning-badge' ?>">
                        <?= (int)str_replace('M', '', $systemInfo['php_memory_limit']) >= 64 ? 'check' : 'at least 64MB recommended' ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>Max Upload Size</td>
                <td><?= htmlspecialchars($systemInfo['php_max_upload_size']) ?></td>
                <td><span class="check-badge">check</span></td>
            </tr>
            <tr>
                <td>Uploads</td>
                <td><?= $systemInfo['php_file_uploads'] ? 'activated' : 'deactivated' ?></td>
                <td><span class="check-badge">check</span></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="uk-margin-top">
    <button class="uk-button uk-button-primary">Clear Website Image Cache</button>
</div>