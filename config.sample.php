<?php
/**
 *	 ===================================================================================
 *	
 *	 Bean Counter Configuration file
 *	 -----------------------------------------------------------------------------------
 **/
 
// Want to replace all your private figures with xxx's? Set $hide_figures to true 
$hide_figures = false;

// Site Mode	
define('MODE', 'LIVE'); // Can be 'LIVE' or 'TEST'

// Cache - cache database queries to speed up the site (experimental feature)
// This defintiely speeds up the site (but can cause issues so it's advised to leave as false)
define('CACHE', false);

/**
 * Database connection settings
 * -----------------------------------------------------------------------------------
 * 
 * Variables explained:
 * $sqlserver: database server: usually this either localhost, 
 * your web address (www.example.com) or something else (ask your host)
 * $username = database username - may be 'root'
 * $password = database password.
 * $database = the name of your database - if you can try to call it beancounter or 
 * bean counter_sitename
 */
$sqlserver	= 'localhost';
$username 	= '';
$password 	= '';	
$database 	= '';

/**
 * Security settings
 * -----------------------------------------------------------------------------------
 * 
 * Variables explained:
 * SECRET_PHRASE - random word(s) that will be used to make sure only you can be 
 * remembered with cookies when you chekc the remember me box on the log-in screen
 */
 define('SECRET_PHRASE', ''); 
 
 
 
/**
 *	Email settings
 *	-----------------------------------------------------------------------------------
 *	Bean Counter uses Swift Mailer (http://swiftmailer.org/) to send emails. By default,
 *	this uses php mail() which is ok BUT if you have your own email sending account,
 *	e.g. deliverhq.com you cna use those settings instead.
 *	Using a system like deliverhq.com means you can see if emails have definitely been
 *	sent/delivered. We all know clients like to pretend they have not received invoices
 *	
 */
 
define('EMAIL_SMTP', '');
define('EMAIL_USERNAME', '');
define('EMAIL_PASSWORD', '');

?>