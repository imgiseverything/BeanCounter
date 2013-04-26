<?php
/**
 *	=========================================================================
 *	
 *	PHPError Class
 *	-------------------------------------------------------------------------
 *	Display all the php errors from the database
 *	
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
 *	@since		07/08/2009
 *	
 *	edited by: 	Phil Thompson
 *	@modified	
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
 *	
 *		
 *	
 *	=========================================================================
 */

	class PHPError extends Scaffold{
	
		// Variables
		
		/**
		 *	@var object
		 *	Database connection object
		 */
		protected $_db;
		
		/**
		 *	@var string
		 */
		protected $_location;
		
		/**
		 *	Constructor
		 *	@param object $db
		 *	@param string $location (admin|site|mobile)
		 *	@param array $filter - data options for SQL
		 *	@param (id|bool) $id (FALSE)
		 */
		public function __construct($db, $filter = array(), $id = false){
			
			// Local variable objects
			$this->_db = $db;
			$this->_filter = $filter;
			$this->_id = $id;

			parent::__construct($this->_db, $this->_filter, false);
			
		}
		
		public function __destruct(){}
		
		// Methods	
		
		/**
		 *	setNamingConventions()
		 *	This whole system's automation is *very* dependant
		 *	upon the naming of key variables $_name, $_namePlural
		 *	and $_folder - it used to slim down the amount of code needed.
		 *	Object naming conventions
		 *  unless a child object have themselves set some defaults
		 */
		protected function setNamingConventions(){
		
			// The name = <h1> on pages in <title>s - usually the database table too
			$this->_name = 'error';
			$this->_sql['main_table'] = 'errors';
			$this->_folder = '/php-errors/';
			// run parent class' method
			parent::setNamingConventions();
			
		}
		
		/**
		 *	customQueryFilters
		 */
		protected function customQueryFilters(){
			
			$this->_queryFilter['custom'] = " GROUP BY string";
			$this->_queryFilter['order_by'] = "total DESC";
			$this->_queryFilter['status'] = " ";
			
		}
		
		
		
		/**
		 *	queryOptions
		 */
		protected function queryOptions(){
			
			parent::queryOptions();
			
			$this->_sql['select'] = 't.*, t.string AS title, t2.description AS error_level_title, COUNT(t.string) AS total';
			$this->_sql['joins'] = " LEFT JOIN errors_level t2 ON t2.id = t.errors_level";
			
		}
		
		/**
		 *	delete()
		 *	remove an item from the system - don't delete just set to inactive. 
		 *	See trash() to permanently delete items
		 *	@return array $user_feedback
		 */
		protected function delete(){
			
			// Error counter. Increment everytime there is an error.
			$error = 0;

			
			// query
			$query = "TRUNCATE TABLE `{$this->_sql['main_table']}`;";
			
			niceError($query, true); // Debugging - echo SQL

			// run query
			$this->_db->query($query);
			$user_feedback['type'] = 'success';
			$user_feedback['content'] = 'You have successfully deleted all ' . $this->_namePlural;
			
			// Delete cached files as they are now out of date
			$this->deleteCache();
				
			
			// provide user with feedback
			$user_feedback['type'] = ($error > 0) ? 'error' : 'success';
			
			return $user_feedback;
		}
	
	
	}
?>