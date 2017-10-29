<?php #JORGE ESPADA

$page_title = 'Kean Career Services';
include ('includes/header.html');

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Minimal form validation:
	if (isset($_POST['type_appointment'])) {
		include ('includes/db_config.php');
		$conex = mysqli_connect($server, $login, $password, $database) or die("Error: Unable to connect to DataBase.");	

		// Print the results:
		echo "<h1>Check-In Result</h1>";
		echo "<h2>" . $_POST['type_appointment'] . "</h2>";
		if (!$_POST['student_id1'] == "") {
			echo "<p>ID: " . $_POST['student_id1'] . "</p>";

		} else {
			echo "<p>ID: " . $_POST['student_id2'] . "</p>";
			echo "<p>Name: " . $_POST['last_name'] . ", " . $_POST['first_name'] . "</p>";
			$query = sprintf("SELECT description FROM Reasons WHERE id = '%s'", $_POST['reason']);
			$result = mysqli_query($conex, $query);
			$row = mysqli_fetch_array($result);
			echo "<p>Reason: " . $row[0] . "</p>";
			$query = sprintf("SELECT CONCAT(last_name,', ',first_name) FROM Consultants WHERE id = '%s'", $_POST['consultant']);
			$result = mysqli_query($conex, $query);
			$row = mysqli_fetch_array($result);
			echo "<p>Consultant: " . $row[0] . "</p>";
			$query = sprintf("SELECT CONCAT(detail,' ',building_id,room) FROM Locations WHERE id = '%s'", $_POST['location']);
			$result = mysqli_query($conex, $query);
			$row = mysqli_fetch_array($result);
			echo "<p>Location: " . $row[0] . "</p>";
			echo "<p><h2 style='color: #6CBB3C'><i>Take a seat please.</i></h2></p>";
		}

		mysqli_free_result($result);
		mysqli_close($conex);
	} else { // Invalid submitted values.
		echo '<h1>Error!</h1>
		<p class="error">Please enter valid data.</p>';
	}
	
} // End of main submission IF.

// Leave the PHP section and create the HTML form:
?>

<div id="check_in_process"<?php if (isset($_POST['type_appointment'])) echo ' style="display: none;"'; ?>>
	<h1>Check-In Process</h1>
	<form action="check_in.php" method="post">	
		<p>Type of Check-In:<br><br>
			<span class="input">
				<input type="radio" name="type_appointment" value="By-Appoitnment"<?php if (isset($_POST['type_appointment']) && ($_POST['type_appointment'] == 'By-Appoitnment')) echo ' checked="checked"'; ?> onclick="byAppointment()" /> By-Appointment
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
			<p>Student ID (without leading zeros):
				<br><input type="text" name="student_id1" value="<?php if (isset($_POST['student_id1'])) echo $_POST['student_id1']; ?>" style="width: 190px" />
			</p>
			<p>Confirmation Code:
				<br><input type="text" name="confirm_num" value="<?php if (isset($_POST['confirm_num'])) echo $_POST['confirm_num']; ?>" style="width: 190px" />
			</p>
		</div>
		<div id="show_walk_in" style="display: none;">
			<?php include ('includes/db_config.php');
				$conex = mysqli_connect($server, $login, $password, $database) or die("Error: Unable to connect to DataBase.");
				
				#LOCATIONS.
				$query = "SELECT id, CONCAT(detail,' ',building_id,room) AS location FROM Locations ORDER BY location";
				$result = mysqli_query($conex, $query);
				if ($result) {
					if (mysqli_num_rows($result) > 0) {
						echo "<p>Location:";
						echo "<br><select name='location' required='required' style='width: 200px'>";
							while ($row = mysqli_fetch_array($result)) {
								$loc_id = $row['id'];
								$loc_location = $row['location'];
								echo "<option value='$loc_id'>$loc_location</option>\n";
							}							
						echo "</select>";
						echo "</p>";
					} else {
						echo "<p>Location:";
						echo "<br><select name='location' required='required' style='width: 200px'>";
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
						echo "<p>Consultant:";
						echo "<br><select name='consultant' required='required' style='width: 200px'>";
							while ($row = mysqli_fetch_array($result)) {
								$con_id = $row['id'];
								$con_consultant = $row['consultant'];
								echo "<option value='$con_id'>$con_consultant</option>\n";
							}							
						echo "</select>";
						echo "</p>";
					} else {
						echo "<p>Consultant:";
						echo "<br><select name='consultant' required='required' style='width: 200px'>";
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
						echo "<p>Reason:";
						echo "<br><select name='reason' required='required' style='width: 200px'>";
							while ($row = mysqli_fetch_array($result)) {
								$re_id = $row['id'];
								$re_description = $row['description'];
								echo "<option value='$re_id'>$re_description</option>\n";
							}							
						echo "</select>";
						echo "</p>";
					} else {
						echo "<p>Reasons:";
						echo "<br><select name='reason' required='required' style='width: 200px'>";
							echo "<option value='' selected>EMPTY LIST</option>\n";
						echo "</select>";
						echo "</p>";
					}
				} else {

				}

				mysqli_free_result($result);
				mysqli_close($conex);
			?>

			<p>Student ID (without leading zeros):
				<br><input type="text" name="student_id2" required="required" value="<?php if (isset($_POST['student_id2'])) echo $_POST['student_id2']; ?>" style="width: 190px" />
			</p>
			<p>First Name:
				<br><input type="text" name="first_name" required="required" value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name']; ?>" style="width: 190px" />
			</p>
			<p>Last Name:
				<br><input type="text" name="last_name" required="required" value="<?php if (isset($_POST['last_name'])) echo $_POST['last_name']; ?>" style="width: 190px" />
			</p>
			<p>E-mail:
				<br><input type="text" name="email" required="required" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" style="width: 190px" />
			</p>
		</div>
		<div id="show_submit" style="display: none;">
			<p><input type="submit" name="submit" value="CHECK-IN" style="background-color: #6CBB3C; width: 200px" /></p>
		</div>
	</form>
</div>

<?php include ('includes/footer.html'); ?>