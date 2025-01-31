<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Formular gesendet!");
    error_log("Titel: " . ($_POST['title'] ?? 'nicht gesetzt'));
    error_log("Inhalt: " . ($_POST['content'] ?? 'nicht gesetzt'));
}

require_once __DIR__ . '/../../functions/functions.php'; // Funktionen einbinden

// Initialisierung von Fehler- und Erfolgsnachrichten
$errors = [];
$success = "";

// Formularverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    // Validierung
    if (empty($title)) {
        $errors[] = "Der Titel darf nicht leer sein.";
    }
    if (empty($content)) {
        $errors[] = "Der Inhalt darf nicht leer sein.";
    }

    // Essay erstellen, wenn keine Fehler vorliegen
    if (empty($errors)) {
        $result = createEssay($title, $content);
        if ($result === false) {
            $errors[] = "Es gab ein Problem beim Erstellen des Essays.";
        } else {
            $success = $result;
        }
    }
}
?>

<div class="uk-container uk-margin-top">
    <h2>Neues Essay erstellen</h2>

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

    <form method="post" class="uk-form-stacked">
        <div class="uk-margin">
            <label class="uk-form-label" for="title">Titel</label>
            <div class="uk-form-controls">
                <input class="uk-input" type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label" for="content">Inhalt</label>
            <div class="uk-form-controls">
                <textarea id="content" name="content"></textarea>
            </div>
        </div>

        <button type="submit" class="uk-button uk-button-primary">Erstellen</button>
    </form>
</div>
