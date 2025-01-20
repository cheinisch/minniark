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
        'all' => 'inc/media_all.php',
        'upload' => 'inc/media_upload.php',
    ],
    'albums' => [
        'all' => 'inc/albums_all.php',
        'create' => 'inc/albums_create.php',
    ],
    'essays' => [
        'all' => 'inc/essays_all.php',
        'create' => 'inc/essays_create.php',
    ],
    'pages' => [
        'all' => 'inc/pages_all.php',
        'create' => 'inc/pages_create.php',
    ],
    'settings' => [
        'account' => 'inc/settings_account.php',
        'general' => 'inc/settings_general.php',
        'system' => 'inc/settings_system.php',
    ],
];

// Standarddatei für den Fall, dass kein oder ein ungültiger Parameter übergeben wurde
$defaultFile = 'inc/default.php';

// Eingebundene Datei bestimmen
$includeFile = $defaultFile;
if (isset($files[$mainMenu])) {
    if ($subMenu && isset($files[$mainMenu][$subMenu])) {
        $includeFile = $files[$mainMenu][$subMenu];
    } elseif (!$subMenu && isset($files[$mainMenu]['all'])) {
        $includeFile = $files[$mainMenu]['all'];
    }
}

// Submenu-Daten abrufen
$subMenuItems = $files[$mainMenu] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- UIkit CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.16.26/dist/css/uikit.min.css" />
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        .dashboard-layout {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            background-color: #333;
            color: #fff;
            width: 250px;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        .sidebar h3 {
            color: #fff;
        }
        .submenu {
            background-color: #444;
            color: #fff;
            width: 200px;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        .submenu h4 {
            color: #fff;
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .content {
            flex-grow: 1;
            background-color: #f5f5f5;
            padding: 20px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Main Sidebar -->
        <nav class="sidebar">
            <h3>Portfolio Admin</h3>
            <ul class="uk-nav uk-nav-default">
                <li class="<?= $mainMenu === 'media' ? 'uk-active' : '' ?>"><a href="?main=media">Media Library</a></li>
                <li class="<?= $mainMenu === 'albums' ? 'uk-active' : '' ?>"><a href="?main=albums">Albums</a></li>
                <li class="<?= $mainMenu === 'essays' ? 'uk-active' : '' ?>"><a href="?main=essays">Essays</a></li>
                <li class="<?= $mainMenu === 'pages' ? 'uk-active' : '' ?>"><a href="?main=pages">Pages</a></li>
                <li class="<?= $mainMenu === 'settings' ? 'uk-active' : '' ?>"><a href="?main=settings">Settings</a></li>
                <li class="uk-nav-divider"></li>
                <li><a href="login.php?logout=true">Logout</a></li>
            </ul>
        </nav>

        <!-- Submenu Sidebar -->
        <?php if (!empty($subMenuItems)): ?>
            <nav class="submenu">
                <h4><?= ucfirst($mainMenu) ?></h4>
                <ul class="uk-nav uk-nav-default">
                    <?php foreach ($subMenuItems as $key => $file): ?>
                        <li class="<?= $subMenu === $key ? 'uk-active' : '' ?>"><a href="?main=<?= $mainMenu ?>&sub=<?= $key ?>"><?= ucfirst($key) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="content">
            <!-- Header -->
            <header class="uk-background-muted uk-padding-small uk-box-shadow-small uk-flex uk-flex-between uk-flex-middle">
                <h2 class="uk-margin-remove">Welcome, Admin</h2>
                <a href="dashboard.php?main=settings&sub=account" class="uk-button uk-button-default">Profile</a>
            </header>

            <!-- Dynamic Content -->
            <div class="uk-container uk-margin-top">
                <h3 class="uk-heading-line"><span><?= ucfirst($mainMenu) ?> Overview</span></h3>
                <!-- Dynamische Inhalte -->
                <?php
                if (file_exists($includeFile)) {
                    include $includeFile;
                } else {
                    echo "<p>Content not available for this section.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- UIkit JS -->
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.16.26/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.16.26/dist/js/uikit-icons.min.js"></script>
</body>
</html>