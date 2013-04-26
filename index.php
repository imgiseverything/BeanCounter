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
	define('SITE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/site/');
	define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application/'));
	define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/library/'));

	// Include required rewrite class files
	require_once(APPLICATION_PATH . '/class/rewrite.class.php');

	// Rewrite
	$objRewrite = new Rewrite();
	
	// Include required controller
	include(APPLICATION_PATH . '/' . $objRewrite->getIncludeFile());
	exit;