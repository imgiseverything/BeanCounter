<?php

	/**
	 *  Clients Controller
	 *  View all or individual clients and 
	 *	add/edit/delete them
	 */

	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");
	
	// Order by A-Z
	$objApplication->setFilter('order_by', 'title_az');
	
	// Zero spend clients (show/hide
	$objApplication->setFilter('include_zero_spend', false);
	
	// Initialise Object
	$objScaffold = new Client($db, $objApplication->getFilters(), $objApplication->getId());
	
	// Pagination e.g. Previous 1 2 3 4 5 Next
	$objPagination = new Pagination($objScaffold->getTotal(), $objScaffold->getPerPage(), $objScaffold->getCurrentPage());
	
	// Process data and set up user feedback
	$user_feedback = $objScaffold->processData();
	$objFeedback = new Feedback($user_feedback);
	
	// Put object properties into easy to use variables
	$properties = $objScaffold->getProperties();
	$properties_size = sizeof($properties);
	
	// Work out which 'view' to show
	// Controller object
	$objController = new Controller($db, $objApplication, $objTemplate, $objMenu, $objVcard, $objAuthorise, $objScaffold);	
	
	// Perform any calculations/operations here that a view needs
	if(!$objApplication->getAction() && !empty($properties)){
		extract($properties);
	} 	
	
	// Include View file
	include($objController->getView());
	exit;
?>