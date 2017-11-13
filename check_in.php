<?php #JORGE ESPADA

$page_title = 'Kean Career Services';
include ('includes/header.html');
include ('includes/db_config.php');
// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	echo "<div id='check_in_result' style='display: block;'>";
	// Print the results:
	echo "<h1>Check-In Result</h1>";
	// Minimal form validation:
	if (isset($_POST['type_appointment'])) {
		echo "<h2>" . $_POST['type_appointment'] . "</h2>";
		if ($_POST['type_appointment'] == 'By-Appointment') {
			if ($_POST['student_id1'] == '' || $_POST['confirm_num'] == '') {
				echo "<h2>The following fields cannot be empty!</h2>";
				if ($_POST['student_id1'] == '') echo "<p class='error'>\"ID\"</p>";
				if ($_POST['confirm_num'] == '') echo "<p class='error'>\"Confirmation Code\"</p>";
				echo "<p><button type='button' style='height: 30px;' onclick='goBack()'>BACK</button>";
			} else {
				$query = sprintf("CALL usp_CheckIn_ByAppointment('%s','%s', '%s',@done,@id,@name,@reason,@consultant,@location,@message)", strtoupper($_POST['student_id1']), strtoupper($_POST['confirm_num']), $_POST['type_appointment']);
				$result = mysqli_query($conex, $query);
				$row = mysqli_fetch_array($result);
				// Done or not.
				if ($row[0] == 0) {
					echo "<p class='error'>" . $row[6] . "</p>";
					echo "<p><button type='button' style='height: 30px;' onclick='goBack()'>BACK</button>";
				} else {
					echo "<p>Student ID: " . $row[1] . "</p>";
					echo "<p>Name: " . $row[2] . "</p>";
					echo "<p>Reason: " . $row[3] . "</p>";
					echo "<p>Consultant: " . $row[4] . "</p>";
					echo "<p>Location: " . $row[5] . "</p>";
					echo "<p><h2 style='color: #6CBB3C'><i>" . $row[6] . "</i></h2></p>";
				}

				mysqli_free_result($result);
				mysqli_close($conex);
			}
		} else if ($_POST['type_appointment'] == 'Walk-In') {
			if ($_POST['location'] == '' || $_POST['consultant'] == '' || $_POST['reason'] == '' || 
				$_POST['student_id2'] == '' || $_POST['first_name'] == '' || $_POST['last_name'] == '' || 
				$_POST['email'] == '') {
				echo "<h2>The following fields cannot be empty!</h2>";
				if ($_POST['reason'] == '') echo "<p class='error'>\"Reason\"</p>";
				if ($_POST['student_id2'] == '') echo "<p class='error'>\"ID\"</p>";
				if ($_POST['first_name'] == '') echo "<p class='error'>\"First Name\"</p>";
				if ($_POST['last_name'] == '') echo "<p class='error'>\"Last Name\"</p>";
				if ($_POST['email'] == '') echo "<p class='error'>\"E-mail\"</p>";
				if ($_POST['consultant'] == '') echo "<p class='error'>\"Consultant\"</p>";
				if ($_POST['location'] == '') echo "<p class='error'>\"Location\"</p>";
				echo "<p><button type='button' style='height: 30px;' onclick='goBack()'>BACK</button>";
			} else {
				# Validate Id and Email.
				$query = sprintf("SELECT * FROM Students WHERE id = '%s' AND email = '%s'", $_POST['student_id2'], $_POST['email']);
				$result = mysqli_query($conex, $query);
				if ($result) {
					if (mysqli_num_rows($result) > 0) {
						# Update Student.
						$query = sprintf("UPDATE Students SET first_name = '%s', last_name = '%s' WHERE id = '%s'", $_POST['first_name'], $_POST['last_name'], $_POST['student_id2']);
						$result = mysqli_query($conex, $query);
						# Save check-in.
						$set_in_datetime = date("Y-m-d H:i:s");
						$query = sprintf("INSERT INTO Students_Check_In VALUES(NULL, 1, '%s', '%s', '%s', '%s', '%s', '%s', '')", $_POST['student_id2'], $_POST['consultant'], $_POST['location'], $_POST['type_appointment'], $_POST['reason'], $set_in_datetime);
						$result = mysqli_query($conex, $query);
						# Show results.
						echo "<p>Student ID: " . $_POST['student_id2'] . "</p>";
						echo "<p>Name: " . $_POST['last_name'] . ", " . $_POST['first_name'] . "</p>";
						$query = sprintf("SELECT description FROM Reasons WHERE id = '%s'", $_POST['reason']);
						$result = mysqli_query($conex, $query);
						$row = mysqli_fetch_array($result);
						if ($result) {
							echo "<p>Reason: " . $row[0] . "</p>";
						} else {
							echo "<p class='error'>Reason: Error!</p>";
						}
						$query = sprintf("SELECT CONCAT(last_name,', ',first_name) FROM Consultants WHERE id = '%s'", $_POST['consultant']);
						$result = mysqli_query($conex, $query);
						$row = mysqli_fetch_array($result);
						if ($result) {
							echo "<p>Consultant: " . $row[0] . "</p>";
						} else {
							echo "<p class='error'>Consultant: Error!</p>";
						}
						$query = sprintf("SELECT CONCAT(detail,' ',building_id,room) FROM Locations WHERE id = '%s'", $_POST['location']);
						$result = mysqli_query($conex, $query);
						$row = mysqli_fetch_array($result);
						if ($result) {
							echo "<p>Location: " . $row[0] . "</p>";
						} else {
							echo "<p class='error'>Location: Error!</p>";
						}
						echo "<p><h2 style='color: #6CBB3C'>Take a seat please.</h2></p>";
					} else {
						# Validate Student.
						$query = sprintf("SELECT * FROM Students WHERE id = '%s'", $_POST['student_id2']);
						$result = mysqli_query($conex, $query);
						if ($result) {
							if (mysqli_num_rows($result) > 0) {
								echo "<h2>This ID \"" . $_POST['student_id2'] .  "\" has been used before!</h2>";
								# Validate email.
								$query = sprintf("SELECT * FROM Students WHERE email = '%s'", $_POST['email']);
								$result = mysqli_query($conex, $query);
								if ($result) {
									if (mysqli_num_rows($result) > 0) {
										echo "<p><u>NEW DATA</u>
												<br>  Name: " . $_POST['last_name'] . ", " . $_POST['first_name'] .
												"<br>  E-mail: " . $_POST['email'] . 
												"</p><p class='ad'><u>BLOCKING</u>: 
												<br>The new email is used by another student, please click back and use a different email.</p>";
										echo "<p><button type='button' style='height: 30px;' onclick='goBack()'>BACK</button>";
									} else {
										echo "<p><u>NEW DATA</u>
												<br>  Name: " . $_POST['last_name'] . ", " . $_POST['first_name'] .
												"<br>  E-mail: " . $_POST['email'] . 
												"</p><p class='ad'><u>WARNING</u>: 
												<br>If you choose \"CONFIRM\" student information will be updated with the new data. 
												<u>Identification will be required during meeting</u>.\" If you believe that
												someone else has used your information, please confirm and let the office
												know about it, thank you.</p>";
										echo "<p><button type='button' style='height: 30px;' onclick='goBack()'>BACK</button>


												<button type='button' style='height: 30px;' onclick=''>CONFIRM</button></p>"; #REMOVE TYPE
									}
								} else {
									echo "<br>Problem trying to validate email: " . mysqli_error();
									echo "<br>Contact Administrator!";
									echo "<br><a href='check_in.php'>TRY AGAIN</a>";
								}
							} else {
								# Validate email.
								$query = sprintf("SELECT * FROM Students WHERE email = '%s'", $_POST['email']);
								$result = mysqli_query($conex, $query);
								if ($result) {
									if (mysqli_num_rows($result) > 0) {
										echo "<p><u>NEW DATA</u>
												<br>  Name: " . $_POST['last_name'] . ", " . $_POST['first_name'] .
												"<br>  E-mail: " . $_POST['email'] . 
												"</p><p class='ad'><u>BLOCKING</u>: 
												<br>The new email is used by another student, please click back and use a different email.</p>";
										echo "<p><button type='button' style='height: 30px;' onclick='goBack()'>BACK</button>";
									} else {
										# Save New Student.
										$query = sprintf("INSERT INTO Students VALUES('%s', '%s', '%s', '', '', '', '%s', '', '', '', '', '')", $_POST['student_id2'], $_POST['first_name'], $_POST['last_name'], $_POST['email']);
										$result = mysqli_query($conex, $query);
										# Save check-in.
										$set_in_datetime = date("Y-m-d H:i:s");
										$query = sprintf("INSERT INTO Students_Check_In VALUES(NULL, 1, '%s', '%s', '%s', '%s', '%s', '%s', '')", $_POST['student_id2'], $_POST['consultant'], $_POST['location'], $_POST['type_appointment'], $_POST['reason'], $set_in_datetime);
										$result = mysqli_query($conex, $query);
										# Show results.
										echo "<p>Student ID: " . $_POST['student_id2'] . "</p>";
										echo "<p>Name: " . $_POST['last_name'] . ", " . $_POST['first_name'] . "</p>";
										$query = sprintf("SELECT description FROM Reasons WHERE id = '%s'", $_POST['reason']);
										$result = mysqli_query($conex, $query);
										$row = mysqli_fetch_array($result);
										if ($result) {
											echo "<p>Reason: " . $row[0] . "</p>";
										} else {
											echo "<p class='error'>Reason: Error!</p>";
										}
										$query = sprintf("SELECT CONCAT(last_name,', ',first_name) FROM Consultants WHERE id = '%s'", $_POST['consultant']);
										$result = mysqli_query($conex, $query);
										$row = mysqli_fetch_array($result);
										if ($result) {
											echo "<p>Consultant: " . $row[0] . "</p>";
										} else {
											echo "<p class='error'>Consultant: Error!</p>";
										}
										$query = sprintf("SELECT CONCAT(detail,' ',building_id,room) FROM Locations WHERE id = '%s'", $_POST['location']);
										$result = mysqli_query($conex, $query);
										$row = mysqli_fetch_array($result);
										if ($result) {
											echo "<p>Location: " . $row[0] . "</p>";
										} else {
											echo "<p class='error'>Location: Error!</p>";
										}
										echo "<p><h2 style='color: #6CBB3C'>Take a seat please.</h2></p>";
									}
								} else {
									echo "<br>Problem trying to validate email: " . mysqli_error();
									echo "<br>Contact Administrator!";
									echo "<br><a href='check_in.php'>TRY AGAIN</a>";
								}
							}
						} else {
							echo "<br>Problem trying to validate student: " . mysqli_error();
							echo "<br>Contact Administrator!";
							echo "<br><a href='check_in.php'>TRY AGAIN</a>";
						}
					}
				} else {
					echo "<br>Problem trying to validate Student: " . mysqli_error();
					echo "<br>Contact Administrator!";
					echo "<br><a href='check_in.php'>TRY AGAIN</a>";
				}
				
				mysqli_free_result($result);
				mysqli_close($conex);				
			}
		}
	} else { // Invalid submitted values.
		echo '<h1>Error!</h1>
		<p class="error">Please enter valid data.</p>';
	}
	echo "</div>";
	echo "<div id='check_in_confirmed' style='display: none;'></div>";
}

