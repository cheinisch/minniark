<?php
require_once __DIR__ . '/../../functions/func_essay.php'; // Funktionen laden

// Verzeichnis für Essays
$essaysDir = __DIR__ . '/../../content/essays/';
$errors = [];
$success = "";

// Prüfen, ob ein spezifisches Essay geladen werden soll
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']); // Dateiname aus GET-Parameter
    $filePath = $essaysDir . $file;

    // Prüfen, ob die Datei existiert und geladen werden kann
    if (file_exists($filePath)) {
        $essayData = readEssayFromFile($filePath);
        if ($essayData === false) {
            $errors[] = "Das Essay konnte nicht geladen werden.";
        }
    } else {
        $errors[] = "Die Datei existiert nicht.";
    }
} else {
    $errors[] = "Kein Essay zum Bearbeiten ausgewählt.";
}

// Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $filePath = $_POST['filePath'];

    // Validierung
    if (empty($title)) {
        $errors[] = "Der Titel darf nicht leer sein.";
    }
    if (empty($content)) {
        $errors[] = "Der Inhalt darf nicht leer sein.";
    }

    // Aktualisierung der JSON-Daten
    if (empty($errors)) {
        // Bestehende JSON-Daten laden
        $existingEssay = readEssayFromFile($filePath);

        if ($existingEssay === false) {
            $errors[] = "Die bestehenden Daten konnten nicht gelesen werden.";
        } else {
            // Aktualisierte Daten
            $essayData = [
                'title' => $title,
                'content' => $content,
                'created_at' => $existingEssay['created_at'], // Behalte ursprüngliches Datum bei
                'updated_at' => date('Y-m-d H:i:s')          // Neues Aktualisierungsdatum
            ];

            // Essay speichern
            if (file_put_contents($filePath, json_encode($essayData, JSON_PRETTY_PRINT)) !== false) {
                $success = "Das Essay wurde erfolgreich aktualisiert.";
            } else {
                $errors[] = "Es gab ein Problem beim Speichern des Essays.";
            }
        }
    }
}
?>

<div class="uk-container uk-margin-top">
    <h2>Essay bearbeiten</h2>

    <?php if (!empty($errors)): ?>
        <div class="uk-alert-danger" uk-alert>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="uk-alert-success" uk-alert>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($essayData)): ?>
        <form method="post" class="uk-form-stacked">
            <input type="hidden" name="filePath" value="<?= htmlspecialchars($filePath) ?>">

            <div class="uk-margin">
                <label class="uk-form-label" for="title">Titel</label>
                <div class="uk-form-controls">
                    <input class="uk-input" type="text" id="title" name="title" value="<?= htmlspecialchars($essayData['title']) ?>" required>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label" for="content">Inhalt</label>
                <div class="uk-form-controls">
                    <textarea id="content" name="content" required><?= htmlspecialchars($essayData['content']) ?></textarea>
                </div>
            </div>

            <button type="submit" class="uk-button uk-button-primary">Speichern</button>
        </form>
    <?php else: ?>
        <p>Kein Essay gefunden.</p>
    <?php endif; ?>
</div>
