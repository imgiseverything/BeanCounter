<?php

	/**
	 *	Projects Controller
	 *  View all or individual projects and add/edit/delete them
	 */	 
	 

	
	// Generic site settings
	include_once(APPLICATION_PATH . "/inc/settings.inc.php");
	
	// Project object filter
	$project_stage = (!$id) ? $objApplication->getParameter('project_stage', array(2, 3, 4, 5)) : array();
	
	if($action != 'pdf' && $action != 'download' && $objAuthorise->getLevel() == 'Basic'){
		$project_stage = (!$id) ? $objApplication->getParameter('project_stage', array(1, 2, 3, 4, 5)) : $project_stage;
	}
	
	$objApplication->setFilter('project_stage', $project_stage);
	// Filter items by the transaction_date field when looking through timeframes
	//$objApplication->setFilter('date_order_field', 'transaction_date');

	// Initialise Object
	$objScaffold = new Project($db, $objApplication->getFilters(), $objApplication->getId());
	
	// Pagination e.g. Previous 1 2 3 4 5 Next
	$objPagination = new Pagination($objScaffold->getTotal(), $objScaffold->getPerPage(), $objScaffold->getCurrentPage());
	
	// Process data and set up user feedback
	$user_feedback = $objScaffold->processData();
	$objFeedback = new Feedback($user_feedback);
	
	// Put object properties into easy to use variables
	$properties = $objScaffold->getProperties();
	$properties_size = sizeof($properties);
	
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
		
		if(!$action){
			// No action so show contents of ID
			// but do properties exist?
			if(!empty($properties)){
			
				// Get Client Vcard
				$objClient = new Client($db, array(), $properties['client']);
				$objVcardClient = new Vcard($objClient->getProperties());
				
				
				
				// Set FAO is empty
				if(empty($properties['for_the_attention_of'])){
					$properties['for_the_attention_of'] = strip_tags(str_replace("\n", '', $objVcardClient->getVcard()));
				}
				
				// Set appendix if empty
				if(empty($properties['appendix'])){
					$properties['appendix'] = APPENDIX;
				}

			
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
				
				
				
				// Some variables
				extract($properties);
				$stage = strtolower(str_replace('_', ' ', $project_stage_title));
				
				
				$invoice_days = (int)str_replace(' days', '', DateFormat::howManyDays($payment_required));
				if($project_stage == 3 && $invoice_days > 0 && $outstanding > 0){
					$stage = 'overdue';
				}
				
				
				// Textile the HTML fields
				$description = $objTextile->TextileThis($description);
				$appendix = $objTextile->TextileThis($appendix);
				
				
			} // end id properties exist
			
		} else{
			// Action is present - 
			
			
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
				
				// Clients just see the download file
				$view = 'projects_download';
				
				
				// Textile the HTML fields
				extract($properties);
				$description = $objTextile->TextileThis($description);
				$appendix = $objTextile->TextileThis($appendix);
			}
		}
	
	} else{
	
	
	
		// No id: Show all or Add new
		// firstly, check for add action
		if($action == 'add' && $objAuthorise->getLevel() == 'Superuser'){
			$objTemplate->setForm($objScaffold->getName(), $objApplication->getAction());
			$form = $objTemplate->getForm();
			
			$view = 'projects_form';
			
			
			// Set user vcard information
			if(empty($_POST['sender_address'])){
				$_POST['sender_address'] = strip_tags(str_replace("\n", '', $objVcard->getAddress()));
			}
			
		} else{
			// Include show all view
			if(empty($properties)){
				$view = 'scaffold_empty';
			} else{
				$view = 'projects_all';
			}
		}
		
	}
	
	include(APPLICATION_PATH . '/views/' . $view . '.php');
?>