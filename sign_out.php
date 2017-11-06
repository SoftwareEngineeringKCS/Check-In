<?php
	session_start();
	session_unset();
	session_destroy();

	// Destroy login cookie.
	setcookie('logged_in', "", time() - 86400);
	
	header("Location: index.php"); // Redirecting To Home Page
?>