<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require_once(__DIR__ . "/../../functions/function_backend.php");
    security_checklogin();


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