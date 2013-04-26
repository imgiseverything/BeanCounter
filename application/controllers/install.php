<?php

	/**
	 *	Product Installation Controller
	 */
	 
	$i = 0; // counter
	
	// Generic settings
	include(APPLICATION_PATH . '/inc/settings.inc.php');
	
	// Initialise install object
	$objInstall = new Install($db, $database, $objApplication, $objError, $objSite, $objAuthorise);
	
	// Form variables
	$form_variables = array('business_name', 'firstname', 'surname', 'email', 'password_x');
	extract(formVariables($form_variables));
	
	// Deal with form processing
	if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['action'])) {
	
	
		switch($_POST['action']) {
		
			default:
			case 'install':
			
				$all_data_present = ($business_name && $email && $password_x && $firstname && $surname) ? true : false;
				if($all_data_present === true) {
					$user_feedback = $objInstall->createDatabaseTables();
				} else {
					
					$user_feedback['type'] = 'error';
					$user_feedback['content'][] = 'Installation failed because you missed the following information out:';
					
					// No business name
					if(!$business_name) {
						$user_feedback['content'][] = 'Business name';
					}
					
					// No firstname
					if(!$firstname) {
						$user_feedback['content'][] = 'Your first name';
					}
					
					// No surname
					if(!$surname) {
						$user_feedback['content'][] = 'Your surname';
					}
					
					// No email
					if(!$email) {
						$user_feedback['content'][] = 'Your email address';
					}
					
					// No password
					if(!$password_x) {
						$user_feedback['content'][] = 'Your password';
					}
				}				
				break;
				
		}
	}
	
	$_SESSION['feedback'] = $user_feedback;
	$objFeedback = new Feedback();
	
	// View
	include(APPLICATION_PATH . '/views/install.php');

?>