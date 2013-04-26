<?php
/**
 * Client vcard AJAX file
 * produces a vcard from a client ID
 */
 
	// generic site settings
	require_once(APPLICATION_PATH . "/inc/settings.inc.php");
	
	
	$objClient = new Client($db, array(), $_GET['id']);
	$objVcardClient = new Vcard($objClient->getProperties());
	
	echo trim(strip_tags(str_replace("\n", '', $objVcardClient->getVcard())));

?>