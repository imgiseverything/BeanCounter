<?php

	/**
	 *	Outgoing documentation controller
	 *  View all or individual outgoing documents
	 *	and add/edit/delete them
	 */
	 
	// Generic settings
	require_once(APPLICATION_PATH . "/inc/settings.inc.php");

	// Object data filters
	// filter documents to a set outgoing
	$objApplication->setFilter('outgoing', $objApplication->getParameter('outgoing'));
	
	// Initialise Object
	$objScaffold = new OutgoingDocumentation($db, $objApplication->getFilters(), $objApplication->getId());
	
	// Pagination e.g. Previous 1 2 3 4 5 Next
	$objPagination = new Pagination($objScaffold->getTotal(), $objScaffold->getPerPage(), $objScaffold->getCurrentPage());
	
	// Process data and set up user feedback
	$user_feedback = $objScaffold->processData();
	$objFeedback = new Feedback($user_feedback);
	
	// Put object properties into easy to use variables
	$properties = $objScaffold->getProperties();
	$properties_size = sizeof($properties);
	
	
	// That scaffold doesn't exist or a basic user is trying to
	// access it so give an error message (404)
	if($objAuthorise->getLevel() == 'Basic'){
		$obj404 = new Error($objApplication);
		$obj404->throw404($objTemplate, $objMenu, $objVcard, $objAuthorise);
	}
	
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