<?php

include "inc/functions.php";

session_start();


if (isset($_SESSION['user_id']))
{
	if($_SESSION['user_id'] == userid())
	{
		header("Location: admin.php");
	}else{
		include 'login.php';
	}
}else{
	include 'login.php';
}

?>