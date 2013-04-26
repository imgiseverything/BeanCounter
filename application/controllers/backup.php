<?php

	/**
	 *	Backup Controller
	 *  Backup our database
	 */

	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");
	
	// If a user isn't a superuser then give them error message (404)
	if($objAuthorise->getLevel() != 'Superuser'){
		$obj404 = new Error();
		$obj404->throw404($objTemplate, $objMenu, $objVcard, $objAuthorise);
	}
	
	// Normal/Default view - previous backups
	$include_file = 'backup.php';
	
	// Create and grab latest backup
	if($action == 'download'){	
		$objBackup = new Backup($db);
		$include_file = 'backup_download.php';
	}
	
	$objFeedback = new Feedback($user_feedback);
	
	// Include view file
	include(APPLICATION_PATH . '/views/' . $include_file);
	
?>