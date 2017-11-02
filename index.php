<?php

	function create_ad() {
		echo '<p class="ad">BREAKING NEWS: KEAN CAREER SERVICES!</p>';
	}

	$page_title = "Kean Career Services";
	include ('includes/header.html');

	// Call the function:
	create_ad();

	echo "<h1>Welcome to the Appointments Portal of Career Services</h1>";
	echo "<p>The Office of Career Services assists students in preparing to compete in today's changing job market, offering a multitude of services to assist students on their pathway to success. Career Development and Advancement maintains information on the latest career resources, techniques and strategies designed to meet the needs of our diverse student population. Our services are personalized and serve to empower students through each stage of their career development, from first year through graduation.</p>";
	echo "<p><b>Staff:</b> <br>Login to Staff section (Administrators/Consultants). Check/Cancel/Finish active appointments, update personal information, and other.</p>";
	echo "<p><b>Appointments:</b> <br><i><u>Set up appointments</u></i>, update personal information from previous appointments, and check out visit history.</p>";
	echo "<p><b>Check-In:</b> <br>Let the office know that you are waiting for counseling.</p>";

	include ('includes/footer.html');

?>
