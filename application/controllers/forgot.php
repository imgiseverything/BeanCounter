<?php

	/**
	 *	Forgotten Password Controller
	 */

	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");
	
	// Attempt to use authorise object's forgot method
	$user_feedback = $objAuthorise->forgot();
	$objFeedback = new Feedback($user_feedback);
	
	// View
	include(APPLICATION_PATH . "/views/forgot.php");
	
?>