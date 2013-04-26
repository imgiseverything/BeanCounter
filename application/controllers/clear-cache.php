<?php

	/**
	 *	Cache controller
	 *	Allow user to the delete the entire cache from
	 *	the admin area
	 */

	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php"); 

	$objCache = new Cache('cache', 1);
	$objCache->delete();
	include($objApplication->getViewFolder() . '/cache.php');



?>