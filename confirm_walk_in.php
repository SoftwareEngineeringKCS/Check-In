<?php #JORGE ESPADA
	
	$conf_show_error = FALSE; // Maintenance.
	# If ID but no Email.
	include ('includes/db_config.php');

	date_default_timezone_set('America/New_York');

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

	$getSAD = $_REQUEST['getSAD'];
	$getSST = $_REQUEST['getSST'];
	$getSZC = $_REQUEST['getSZC'];
	$getSBD = $_REQUEST['getSBD'];
	$getSCP = $_REQUEST['getSCP'];
	$getSHP = $_REQUEST['getSHP'];
	$getSGE = $_REQUEST['getSGE'];
	$getSER = $_REQUEST['getSER'];
	$getSED = $_REQUEST['getSED'];
	$getSMA = $_REQUEST['getSMA'];

	# Update Student.
	$query = sprintf("UPDATE Students 
						SET first_name = '%s', last_name = '%s', email = '%s', address = '%s', state = '%s', zipcode = '%s', major_id = '%s', edu_id = '%s', er_id = '%s', gender = '%s', birthdate = '%s', cell_phone = '%s', home_phone = '%s' 
						WHERE id = '%s'", 
						$getSFN, $getSLN, $getSEM, $getSAD, $getSST, $getSZC, $getSMA, $getSED, $getSER, $getSGE, $getSBD, $getSCP, $getSHP, $getSID);
	$conf_res1 = mysqli_query($conex, $query);

	if (mysqli_affected_rows($conex) == 0) {
		echo "<p class='error'>Updating student new data... Failed! [No Student found]";
		if ($conf_show_error) {
			echo "<br>[<i>" . mysqli_error() . "</i>]";
		}
		echo "<br>Contact Administrator!</p>";
		echo "<p><a href='check_in.php'>TRY AGAIN</a></p>";
	} else {
		# Save check-in.
		$set_in_datetime = date("Y-m-d H:i:s");
		$query = sprintf("INSERT INTO Students_Check_In VALUES(NULL, 1, '%s', '%s', '%s', '%s', '%s', '%s', '')", $getSID, $getCID, $getLID, $getTA, $getRID, $set_in_datetime);
		$conf_res2 = mysqli_query($conex, $query);

		if (mysqli_affected_rows($conex) == 0) {
			echo "<p class='result'>Updating student new data... Done!</p>";
			echo "<p class='error'>Saving Check-In... Failed! [Connection Error]";
			if ($conf_show_error) {
				echo "<br>[<i>" . mysqli_error() . "</i>]";
			}
			echo "<br>Contact Administrator!</p>";
			echo "<p><a href='check_in.php'>TRY AGAIN</a></p>";
		} else {
			# Show results.
			echo "<p class='result'>Updating student new data... Done!";
			echo "<br>Saving Check-In... Done!";
			echo "<br>";
			echo "<br>Student ID: " . $getSID;
			echo "<br>Name: " . $getSLN . ", " . $getSFN;
			
			$query = sprintf("SELECT description FROM Reasons WHERE id = '%s'", $getRID);
			$conf_res3 = mysqli_query($conex, $query);
			if ($conf_res3) {
				if (mysqli_num_rows($conf_res3) > 0) {
					$row = mysqli_fetch_array($conf_res3);
					echo "<br>Reason: " . $row[0];
				} else {
					echo "<p class='error'>Reason: [No Reason found]</p>";	
				}
				mysqli_free_result($conf_res3);
			} else {
				echo "<p class='error'>Reason: [Connection Error]</p>";
			}
			
			$query = sprintf("SELECT CONCAT(last_name,', ',first_name) FROM Consultants WHERE id = '%s'", $getCID);
			$conf_res4 = mysqli_query($conex, $query);
			if ($conf_res4) {
				if (mysqli_num_rows($conf_res4) > 0) {
					$row = mysqli_fetch_array($conf_res4);
					echo "<br>Consultant: " . $row[0];
				} else {
					echo "<p class='error'>Consultant: [No Consultant found]</p>";	
				}
				mysqli_free_result($conf_res4);
			} else {
				echo "<p class='error'>Consultant: [Connection Error]</p>";
			}
			
			$query = sprintf("SELECT CONCAT(detail,' ',building_id,room) FROM Locations WHERE id = '%s'", $getLID);
			$conf_res5 = mysqli_query($conex, $query);
			if ($conf_res5) {
				if (mysqli_num_rows($conf_res5) > 0) {
					$row = mysqli_fetch_array($conf_res5);
					echo "<br>Location: " . $row[0];
				} else {
					echo "<p class='error'>Location: [No Location found]</p>";	
				}
				mysqli_free_result($conf_res5);
			} else {
				echo "<p class='error'>Location: [Connection Error]</p>";
			}

			echo "<br><h2 style='color: #6CBB3C'>Take a seat please.</h2>";
			echo "</p>";

			# Auto-Redirect


		}
	}

	mysqli_close($conex);

?>