?>
<script type="text/javascript">
	function goBack() {
	    var x = document.getElementById("check_in_process");
	    var y = document.getElementById("check_in_result");
	    if (x.style.display === "block" && y.style.display === "none") {
	    	x.style.display = "none";
		    y.style.display = "block";
	    } else if (x.style.display === "none" && y.style.display === "block") {
	    	x.style.display = "block";
		    y.style.display = "none";
	    }
	}
</script>
<div id="check_in_process"<?php if (isset($_POST['type_appointment'])) echo ' style="display: none;"'; ?>>
	<h1>Check-In Process</h1>
	<form action="check_in.php" method="post">	
		<h2>* Required Fields</h2>
		<p>
			<span class="input">
				<input type="radio" name="type_appointment" value="By-Appointment"<?php if (isset($_POST['type_appointment']) && ($_POST['type_appointment'] == 'By-Appoitnment')) echo ' checked="checked"'; ?> onclick="byAppointment()" /> By-Appointment
				<input type="radio" name="type_appointment" value="Walk-In"<?php if (isset($_POST['type_appointment']) && ($_POST['type_appointment'] == 'Walk-In')) echo ' checked="checked"'; ?> onclick="walkIn()" /> Walk-In
			</span>
		</p>
		<script type="text/javascript">
			function byAppointment() {
			    var x = document.getElementById("show_by_appointment");
			    var y = document.getElementById("show_walk_in");
			    var z = document.getElementById("show_submit");
			    x.style.display = "block";
			    y.style.display = "none";
			    z.style.display = "block";
			}
			function walkIn() {
			    var x = document.getElementById("show_by_appointment");
			    var y = document.getElementById("show_walk_in");
			    var z = document.getElementById("show_submit");
			    x.style.display = "none";
			    y.style.display = "block";
			    z.style.display = "block";
			}
		</script>
		<div id="show_by_appointment" style="display: none;">
			<p>* Student ID (no leading zeros):
				<br><input type="text" name="student_id1" value="<?php if (isset($_POST['student_id1'])) echo $_POST['student_id1']; ?>" style="width: 190px" />
			</p>
			<p>* Confirmation Code:
				<br><input type="text" name="confirm_num" value="<?php if (isset($_POST['confirm_num'])) echo $_POST['confirm_num']; ?>" style="width: 190px" />
			</p>
		</div>
		<div id="show_walk_in" style="display: none;">
			<?php include ('includes/db_config.php');
								
				#LOCATIONS.
				$query = "SELECT id, CONCAT(detail,' ',building_id,room) AS location FROM Locations ORDER BY location";
				$result = mysqli_query($conex, $query);
				if ($result) {
					if (mysqli_num_rows($result) > 0) {
						echo "<p>* Location:";
						echo "<br><select name='location' style='width: 200px'>";
							//echo "<option value=''>#Select</option>\n";
							while ($row = mysqli_fetch_array($result)) {
								$loc_id = $row['id'];
								$loc_location = $row['location'];
								echo "<option value='$loc_id'>$loc_location</option>\n";
							}							
						echo "</select>";
						echo "</p>";
					} else {
						echo "<p>* Location:";
						echo "<br><select name='location' style='width: 200px'>";
							echo "<option value='' selected>EMPTY LIST</option>\n";
						echo "</select>";
						echo "</p>";
					}
				} else {

				}

				#CONSULTANTS.
				$query = "SELECT id, CONCAT(last_name,', ',first_name) AS consultant FROM Consultants ORDER BY consultant";
				$result = mysqli_query($conex, $query);
				if ($result) {
					if (mysqli_num_rows($result) > 0) {
						echo "<p>* Consultant:";
						echo "<br><select name='consultant' style='width: 200px'>";
							echo "<option value=''>#Select</option>\n";
							while ($row = mysqli_fetch_array($result)) {
								$con_id = $row['id'];
								$con_consultant = $row['consultant'];
								echo "<option value='$con_id'>$con_consultant</option>\n";
							}							
						echo "</select>";
						echo "</p>";
					} else {
						echo "<p>* Consultant:";
						echo "<br><select name='consultant' style='width: 200px'>";
							echo "<option value='' selected>EMPTY LIST</option>\n";
						echo "</select>";
						echo "</p>";
					}
				} else {

				}

				#REASONS.
				$query = "SELECT id, description FROM Reasons ORDER BY description";
				$result = mysqli_query($conex, $query);
				if ($result) {
					if (mysqli_num_rows($result) > 0) {
						echo "<p>* Reason:";
						echo "<br><select name='reason' style='width: 200px'>";
							echo "<option value=''>#Select</option>\n";
							while ($row = mysqli_fetch_array($result)) {
								$re_id = $row['id'];
								$re_description = $row['description'];
								echo "<option value='$re_id'>$re_description</option>\n";
							}							
						echo "</select>";
						echo "</p>";
					} else {
						echo "<p>* Reasons:";
						echo "<br><select name='reason' style='width: 200px'>";
							echo "<option value='' selected>EMPTY LIST</option>\n";
						echo "</select>";
						echo "</p>";
					}
				} else {

				}

				mysqli_free_result($result);
				mysqli_close($conex);
			?>

			<p>* Student ID (without leading zeros):
				<br><input type="text" name="student_id2" value="<?php if (isset($_POST['student_id2'])) echo $_POST['student_id2']; ?>" style="width: 190px" />
			</p>
			<p>* First Name:
				<br><input type="text" name="first_name" value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name']; ?>" style="width: 190px" />
			</p>
			<p>* Last Name:
				<br><input type="text" name="last_name" value="<?php if (isset($_POST['last_name'])) echo $_POST['last_name']; ?>" style="width: 190px" />
			</p>
			<p>* E-mail:
				<br><input type="text" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" style="width: 190px" />
			</p>
		</div>
		<div id="show_submit" style="display: none;">
			<p><input type="submit" name="submit" value="CHECK-IN" style="background-color: #f7dc6f; height: 30px; width: 200px" /></p>
		</div>
	</form>
</div>

<?php include ('includes/footer.html'); ?>
