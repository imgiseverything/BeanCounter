<?php
/**
 *	=========================================================================
 *	
 *	Discount Class
 *	-------------------------------------------------------------------------
 *	
 *	Add/Edit/Delete/View Items from database
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
 *	@since		19/08/2009
 *	
 *	edited by:  Phil Thompson
 *	@modified	
 *	
 *	=========================================================================
 *  
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	variables
 *	
 *	construct
 *	
 *	methods
 *		add()
 *		delete()
 *		duplicate()
 *		edit()
 *		deleteProjectDiscountCache()
 *
 *  =========================================================================
 */


	class Discount extends Scaffold{
	
		
		// Variables	
		
		/**
		 *	@var string
		 */	
		protected $_name;
	
		/**
		 *	Constructor
		 *	@param object $db
		 *	@param array $filter
		 *	@param (int|boolena) $id (FLASE)
		 */
		public function __construct($db, $filter, $id = false){
		
			// Object naming conventions
			$this->_name = 'discount';		
			$this->_folder = '/discounts/';	
			// SQL - Database related namings
			$this->_sql['main_table'] =  'project_discount';
			
			parent::__construct($db, $filter, $id);

		}
		
		
		/**
		 *	add()
		 *	Use parent method but ensure project cache is
		 *	properly deleted
		 */
		protected function add(){
		
			$user_feedback = parent::add();		
				
			$this->deleteProjectDiscountCache($user_feedback);
			
			return $user_feedback;
		}
		
		/**
		 *	delete()
		 *	Use parent method but ensure project cache is
		 *	properly deleted
		 */
		protected function delete(){
		
			$user_feedback = parent::delete();		
				
			$this->deleteProjectDiscountCache($user_feedback);
			
			return $user_feedback;
		}
		
		/**
		 *	duplicate()
		 *	Duplciation is just adding except the form
		 *	view has data pre-filled. So just use the
		 *	add method :)
		 */
		protected function duplicate(){
			return $this->add();
		}
		
		/**
		 *	edit()
		 *	Use parent method but ensure project cache is
		 *	properly deleted
		 */
		protected function edit(){
		
			$user_feedback = parent::edit();		
				
			$this->deleteProjectDiscountCache($user_feedback);
			
			return $user_feedback;
		}	
		
		/**
		 *	deleteProjectDiscountCache()
		 *	@$user_feedback array
		 *	If the parent method has worked then we need
		 *	to delete the cache for projects
		 */
		private function deleteProjectDiscountCache($user_feedback){

			if(!empty($user_feedback['type']) && $user_feedback['type'] == 'success'){
				// create the cache file name
				// should be projects_id_tasks.cache
				$cacheFilename = 'project_' . $this->_properties['project'] . '_' . strtolower($this->_namePlural) . '.cache';
				$objCache = new Cache($cacheFilename, 1, 'project');
				$objCache->delete('file', $cacheFilename);
			}
			
		}		
		
	
	}

?>