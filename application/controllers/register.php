<?php

	/**
	 *	Register Controller
	 *  Allow a new user to register to use the system
	 */
	 
	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");
	
	// Form variables
	$firstname = ($_SERVER['REQUEST_METHOD'] == 'POST') ? read($_POST, 'firstname', '') : '';
	$surname = ($_SERVER['REQUEST_METHOD'] == 'POST') ? read($_POST, 'surname', '') : '';
	$email = ($_SERVER['REQUEST_METHOD'] == 'POST') ? read($_POST, 'email', '') : '';
	$client = ($_SERVER['REQUEST_METHOD'] == 'POST') ? read($_POST, 'client', '') : '';
	
	// Attempt to register user with authorise object
	$user_feedback = $objAuthorise->register();
	$objFeedback = new Feedback($user_feedback);
	
	// View
	include(APPLICATION_PATH . "/views/register.php");
	
?>