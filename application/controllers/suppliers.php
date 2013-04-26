<?php

	/**
	 *	Suppliers controller
	 *  View all or individual suppliers and add/edit/delete them
	 */
	 
	// Generic settings
	include(APPLICATION_PATH."/inc/settings.inc.php");
	
	// Order by A-Z
	$objApplication->setFilter('order_by', 'title_az');
	
	// Initialise Object
	$objScaffold = new Supplier($db, $objApplication->getFilters(), $objApplication->getId());
	
	// Pagination e.g. Previous 1 2 3 4 5 Next
	$objPagination = new Pagination($objScaffold->getTotal(), $objScaffold->getPerPage(), $objScaffold->getCurrentPage());
	
	// Process data and set up user feedback
	$user_feedback = $objScaffold->processData();
	$objFeedback = new Feedback($user_feedback);
	
	// Put object properties into easy to use variables
	$properties = $objScaffold->getProperties();
	$properties_size = sizeof($properties);
	
	
	// That scaffold doesn't exist or a basic user is trying to access 
	// it so give an error message (404)
	if(($objScaffold->getExists() === false && !$objScaffold->getSearch() && $action != 'add') || $objAuthorise->getLevel() == 'Basic'){
		$obj404 = new Error();
		$obj404->throw404($objTemplate, $objMenu, $objVcard, $objAuthorise);
	}
	
	// Work out which 'view' to show
	
	// Id exists so either so either show one item or the edit/delete forms
	if($objScaffold->getId()){
		// No action so show contents of ID
		if(!$action){
			// do properties exist?
			if(!empty($properties)){
				include(APPLICATION_PATH . '/views/suppliers_view.php');
			} // end id properties exist
		}
		// Action is present - show form (if user is allowed to see it)
		else{
			$objTemplate->setForm($objScaffold->getName(), $objApplication->getAction());
			// Include form view
			include(APPLICATION_PATH . '/views/suppliers_form.php');
		}
	
	}
	// No id: Show all or Add new
	else{
		// check for add action
		if($action == 'add'){
			$objTemplate->setForm($objScaffold->getName(), $objApplication->getAction());
			// Include form view
			include(APPLICATION_PATH . '/views/suppliers_form.php');
		}
		else{
			// Include show all view
			include(APPLICATION_PATH . '/views/suppliers_all.php');
		}
	}

?>