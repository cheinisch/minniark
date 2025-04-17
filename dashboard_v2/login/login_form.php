<?php
$usersPath = __DIR__ . '/../../userdata/users.json';
$usersData = file_exists($usersPath) ? file_get_contents($usersPath) : '[]';
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex justify-center items-center min-h-screen">
  <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Dummyname</h2>
    <form id="login-form" action="login/login.php" method="POST">

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700" for="login-identifier">E-Mail oder Benutzername</label>
        <input class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="login-identifier" name="username" type="text" placeholder="name@example.com" required>
      </div>

      <div class="mb-4 hidden" id="login-step2">
        <label class="block text-sm font-medium text-gray-700" id="second-label" for="second-input"></label>
        <input class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="second-input" name="password" type="text" required>
      </div>

      <button class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition" type="submit">Login</button>
    </form>
  </div>

<script>
  const identifierInput = document.getElementById('login-identifier');
  const step2Container = document.getElementById('login-step2');
  const secondLabel = document.getElementById('second-label');
  const secondInput = document.getElementById('second-input');

  const users = <?php echo $usersData; ?>;

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
