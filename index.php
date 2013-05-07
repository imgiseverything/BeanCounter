<?php

	/**
	 *	Rewrite (route): All URL requests come through here,
	 *	to make less use of .htaccess mod_rewrite rules
	 *	
	 *	See class file for more detailed details :)
	 *		
	 */

	// Define application and site paths
	// this is so we can avoid using $_SERVER["DOCUMENT_ROOT'] everywhere 
	// and so the application folder can sit above the site root
	define('BEANCOUNTER_PATH', getcwd());
	define('SITE_PATH', BEANCOUNTER_PATH . '/site/');
	define('APPLICATION_PATH', BEANCOUNTER_PATH . '/application/');
	define('LIBRARY_PATH', BEANCOUNTER_PATH . '/library/');

	// Include required rewrite class files
	require_once(APPLICATION_PATH . '/class/rewrite.class.php');

	// Rewrite
	$objRewrite = new Rewrite();
	
	// Include required controller
	include(APPLICATION_PATH . '/' . $objRewrite->getIncludeFile());
	exit;