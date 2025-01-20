<?php
// Funktionen einbinden
require_once './../functions/functions.php';

// Aktuellen Benutzer ermitteln
$currentUsername = $_SESSION['username'] ?? null;
if (!$currentUsername) {
    die("Benutzer nicht angemeldet!");
}

// Benutzerdaten laden
$user = getUserData($currentUsername); // Funktion aus dashboard.php
if (!$user) {
    die("Benutzerdaten konnten nicht geladen werden!");
}

// Fehler- und Erfolgsnachrichten
$errors = [];
$success = "";

// Formularverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $loginName = trim($_POST['login_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Validierung
    if (empty($name)) $errors[] = "Der Name darf nicht leer sein.";
    if (empty($loginName)) $errors[] = "Der Login-Name darf nicht leer sein.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Die E-Mail-Adresse ist ungültig.";
    if (!empty($password) && $password !== $confirmPassword) $errors[] = "Die Passwörter stimmen nicht überein.";

    // Benutzer aktualisieren, wenn keine Fehler vorliegen
    if (empty($errors)) {
        $updateData = [
            'name' => $name,
            'login_name' => $loginName,
            'email' => $email,
        ];
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT); // Passwort verschlüsseln
        }

        if (updateUserData($currentUsername, $updateData)) { // Funktion aus dashboard.php
            $success = "Benutzerdaten wurden erfolgreich aktualisiert.";
            $currentUsername = $loginName; // Aktuellen Benutzernamen anpassen, falls geändert
            $_SESSION['username'] = $loginName;
        } else {
            $errors[] = "Es gab ein Problem beim Speichern der Daten.";
        }
    }
}
?>


<h2>Benutzerdaten bearbeiten</h2>

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
            <label class="uk-form-label" for="name">Name</label>
            <div class="uk-form-controls">
                <input class="uk-input" type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label" for="login_name">Login-Name</label>
            <div class="uk-form-controls">
                <input class="uk-input" type="text" id="login_name" name="login_name" value="<?= htmlspecialchars($user['login_name']) ?>" required>
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label" for="email">E-Mail-Adresse</label>
            <div class="uk-form-controls">
                <input class="uk-input" type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label" for="password">Passwort</label>
            <div class="uk-form-controls">
                <input class="uk-input" type="password" id="password" name="password">
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label" for="confirm_password">Passwort bestätigen</label>
            <div class="uk-form-controls">
                <input class="uk-input" type="password" id="confirm_password" name="confirm_password">
            </div>
        </div>

        <button type="submit" class="uk-button uk-button-primary">Speichern</button>
    </form>