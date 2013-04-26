<?php

	/**
	 *	Bean Counter
	 *	Controllers
	 *	Bookings
	 *  
	 */
	 
	

	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");
		
	// Object filters
	$objApplication->setFilter(array('timeframe_custom', 'start'), read($_GET, 'start_year', date('Y')) . '-' . read($_GET,'start_month', date('m')) . '-01 00:00:00');
	$objApplication->setFilter(array('timeframe_custom', 'end'), read($_GET, 'start_year', date('Y')) . '-' . read($_GET, 'start_month', date('m')) . '-31 23:59:59');
	
	$objApplication->setFilter('per_page', 100);
	
	// Initialise new calendar
	// calendar class works out what month we're on and what days
	// are in the month, what day it starts on etc
	$objCalendar = new Calendar(read($_GET, 'start_month', date('n')), read($_GET, 'start_year', date('Y')));

	// Initialise Booking Object
	$objScaffold = new Booking($db, $objApplication->getFilters(), $objApplication->getId());
	
	// Pagination e.g. Previous 1 2 3 4 5 Next
	$objPagination = new Pagination($objScaffold->getTotal(), $objScaffold->getPerPage(), $objScaffold->getCurrentPage());
	
	// Process data and set up user feedback
	$user_feedback = $objScaffold->processData();
	$objFeedback = new Feedback($user_feedback);
	
	// Put object properties into easy to use variables
	$properties = $objScaffold->getProperties();
	$properties_size = sizeof($properties);
	
	// Work out which 'view' to show
	
	// Id exists so either so either show one item or the edit/delete forms
	if($objScaffold->getId()){
	
		// No action so show contents of ID
		if(!$action){
			// Textile the HTML fields
			extract($properties);
			$description = $objTextile->TextileThis($description);
			include(APPLICATION_PATH . '/views/bookings_view.php');
		} else{
			if($action == 'ical'){
				$objScaffold->ical();
			} else {
				// Action is present - show form (if user is allowed to see it)
				$objTemplate->setForm($objScaffold->getName(), $objApplication->getAction());
				
				// Include form view
				include(APPLICATION_PATH . '/views/bookings_form.php');
			}
		}
	
	} else{
	
		// No id: Show all or Add new
	
		// check for add action
		if($action == 'add'){
			// Hardcode 9-5 as start/end times
			if(!isset($_POST['action'])){
				$_POST['date_started_hour'] = '09';
				$_POST['date_ended_hour'] = '17';
			}
			
			$objTemplate->setForm($objScaffold->getName(), $objApplication->getAction());
			// Include form view
			include(APPLICATION_PATH . '/views/bookings_form.php');
		} else{
			// Bookings month/year pagination settings
			$previous_month = (($objCalendar->getMonth() - 1) == 0) ? 12 : str_pad(($objCalendar->getMonth() - 1), 2, '0', STR_PAD_LEFT);
			$previous_month_name = date('F', strtotime(date('Y-' . $previous_month . '-01 H:i:s')));
			$next_month = (($objCalendar->getMonth() + 1) == 13) ? '01': str_pad(($objCalendar->getMonth() + 1), 2, '0', STR_PAD_LEFT);
			$next_month_name = date('F', strtotime(date('Y-' . $next_month . '-01 H:i:s')));
			$previous_year = (($objCalendar->getMonth() - 1) == 0) ? ($objCalendar->getYear() - 1) : $objCalendar->getYear();
			$next_year = (($objCalendar->getMonth() + 1) == 13) ? ($objCalendar->getYear() + 1) : $objCalendar->getYear();
			
			$page_title = ucfirst($objScaffold->getNamePlural()) . ' for ' . $objCalendar->getMonthName() . ' ' . $objCalendar->getYear();
		
			// Include show all view
			include(APPLICATION_PATH . '/views/bookings_all.php');
		}
		
	}

?>