<?php

	/**
	 *	Scaffold (generic) Controller
	 *  View all or individual items and add/edit/delete them
	 */
	 
	// Generic settings
	require_once(APPLICATION_PATH . "/inc/settings.inc.php");
	
	/**
	 *	Payments object initialisation
	 *	include at top of each view using a ProjectPayment object
	 *	
	 *	// initialises the required object based on settings 
	 *	   (most are set by default in '/inc/settings.inc.php')
	 *	// sets up pagination of data
	 *	// tells the object to process data
	 *	// gives user feedback based on processed data
	 *	// sends a 404 if the requested item doesn't exist
	 *
	 */
	 
	// Filter items by the transaction_date field when looking through timeframes
	$objApplication->setFilter('date_order_field', 'transaction_date');

	
	// Initialise Object
	$objScaffold = new ProjectPayment($db, $objApplication->getFilters(), $objApplication->getId());
	
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