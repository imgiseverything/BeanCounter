<?php

	/**	 
	 *	Download file controller
	 *	This is what the user sees when they are forced to download a file
	 *	
	 *	The mime type must be set and the download must be included
	 */

	// Generic settings
	require_once(APPLICATION_PATH."/inc/settings.inc.php");
	
	exit('ffddf');
	// Object filters
	/*$filter['type'] = read($_GET,'type','');
	$id = read($_GET,'id','');*/
	$objApplication->setFilter('type', read($_GET,'type',''));
	
	// Initialise Download object
	$objDownload = new Download($db, $objApplication->getFilters(), $objApplication->getId()); 

	// Mime type - what type of file is?
	if($objDownload->mime_type == 'application/x-msexcel'){
		header ("Expires: Mon, 26 Jul 2009 05:00:00 GMT");
		header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Pragma: no-cache");
		header ("Content-type: ".$objDownload->mime_type);
		header ("Content-Disposition: attachment; filename=\"" . $filename . "\"" );
		header ("Content-Description: PHP/INTERBASE Generated Data" );
	} else{
		header('Content-type: '.$objDownload->mime_type);
	} // end else
	
	// file header (if it has one)
	echo $objDownload->file_header;

	// include the download - can't because of Dreamhost
	echo $objDownload->file_body;
	
	// file footer (if it has one)
	echo $objDownload->file_footer;
	
	// now exit to prevent PHP header error messages
	exit();

?>