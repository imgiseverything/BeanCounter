<?php

	/**
	 *	Dashboard/homepage/control panel Controller
	 */

	// Generic settings
	include(APPLICATION_PATH . '/inc/settings.inc.php'); 

	// Initialise control panel object
	$objControlPanel = new ControlPanel($db);	
	
	
	$common_tasks = $objControlPanel->getCommonTasks();
	$common_tasks_size = sizeof($common_tasks);

	// Depending on the access level of
	// the user we show different data
	// Superuser (Site owner): Invoices due
	// Basic (Client): Invoices due and quotes
	
	if($objAuthorise->getLevel() == 'Superuser'){
		
		// Invoiced projects		
		$objApplication->setFilter('per_page', '1000');
		$objApplication->setFilter('order_by', 'oldest');
		$objApplication->setFilter('project_stage', 3);
		
		// Initialise Object
		$objInvoicedProjects = new Project($db, $objApplication->getFilters(), '');

		
		
		// Started projects - work currently ongoing
		$objApplication->setFilter('project_stage', 2);
		
		// Initialise Object
		$objStartedProjects = new Project($db, $objApplication->getFilters(), '');
		
		
		// Green lit projects - work slated to start
		$objApplication->setFilter('project_stage', 5);
		
		// Initialise Object
		$objGreenLitProjects = new Project($db, $objApplication->getFilters(), '');
		
		
		// Get bookings calendar - from today until 4 weeks from now.
		$objApplication->setFilter(array('timeframe_custom', 'start'), date('Y'). '-' . date('m') . '-01 00:00:00');
		//$objApplication->setFilter(array('timeframe_custom', 'end'), date('Y-m-d 23:59:59', strtotime('+4 weeks')));
		$objApplication->setFilter(array('timeframe_custom', 'end'), date('Y'). '-' . date('m') . '-31 23:59:59');
		
	
		// Initialise new calendar
		// calendar class works out what month we're on and what days
		// are in the month, what day it starts on etc
		$objCalendar = new Calendar(read($_GET, 'start_month', date('n')), read($_GET, 'start_year', date('Y')));
		
		$objApplication->setFilter('project_stage', null);
		$objBooking = new Booking($db, $objApplication->getFilters(), NULL);
		
		
	
	} else if($objAuthorise->getLevel() == 'Basic'){
	
		// Client - show invoices / proposals
		
		// Projects
		$objApplication->setFilter('per_page', 5);
		$objApplication->setFilter('project_stage', 3);
		
		// Initialise Object
		$objInvoice = new Project($db, $objApplication->getFilters(), '');
		
		$filter['project_stage'] = 1;
		$objProposal = new Project($db, $filter, '');
	}
	
	$objFeedback = new Feedback($user_feedback);
		
	// View
	include(APPLICATION_PATH . "/views/index.php");
?>