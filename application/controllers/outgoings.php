<?php

	/**
	 *	Outgoings (expenses) Controller
	 *  View all or individual outgoings and add/edit/delete them
	 */
	 
	// Generic settings
	require_once(APPLICATION_PATH . "/inc/settings.inc.php");
	
	// Set up outgoings specific datafilters
	
	// Object data filters
	// sort query/results
	$objApplication->setFilter('order_by', $objApplication->getParameter('sort', 'transaction_date'));
	// Filter items by the transaction_date field when looking through timeframes
	$objApplication->setFilter('date_order_field', 'transaction_date');
	// show outgoings to a set supplier
	$objApplication->setFilter('supplier', $objApplication->getParameter('supplier'));
	 // show outgoings in a set category
	$objApplication->setFilter('category', $objApplication->getParameter('category'));
	
	// Initialise Object
	$objScaffold = new Outgoing($db, $objApplication->getFilters(), $objApplication->getId());
	
	// Pagination e.g. Previous 1 2 3 4 5 Next
	$objPagination = new Pagination($objScaffold->getTotal(), $objScaffold->getPerPage(), $objScaffold->getCurrentPage());
	//print_r($_SESSION);
	// Process data and set up user feedback
	$user_feedback = $objScaffold->processData();
	$objFeedback = new Feedback($user_feedback);
	
	// Put object properties into easy to use variables
	$properties = $objScaffold->getProperties();
	$properties_size = sizeof($properties);
	
	// That scaffold doesn't exist or a basic user is trying to access it so give an error message (404)
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
	
	// Id exists so either so either so the content 
	// of the id or edit/delete forms
	if($objScaffold->getId()){
	
		// Cache settings
		$cache_filename = 'download_' . $id . '.html';

		// Supplier Vcard
		$objSupplier = new Supplier($db, array(), $properties['outgoing_supplier']);
		$objVcardSupplier = new Vcard($objSupplier->getProperties());
		
		if($action == 'download' || $action == 'remittance'){			
			// Download object - for scrunched up CSS content 
			$objDownload = new Download($objApplication);
		}
		
		
		if($action == 'download'){	
			$objController->resetView('outgoings_download');
		}
		
		if(!empty($action) || $action == 'download' || $action == 'remittance'){
			// Textile the HTML fields
			extract($properties);
			$description = $objTextile->TextileThis($description);
		}

	}
	
	// Include View file
	include($objController->getView());
	exit;
?>