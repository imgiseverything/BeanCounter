<?php

	/**
	 *	Time tracker Controller
	 *  View all or individual times and add/edit/delete them
	 */
	 
	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");
	
	
	// Lead object filter
	$project = (!$id) ? $objApplication->getParameter('project') : null;
	$client = (!$id) ? $objApplication->getParameter('client') : null;
	
	$objApplication->setFilter('project', $project);
	$objApplication->setFilter('client', $client);
	
	// Filter items by the start_date field when looking through timeframes
	$objApplication->setFilter('date_order_field', 'start_date');
	
	$objApplication->setFilter('per_page', 20);
	
	// Filter items by the first_contact_date field when looking through timeframes
	//$objApplication->setFilter('date_order_field', 'first_contact_date');

	// Initialise Timing object
	$objScaffold = new Timing($db, $objApplication->getFilters(), $objApplication->getId());
	
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
	
	
	if($objScaffold->getId() && !$objApplication->getAction()){
		// Client Vcard
		$objClient = new Client($db, array(), $properties['project_client']);
		$objVcardClient = new Vcard($objClient->getProperties());
		
		// Textile the HTML fields
		$description = $objTextile->TextileThis($description);
		
	}	
	
	
	//print_x($properties);
	
	
	// Include View file
	include($objController->getView());
	exit;
?>