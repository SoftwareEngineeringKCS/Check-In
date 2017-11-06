<?php #JORGE ESPADA
	
	$page_title = 'Kean Career Services';

	if (!isset($_COOKIE['logged_in'])) {
		include ('includes/header.html');
		include ('login.html');
	} else {
		include ('includes/header.html');
		echo "<br><a href='sign_out.php'>SIGN-UP</a>";
		echo "<h1>Administrative Features</h1>";
		echo "<center><p><img src='pictures/under_construction.png' alt='Under Construction Error' style='width: 400px; height: 150px;'></p></center>";

		/*// Check for form submission:
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			include ('includes/db_config.php');
			// Minimal form validation:

		} // End of main submission IF.*/
	}

?>

<?php include ('includes/footer.html'); ?>