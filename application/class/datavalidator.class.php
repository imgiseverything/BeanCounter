<?php

/**
 *	DataValidator class
 *
 *	
 *	
 *	@package		bean counter
 *	@since			03/02/2010
 *	@author			philthompson.co.uk
 *	@copyright		philthompson.co.uk/mediaburst.co.uk
 *	@version 		1.0a	
 *
 *
 *	Contents
 *	
 *	Class variables
 *	Methods
 *		Constructor
 *		setFieldsRequired
 *		allDataPresent()
 *		hasDataChanged()
 *		allDataCorrect()
 *		getErrors()
 *		isRecordUnique()
 */
 
 
class DataValidator{

	/**
	 *	@var	object
	 *	Local database object
	 */
	protected $_db;
	
	
	/**
	 *	@var bool
	 *	True or false on whether all the data that needs to be
	 *	present is
	 */
	protected $_isPresent = false;
	
	/**
	 *	@var bool
	 *	True or false on whether all the data submitted is valid
	 *	e.g. ints are ints, varchars aren't too long etc
	 */
	protected $_isValid = false;
	
	/**
	 *	@var array
	 *	List of errors regarding badly formatted submitted data
	 */
	protected $_invalidData = array();
	
	/**
	 *	@var bool
	 *	True or false on whether all the data submitted is different
	 *	in someway to the currently stored data becasue if it is
	 *	identical the MySQL query will fail
	 */
	protected $_hasChanged = false;
		
	/**
	 *	@var array
	 *	List of the fields that will be checked for validity
	 */
	protected $_fields = array();
	
	/**
	 *	@var array
	 *	Extend details about the $_fields variable e.g.
	 *	maximum and minimum lengths, enum values, whether it's 
	 *	a primary key or not, etc.
	 *	@example
	 *	array(
	 *		'mobile' => 
	 *			array(
	 *          	'Field' => 'mobile', 
	 *				'Type' => 'varchar(20)',
	 *          	'Null' => 'NO', 
	 *				'Key' =>  'UNI',
	 *          	'Default' => '', 
	 *				'Extra' =>  '',
     *   		),
     *		'first_name' =>
	 *			array(
	 *         	 	'Field' => 'firstname', 
	 *				'Type' => "enum('Pending','Active','Suspended','Stopped','Deleted')",
	 *          	'Null' => 'NO', 
	 *				'Key' =>  'PRI',
	 *          	'Default' => '', 
	 *				'Extra' =>  '',
     *   		),
	 *	)
	 */
	protected $_fieldsDetails = array();
	
	/**
	 *	@var array
	 *	List of fields which cannot be NULL
	 */
	protected $_fieldsRequired = array();
	
	
	/**
	 *	@var array
	 *	List of the data currently stored for an item
	 */
	protected $_originalData = array();
	
	/**
	 *	Constructor
	 *	@param	array	$fieldDetails - must be an array of data as if we've done
	 *	SHOW FIELDS FROM table ON the row and got back an associative array of info
	 */
	public function __construct($db, $fieldsDetails, $originalData = array()){
	
		$this->_db = $db;
		$this->_fieldsDetails = $fieldsDetails;
		$this->_fields = Application::create1DArray($fieldsDetails, 'Field');
		
		$this->_originalData = $originalData;
		
		$this->setFieldsRequired($this->_fieldsDetails);
		
		$this->allDataPresent();
		
	}
	
	
	/**
	 *	setFieldsRequired()
	 *	Go through all the fields and see which ones are required i.e. NOT NULL
	 *	@param array $fields
	 */
	protected function setFieldsRequired($fields){
	
		if($fields){
			foreach($fields as $result){
				// if the field can't be null add it to the 
				// required fields
				if($result['Null'] == 'NO'){
					$this->_fieldsRequired[] = $result['Field'];
				} 
			} 			
		}

	}	
	
	
	
	
	
	
	/**
	 *
	 *	Data Validation
	 *	
	 *	RULES
	 *	# We don't put anything in the database unless it's clean
	 *	# We don't attempt to perform query if required data is absent
	 *	# We don't attempt an update if no data has changed
	 *	# We give our user good quality clear feedback if any of 
	 *	  the above happen
	 *	
	 *	Methods:
	 *		
	 */
	
