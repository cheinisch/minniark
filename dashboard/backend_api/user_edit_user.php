<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require_once(__DIR__ . "/../../functions/function_backend.php");
    security_checklogin();


    if(isset($_GET['new']))
    {
        print_r($_POST);
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['mail'] ?? '');
        $password = $_POST['password'] ?? '';
        $userrole = $_POST['userrole'] ?? '';

        if (saveNewUser($username, $email, $password,$userrole)) {
            header("Location: ../dashboard-user.php");
        }
    }

    if(isset($_GET['edit']))
    {
        print_r($_POST);
        $username = trim($_POST['username_old'] ?? '');
        $newUsername = trim($_POST['username'] ?? '');
        $email    = trim($_POST['mail'] ?? '');
        $password = $_POST['password'] ?? '';
        $userrole = $_POST['userrole'] ?? '';

        $data = [
            'username'     => $newUsername,
            'mail'         => $email,
        ];

        if (!empty($password)) {
            $data['password'] = $password;
        }

        if(updateUserData($username, $data, $newUsername))
        {
            header("Location: ../dashboard-user.php");
        }
    }

    if(isset($_GET['delete']))
    {

        $username = $_GET['delete'];

        if(removeUser($username))
        {
            header("Location: ../dashboard-user.php");
        }
    }