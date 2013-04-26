<?php

	/**
	 *	Duplicate form
	 *	Duplication is basically the same as 'add' but with the data of another 
	 *	object prefilled in.
	 *
	 */	
	$include_file = APPLICATION_PATH . '/views/forms/' . strtolower($objScaffold->getName()) . '_add_edit.php';
	if(file_exists($include_file) !== true){
		$include_file = APPLICATION_PATH . '/views/forms/scaffold_add_edit.php';
	}	
	include($include_file);
?>