<?php
require_once __DIR__ . '/../../functions/functions.php'; // Funktionen einbinden

// Verzeichnis für die Essays
$essaysDir = __DIR__ . '/../../content/essays/';
$essays = readAllEssays($essaysDir);
?>

<div class="uk-container uk-margin-top">
    <h2>Alle Essays</h2>

    <div class="uk-grid-match uk-child-width-1-1@m uk-grid-small" uk-grid>
        <?php if (!empty($essays)): ?>
            <?php foreach ($essays as $essay): ?>
                <?php
                    $folder = $essay['folder']; // Dies muss aus der Funktion readAllEssays bereitgestellt werden
                ?>
                <div>
                    <div class="uk-card uk-card-default uk-card-body uk-card-hover">
                        <h3 class="uk-card-title"><?= htmlspecialchars($essay['title']) ?></h3>
                        <p><?= htmlspecialchars(substr($essay['content'], 0, 200)) ?>...</p>
                        <p class="uk-text-meta">Erstellt am: <?= htmlspecialchars($essay['created_at']) ?></p>
                        <a href="?main=essays&sub=edit&file=<?= urlencode($folder . '/' .  $essay['filename']) ?>" class="uk-button uk-button-default">Bearbeiten</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Keine Essays gefunden.</p>
        <?php endif; ?>
    </div>
</div>