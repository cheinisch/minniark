<?php

require ('ip_config.php');

function OpenCon()
{
  
$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASSWORD;
$dbname = DB_NAME;


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