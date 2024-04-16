<?php
if(isset($_GET['create'])) {
    
    require_once('../function/function.php');

    $data = [
        [
            "userID" => 1,
            "username" => $_POST['username'],
            "email" => $_POST['email'],
            "password" => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ]
    ];
    
    // Dateiname für den Download festlegen
    $filename = 'userdata.php';

    downloadJSON($data, $filename);
    

    echo '
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
            <h3 class="uk-card-title uk-text-center">Create User Account</h3>
              <div class="uk-margin">
              Please copy the file into the conf Folder
                </div>
          </div>
        </div>
    
        <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.19.4/js/uikit.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.19.4/js/uikit-icons.min.js"></script>
      </body>
    </html>';

}else{
echo '
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
        <h3 class="uk-card-title uk-text-center">Create User Account</h3>
        <form class="uk-form-stacked" action="bin/install/install.php?create=true" method="post">
          <div class="uk-margin">
            <label class="uk-form-label" for="username">Username</label>
            <div class="uk-form-controls">
              <input class="uk-input" id="username" type="text" name="username" placeholder="Your username">
            </div>
          </div>
          <div class="uk-margin">
            <label class="uk-form-label" for="email">Mailadresse</label>
            <div class="uk-form-controls">
              <input class="uk-input" id="email" type="email" name="email" placeholder="Your mail">
            </div>
          </div>
          <div class="uk-margin">
            <label class="uk-form-label" for="password">Password</label>
            <div class="uk-form-controls">
              <input class="uk-input" id="password" name="password" type="password" placeholder="Your password">
            </div>
          </div>
          <div class="uk-margin uk-text-center">
            <button class="uk-button uk-button-primary" type="submit">Create Loginfile</button>
          </div>
        </form>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.19.4/js/uikit.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.19.4/js/uikit-icons.min.js"></script>
  </body>
</html>';

}