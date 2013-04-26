<?php

	/**
	 *	(Business) Leads Controller
	 *  View all or individual business leads and add/edit/delete them
	 */
	 
	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");
	
	
	// Lead object filter
	$lead_type = (!$id) ? $objApplication->getParameter('lead_type') : null;
	$objApplication->setFilter('lead_type', $lead_type);
	
	
	$objApplication->setFilter('per_page', 1000);
	
	// Filter items by the first_contact_date field when looking through timeframes
	$objApplication->setFilter('date_order_field', 'first_contact_date');

	// Initialise Lead object
	$objScaffold = new Lead($db, $objApplication->getFilters(), $objApplication->getId());
	
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
		$objClient = new Client($db, array(), $client);
		$objVcardClient = new Vcard($objClient->getProperties());
		
		// Textile the HTML fields
		$description = $objTextile->TextileThis($description);
		
	}
	
	
	// Set graph data to show leads per month - @todo leads per week.
	if(!$objApplication->getAction() && !$objScaffold->getId() && !empty($properties)){

		$leads = $chart_months = $simple_months = array();
		foreach($properties as $value){
			
			$date = date('Y-m', strtotime($value['first_contact_date'])) . '-01 00:00:00';
			if(empty($chart_months[$date])){
				$chart_months[$date] = 1;
			} else{
				$chart_months[$date]++;
			}
			
		}
		
		ksort($chart_months);
		
		foreach($chart_months as $month){
			$leads[] = $month;
		}
		
		foreach($chart_months as $key => $value){
			$simple_months[] = date('M Y', strtotime($key));
		}
		
		
		
	}
	
	
	
	// Include View file
	include($objController->getView());
	exit;
?>