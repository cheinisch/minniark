<?php

    require_once(__DIR__ . "/../../functions/function_backend.php");
    security_checklogin();

    // Users may only change their own account (admins use dashboard-user.php).
    $self = $_SESSION['username'] ?? '';
    foreach (['password', 'auth_type'] as $param) {
        if (isset($_GET[$param]) && $_GET[$param] !== $self) {
            minniark_deny(403, 'You can only change your own account.');
        }
    }
    if (isset($_GET['userdata']) && ($_POST['old_username'] ?? '') !== $self) {
        minniark_deny(403, 'You can only change your own account.');
    }

    if(isset($_GET['userdata']))
    {
        $username = $_POST['username'];
        $oldusername = $_POST['old_username'];
        $displayname = $_POST['display-name'];
        $mail = $_POST['email'];

        $data = [
            'username'     => $username,
            'display_name' => $displayname,
            'mail'         => $mail,
        ];

        $result = updateUserData($username, $data, $oldusername);

        if($result)
        {
            $_SESSION['username'] = $username;
            header("Location: ../dashboard-personal.php");
        }else{
            echo "Error";
        }
    }
    
    if(isset($_GET['password']))
    {
        $username = $_GET['password'];

        $password_current = $_POST['current_password'];
        $password_new = $_POST['new_password'];
        $password_confirm = $_POST['confirm_password'];

        $currentData = getUserDataFromUsername($username);

        

        $data = [
            'password' => $password,
        ];

        $result = updateUserData($username, $data, $username);

        if($result)
        {
           // header("Location: ../dashboard-personal.php");
           exit;
        }else{
            echo "Error";
        }
    }

    if(isset($_GET['auth_type']))
    {
        $username = $_GET['auth_type'];
        $login = $_POST['login_type'];
        
        $data = [
            'auth_type' => $login,
        ];

        $result = updateUserData($username, $data, $username);

        if($result)
        {
           header("Location: ../dashboard-personal.php");
           exit;
        }else{
            echo "Error";
        }

    }