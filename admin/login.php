<?php

if(isset($_GET['login'])) {

  require_once('../bin/function/function.php');

  $user = $_POST['username'];
  $passwd = $_POST['password'];

  if(getLogin($user, $passwd))
  {
    echo "Login";
  }else{
    echo "fail";
  }

}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.19.4/css/uikit.min.css" />
  </head>
  <body>

    <div class="uk-container uk-flex uk-flex-center uk-flex-middle" style="height: 100vh;">
      <div class="uk-card uk-card-default uk-card-body uk-width-1-3@m">
        <h3 class="uk-card-title uk-text-center">Login</h3>
        <form class="uk-form-stacked" action="login.php?login=login" method="post">
          <input type="hidden" id="login" name="login" value="login" />
          <div class="uk-margin">
            <label class="uk-form-label" for="username">Username / Mailadresse</label>
            <div class="uk-form-controls">
              <input class="uk-input" id="username" type="text" name="username" placeholder="Your username">
            </div>
          </div>
          <div class="uk-margin">
            <label class="uk-form-label" for="password">Password</label>
            <div class="uk-form-controls">
              <input class="uk-input" id="password" name="password" type="password" placeholder="Your password">
            </div>
          </div>
          <div class="uk-margin uk-text-center">
            <button class="uk-button uk-button-primary" type="submit">Login</button>
          </div>
        </form>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.19.4/js/uikit.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.19.4/js/uikit-icons.min.js"></script>
  </body>
</html>