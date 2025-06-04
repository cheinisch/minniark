<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require_once(__DIR__ . "/../../functions/function_backend.php");
    security_checklogin();

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

        print_r($_POST);

        $password_current = $_POST['current_password'];
        $password_new = $_POST['new_password'];
        $password_confirm = $_POST['confirm_password'];

        $currentData = getUserDataFromUsername($username);

        print_r($currentData);

        

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