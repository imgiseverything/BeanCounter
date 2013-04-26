<?php

	/**
	 *	Contact form Controller
	 * 	Allow users to send email to site owner
	 */
	 
 	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");
		
	
	// Contact object initialisation	
	$objUser = new User($db, array(), $objAuthorise->getId());
	
	$user_properties = $objUser->getProperties();
	
	// Setup form variables
	$name = stripslashes($userProperties['title']) . ' (' . stripslashes($user_properties['client_title']) . ')';
	$email = $user_properties['email'];
	$from = $name . ' &lt;' . $email . '&gt;';
	$message = read($_POST, 'message', NULL);
	
	
	// Send Email
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
	
		// Setup email subject line
		$subject = $objApplication->getParameter('subject', 'Contact us from ' . SITE_NAME . ' website');
		$objContact = new Contact($objApplication);
		// Prevent automated Bcc'd of message - because it's coming to you anyway (!)
		$objContact->bcc_yourself = false;
		$objContact->send_automated_reply = false;
		// SEND EMAIL and SET UP NEW USER FEEDBACK
		$user_feedback = $objContact->sendEmail($name, $email, $subject, $message);		
		
	}
	
	$objFeedback = new Feedback($user_feedback);
	
	// View
	include(APPLICATION_PATH . "/views/contact.php");
?>	