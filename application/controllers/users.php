<?php

	/**
	 *	System users view
	 *  View all or individual users and add/edit/delete them
	 */
	 
	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");

	// Set up a client filter, so basic users can only see 
	// their users and not everyone else's
	$client = ($objAuthorise->getLevel() != 'Superuser') ? $objAuthorise->getClient() : read($filter, 'client', '');
	
	$objApplication->setFilter('client', $client);
	
	// Initialise Object
	$objScaffold = new User($db, $objApplication->getFilters(), $objApplication->getId());
	
	// Pagination e.g. Previous 1 2 3 4 5 Next
	$objPagination = new Pagination($objScaffold->getTotal(), $objScaffold->getPerPage(), $objScaffold->getCurrentPage());
	
	// Process data and set up user feedback
	$user_feedback = $objScaffold->processData();
	$objFeedback = new Feedback($user_feedback);
	
	// Reset the item's title because this object doesn't 
	// have a natural title field in it.
	$objScaffold->resetTitle();
	
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