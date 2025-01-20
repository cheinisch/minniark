<?php
session_start();

// Dummy-Daten für Seiten (ersetze dies mit einer Datenbank oder einer JSON-Datei)
$pages = [
    ['title' => 'Page 1', 'content' => 'Inhalt von Page 1'],
    ['title' => 'Page 2', 'content' => 'Inhalt von Page 2'],
    ['title' => 'Page 3', 'content' => 'Inhalt von Page 3']
];

$selectedPage = $_GET['page'] ?? 0;

// Sicherstellen, dass der ausgewählte Index gültig ist
if (!isset($pages[$selectedPage])) {
    $selectedPage = 0;
}

$currentPage = $pages[$selectedPage];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pages</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/css/uikit.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/js/uikit.min.js"></script>
</head>
<body>

<div class="uk-grid-collapse" uk-grid>
    <!-- Sidebar -->
    <div class="uk-width-1-4 uk-background-muted uk-padding-small">
        <h3 class="uk-heading-line"><span>Pages</span></h3>
        <ul class="uk-nav uk-nav-default">
            <?php foreach ($pages as $index => $page): ?>
                <li class="<?= $index == $selectedPage ? 'uk-active' : '' ?>">
                    <a href="?page=<?= $index ?>"><?= htmlspecialchars($page['title']) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="uk-width-3-4 uk-padding">
        <h1 class="uk-heading-medium"><?= htmlspecialchars($currentPage['title']) ?></h1>
        <p><?= htmlspecialchars($currentPage['content']) ?></p>
    </div>
</div>

</body>
</html>
