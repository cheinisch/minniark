<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../functions/functions.php';

$error = null;

// Verarbeite Login-Formular
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Überprüfe Login-Daten
    if (authenticateUser($username, $password)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;

        header("Location: media.php");
        exit;
    } else {
        $error = "Ungültiger Benutzername oder Passwort!";
    }
}

// Verarbeite Logout
if (isset($_GET['logout'])) {
    session_destroy(); // Sitzung beenden
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow-lg w-96">
        <h1 class="text-2xl font-bold mb-4 text-center">Login</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Benutzername</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Passwort</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">Login</button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-600">
            Sie haben keine Zugangsdaten? Kontaktieren Sie den Administrator.
        </p>
    </div>
</body>
</html>
