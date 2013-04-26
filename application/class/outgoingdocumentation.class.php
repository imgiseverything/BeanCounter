<?php
/**
 *	=========================================================================
 *	
 *	OutgoingDocumentation Class
 *	-------------------------------------------------------------------------
 *	
 *	Look after files or uploads in a database
 *
 *	@copyright Phil Thompson
 *	
 *	=========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 	2008-2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.0	
 *	@author		philthompson.co.uk
 *	@since		20/11/2009
 *	
 *	edited by:  Phil Thompson
 *	@modified	20/11/2009
 *	
 *  =========================================================================
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	Variables
 *	
 *	Constructor
 *	
 *	Methods
 *		setNamingConventions
 *		
 *	
 *	=========================================================================
 */

	class OutgoingDocumentation extends FileManager{
	
		// Variables

		
		
		/**
		 *	Constructor
		 *	@param object $db
		 *	@param array $filter - data options for SQL
		 *	@param (id|bool) $id
		 */
		public function __construct($db, $filter = array(), $id = false){
			
			$this->_file = new File($this->_filter, $this->_id); 
			
			parent::__construct($db, $filter, $id);
			
		}
		
		/**
		 *	setNamingConventions()
		 *	This whole system's automation is *very* dependant
		 *	upon the naming of key variables $_name, $_namePlural
		 *	and $_folder - it used to slim down the amount of code needed.
		 *	Object naming conventions
		 *  unless a child object have themselves set some defaults
		 */
		protected function setNamingConventions(){
			$this->_name = 'outgoing document';
			$this->_namePlural = 'outgoings documentation';
			$this->_folder = '/outgoings/documentation/';
			$this->_sql['main_table'] = 'outgoing_documentation';
			
			parent::setNamingConventions();
		}
		
		
		/**
		 *	customQueryFilters()
		 *	This method will appear in child classes
		 *	it's just here to remind you 
		 *	that you can use it - what do you mean 
		 *	"that's what interfaces are for"?
		 */
		protected function customQueryFilters(){
		
			if(!$this->_id && !empty($this->_filter['outgoing'])){
				$this->_queryFilter['custom'] = " AND `t`.`outgoing` = '{$this->_db->escape($this->_filter['outgoing'])}' ";
			}
		
		}
		
		
	
	
	}
?>