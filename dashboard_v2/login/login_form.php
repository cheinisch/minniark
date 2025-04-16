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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.17.10/dist/css/uikit.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/uikit@3.17.10/dist/js/uikit.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/uikit@3.17.10/dist/js/uikit-icons.min.js"></script>
  <style>
    body, html {
      height: 100%;
      margin: 0;
    }
    .login-wrapper {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }
    .login-box {
      width: 100%;
      max-width: 400px;
      padding: 20px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
      border-radius: 8px;
      background: #fff;
    }
    #login-step2 {
      display: none;
    }
  </style>
</head>
<body>
<div class="login-wrapper">
  <h2 class="uk-heading-bullet">Dummyname</h2>
  <div class="login-box">
    <form class="uk-form-stacked" id="login-form" action="login/login.php" method="POST">

      <div class="uk-margin">
        <label class="uk-form-label" for="login-identifier">E-Mail oder Benutzername</label>
        <div class="uk-form-controls">
          <input class="uk-input" id="login-identifier" name="username" type="text" placeholder="name@example.com" required>
        </div>
      </div>

      <div class="uk-margin" id="login-step2">
        <label class="uk-form-label" id="second-label" for="second-input"></label>
        <div class="uk-form-controls">
          <input class="uk-input" id="second-input" name="password" type="text" required>
        </div>
      </div>

      <button class="uk-button uk-button-primary uk-width-1-1" type="submit">Login</button>
    </form>
  </div>
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
      secondInput.placeholder = 'Gebe den Code ein (z.B. 123456)';
    } else {
      secondLabel.textContent = 'Passwort';
      secondInput.type = 'password';
      secondInput.placeholder = 'Passwort';
    }

    step2Container.style.display = 'block';
  });
</script>
</body>
</html>
