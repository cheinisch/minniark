<?php

    $username = $_POST['username'];
    $password = $_POST['password'];
    $sitename = $_POST['sitename'];

    print_r($_POST);

    echo "User: $username, Passwort: $password, Sitename: $sitename";

?>