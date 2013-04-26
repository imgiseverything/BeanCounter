<?php

	/**
	 *	Images Controller
	 */

	// Generic settings
	require_once(APPLICATION_PATH . "/inc/settings.inc.php");
	
	// Initialise Image object
	$objImage = new Image($db, $objApplication->getFilters(), $objApplication->getId());
	
	// Pagination e.g. Previous 1 2 3 4 5 Next
	$objPagination = new Pagination($objImage->total, $objImage->per_page, $objImage->current_page);
	
	// Upload a new image
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$user_feedback = $objImage->upload();		
	}
	
	$objFeedback = new Feedback($user_feedback);
	
	// Work out which view to show/use	
	if($id) {
		$image = false;
		// Get default image - it could be .gif, .png, or .jpg so we check for images with these extensions first
		foreach($objImage->extensions as $extension){
			$src = $objImage->directory . $id . '.' . $extension;
			if(file_exists($src)){
				// image file exists, set up image value into $image variable
				$image = $src;
			}
		}
	
		if($image === false){
			// requested image doesn't exist
			$obj404 = new Error();
			$obj404->throw404($objTemplate, $objMenu, $objVcard, $objAuthorise);
		} else {
			// requested image file exists - show it and different image sizes			
			include(APPLICATION_PATH . '/views/images_view.php');
		}
	} else {
		$images = $objImage->getFiles();
		// no id show all images
		include(APPLICATION_PATH . '/views/images_all.php');
	}
	
?>