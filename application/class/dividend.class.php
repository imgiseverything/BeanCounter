<?php

/**
 *	Dividend Class
 *
 *	@package		Bean Counter
 *	@copyright 		2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@author			philthompson.co.uk
 *	@since			01/09/2017
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

class Dividend extends Scaffold{

	/**
	 *	Constructor
	 *	@param object $db
	 *	@param array $filter - data options for SQL
	 *	@param (int|boolean) $id
	 */
	public function __construct($db, $filter, $id = false){

		// Object naming conventions
		$this->_name = 'dividend';

		// Run parent's constructor
		parent::__construct($db, $filter, $id);

	}


	/**
		 *	add
		 *	@return array $user_feedback
		 */
		protected function add(){
			$user_feedback = parent::add();

			return $user_feedback;
		}

}

?>