<?php
require_once __DIR__ . '/../../functions/functions.php'; // Funktionen einbinden

// Verzeichnis für die Seiten
$pagesDir = __DIR__ . '/../../content/pages/';
$pages = readAllPages($pagesDir);
?>

<div class="uk-container uk-margin-top">
    <h2>Alle Seiten</h2>

    <div class="uk-grid-match uk-child-width-1-1@m uk-grid-small" uk-grid>
        <?php if (!empty($pages)): ?>
            <?php foreach ($pages as $page): ?>
                <?php
                    $folder = $page['folder']; // Dies muss aus der Funktion readAllEssays bereitgestellt werden
                ?>
                <div>
                    <div class="uk-card uk-card-default uk-card-body uk-card-hover">
                        <h3 class="uk-card-title"><?= htmlspecialchars($page['title']) ?></h3>
                        <p><?= htmlspecialchars(substr($page['content'], 0, 200)) ?>...</p>
                        <p class="uk-text-meta">Erstellt am: <?= htmlspecialchars($page['created_at']) ?></p>
                        <a href="?main=pages&sub=edit&file=<?= urlencode($folder . '/' .  $page['filename']) ?>" class="uk-button uk-button-default">Bearbeiten</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Keine Seiten gefunden.</p>
        <?php endif; ?>
    </div>
</div>