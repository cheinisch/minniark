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
                // Hier würdest du z. B. gegen einen Code in der Datenbank oder Session prüfen
                if ($inputValue === '123456') { // Dummy-OTP
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

// Ausgabe bei Fehler oder initialem Aufruf
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Fehler</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.17.10/dist/css/uikit.min.css" />
</head>
<body class="uk-background-muted uk-flex uk-flex-middle uk-height-viewport">
<div class="uk-container">
    <div class="uk-card uk-card-default uk-card-body uk-width-medium uk-margin-auto uk-margin-top">
        <h3 class="uk-card-title">Login fehlgeschlagen</h3>
        <?php if ($error): ?>
            <div class="uk-alert-danger" uk-alert>
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>
        <a href="../index.php" class="uk-button uk-button-default uk-width-1-1">Zurück zum Login</a>
    </div>
</div>
</body>
</html>
