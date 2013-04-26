<?php

/**
 *	Proposal Class
 *	
 *	@package		Bean Counter
 *	@copyright 		2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@author			philthompson.co.uk
 *	@since			12/12/2010
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

class Proposal extends Project{

	/**
	 *	Constructor
	 *	@param object $db
	 *	@param array $filter - data options for SQL
	 *	@param (int|boolean) $id
	 */
	public function __construct($db, $filter, $id = false){

		// Object naming conventions
		$this->_name = 'proposal';
		$this->_sql['main_table'] = 'project';
		
		
		// Project object filter
		$filter['project_stage'] = 1;

		// Run parent's constructor
		parent::__construct($db, $filter, $id);
		
		// Object naming conventions
		$this->_name = 'proposal';
		$this->_folder = '/proposals/';
		$this->_namePlural = 'proposals';


		
	}
	
	
	/**
	 *	add
	 */
	public function add(){
		
		
		$objCache = new Cache('project', 1, 'project');
		$objCache->delete('folder');
		
		return parent::add();
		
		
	}
	
	/**
	 *	edit
	 */
	public function edit(){
		
		
		$objCache = new Cache('project', 1, 'project');
		$objCache->delete('folder');
		
		return parent::edit();
		
		
	}
	


}
	
?>