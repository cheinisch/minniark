<?php

if(!@include_once('inc/functions.php')) {
  // do something
  include "inc/functions.php";
}
  

  $ini = parse_ini_file('app.ini');


  $_login = "false";
  $_accountfile = true;

	if (!isset($_GET['login']))
	{
		$_login = "false";
	}elseif(isset($_GET['login'])){
		$_login = $_GET['login'];
	}else{
		$_login = "true";
	}

  if(!$_accountfile)
  {
    echo 'create account file';
    ?>

    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Page</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.7.5/css/uikit.min.css" />
      </head>
      <body>

        <div class="uk-container uk-flex uk-flex-center uk-flex-middle" style="height: 100vh;">
          <div class="uk-card uk-card-default uk-card-body uk-width-1-3@m">
            <h3 class="uk-card-title uk-text-center">Login</h3>
            <form class="uk-form-stacked" action="login.php?login=login" method="get">
              <input type="hidden" id="login" name="login" value="login" />
              <div class="uk-margin">
                <label class="uk-form-label" for="username">Username</label>
                <div class="uk-form-controls">
                  <input class="uk-input" id="username" type="text" name="username" placeholder="Your username">
                </div>
              </div>
              <div class="uk-margin">
                <label class="uk-form-label" for="username">Mailadresse</label>
                <div class="uk-form-controls">
                  <input class="uk-input" id="mail" type="text" name="mail" placeholder="Your Mailadress">
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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.7.5/js/uikit.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.7.5/js/uikit-icons.min.js"></script>
      </body>
    </html>

    <?php
  }

	elseif ($_login == "false" && $_accountfile)
	{

    ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.7.5/css/uikit.min.css" />
  </head>
  <body>

    <div class="uk-container uk-flex uk-flex-center uk-flex-middle" style="height: 100vh;">
      <div class="uk-card uk-card-default uk-card-body uk-width-1-3@m">
        <h3 class="uk-card-title uk-text-center">Login</h3>
        <form class="uk-form-stacked" action="login.php?login=login" method="get">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.7.5/js/uikit.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.7.5/js/uikit-icons.min.js"></script>
  </body>
</html>


<?php
  }elseif($_GET['login'] == 'login')
  {
    echo $_GET["username"];

    $user = $_GET["username"];
    $password = $_GET["password"];

    login($user, $password);
/*
    if(login($_POST["user"], $_POST["password"]))
    {
      session_start();
		$_SESSION['user_id'] = $_POST["user"];
		header("Location: index.php");
    }else{
      header("Location: index.php?success=false");
    }
  
	}elseif($_GET['login'] == 'logoff')
  {
    session_start();
    session_destroy();
    header("Location: index.php");
  }else{*/
		/*session_start();
		$_SESSION['user_id'] = "2";
		header("Location: index.php");*/
	}
?>
