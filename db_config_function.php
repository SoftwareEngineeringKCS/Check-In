<?php
	$f_server = "imc.kean.edu";
	$f_login = "seds2017";
	$f_password = "2017fall";
	$f_database = "seds2017db";

	$f_conex = mysqli_connect($f_server, $f_login, $f_password, $f_database) or die("Error: Unable to connect to DataBase.");	
?>