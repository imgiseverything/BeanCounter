<?php
/**
 * Image tracker
 * -----------
 * 
 * This file is stored as an image in HTML email and tells us if someone has viewed an email.
 * useful because clients love to pretend they didn't get your invoice.
 *
 */
header('Content-Type: image/gif'); 

if(isset($_GET['e']) && isset($_GET['id']) && is_numeric($_GET['id'])){  
	// validate and record click-through here
	// generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");  

	// Initialise contact object
	$objContact = new Contact();
	
	// Prevent automated Bcc'd of message - because it's coming to you anyway (!)
	$objContact->bcc_yourself = false;
	$objContact->send_automated_reply = false;
	
	// Email settings
	$objProject = new Project(array(), $_GET['id']);
	$projectProperties = $objProject->getProperties();
	$name = read($objSite->config, 'Website name', '');
	$email = read($objSite->config, 'Email address', '');
	$subject = 'Your invoice was opened';
	$message = 'At ' . date('H:i \o\n d/m/y') . ' your invoice attachment for the project ' . stripslashes($projectProperties['title']) . ' was opened by the email address: ' . read($_GET, 'e', '');
	
	// SEND EMAIL & SET UP NEW USER FEEDBACK
	$objContact->sendEmail($name, $email, $subject, $message);
   
} // end if
       
// push out image  
if(ini_get('zlib.output_compression')) { 
	ini_set('zlib.output_compression', 'Off'); 
} // end if
header('Pragma: public');   // required  
header('Expires: 0');       // no cache  
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');  
header('Cache-Control: private',false);  
//header('Content-Disposition: attachment; filename="blank.gif"');  
header('Content-Transfer-Encoding: binary');  
header('Content-Length: ' . filesize(APPLICATION_PATH . '/images/blank.gif'));   // provide file size  
readfile(APPLICATION_PATH . '/images/blank.gif'); // push it out  
exit(); 
?>