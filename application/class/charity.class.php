<?php

/**
 *	Charity Class
 *	
 *	@package		Bean Counter
 *	@copyright 		2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@author			philthompson.co.uk
 *	@since			14/08/2010
 *	@lastmodified	
 *	@version		1	
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	Variables
 *	Methods
 *		Constructor
 *	
 */

class Charity extends Scaffold{

	/**
	 *	Constructor
	 *	@param object $db
	 *	@param array $filter - data options for SQL
	 *	@param (int|boolean) $id
	 */
	public function __construct($db, $filter, $id = false){

		// Object naming conventions
		$this->_name = 'charity';
		$this->_folder = '/charities/';
		$this->_namePlural = 'charities';
		

		// Run parent's constructor
		parent::__construct($db, $filter, $id);

		
	}
	


}
	
?>