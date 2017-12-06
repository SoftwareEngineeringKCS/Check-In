<?php #JORGE ESPADA

$show_error = FALSE; // Maintenance.
$page_title = 'Kean Career Services';
include ('includes/header.html');
include ('includes/db_config.php');

echo "<div class='menu_help' id='help' style='display: none;'>";
echo "<p><b>Staff:</b><br>Administrators can set-up availability periods, manage appointments, and view statistics. Administrators must login in order to use these features.</p>";
echo "<p><b>Appointments:</b><br>Students can book appointments and update personal information from previous meetings.</p>";
echo "<p><b>Check-In:</b><br>Let the office know that you are waiting for counseling. There are two options: (1) By-Appointment, you will need your student id and a confirmation code which was sent to you by email. (2) Walk-In, no appointment is needed (longer waiting time).</p>";
echo "<center><p><< CLICK HELP TO CLOSE >></p></center>";
echo "</div>";
	
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
				echo "<p class='error'>";
				if ($_POST['student_id1'] == '') echo "\"ID\", ";
				if ($_POST['confirm_num'] == '') echo "\"Confirmation Code\"";
				echo "</p>";
				echo "<p><button type='button' value='BA' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
			} else {
				$query = sprintf("CALL usp_CheckIn_ByAppointment('%s','%s', '%s',@done,@id,@name,@reason,@consultant,@location,@message)", strtoupper($_POST['student_id1']), strtoupper($_POST['confirm_num']), $_POST['type_appointment']);
				$result = mysqli_query($conex, $query);
				if ($result) {
					$row = mysqli_fetch_array($result);
					// Done or not.
					if ($row[0] == 0) {
						echo "<p class='error'>" . $row[6] . "</p>";
						echo "<p><button type='button' value='BA' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
					} else {
						echo "<p class='result'>Student ID: " . $row[1];
						echo "<br>Name: " . $row[2];
						echo "<br>Reason: " . $row[3];
						echo "<br>Consultant: " . $row[4];
						echo "<br>Location: " . $row[5];
						echo "<br><h2 style='color: #6CBB3C'><i>" . $row[6] . "</i></h2></p>";
						# Auto-Redirect


					}

					mysqli_free_result($result);
				} else {
					echo "<p class='error'>Check-In Validation... Failed! [Connection Error]";
					if ($show_error) {
						echo "<br>[<i>" . mysqli_error() . "</i>]";
					}
					echo "<br>Contact Administrator!</p>";
					echo "<p><a href='check_in.php'>TRY AGAIN</a></p>";
				}
				
				mysqli_close($conex);
			}
		} else if ($_POST['type_appointment'] == 'Walk-In') {
			if ($_POST['location'] == '' || $_POST['consultant'] == '' || $_POST['reason'] == '' || 
				$_POST['student_id2'] == '' || $_POST['first_name'] == '' || $_POST['last_name'] == '' || 
				$_POST['email'] == '') {
				echo "<h2>The following fields cannot be empty!</h2>";
				echo "<p class='error'>";
				if ($_POST['location'] == '') echo "\"Location\", ";
				if ($_POST['consultant'] == '') echo "\"Consultant\", ";
				if ($_POST['reason'] == '') echo "\"Reason\", ";
				if ($_POST['student_id2'] == '') echo "\"ID\", ";
				if ($_POST['first_name'] == '') echo "\"First Name\", ";
				if ($_POST['last_name'] == '') echo "\"Last Name\", ";
				if ($_POST['email'] == '') echo "\"E-mail\"";
				echo "</p>";
				echo "<p><button type='button' value='WI' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
			} else {
				# Validate Id and Email.
				$query = sprintf("SELECT * FROM Students WHERE id = '%s' AND email = '%s'", $_POST['student_id2'], $_POST['email']);
				$result = mysqli_query($conex, $query);
				if ($result) {
					if (mysqli_num_rows($result) > 0) {
						# Validate Identical data when updating (Rows changed = 0).
						$row = mysqli_fetch_array($result);
						$getFname = $row['first_name'];
						$getLname = $row['last_name'];

						# Update Student.
						$query = sprintf("UPDATE Students SET first_name = '%s', last_name = '%s' WHERE id = '%s'", $_POST['first_name'], $_POST['last_name'], $_POST['student_id2']);
						$res1 = mysqli_query($conex, $query);

						if (mysqli_affected_rows($conex) == 0 && ($getFname != $_POST['first_name'] || $getLname != $_POST['last_name'])) {
							echo "<p class='error'>Validating ID and Email... Failed! [No Student found]";
							if ($show_error) {
								echo "<br>[<i>" . mysqli_error() . "</i>]";
							}
							echo "<br>Contact Administrator!</p>";
							echo "<p><a href='check_in.php'>TRY AGAIN</a></p>";
						} else {
							# Save check-in.
							$set_in_datetime = date("Y-m-d H:i:s");
							$query = sprintf("INSERT INTO Students_Check_In VALUES(NULL, 1, '%s', '%s', '%s', '%s', '%s', '%s', '')", $_POST['student_id2'], $_POST['consultant'], $_POST['location'], $_POST['type_appointment'], $_POST['reason'], $set_in_datetime);
							$res2 = mysqli_query($conex, $query);

							if (mysqli_affected_rows($conex) == 0) {
								echo "<p class='result'>Updating student new data... Done!</p>";
								echo "<p class='error'>Saving Check-In... Failed! [Connection Error]";
								if ($show_error) {
									echo "<br>[<i>" . mysqli_error() . "</i>]";
								}
								echo "<br>Contact Administrator!</p>";
								echo "<p><a href='check_in.php'>TRY AGAIN</a></p>";
							} else {
								# Show results.
								echo "<p class='result'>Updating student new data... Done!";
								echo "<br>Saving Check-In... Done!";
								echo "<br>";
								echo "<br>Student ID: " . $_POST['student_id2'];
								echo "<br>Name: " . $_POST['last_name'] . ", " . $_POST['first_name'];

								$query = sprintf("SELECT description FROM Reasons WHERE id = '%s'", $_POST['reason']);
								$res3 = mysqli_query($conex, $query);
								if ($res3) {
									if (mysqli_num_rows($res3) > 0) {
										$row = mysqli_fetch_array($res3);
										echo "<br>Reason: " . $row[0];
									} else {
										echo "<p class='error'>Reason: [No Reason found]</p>";	
									}
									mysqli_free_result($res3);
								} else {
									echo "<p class='error'>Reason: [Connection Error]</p>";
								}

								$query = sprintf("SELECT CONCAT(last_name,', ',first_name) FROM Consultants WHERE id = '%s'", $_POST['consultant']);
								$res4 = mysqli_query($conex, $query);
								if ($res4) {
									if (mysqli_num_rows($res4) > 0) {
										$row = mysqli_fetch_array($res4);
										echo "<br>Consultant: " . $row[0];
									} else {
										echo "<p class='error'>Consultant: [No Consultant found]</p>";	
									}
									mysqli_free_result($res4);
								} else {
									echo "<p class='error'>Consultant: [Connection Error]</p>";
								}

								$query = sprintf("SELECT CONCAT(detail,' ',building_id,room) FROM Locations WHERE id = '%s'", $_POST['location']);
								$res5 = mysqli_query($conex, $query);
								if ($res5) {
									if (mysqli_num_rows($res5) > 0) {
										$row = mysqli_fetch_array($res5);
										echo "<br>Location: " . $row[0];
									} else {
										echo "<p class='error'>Location: [No Location found]</p>";	
									}
									mysqli_free_result($res5);
								} else {
									echo "<p class='error'>Location: [Connection Error]</p>";
								}

								echo "<br><h2 style='color: #6CBB3C'>Take a seat please.</h2>";
								echo "</p>";

								# Auto-Redirect


							}
						}

					} else {
						# Validate Student.
						$query = sprintf("SELECT * FROM Students WHERE id = '%s'", $_POST['student_id2']);
						$res1 = mysqli_query($conex, $query);
						if ($res1) {
							if (mysqli_num_rows($res1) > 0) {
								echo "<h2>This ID \"" . $_POST['student_id2'] .  "\" has been used before!</h2>";
								# Validate email.
								$query = sprintf("SELECT * FROM Students WHERE email = '%s'", $_POST['email']);
								$res2 = mysqli_query($conex, $query);
								if ($res2) {
									if (mysqli_num_rows($res2) > 0) {
										echo "<p class='result'><u>NEW DATA</u>
												<br>  Name: " . $_POST['last_name'] . ", " . $_POST['first_name'] .
												"<br>  E-mail: " . $_POST['email'] . 
												"</p><p class='ad'><u>BLOCKING</u>: 
												<br>The new email is used by another student, please click back and use a different email.</p>";
										echo "<p><button type='button' value='WI' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
									} else {
										# If confirm.
										$passSID = $_POST['student_id2'];
										$passSFN = $_POST['first_name'];
										$passSLN = $_POST['last_name'];
										$passSEM = $_POST['email'];
										$passCID = $_POST['consultant'];
										$passLID = $_POST['location'];
										$passTA = $_POST['type_appointment'];
										$passRID = $_POST['reason'];

										echo "<p class='result'><u>NEW DATA</u>
												<br>  Name: " . $_POST['last_name'] . ", " . $_POST['first_name'] .
												"<br>  E-mail: " . $_POST['email'] . 
												"</p><p class='ad'><u>WARNING</u>: 
												<br>If you choose \"CONFIRM\" student information will be updated with the new data. 
												<u>Identification will be required during meeting</u>. If you believe that
												someone else has used your information, please confirm and let the office
												know about it, thank you.</p>";
										echo "<p><button type='button' value='WI' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button>
												<button type='button' style='height: 30px;' onclick='confirmWalkin()'>CONFIRM</button></p>";
									}

									mysqli_free_result($res2);
								} else {
									echo "<p class='error'>Email Validation... Failed! [Connection Error]";
									if ($show_error) {
										echo "<br>[<i>" . mysqli_error() . "</i>]";
									}
									echo "<br>Contact Administrator!</p>";
									echo "<p><a href='check_in.php'>TRY AGAIN</a></p>";
								}
							} else {
								# Validate email.
								$query = sprintf("SELECT * FROM Students WHERE email = '%s'", $_POST['email']);
								$res2 = mysqli_query($conex, $query);
								if ($res2) {
									if (mysqli_num_rows($res2) > 0) {
										echo "<p class='result'><u>NEW DATA</u>
												<br>  Name: " . $_POST['last_name'] . ", " . $_POST['first_name'] .
												"<br>  E-mail: " . $_POST['email'] . 
												"</p><p class='ad'><u>BLOCKING</u>: 
												<br>The new email is used by another student, please click back and use a different email.</p>";
										echo "<p><button type='button' value='WI' style='height: 30px;' onclick='mainDisplay(this)'>BACK</button></p>";
									} else {
										# Save New Student.
										$query = sprintf("INSERT INTO Students VALUES('%s', '%s', '%s', '', '', '', '%s', '', '', '', '', '')", $_POST['student_id2'], $_POST['first_name'], $_POST['last_name'], $_POST['email']);
										$res3 = mysqli_query($conex, $query);

										if (mysqli_affected_rows($conex) == 0) {
											echo "<p class='error'>Saving new student... Failed! [Connection Error]";
											if ($show_error) {
												echo "<br>[<i>" . mysqli_error() . "</i>]";
											}
											echo "<br>Contact Administrator!</p>";
											echo "<p><a href='check_in.php'>TRY AGAIN</a></p>";
										} else {
											# Save check-in.
											$set_in_datetime = date("Y-m-d H:i:s");
											$query = sprintf("INSERT INTO Students_Check_In VALUES(NULL, 1, '%s', '%s', '%s', '%s', '%s', '%s', '')", $_POST['student_id2'], $_POST['consultant'], $_POST['location'], $_POST['type_appointment'], $_POST['reason'], $set_in_datetime);
											$res4 = mysqli_query($conex, $query);

											if (mysqli_affected_rows($conex) == 0) {
												echo "<p class='result'>Saving new student... Done!</p>";
												echo "<p class='error'>Saving Check-In... Failed! [Connection Error]";
												if ($show_error) {
													echo "<br>[<i>" . mysqli_error() . "</i>]";
												}
												echo "<br>Contact Administrator!</p>";
												echo "<p><a href='check_in.php'>TRY AGAIN</a></p>";
											} else {
												# Show results.
												echo "<p class='result'>Saving new student... Done!";
												echo "<br>Saving Check-In... Done!";
												echo "<br>";
												echo "<br>Student ID: " . $_POST['student_id2'];
												echo "<br>Name: " . $_POST['last_name'] . ", " . $_POST['first_name'];

												$query = sprintf("SELECT description FROM Reasons WHERE id = '%s'", $_POST['reason']);
												$res5 = mysqli_query($conex, $query);
												if ($res5) {
													if (mysqli_num_rows($res5) > 0) {
														$row = mysqli_fetch_array($res5);
														echo "<br>Reason: " . $row[0];
													} else {
														echo "<p class='error'>Reason: [No Reason found]</p>";	
													}
													mysqli_free_result($res5);
												} else {
													echo "<p class='error'>Reason: [Connection Error]</p>";
												}

												$query = sprintf("SELECT CONCAT(last_name,', ',first_name) FROM Consultants WHERE id = '%s'", $_POST['consultant']);
												$res6 = mysqli_query($conex, $query);
												if ($res6) {
													if (mysqli_num_rows($res6) > 0) {
														$row = mysqli_fetch_array($res6);
														echo "<br>Consultant: " . $row[0];
													} else {
														echo "<p class='error'>Consultant: [No Consultant found]</p>";	
													}
													mysqli_free_result($res6);
												} else {
													echo "<p class='error'>Consultant: [Connection Error]</p>";
												}

												$query = sprintf("SELECT CONCAT(detail,' ',building_id,room) FROM Locations WHERE id = '%s'", $_POST['location']);
												$res7 = mysqli_query($conex, $query);
												if ($res7) {
													if (mysqli_num_rows($res7) > 0) {
														$row = mysqli_fetch_array($res7);
														echo "<br>Location: " . $row[0];
													} else {
														echo "<p class='error'>Location: [No Location found]</p>";	
													}
													mysqli_free_result($res7);
												} else {
													echo "<p class='error'>Location: [Connection Error]</p>";
												}

												echo "<br><h2 style='color: #6CBB3C'>Take a seat please.</h2>";
												echo "</p>";

												# Auto-Redirect


											}
										}

									}

									mysqli_free_result($res2);
								} else {
									echo "<p class='error'>Email Validation... Failed! [Connection Error]";
									if ($show_error) {
										echo "<br>[<i>" . mysqli_error() . "</i>]";
									}
									echo "<br>Contact Administrator!</p>";
									echo "<p><a href='check_in.php'>TRY AGAIN</a></p>";
								}
							}

							mysqli_free_result($res1);
						} else {
							echo "<p class='error'>Student Validation... Failed! [Connection Error]";
							if ($show_error) {
								echo "<br>[<i>" . mysqli_error() . "</i>]";
							}
							echo "<br>Contact Administrator!</p>";
							echo "<p><a href='check_in.php'>TRY AGAIN</a></p>";
						}
					}

					mysqli_free_result($result);
				} else {
					echo "<p class='error'>Student Validation... Failed! [Connection Error]";
					if ($show_error) {
						echo "<br>[<i>" . mysqli_error() . "</i>]";
					}
					echo "<br>Contact Administrator!</p>";
					echo "<p><a href='check_in.php'>TRY AGAIN</a></p>";
				}
				
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
	function showHelp() {
	    var x = document.getElementById("help");
	    if (x.style.display === "none") {
	        x.style.display = "block";
	    } else {
	        x.style.display = "none";
	    }
	}
</script>
<script>
	function confirmWalkin() {
		//
		var x = document.getElementById("check_in_result");
	    var y = document.getElementById("check_in_confirmed");
	    x.style.display = "none";
		y.style.display = "block";
		//
		var passSID = "<?php echo $passSID ?>";
		var passSFN = "<?php echo $passSFN ?>";
		var passSLN = "<?php echo $passSLN ?>";
		var passSEM = "<?php echo $passSEM ?>";
		var passCID = "<?php echo $passCID ?>";
		var passLID = "<?php echo $passLID ?>";
		var passTA = "<?php echo $passTA ?>";
		var passRID = "<?php echo $passRID ?>";

	    var htm = $.ajax({
	    type: "POST",
	    url: "confirm_walk_in.php",
	    data: {getSID: passSID, getSFN: passSFN, getSLN: passSLN, getSEM: passSEM, getCID: passCID, getLID: passLID, getTA: passTA, getRID: passRID},
	    async: false
	    }).responseText;

	    if (htm) {
	        $("#check_in_confirmed").html("<p>" + htm + "</p>");
	        return true;
	    } else {
	        $("#check_in_confirmed").html("<p class='error'>Problem trying to set Walk-In!</p>");
	        return false;
	    }
	}
</script>
<script type="text/javascript">
	function mainDisplay(btn) {
	    var x = document.getElementById("check_in_process");
	    var y = document.getElementById("check_in_result");
	    if (x.style.display === "block" && y.style.display === "none") {
	    	x.style.display = "none";
		    y.style.display = "block";
	    } else if (x.style.display === "none" && y.style.display === "block") {
	    	x.style.display = "block";
		    y.style.display = "none";
	    }
	    if (btn.value === "BA") {
	    	byAppointment();
	    } else if (btn.value === "WI") {
	    	walkIn();
	    }
	}
</script>
<div id="check_in_process"<?php if (isset($_POST['type_appointment'])) echo ' style="display: none;"'; ?>>
	<h1>Check-In Process</h1>
	<form action="check_in.php" method="post">	
		<h2>* Required Fields</h2>
		<p>
			<span class="input">
				<input type="radio" name="type_appointment" value="By-Appointment"<?php if (isset($_POST['type_appointment']) && ($_POST['type_appointment'] == 'By-Appointment')) echo ' checked="checked"'; ?> onclick="byAppointment()" /> By-Appointment
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
			<p><input class="button" type="submit" name="submit" value="CHECK-IN" style="height: 30px; width: 200px" /></p>
		</div>
	</form>
</div>

<?php include ('includes/footer.html'); ?>
