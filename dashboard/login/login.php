<?php
session_start();

// Pfad zur Benutzerdatei
$userdataPath = __DIR__ . '/../../userdata/users.json';
$error = null;

// Hilfsfunktion zum Benutzerabgleich
function findUserByLogin($identifier, $users) {
    foreach ($users as $user) {
        if ($user['login_name'] === $identifier || $user['email'] === $identifier) {
            return $user;
        }
    }
    return null;
}

// POST-Loginverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['username'] ?? '');
    $inputValue = trim($_POST['password'] ?? '');

    if (!file_exists($userdataPath)) {
        $error = "Benutzerdaten nicht gefunden.";
    } else {
        $users = json_decode(file_get_contents($userdataPath), true);
        $user = findUserByLogin($identifier, $users);

        if (!$user) {
            $error = "Benutzer nicht gefunden.";
        } else {
            if ($user['login_type'] === 'password') {
                if (password_verify($inputValue, $user['password'])) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $user['login_name'];
                    header("Location: ../");
                    exit;
                } else {
                    $error = "Falsches Passwort.";
                }
            } elseif ($user['login_type'] === 'otp') {
                if ($inputValue === '123456') {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $user['login_name'];
                    header("Location: ../");
                    exit;
                } else {
                    $error = "Ungültiger Einmalcode.";
                }
            } else {
                $error = "Unbekannter Login-Typ.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Fehler</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-800 flex items-center justify-center min-h-screen">
    <div class="bg-stone-900 shadow-lg rounded-lg p-8 w-full max-w-md">
        <h3 class="text-2xl font-bold mb-4 text-gray-300">Login fehlgeschlagen</h3>

        <?php if ($error): ?>
            <div class="bg-red-300 text-red-700 p-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <a href="../index.php" class="block w-full bg-sky-400 text-white text-center font-semibold py-2 rounded hover:bg-sky-600 transition">
            Zurück zum Login
        </a>
    </div>
</body>
</html>
