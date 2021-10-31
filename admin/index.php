<?php
session_start();



if($_SESSION['user_id'] == "2")
{
	header("Location: admin.php");
}else{
	include 'login.php';
}

?>