	/**
	 *	allDataPresent()
	 *	loop through all required fields 
	 *	(FYI: are database field names e.g. id, title, etc)
	 *	and ensure required fields are present
	 *	@return bool $all_data_present
	 */
	protected function allDataPresent(){
	
	
				
		// set intitial value to be 1 just in case data is missing completely
		$missing_fields = 1;
		
		// this object has fields
		if(!empty($this->_fields)){
		
			// fields array exist so set value to none
			$missing_fields = 0;
			// loop through all fields
			foreach($this->_fields as $field){
				// if the field is present in the required array
				if(in_array($field, $this->_fieldsRequired)){

					// is that required field present as a post variable?
					if(empty($_POST[$field]) && $field != 'status'){
					
						// is it a datetime field?
						if($this->_fieldsDetails[$field]['Type'] == 'datetime'){					
						
							// the required datetime values aren't present
							if(empty($_POST[$field . '_year']) || empty($_POST[$field . '_month']) || empty($_POST[$field . '_day']) || empty($_POST[$field . '_hour'])){
								$missing_fields++;
							} else{
								// is date valid ?
								$objCalendar = new Calendar($_POST[$field . '_month'], $_POST[$field . '_year']);						
								$validDate = $objCalendar->validDate($_POST[$field . '_day']);
								if($validDate !== true){
									$missing_fields++;
								}
							}
							
						} else if($this->_fieldsDetails[$field]['Type'] == 'date'){
								
								// the required date values aren't present
								if(empty($_POST[$field . '_year']) || empty($_POST[$field . '_month']) || empty($_POST[$field . '_day']) || empty($_POST[$field . '_hour'])){
									$missing_fields++;
								}
								
						} else{
						
							// this is edit mode and the password is missing - we don't need it
							if($field  == 'password' && !empty($_POST['action']) && $_POST['action'] == 'edit'){
								// make sure the password field in edit mode doesn't throw errors
								// becasue the password field is in another form :)
							} else{
								// if not, increment the number of missing fields
								$missing_fields++;
							}// end else
							
						}
						
					} // end if
				} else if($this->_fieldsDetails[$field]['Type'] == 'datetime'){
				
					// Date time passed in a unrequired field isn't legal e.g. 30th February :(
					$objCalendar = new Calendar(Application::read($_POST, $field . '_month', ''), Application::read($_POST, $field . '_year', ''));									
					$validDate = $objCalendar->validDate(Application::read($_POST, $field . '_day', ''));
					if($validDate !== true){
						$missing_fields++;							
					}
					
				}	
							
			}

		}

		// if there are no missing fields (count = 0) then all 
		// data is present (true) otherwise data is absent (flase)
		$this->_isPresent = ($missing_fields == 0) ? true : false;
		
		// Now check all the data is correct
		// if it isn't return false
		$this->allDataCorrect();	
		
		if(!empty($this->_originalData)){
			$this->dataHasChanged();
		}	
			
		if($this->_isValid !== true){
			$this->_isPresent = false;
		}
		
		
	}
	
