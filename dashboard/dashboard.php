<?php
session_start();

// Dummy-Login-Check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Aktive Hauptnavigation und Untermenü bestimmen
$mainMenu = $_GET['main'] ?? 'media';
$subMenu = $_GET['sub'] ?? null;

// Mögliche Dateien für Main Content
$files = [
    'media' => [
        'all' => 'media_all.php',
        'upload' => 'media_upload.php',
    ],
    'albums' => [
        'all' => 'albums_all.php',
        'create' => 'albums_create.php',
    ],
    'essays' => [
        'all' => 'essays_all.php',
        'create' => 'essays_create.php',
    ],
    'pages' => [
        'all' => 'pages_all.php',
        'create' => 'pages_create.php',
    ],
    'settings' => [
        'account' => 'settings_account.php',
        'general' => 'settings_general.php',
        'system' => 'settings_system.php',
    ],
];

// Standarddatei für den Fall, dass kein oder ein ungültiger Parameter übergeben wurde
$defaultFile = 'default.php';

// Eingebundene Datei bestimmen
$includeFile = $defaultFile;
if (isset($files[$mainMenu])) {
    if ($subMenu && isset($files[$mainMenu][$subMenu])) {
        $includeFile = $files[$mainMenu][$subMenu];
    } elseif (!$subMenu && isset($files[$mainMenu]['all'])) {
        $includeFile = $files[$mainMenu]['all'];
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/css/uikit.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/js/uikit-icons.min.js"></script>
    <style>
        .full-height {
            height: 100vh;
        }
        .flex-column {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
    </style>
</head>
<body>

<div class="uk-grid-collapse" uk-grid>
    <!-- Main Sidebar (erste Ebene) -->
    <div class="uk-width-auto uk-background-muted uk-padding-small full-height flex-column">
        <div>
            <h3 class="uk-heading-line"><span>Navigation</span></h3>
            <ul class="uk-nav uk-nav-default">
                <li class="<?= $mainMenu === 'media' ? 'uk-active' : '' ?>"><a href="?main=media">Media</a></li>
                <li class="<?= $mainMenu === 'albums' ? 'uk-active' : '' ?>"><a href="?main=albums">Albums</a></li>
                <li class="<?= $mainMenu === 'essays' ? 'uk-active' : '' ?>"><a href="?main=essays">Essays</a></li>
                <li class="<?= $mainMenu === 'pages' ? 'uk-active' : '' ?>"><a href="?main=pages">Pages</a></li>
            </ul>
        </div>
        <div>
            <ul class="uk-nav uk-nav-default">
                <li><a href="?main=settings"><span uk-icon="settings"></span> Settings</a></li>
                <li><a href="login.php?logout=true"><span uk-icon="sign-out"></span> Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Submenu Sidebar (zweite Ebene) -->
    <div class="uk-width-auto uk-background-secondary uk-light uk-padding-small full-height">
        <h3 class="uk-heading-line uk-text-light"><span>Untermenü</span></h3>
        <ul class="uk-nav uk-nav-default">
            <?php if (isset($files[$mainMenu])): ?>
                <?php foreach ($files[$mainMenu] as $key => $file): ?>
                    <li class="<?= $subMenu === $key ? 'uk-active' : '' ?>">
                        <a href="?main=<?= $mainMenu ?>&sub=<?= $key ?>"><?= htmlspecialchars(ucfirst($key)) ?></a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li><em>Keine Untermenüs verfügbar</em></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="uk-width-expand uk-padding full-height uk-overflow-auto">
        <?php
        // PHP-Datei dynamisch einbinden
        if (file_exists(__DIR__ . '/inc/' . $includeFile)) {
            include __DIR__ . '/inc/' . $includeFile;
        } else {
            echo '<h1>Datei nicht gefunden</h1>';
        }
        ?>
    </div>
</div>

</body>
</html>
