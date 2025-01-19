<?php
// Sitzung nur starten, wenn keine aktiv ist
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../functions/functions.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$message = null;

// Verarbeiten des Formulars
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $currentPassword = trim($_POST['current_password'] ?? '');

    // Funktion zur Aktualisierung der Benutzerdaten aufrufen
    $result = updateUserSettings($_SESSION['username'], $username, $email, $newPassword, $currentPassword);
    if ($result === true) {
        $message = "Einstellungen erfolgreich aktualisiert.";
        $_SESSION['username'] = $username; // Sitzung aktualisieren
    } else {
        $message = $result; // Fehlermeldung von der Funktion
    }
}
?>

<div>
    <h1 class="text-2xl font-bold mb-4">Benutzereinstellungen</h1>

    <?php if ($message): ?>
        <div class="mb-4 p-4 rounded <?= strpos($message, 'erfolgreich') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form action="/dashboard.php?usersettings" method="POST" class="space-y-4">
        <div>
            <label for="username" class="block text-sm font-medium">Benutzername</label>
            <input type="text" id="username" name="username" class="w-full px-4 py-2 border rounded" value="<?= htmlspecialchars($_SESSION['username']) ?>" required>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium">E-Mail-Adresse</label>
            <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded" required>
        </div>
        <div>
            <label for="current_password" class="block text-sm font-medium">Aktuelles Passwort</label>
            <input type="password" id="current_password" name="current_password" class="w-full px-4 py-2 border rounded" required>
        </div>
        <div>
            <label for="new_password" class="block text-sm font-medium">Neues Passwort (optional)</label>
            <input type="password" id="new_password" name="new_password" class="w-full px-4 py-2 border rounded">
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Änderungen speichern</button>
    </form>
</div>
