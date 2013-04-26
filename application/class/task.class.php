<?php
/**
 *	=========================================================================
 *	
 *	Task Class
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
 *	@since		10/01/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	20/06/2010
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
 *		edit()
 *		deleteProjectTaskCache()
 *
 *  =========================================================================
 */


	class Task extends Scaffold{
	
		
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
			$this->_name = 'task';		
		
			// SQL - Database related namings
			$this->_sql['main_table'] =  'project_task';
			
			parent::__construct($db, $filter, $id);
			
			// Format the object name so it looks better in the view
			$this->_name = 'task';
		}
		
		
		/**
		 *	add()
		 *	Use parent method but ensure project cache is
		 *	properly deleted
		 */
		protected function add(){
		
			$user_feedback = parent::add();		
				
			$this->deleteProjectTaskCache($user_feedback);
			
			return $user_feedback;
		}
		
		/**
		 *	delete()
		 *	Use parent method but ensure project cache is
		 *	properly deleted
		 */
		protected function delete(){
		
			$user_feedback = parent::delete();		
				
			$this->deleteProjectTaskCache($user_feedback);
			
			return $user_feedback;
		}
		
		/**
		 *	edit()
		 *	Use parent method but ensure project cache is
		 *	properly deleted
		 */
		protected function edit(){
		
			$user_feedback = parent::edit();		
				
			$this->deleteProjectTaskCache($user_feedback);
			
			return $user_feedback;
		}	
		
		/**
		 *	deleteProjectTaskCache()
		 *	@$user_feedback array
		 *	If the parent method has worked then we need
		 *	to delete the cache for projects
		 */
		private function deleteProjectTaskCache($user_feedback){

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