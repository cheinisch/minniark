<?php
session_start();


if (isset($_SESSION['user_id']))
{
	if($_SESSION['user_id'] == "2")
	{
		header("Location: admin.php");
	}else{
		include 'login.php';
	}
}else{
	include 'login.php';
}

?>