	/** 
	 *	dataHasChanged
	 *	check to see if any data has changed when an edit 
	 *	has been requested if no fields have changed then 
	 *	the edit method shouldn't let a query be sent to 
	 *	the database AND the query should only update those 
	 *	fields were changes have been made
	 */
	public function dataHasChanged(){

		// set intitial value to be 0 - because if no 
		// fields exist then data can't have been changed 
		// (technically it can but we don't want to 
		// allow *all* NULL values
		$changed_fields = 0;
		
		if(!empty($this->_fields)){
			// loop through all fields
			foreach($this->_fields as $field){
			
				if(isset($this->_originalData[$field]) && !is_array($this->_originalData[$field])){
				
					if(!empty($_POST[$field]) && $this->_db->escape($_POST[$field]) != $this->_db->escape($this->_originalData[$field]) && $field != 'password'){
						// if the field exists and is different to the object value
						// and is present as a post variable?
						$changed_fields++;
					} else if(empty($_POST[$field]) && $this->_db->escape(Application::read($this->_originalData, $field, '')) != '' ){
						// field is missing from the post but it currently has a value
						$changed_fields++;
					}
				}
			}
		}
		
		// if there are no missing field then all data is present
		$this->_dataHasChanged = ($changed_fields > 0) ? true : false;
		
		if($this->_dataHasChanged === false){
			$this->_invalidData[] = 'You have not changed any information';
		}
		
	}

		
	/**
	 *	allDataCorrect()
	 *	loop through all fields 
	 *	(FYI: are database field names e.g. id, title, etc)
	 *	and ensure data is the right format e.g. ints are 
	 *	numbers, strings are text
	 *	@return bool $all_data_present
	 */
	protected function allDataCorrect(){
	
		// set intitial value to be 1 just in case 
		// data is all wrong
		$incorrect_fields = 1;

		// this object has fields
		if(!empty($this->_fields)){
			// fields array exist so set value to none
			$incorrect_fields = 0;
			
			// loop through all fields
			foreach($this->_fields as $field){


				// Grab the field type, then check the data 
				// against it set an error for each
				// wrong date, data that is too long etc						
				$fieldType = explode('(', $this->_fieldsDetails[$field]['Type']);
				$fieldType = $fieldType[0];						
				$fieldLabel = ucfirst(str_replace('_', ' ', $field));

				// is that required field present as a post variable?
				if(!empty($_POST[$field]) && $field != 'status' && $fieldType != 'datetime'){

					switch($fieldType){
						
						case 'int':
							// This field should be numeric but isn't :(
							$cleanField = preg_replace("/[^0-9]/", "", trim($_POST[$field]));
							
							if($_POST[$field] != $cleanField){
								$incorrect_fields++;
								$this->_invalidData[ucwords($field)] = 'must be a number';
							}
							break;
							
						case 'float':
							// This field should be numeric but isn't :(
							$cleanField = preg_replace("/[^0-9.,]/", "", trim($_POST[$field]));
							
							if($cleanField != $_POST[$field]){
								$incorrect_fields++;
								$this->_invalidData[ucwords($field)] = ' must be a number';
							}
							break;							
						
						case 'varchar':
							// Check for string length 
							// (supplied in Type array_key like so varchar(100)
							$maxLength = preg_replace("/[^0-9]/", "", trim($this->_fieldsDetails[$field]['Type']));
							
							
							if(strlen($_POST[$field]) > $maxLength){
								$incorrect_fields++;
								$this->_invalidData[ucwords($field)] = 'is too long. It can only be ' . $maxLength . ' character(s) long.';
							}
							
							break;
							
						default:
							break;
							
					} 
					
				} else if($fieldType == 'datetime'){
				
					// Date time passed in a unrequired field 
					// isn't legal e.g. 30th February :(
					$objCalendar = new Calendar(Application::read($_POST, $field . '_month', ''), Application::read($_POST,$field . '_year', ''));					$validDate = $objCalendar->validDate(Application::read($_POST, $field . '_day', ''));

					if($validDate !== true){
						$incorrect_fields++;
						$this->_invalidData[ucwords($field)] = 'must be a valid date.';
					}
					
				}	
						
			}
			
		}
	
		// if there are no incorrect fields (count = 0) then all 
		// data is correct (true) otherwise data is incorrect (false)
		$this->_isValid = ($incorrect_fields == 0) ? true : false;
		
	}
	
	/**
	 *	getErrors
	 *	If required $_POST values are missing then create 'user-friendly' 
	 *	error messages e.g.
	 *	`Field name` was missing
	 *	@return array $errors
	 */
	public function getErrors(){
		
		// setup (error as array now to avoid PHP errors)
		// it will store all text error messages
		$errors = array(); 
					
		// loop through all fields
		foreach($this->_fields as $field){
			// check to see if the field is required
			// then check it's not a datetime field and check whether
			// a $_POST value exist for it
			if(in_array($field, $this->_fieldsRequired)){
			
				$fieldLabel = ucfirst(str_replace('_', ' ', $field));
				
				if($field  == 'password' && !empty($_POST['action']) && $_POST['action'] == 'update'){
					// make sure the password field in edit mode doesn't throw errors
					// becasue the password field is in another form :)
				} else if($this->_fieldsDetails[$field]['Type'] != 'datetime' && $this->_fieldsDetails[$field]['Type'] != 'date' && empty($_POST[$field])){
					// this field is required but the field doesn't exist
					// create (hopefully) user friendly error message
					$errors[ucwords($fieldLabel)] = 'is missing';
				} // end if
				
			}
		
		} // end foreach

		// Add the invalid data messages to the array
		if(!empty($this->_invalidData)){			
			$errors = array_merge($errors, $this->_invalidData);
		}
		
		return $errors;
	}
	
	
	
	/*
	 *	isRecordUnique()
	 *
	 *	@param array $fields 
	 *		e.g. array('email'=>'username@example.com','status'=>1);
	 *	@param bool $combo
	 *		if this is true then all the supplied fields must match
	 *		if it's false then only one supplied field must match
	 *	@return bool
	 */
	protected function isRecordUnique($fields, $combo = true){
		
		$query_joins = array();
	
		if(is_array($fields) && !empty($fields)){
			// start query
			$query = "SELECT * FROM `{$this->_sql['main_table']}` WHERE 1 ";
			$i = 1;
			// build up query with array values
			foreach($fields as $key => $value){
				// clean up value
				$value = $this->_db->escape($value);
				
				$and_or = ($i > 1 && $combo !== true) ? 'OR' : 'AND';
				
				$query .= " {$and_or} `{$key}` = '{$value}'";
				$i++;
			}
			// end query
			$query .= " LIMIT 1;";
			
			niceError($query);
			if($results = $this->_db->get_row($query)){
				return false;
			} else{
				return true;
			}
		}
		
	}

	
	
}

?>