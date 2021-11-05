<?php

require 'db-connect.php';

function get_essays(){

    $conn = OpenCon();

    $sql = "SELECT * FROM essay;";
    $result = $conn->query($sql);

    $conn->close();

    return $result;

}

function get_essay($id){

    $conn = OpenCon();

    $sql = "SELECT * FROM `essay` WHERE `id` = $id;";
    $result = $conn->query($sql);

    $conn->close();

    return $result;

}


?>