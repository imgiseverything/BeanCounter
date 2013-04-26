<?php

	/**
	 *	Files Controller
	 */

	// Generic settings
	require_once(APPLICATION_PATH . "/inc/settings.inc.php");
	
	// Initialise Image object
	$objFile = new FileManager($db, $objApplication->getFilters(), $objApplication->getId());
	
	// Pagination e.g. Previous 1 2 3 4 5 Next
	$objPagination = new Pagination($objFile->total, $objFile->per_page, $objFile->current_page);
	
	// Process data
	$user_feedback = $objFile->processData();
	$objFeedback = new Feedback($user_feedback);
	
	// Put object properties into easy to use variables
	$results = $objFile->getProperties();
	$properties = $results; // I know, I know.
	
	
	// Ig the action variable is present create
	// a $field variable which is used to create fields
	// in the form class helper
	if($action && $action != 'delete'){	 
	 	$fields = array();
	 	foreach($objFile->getFields() as $field){
	 		if($field != 'filename'){
	 			$fields[] = $field;
	 		}
	 	}
	}
	
	// Work out which view to show/use	
	if($id) {
		extract($properties);
		// No action so show contents of ID
		if(!$action){
			$fileExists = $objFile->getExists();
		
			if($fileExists === false){
				// requested file doesn't exist
				$obj404 = new Error();
				$obj404->throw404($objTemplate, $objMenu, $objVcard, $objAuthorise);
			} else {				
				
				$directory = str_replace(SITE_PATH,'',$objFile->upload->getDirectory());
				$filesize = Upload::convertBytes(filesize($objFile->upload->getDirectory().$filename));
				$title = stripslashes($title);
				
				// requested file exists - show it			
				include(APPLICATION_PATH . '/views/uploads_view.php');
			}
		} else{
			$objTemplate->setForm($objFile->getName(), $objApplication->getAction());
			// Include form view
			include(APPLICATION_PATH . '/views/uploads_form.php');
		}
	} else {
		// no id show all files
		// check for add action
		if($action == 'add'){
			$objTemplate->setForm($objFile->getName(), $objApplication->getAction());
			// Include form view
			include(APPLICATION_PATH . '/views/uploads_form.php');
		}
		else{
			// Include show all view
			include(APPLICATION_PATH . '/views/uploads_all.php');
		}
	}
	
?>