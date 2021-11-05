<?php

function OpenCon()
{
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "picture_cms";

// Create connection
// Create connection
$conn= mysqli_connect($servername,$username,$password,$dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
return $conn;
}

function CloseCon($conn)
{
$conn -> close();
}

?> 