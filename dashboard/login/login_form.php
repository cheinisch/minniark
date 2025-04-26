<?php
require_once(__DIR__ . "/../../functions/function_backend.php");

// Schutzkonstante setzen
// define('IMAGEPORTFOLIO', true);

// Konfigurationsdatei einlesen
$userConfigPath = __DIR__ . '/../../userdata/config/user_config.php';
$userConfig = file_exists($userConfigPath) ? require $userConfigPath : [];

// Login-Datenstruktur für JavaScript vorbereiten
$usersData = [
  [
    'login_name' => $userConfig['USERNAME'] ?? '',
    'email' => $userConfig['EMAIL'] ?? '',
    'login_type' => $userConfig['AUTH_TYPE'] ?? 'password'
  ]
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-800 flex justify-center items-center min-h-screen">
  <div class="w-full max-w-md bg-stone-900 shadow-lg rounded-lg p-6">
    <h2 class="text-2xl font-bold text-center text-gray-300 mb-4">
      <?php echo htmlspecialchars(get_sitename()); ?>
    </h2>
    <form id="login-form" action="login/login.php" method="POST">
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-300" for="login-identifier">
          E-Mail oder Benutzername
        </label>
        <input class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-sky-400 focus:border-sky-400" 
               id="login-identifier" name="username" type="text" placeholder="name@example.com" required>
      </div>

      <div class="mb-4 hidden" id="login-step2">
        <label class="block text-sm font-medium text-gray-300" id="second-label" for="second-input"></label>
        <input class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-sky-400 focus:border-sky-400"
               id="second-input" name="password" type="text" required>
      </div>

      <button class="w-full bg-sky-400 text-white py-2 rounded-md hover:bg-sky-600 transition" type="submit">
        Login
      </button>
    </form>
  </div>

<script>
  const identifierInput = document.getElementById('login-identifier');
  const step2Container = document.getElementById('login-step2');
  const secondLabel = document.getElementById('second-label');
  const secondInput = document.getElementById('second-input');

  // Nutzerdaten aus PHP übernehmen
  const users = <?php echo json_encode($usersData); ?>;

  function getLoginType(identifier) {
    return users.find(
      u => u.email === identifier || u.login_name === identifier
    )?.login_type;
  }

  identifierInput.addEventListener('blur', () => {
    const identifier = identifierInput.value.trim();
    if (!identifier) return;

    const type = getLoginType(identifier) || (identifier.includes('@') ? 'otp' : 'password');

    if (type === 'otp') {
      secondLabel.textContent = 'Einmal-Code (OTP)';
      secondInput.type = 'text';
      secondInput.placeholder = 'Gib den Code ein (z.B. 123456)';
    } else {
      secondLabel.textContent = 'Passwort';
      secondInput.type = 'password';
      secondInput.placeholder = 'Passwort';
    }

    step2Container.classList.remove('hidden');
  });
</script>
</body>
</html>
