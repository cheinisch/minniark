<?php
require_once __DIR__ . '/../../functions/functions.php'; // Funktionen laden

// Verzeichnis für Pages
$pagesDir = __DIR__ . '/../../content/pages/';
$errors = [];
$success = "";

// Prüfen, ob eine spezifische Page geladen werden soll
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']); // Dateiname aus GET-Parameter
    $filePath = $pagesDir . $file;

    // Prüfen, ob die Datei existiert und geladen werden kann
    if (file_exists($filePath)) {
        $pageData = readPageFromFile($filePath);
        if ($pageData === false) {
            $errors[] = "Die Page konnte nicht geladen werden.";
        }
    } else {
        $errors[] = "Die Datei existiert nicht.";
    }
} else {
    $errors[] = "Keine Page zum Bearbeiten ausgewählt.";
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
        $existingPage = readPageFromFile($filePath);

        if ($existingPage === false) {
            $errors[] = "Die bestehenden Daten konnten nicht gelesen werden.";
        } else {
            // Aktualisierte Daten
            $pageData = [
                'title' => $title,
                'content' => $content,
                'created_at' => $existingPage['created_at'], // Behalte ursprüngliches Datum bei
                'updated_at' => date('Y-m-d H:i:s')          // Neues Aktualisierungsdatum
            ];

            // Page speichern
            if (file_put_contents($filePath, json_encode($pageData, JSON_PRETTY_PRINT)) !== false) {
                $success = "Die Page wurde erfolgreich aktualisiert.";
            } else {
                $errors[] = "Es gab ein Problem beim Speichern der Page.";
            }
        }
    }
}
?>

<div class="uk-container uk-margin-top">
    <h2>Page bearbeiten</h2>

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

    <?php if (isset($pageData)): ?>
        <form method="post" class="uk-form-stacked">
            <input type="hidden" name="filePath" value="<?= htmlspecialchars($filePath) ?>">

            <div class="uk-margin">
                <label class="uk-form-label" for="title">Titel</label>
                <div class="uk-form-controls">
                    <input class="uk-input" type="text" id="title" name="title" value="<?= htmlspecialchars($pageData['title']) ?>" required>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label" for="content">Inhalt</label>
                <div class="uk-form-controls">
                    <textarea id="content" name="content" required><?= htmlspecialchars($pageData['content']) ?></textarea>
                </div>
            </div>

            <button type="submit" class="uk-button uk-button-primary">Speichern</button>
        </form>
    <?php else: ?>
        <p>Keine Page gefunden.</p>
    <?php endif; ?>
</div>
