<?php

if(!@include_once('inc/functions.php')) {
  // do something
  include "inc/functions.php";
}
  

  $ini = parse_ini_file('app.ini');


  $_login = "false";

	if (!isset($_GET['login']))
	{
		$_login = "false";
	}elseif(isset($_GET['login'])){
		$_login = $_GET['login'];
	}else{
		$_login = "true";
	}

	if ($_login == "false")
	{

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.88.1">
    <title>Signin Template · Bootstrap v5.1</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.1/examples/sign-in/">

    

    <!-- Bootstrap core CSS -->
<link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="assets/dist/css/signin.css" rel="stylesheet">
  </head>
  <body class="text-center">
    
<main class="form-signin">
  <form action="login.php?login=login" method="post">
    <img class="mb-4" src="assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
    <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

    <div class="form-floating">
      <input type="email" class="form-control" id="floatingInput" name="user" placeholder="name@example.com">
      <label for="floatingInput">User</label>
    </div>
    <div class="form-floating">
      <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password">
      <label for="floatingPassword">Password</label>
    </div>
    <?php
      if(isset($_GET['success']))
      {
        if($_GET['success'] == 'false')
        {
    ?>
    <p>Wrong Login Data <a href="#">Reset Passwort</a></p>
      <?php
        }
      }
      ?>
    <div class="checkbox mb-3">
      <label>
        <input type="checkbox" value="remember-me"> Remember me
      </label>
    </div>
    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
    <p class="mt-5 mb-3 text-muted">&copy; 2021-2022 Version <?php echo $ini['app_version']; ?></p>
  </form>
</main>


    
  </body>
</html>

<?php
  }elseif($_GET['login'] == 'login')
  {
    echo $_POST["user"];

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
  }else{
		/*session_start();
		$_SESSION['user_id'] = "2";
		header("Location: index.php");*/
	}
?>
