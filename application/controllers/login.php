<?php

	/**
	 *	Log in Controller
	 */

	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");
	
	// Attempt user login via authorise object
	$user_feedback = $objAuthorise->login();
	$objFeedback = new Feedback($user_feedback);
	
	// View
	include(APPLICATION_PATH . "/views/login.php");
	exit;
?>	