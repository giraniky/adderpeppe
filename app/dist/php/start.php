<?php

	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	session_start();

    if(empty($_SESSION["id"]) || empty($_SESSION["token"])){
		header("location: auth/login.php");
		exit;
	}
?>