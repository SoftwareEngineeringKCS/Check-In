<?php #JORGE ESPADA
	
	# If ID but no Email.
	include ('includes/db_config.php');

	// Print the results:
	echo "<h1>Check-In Result</h1>";
	echo "<h2>Walk-In</h2>";

	$getSID = $_REQUEST['getSID'];
	$getSFN = $_REQUEST['getSFN'];
	$getSLN = $_REQUEST['getSLN'];
	$getSEM = $_REQUEST['getSEM'];
	$getCID = $_REQUEST['getCID'];
	$getLID = $_REQUEST['getLID'];
	$getTA = $_REQUEST['getTA'];
	$getRID = $_REQUEST['getRID'];

	# Update Student.
	$query = sprintf("UPDATE Students SET first_name = '%s', last_name = '%s', email = '%s' WHERE id = '%s'", $getSFN, $getSLN, $getSEM, $getSID);
	$result = mysqli_query($conex, $query);
	# Save check-in.
	$set_in_datetime = date("Y-m-d H:i:s");
	$query = sprintf("INSERT INTO Students_Check_In VALUES(NULL, 1, '%s', '%s', '%s', '%s', '%s', '%s', '')", $getSID, $getCID, $getLID, $getTA, $getRID, $set_in_datetime);
	$result = mysqli_query($conex, $query);
	# Show results.
	echo "<p>Student ID: " . $getSID . "</p>";
	echo "<p>Name: " . $getSLN . ", " . $getSFN . "</p>";
	$query = sprintf("SELECT description FROM Reasons WHERE id = '%s'", $getRID);
	$result = mysqli_query($conex, $query);
	$row = mysqli_fetch_array($result);
	if ($result) {
		echo "<p>Reason: " . $row[0] . "</p>";
	} else {
		echo "<p class='error'>Reason: Error!</p>";
	}
	$query = sprintf("SELECT CONCAT(last_name,', ',first_name) FROM Consultants WHERE id = '%s'", $getCID);
	$result = mysqli_query($conex, $query);
	$row = mysqli_fetch_array($result);
	if ($result) {
		echo "<p>Consultant: " . $row[0] . "</p>";
	} else {
		echo "<p class='error'>Consultant: Error!</p>";
	}
	$query = sprintf("SELECT CONCAT(detail,' ',building_id,room) FROM Locations WHERE id = '%s'", $getLID);
	$result = mysqli_query($conex, $query);
	$row = mysqli_fetch_array($result);
	if ($result) {
		echo "<p>Location: " . $row[0] . "</p>";
	} else {
		echo "<p class='error'>Location: Error!</p>";
	}
	echo "<p><h2 style='color: #6CBB3C'>Take a seat please.</h2></p>";

	mysqli_free_result($result);
	mysqli_close($conex);

?>