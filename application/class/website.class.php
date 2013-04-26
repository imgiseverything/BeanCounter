<?php
/**
 *	=========================================================================
 *	
 *	Website Class	
 *	-------------------------------------------------------------------------
 *	
 *	Add/Edit/Delete/View website settings from the config database table.
 *	Wesbite settigns refer to items like site name, contatc details,
 *	API keys etc.
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
 *	@since		07/01/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	13/12/2010
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
 *	destruct
 *	
 *	methods
 *		edit()
 *		
 *		setAll()
 *		setById()
 *		setTotal()
 *	
 *	=========================================================================
 *
 */

	class Website extends Scaffold{
	
		
		// Variables
		public $customer_id;
		protected $_cacheFilename;
		protected $_cacheFullFilename;
		public $xml;
	
		/**
		 *	Construct()
		 *	@param object $db
		 *	@param array $filter - data options for SQL
		 *	@param (id|bool) $id
		*/
		public function __construct($db, $filter = array(), $id = false){			
			
			// Object naming conventions
			$this->_name = 'setting';
			
		
			$this->_sql['main_table'] = 'config';
			$this->_filter = $filter;
			$this->_filter['per_page'] = 100;
			
			// use parent object's construct
			parent::__construct($db, $this->_filter, $id);
			
			// cache file settings
			$this->_cacheFilename = 'settings.cache';
			$this->_cacheFullFilename = APPLICATION_PATH . '/cache/' . $this->_cacheFilename;
			
			if($id != ''){
				$this->_id =  $id;
			}
			$this->setAll();
			$this->setTotal();
		
		}
		
		/**
		 *	Destruct()
		 *	overwite CRUD::__destruct so purge doesn't run
		 */
		public function __destruct(){}
		
		/**
		 *	edit
		 *	edit an item already in the database
		 */
		public function edit(){
			
			// Set error counter to 0. 
			// Increment everytime there is an error.
			$error = 0;
			
			$this->logo();
			
			// what fields are needed/might be submitted?
			$fields_array = $this->_db->get_results("SELECT `title` FROM `{$this->_sql['main_table']}`;", "ARRAY_A");
			
			// clean data: make nice for data input
			$fields = array();
			foreach($fields_array as $field){
				$field_name = str_replace(array('/', ' '), '_', stripslashes(strtolower($field['title'])));
				$fields[$field['title']] = $this->_db->escape(read($_POST, $field_name, ''));
			}
			
			// is all data present and correct?
			$all_data_present = (1) ? true : false;

			// has data changed?
			$data_has_changed = (1 == 2) ? false : true; // seriously - why have I done this?
			
			$old_values = array();
			foreach($this->_properties as $property){
				$old_values[$property['title']] = $property['value'];
			}		

			
			// check for data
			if($all_data_present === true && $data_has_changed === true){ // all data present
			
				$changed = 0; //counter. - used to determine whether to update the cache later
				
				// loop through all values
				foreach($fields as $field_name => $field_value){
					// only add/update ones that have changed
					if(strtolower($field_name) == 'logo' && !empty($_FILES['logo']['name'])){
						// Fudge.
						$changed++;
					} else if($field_value != $old_values[$field_name]){
						// Query
						$query = "UPDATE {$this->_sql['main_table']} SET value = '{$field_value}', date_edited = Now() WHERE title = '{$field_name}' LIMIT 1;";
						niceError($query); // Debugging - echo SQL
						
						//Run query
						if($results = $this->_db->query($query)){
							$changed++; // hooray a value has been changed. increment the change counter
						}
						
						niceError($query); // Debugging - echo SQL
						
					}
									
					
				}
				
				
				
				// run query
				if($changed > 0){ /// success
					$user_feedback['content'] = 'You have successfully updated your settings.';
					// update object with new variables
					$clear_cache = true;
					$this->setAll($clear_cache);
						
				} else{ // failure
					$error++;
					$user_feedback['content'] = 'You have failed update your settings because you haven\'t entered any new or different information.';
				}
			} else{ // vital data missing
				$error++;
				$user_feedback['content'][] = 'This item hasn\'t been updated due to the following problems:';
				
				// No value
				if(!$value){
					$user_feedback['content'][] = 'Setting was missing';
				}
				
				// no data has changed
				if($data_has_changed === false){
					$user_feedback['content'][] = 'You have not changed any information';
				}
			}		

			
			// redirect user & give feedback
			$user_feedback['type'] = ($error > 0) ? 'error' : 'success';
			
			return $user_feedback;
			
		}
		
		/**
		 *	setAll
		 *	return all the details of all settings
		 */
		public function setAll($clear_cache = false){

			
			$query = "SELECT * FROM `{$this->_sql['main_table']}` LIMIT {$this->_queryFilter['limit']};";
			
			niceError($query); // Debugging - echo SQL			
			
			$this->_properties = $this->_db->get_results($query, "ARRAY_A");
		
			if(!empty($this->_properties)){
				foreach($this->_properties as $property){
					if($property['title'] == 'Main currency'){
						$currency = $property['value'];
					}
				}
			}
			// grab currency value - e g. £/$/€ etc
			if(!empty($currency)){
				$query = "SELECT value FROM currency WHERE title = '{$currency}' LIMIT 1";
				niceError($query); // Debugging - echo SQL
				
				$this->_properties[sizeof($this->_properties)] = array('id' => 9999, 'title'=>'currency_value', 'value' => $this->_db->get_var($query), 'date_edited' => date('Y-m-d H:i:s'));
			
			}
	

			
		}	
		
		/**
		 *	setById
		 *	return a specific setting info from a supplied id
		 */
		public function setById(){
			
			// If id exists, go ahead with query
			if($this->_id){
			
				$query = "SELECT * FROM {$this->_sql['main_table']} WHERE id = '{$this->_id}'";
				
				niceError($query); // Debugging - echo SQL
				
				// Run query
				if($this->_properties = $this->_db->get_row($query,"ARRAY_A")){ // worked					
					return true;
				} else{ // failed
					return false;
				}
			} else{
				// No id: no nothing:(
				return false;
			}

		}
		
		/**
		 *	getTotal
		 *	get all website settings in the system: used for pagination and display
		 */
		public function setTotal(){
			
			// SQL
			$query = "SELECT COUNT(id) FROM {$this->_sql['main_table']} WHERE 1" ;
			
			niceError($query); // Debugging - echo SQL

			// Run query
			if($this->_total = $this->_db->get_var($query)){ // worked
				return true;
			} else{ // failed
				return false;
			}
			
		}	
		
		/**
		 *	logo
		 *	
		 */
		public function logo(){
			
			if(!empty($_FILES['logo']['name'])){
			
				$objImage = new Image($this->_db, array(), false);
			
				$objImage->upload();
				
				$_POST['logo'] = str_replace(SITE_PATH, '', $objImage->getFilename());

			}
			
			
		}
		
	
	}

?>