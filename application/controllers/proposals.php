<?php

	/**
	 *	Proposals Controller
	 *  View all or individual proposals and add/edit/delete them
	 */	 
	 

	
	// Generic site settings
	include_once(APPLICATION_PATH . "/inc/settings.inc.php");

	// Initialise Object
	$objScaffold = new Proposal($db, $objApplication->getFilters(), $objApplication->getId());
	
	// Pagination e.g. Previous 1 2 3 4 5 Next
	$objPagination = new Pagination($objScaffold->getTotal(), $objScaffold->getPerPage(), $objScaffold->getCurrentPage());
	
	// Process data and set up user feedback
	$user_feedback = $objScaffold->processData();
	$objFeedback = new Feedback($user_feedback);
	
	// Put object properties into easy to use variables
	$properties = $objScaffold->getProperties();
	$properties_size = sizeof($properties);
	
	// set some variables to override the default Project variables
	$project_stage = 1;
	$completed = false;
	$project_stage_title = $objScaffold->getName();
	$stage = $objScaffold->getName();
	
	//print_r($properties);
	
	// That scaffold doesn't exist so give an error message (404)
	if($objScaffold->getExists() === false && !$objScaffold->getSearch() && $action != 'add'){
		$obj404 = new Error($objApplication);
		$obj404->throw404($objTemplate, $objMenu, $objVcard, $objAuthorise);
	}
	
	
	
	// Cache settings
	if($objScaffold->getId()){
		// cache
		$cache_filename = 'download_' . $id . '.html';
	}	
	// Work out which 'view' to show
	
	// Id exists so either so either show one 
	// item or the edit/delete forms
	if($objScaffold->getId()){
	
		// Client vcard - used under the heading
		// 'For the attention of'
		$objClient = new Client($db, array(), $properties['client']);
		$objVcardClient = new Vcard($objClient->getProperties());
		
		if(!$action){
			// No action so show contents of ID
			// but do properties exist?
			if(!empty($properties)){

				// Set FAO is empty
				if(empty($properties['for_the_attention_of'])){
					$properties['for_the_attention_of'] = strip_tags(str_replace("\n", '', $objVcardClient->getVcard()));
				}
				
				// Set appendix if empty
				if(empty($properties['appendix'])){
					$properties['appendix'] = APPENDIX;
				}
			
			
				extract($properties);
			
				

			
				// Which view to show user - clients should see the download view
				switch($objAuthorise->getLevel()){
				
					default:
					case 'Superuser':
						$view = 'projects_view';
						break;
						
					case 'Basic':
						$view = 'projects_download';
						break;
						
				}
				
			} // end id properties exist
			
		} else{
		
			// Set user vcard information
			if(empty($properties['sender_address'])){
				$properties['sender_address'] = strip_tags(str_replace("\n", '', $objVcard->getAddress()));
			}
			
			
			// Get Client Vcard
			$objClient = new Client($db, array(), (int)$properties['client']);
			$objVcardClient = new Vcard($objClient->getProperties());
			
			// Set FAO is empty
			if(empty($properties['for_the_attention_of'])){
				$properties['for_the_attention_of'] = strip_tags($objVcardClient->getVcard());
			}
			
			// Set appendix if empty
			if(empty($properties['appendix'])){
				$properties['appendix'] = APPENDIX;
			}
			// Action is present - 
			
			if($action == 'download' || $action == 'invoice' || $action == 'quote'){

				// Download object - for scrunched up CSS content 
				$objDownload = new Download($objApplication);
				
				// Textile the HTML fields
				extract($properties);
				$description = $objTextile->TextileThis($description);
				$appendix = $objTextile->TextileThis($appendix);
				
				
			} else if($action == 'pdf'){
				$objScaffold->pdf();
			}
			
			
			
			
			
			// Show form (if user is allowed to see it)
			if($action == 'download'){
				$view = 'projects_download';
				
			} else if($objAuthorise->getLevel() == 'Superuser'){
				$objTemplate->setForm($objScaffold->getName(), $objApplication->getAction());
				$form = $objTemplate->getForm();
				// Include form view
				$view = 'projects_form';
			} else{ 				
				// Download object - for scrunched up CSS content 
				$objDownload = new Download($objApplication);
				// Get Client Vcard
				
				$objClient = new Client($db, array(), $properties['client']);
				$objVcardClient = new Vcard($objClient->getProperties());
				// Clients just see the download file
				$view = 'projects_download';
			}
			
		}
		
		 
		
	
	} else{
	

	
		// No id: Show all or Add new
		// firstly, check for add action
		if($action == 'add' && $objAuthorise->getLevel() == 'Superuser'){
			$objTemplate->setForm($objScaffold->getName(), $objApplication->getAction());
			$form = $objTemplate->getForm();
			
			$view = 'projects_form';
			
			
			
			// Force proprosla as project_stage
			if(empty($_POST['project_stage'])){
				$_POST['project_stage'] = 1;
			}
			
			
			
			// Set user vcard information
			if(empty($_POST['sender_address'])){
				$_POST['sender_address'] = strip_tags(str_replace("\n", '', $objVcard->getAddress()));
			}
			
		} else{
			// Include show all view
			if(empty($properties)){
				$view = 'scaffold_empty';
			} else{
				$view = 'proposals_all';
			}
		}
		
	}
	
	include(APPLICATION_PATH . '/views/' . $view . '.php');
?>