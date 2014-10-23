<?php

	/**
	 *	System settings Controller
	 *  View and edit system settings e.g. site name, address details etc
	 */
	 
	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");
	
	
	// That scaffold doesn't exist  so give an error message (404)
	if($objAuthorise->getLevel() != 'Superuser'){
		$obj404 = new Error($objApplication);
		$obj404->throw404($objTemplate, $objMenu, $objVcard, $objAuthorise);
	}
	
	
	// Initialise Settings / Website Object
	$objScaffold = new Website($db, $objApplication->getFilters(), $objApplication->getId());
	
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
	} else{
	
		$settings_type = $objApplication->getParameter('type', 'address');
	
		// Fields which are to be displayed (all other fields will be hidden)
		if($settings_type == 'address'){
		
			$activeFields = array(
				'Business name', 
        		'Address line 1', 
        		'Address line 2', 
        		'Address line 3', 
        		'City/Town', 
        		'County/State', 
        		'Postal code', 
        		'Country', 
        		'Main telephone number', 
        		'Email address', 
			);
		
		} else if($settings_type == 'financial'){
			$activeFields = array(
        		'Main currency',
				'VAT rate', 
        		'National insurance', 
        		'Income tax rate', 
        		'Bank account number', 
        		'Bank sort code',
        		'IBAN',
        		'Start of financial year',
        		'Invoice appendix',
        		'VAT flat rate scheme percentage',
        		'VAT flat rate scheme registration date'
			);
		}
    
    	// Fields which require the int class because they are usually small numbers
    	$arrInts = array(
    		'VAT rate', 
    		'National insurance', 
    		'Income tax rate', 
    		'Postal code', 
    		'Bank account number', 
    		'Bank sort code',
    		'IBAN',
    		'Start of financial year'
    	);
    	
    	// Fields which need the required class because they are mandatory fields
    	$arrRequired = array(
    		'Website name', 
    		'Address line 1', 
    		'City/Town', 
    		'Postal code', 
    		'Country', 
    		'Main telephone number', 
    		'Email address', 
    		'Main currency', 
    		'Start of financial year',
    		'Invoice appendix'
    	);
	}
	
	
	$view = $objController->getView();
	
	$view = str_replace('_view', '_all', $view);
	
	// Include View file
	include($view);
	exit;
?>