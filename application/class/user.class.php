<?php
/**
 *	=========================================================================
 *	
 *	User Class	
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
 *	@modified	22/08/2009
 *	
 *  =========================================================================
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	variables
 *	
 *	construct
 *	
 *	methods
 *	
 *		customQueryFilters
 *		isRecordUnique
 *		setTitle	
 *		getClient
 *		setTitle		
 *		password	
 *		clientFilter
 *		resetTitle
 *	
 *	=========================================================================
 */
	
	
	class User extends Scaffold{
	
		
		// Variables		
		protected $_name;
	
		// construct
		public function __construct($db, $filter, $id = false){
					
			// Object naming conventions
			$this->_name = 'user';		
			
			$this->_filter = $filter;
		
			// SQL - Database related namings
			$this->_sql['main_table'] =  'user_client';
			
			// client filter
			$this->client = read($this->_filter, 'client', '');
			
			parent::__construct($db, $this->_filter, $id);
			
			// Format the object name so it looks better in the view
			$this->_name = 'user';
			
			// setTitle
			$this->setTitle();
			
			// setPageTitle
			$this->setPageTitle();
			
			// set breadcrumb page title
			$this->setBreadcrumb();
			
			if($this->client){
				$_POST['client'] = $this->client;
				$_POST['access_level'] = 2;
			}
	
			
			
		}
		
		/**
		 *
		 *	Methods
		 *	
		 *	customQueryFilters()
		 *	filter the object so we only see what we want
		 *
		 *	isRecordUnique()
		 *	ensure the new record to be added is unique and not a duplicate
		 *	
		 *	
		 *	setTitle()
		 *	Users don't have a title but all objects rely on this so we grab the user's 
		 *	firstname and surname and concatenate them.
		 *
		 *	
		 *	password()
		 *	
		 *	Allow the user to change thier password
		 *	
		 *	clientFilter()
		 *	
		 *	Only show user of a set client
		 *
		 *  Others
		 *
		 *	setPageTitle		
 		 *	setBreadcrumbTitle
 		 *
		 *
		 */
		
		/**
		 *	customQueryFilters
		 */
		protected function customQueryFilters(){
		
			$this->_queryFilter['custom'] = '';
			if($this->client){
				// show only the clients of this user 
				$this->_queryFilter['custom'] = " AND client = '{$this->client}' ";
			}
			
			$this->_queryFilter['search'] = (!empty($this->_filter['search'])) ? " AND (firstname LIKE '%{$this->_filter['search']}%' OR surname LIKE '%{$this->_filter['search']}%' OR CONCAT(firstname,' ',surname) LIKE '%{$this->_filter['search']}%') " : '';
			
		}
		
		/**
		 *	add
		 */
		protected function add(){
		
			$email = read($_POST, 'email', '');
			$check_values = array('email' => $email);
			
			$record_is_unique = (!empty($email)) ? $this->isRecordUnique($check_values) : true;
			
			// if the information s unique run the parent's add method
			if($record_is_unique === true){
				return parent::add();
			}
			else{ // record is not unique, so tell the user that
				$user_feedback['type'] ='error';
				$user_feedback['content'] = 'A user with that email is already registered';
				return $user_feedback;
			}
			
		}
		
		/*
		 * isRecordUnique
		 * @param: $fields array e.g. array('email'=>'username@example.com','status'=>1);
		 */
		public function isRecordUnique($fields){
			
			$query_joins = array();
		
			if(is_array($fields) && !empty($fields)){
				// start query
				$query = "SELECT * FROM `{$this->_sql['main_table']}` WHERE 1 ";
				
				// build up query with array values
				foreach($fields as $key => $value){
					// clean up value
					$value = $this->_db->escape($value);
					$query .= " AND `{$key}` = '{$value}'";
				}
				// end query
				$query .= " LIMIT 1;";
				
				if($results = $this->_db->get_row($query)){
					return false;
				} else{
					return true;
				}
			}
			
		}
		
		/**
		 *	setTitle
		 *	set a title for each user array_key in $_properties becaus there isn't one
		 *	in the table - but a lot of code relies upon its presence
		 */
		protected function setTitle(){
			
			if($this->_id){
				// get just one
				$this->_properties['title'] = trim(read($this->_properties, 'firstname', '') . ' ' . read($this->_properties, 'surname', ''));
			} else{
				// go through all properties (if they exist)
				if(!empty($this->_properties)){
					for($i = 0; $i < sizeof($this->_properties); $i++){
						$this->_properties[$i]['title'] = $this->_properties[$i]['firstname'] . ' ' . $this->_properties[$i]['surname'];
						//echo $this->_properties[$i]['title'];
					} 
				}
			} 
			
			
			// Not sure why this is here, seems very redundant
			$this->_exists = (($this->_id && empty($this->_properties['title'])))? false : true;
			
		}		
		
		/**
		 *	password
		 *	@return array $user_feedback
		 */
		protected function password(){
		
			// Error counter
			// Increment everytime there is an error.
			$error = 0;
			
			// clean data: make nice for data input
			$fields = array('old_password', 'password', 'password2');
			// turn field name into easy to use variable names: e.g. $email
			extract(cleanFields($fields));
			
			// check for ID - no ID = no database interaction
			if($this->_id){
			
				$query = "SELECT password FROM `{$this->_sql['main_table']}` WHERE id = '{$this->_id}' LIMIT 1";
				
				$current_password = $this->_db->get_var($query);

				// is all data present and correct?
				$all_data_present = ($old_password && $password && $password2 && ($old_password != $password) && ($password == $password2) && (md5($old_password) == $current_password) && strpos($password, ';') === false) ? true : false;		
			
				
				// has data changed?
				$data_has_changed = (md5($password) == $current_password) ? false : true;		
				
				// check for data
				if($all_data_present === true && $data_has_changed === true){ // all data present
					// query
					$query = "UPDATE `{$this->_sql['main_table']}` SET password = md5('$password'), date_edited = Now() WHERE id = '{$this->_id}' LIMIT 1;";
					
					//niceError($query); // DEBUGGING
				
					// run query
					if($results = $this->_db->query($query)){ /// success
						// Delete cached files as they are now out of date
						$objCache = new Cache($this->_name);
						$objCache->delete('folder', $this->_name);
						$user_feedback['content'] = 'You have successfully updated this user\'s password';
						$user_feedback['id'] = $this->_id;
					} else{ // failure
						$error++;
						$user_feedback['content'] = 'Due to a technical error, you have failed update this user\'s password';
					}
				} else{ // vital data missing
					$error++;
					$user_feedback['content'][] = 'This user\'s password hasn\'t been updated due to the following problems:';
					
					// Current password was not correct
					if(md5($old_password) != $current_password){
						$user_feedback['content'][] = 'Current password was incorrect - it should be';
					}
						
					// No old password
					if(!$old_password){
						$user_feedback['content'][] = 'Current password was missing';
					}					
					
					// No new password
					if(!$password){
						$user_feedback['content'][] = 'New password was missing';
					}
					
					// No new password confirm
					if(!$password2){
						$user_feedback['content'][] = 'New password confirmation was missing';
					}
					
					// Confirmation doesn't match
					if($password != $password2){
						$user_feedback['content'][] = 'Password confirmation does not match new password';
					}
					
					// password contains illegal character ; // suspected SQL injection
					if(strpos($password, ';') !== false){
						$user_feedback['content'] = 'The new password contains an illegal character (<em>;</em>). Please remove it and try again.';
					}
					
					// no data has changed
					if($data_has_changed === false){
						$user_feedback['content'][] = 'You have not changed any information. Old and new passwords are the same';
					}
					
					
				}
			
			} else{ // No id: so cancel
				$error++;
				$user_feedback['content'] = 'Due to a technical error, you have failed to update this user\'s password';
			}
			
			// redirect user & give feedback
			$user_feedback['type'] = ($error > 0) ? 'error' : 'success';
			
			return $user_feedback;
		}		
		
		/**
		 *	clientFilter
		 */
		public function clientFilter(){
			
			if($this->client){
			
				$i = 0; // counter
				$irrelevant_users = 0; // counter of users which shouldn't be here
				foreach($this->_properties as $property){
					// remove unwanted users
					if($property['client'] != $this->client){
						unset($this->_properties[$i]);
							
						//$this->_properties[$i] = array();
						array_values($this->_properties);
						//$this->_properties[$i] = array_merge($this->_properties[$i]);
						$irrelevant_users++;
					}
					$i++;
					
				}
				// now reset the total number of users to reflect the removed users
				$this->total = ($this->total - $irrelevant_users);
			}
			
		}
		
		/**
		 *	resetTitle()
		 */
		public function resetTitle(){
			$this->setTitle();
		}
		
		
		
	
	}

?>