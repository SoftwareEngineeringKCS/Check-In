<?php
	$server = "imc.kean.edu";
	$login = "seds2017";
	$password = "2017fall";
	$database = "seds2017db";

	$conex = mysqli_connect($server, $login, $password, $database) or die("Error: Unable to connect to DataBase.");	
?>
