<?php
  require_once(__DIR__ . "/../functions/function_backend.php");

  // Logout
  $logout = $_GET['logout'] ?? null;
  if ($logout === "true") {
    session_start();
    session_destroy();
    header("Location: index.php");
    exit;
  }

  // Inputs
  $username = $_POST['username'] ?? null;
  $password = $_POST['password'] ?? null;

  // State
  $step_2     = false;
  $user_exist = false;
  $user_wrong = false;
  $pass_wrong = false;
  $login_type = "";

  if ($username !== null) {
    $user_exist = check_username($username);
    if ($user_exist) {
      $step_2 = true;
    } else {
      $user_wrong = true;
    }
  }

  if ($step_2) {
    $login_type = get_logintype($username);
    if ($login_type === "mail" && $password === null) {
      $mail = get_usermail($username);
      send_otp_mail($mail, $username);
    }
  }

  if ($username !== null && $password !== null) {
    if (check_password($password, $username)) {
      session_start();
      $_SESSION['loggedin'] = true;
      $_SESSION['username'] = $username;
      $_SESSION['userid']   = getIDfromUsername($username);
      $_SESSION['admin']    = isAdmin($username);
      header("Location: dashboard.php");
      exit;
    } else {
      $pass_wrong = true;
      $step_2     = true; // im Fehlerfall auf Schritt 2 bleiben
      if ($login_type === "") { $login_type = get_logintype($username); }
    }
  }
?>
<!doctype html>
<html lang="<?php echo get_language(); ?>">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - <?php echo get_sitename(); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="icon" type="image/png" href="../lib/img/favicon.png" />
  </head>
  <body class="min-h-screen bg-white text-black dark:bg-black dark:text-white">
    <main class="grid min-h-screen place-items-center px-4">
      <!-- Card -->
      <section class="w-full max-w-md rounded-sm border border-black/10 bg-white shadow-xs p-6 dark:bg-black/40 dark:border-white/10">
        <!-- Logo / Title -->
        <header class="mb-6">
          <h1 class="text-lg font-semibold"><?php echo get_sitename(); ?></h1>
          <p class="mt-1 text-sm text-black/60 dark:text-gray-400">
            <?php echo languageString('login.login'); ?>
          </p>
        </header>

        <!-- Schritt 1: Username -->
        <form id="user-form"
              method="post"
              action="login.php"
              class="<?php echo $step_2 ? 'hidden' : 'block'; ?>">
          <div class="space-y-2">
            <label for="username_input" class="text-xs font-medium">
              <?php echo languageString('login.username'); ?>
            </label>
            <input
              type="text"
              id="username_input"
              name="username"
              placeholder="<?php echo languageString('login.username'); ?>"
              class="w-full rounded bg-white dark:bg-black px-3 py-2 text-sm outline outline-1 -outline-offset-1 outline-black/10 placeholder:text-gray-400
                     focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600 dark:outline-white/10"/>
            <p id="wrong-user"
               class="mt-1 text-xs text-rose-600 <?php echo $user_wrong ? '' : 'hidden'; ?>">
               <?php echo languageString('login.username_wrong'); ?>
            </p>
          </div>

          <div class="mt-6 flex justify-end">
            <!-- Outline-Button -->
            <button type="submit"
              class="inline-flex items-center gap-2 rounded px-4 py-2 text-sm border border-black/20 hover:bg-black/5
                     dark:border-white/20 dark:hover:bg-white/10">
              <?php echo languageString('login.login'); ?>
            </button>
          </div>
        </form>

        <!-- Schritt 2: Passwort / OTP -->
        <form id="pass-form"
              method="post"
              action="login.php"
              class="<?php echo $step_2 ? 'block' : 'hidden'; ?>">
          <div class="mb-4">
            <nav class="text-xs text-black/60 dark:text-gray-400">
              <ol class="flex items-center gap-2">
                <li>
                  <a href="login.php" class="hover:underline"><?php echo languageString('login.login'); ?></a>
                </li>
                <li aria-hidden="true">/</li>
                <li class="font-medium"><?php echo htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8'); ?></li>
              </ol>
            </nav>
          </div>

          <div class="space-y-2">
            <label for="password_input" class="text-xs font-medium">
              <?php
                echo ($login_type === "password")
                  ? languageString('login.password')
                  : languageString('login.enter_otp');
              ?>
            </label>
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <input
              type="password"
              id="password_input"
              name="password"
              placeholder="<?php echo languageString('login.password'); ?>"
              class="w-full rounded bg-white dark:bg-black px-3 py-2 text-sm outline outline-1 -outline-offset-1 outline-black/10 placeholder:text-gray-400
                     focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600 dark:outline-white/10"/>
            <p id="wrong-pass"
               class="mt-1 text-xs text-rose-600 <?php echo $pass_wrong ? '' : 'hidden'; ?>">
               <?php echo languageString('login.password_wrong'); ?>
            </p>
          </div>

          <div class="mt-6 flex items-center justify-between">
            <a href="login.php"
               class="text-xs underline underline-offset-2 text-black/70 hover:text-black dark:text-gray-300 dark:hover:text-white">
              <?php echo languageString('login.change_user') ?? 'Change user'; ?>
            </a>
            <!-- Outline-Button -->
            <button type="submit"
              class="inline-flex items-center gap-2 rounded px-4 py-2 text-sm border border-black/20 hover:bg-black/5
                     dark:border-white/20 dark:hover:bg-white/10">
              <?php echo languageString('login.login'); ?>
            </button>
          </div>
        </form>
      </section>

      <!-- Footer minimal -->
      <p class="mt-6 text-xs text-black/50 dark:text-gray-500">
        &copy; <?php echo date('Y'); ?> <?php echo get_sitename(); ?>
      </p>
    </main>
  </body>
</html>
