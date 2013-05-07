<?php
/**
 *	=========================================================================
 *	
 *	Form (helper) Class	
 *	-------------------------------------------------------------------------
 *	
 *	Automatically create add/edit forms based on pre-supplied data. Show
 *	error messages for missing/incorrect data
 *	
 *	=========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 		2008-2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license		n/a
 *	@version		1.0	
 *	@author			philthompson.co.uk
 *	@since			11/02/2008
 *
 *	@lastmodified	07/05/2013
 *	
 *	=========================================================================
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	Variables
 *	Methods
 *		Constructor
 *		fromGetToPost
 *		generateFields
 *		setRequired
 *		labelText
 *		label
 *		getValue
 *		getFieldType
 *		getError
 *		input
 *		inputHidden
 *		inputPassword
 *		dateTime
 *		textarea
 *		inputInt
 *		enum
 *		fieldElement
 *		fieldElementForeignKey
 *		setTables
 *		getMaxLength
 *		getAllFieldElements
 *		
 *	=========================================================================
 *
 */

class Form{

	// Variables
	
	
	/**
	 *	@var array
	 *	List of database columns e.g. id, title, description, etc
	 */
	private $_fields = array();
	
	/**
	 *	@var array
	 *	List of information about the database columns - as if
	 *	a SHWO FIELDS FROM tablename; query had been done.
	 */
	private $_fieldsDetails = array();
	
	/**
	 *	@var array
	 *	A list of related tables
	 */
	private $_foreignKeys = array();
	
	/**
	 *	@var array
	 *	The current data which we will pre-populate the form values with
	 */
	private $_properties = array();
	
	/**
	 *	@var array
	 *	Fields we don't want automatically building for whateever reason.
	 *	By default this class builds a form field for each column in the
	 *	database table but that isn't always required.
	 */
	private $_ignoredFields = array();
	
	/**
	 *	@var string
	 *	The HTML form fields that have been created
	 */
	protected $_allFieldElements;
	
	/**
	 *	@var object
	 *	Local database object
	 */
	protected $_db;
	
	/**
	 *	@var array
	 *	List of all database tables in the database
	 */
	private $_tables = array();
	
	/**
	 *	@var object
	 *	Local Site object
	 */
	private $_site;
	
	/**
	 *	@var object
	 *	Local Application object
	 */
	private $_application;
	
	/**
	 *	@var string
	 *	What is the form doing? (add|edit|delete|duplicate|etc)
	 */
	private $_action;
	
	
	/**
	 *	@var array
	 *	Not used. Would be used to ensure the automatic form generation 
	 *	grouped set fields into <fieldset>s for usabilit purposes
	 */
	protected $_groupings;
	
	/** 
	 *	Constructor
	 *	Build the form fields immediately and automatically.
	 *
	 *	@param array $fields  the form fields you want
	 *	@param array $fields_details the MySQL table values for $fields array
	 *	@param array $foreign_keys array  does the table have foreign keys  e.g. one to many relationship table
	 * 	@param array $current_data the current object data used in  edit forms default = false
	 * 	@param array $ignored_fields field sthat shouldn't be included in the automatic form generator
	 */
	public function __construct($fields, $fields_details, $foreign_keys, $current_data = false, $ignored_fields = array(), $groupings = array()){
	
		global $db, $objSite, $action, $objApplication; // sorry mum :(
	
		// Setup local variables
		$this->_db = $db;
		$this->_sql['database_name'] = $this->_db->dbname;
		$this->_site = $objSite;	
		$this->_application = $objApplication;		
		$this->setAction($action);		
		$this->setTables();
		$this->_fields = $fields;
		$this->_fieldsDetails = $fields_details;
		$this->_foreignKeys = $foreign_keys;
		$this->_properties = $current_data;
		$this->_ignoredFields = $ignored_fields;


		if(empty($groupings)){
			$this->_groupings[] = $fields;
		} else{
			$this->_groupings = $groupings;
		}

		// Turn $_GET into $_POST (just in case)
		$this->fromGetToPost();		
		
		// get form fields
		$this->generateFields();
		
		
	}
	
	
	/**
	 *	setAction()
	 */
	public function setAction($action){
		$this->_action = $action;
	}
	
	
	/**
	 *	fromGetToPost()
	 *	A bit weird this one.
	 *	If there are any $_GET variables turn them into 
	 *	$_POSTs so some form fields are pre-filled. This allows us to
	 *	link to a page e.g. /projects/add/?title=The+title and the
	 *	the title input field's value will be 'The title'
	 *	
	 *	Why not just use $_REQUEST. Because it's shut-up; that's why.
	 */
	private function fromGetToPost(){

		if(!empty($_GET) && $_SERVER['REQUEST_METHOD'] != 'POST'){
			foreach($_GET as $key => $value){
				if($key != 'status' && $key != 'action'){
					$_POST[$key] = $value;
				}
			}
		}	
		
	} 
	 
	

	/**
	 *	generateFields()
	 *	
	 *	This handy method will automatically generate form fields - 
	 *	the label, the input, the error from the object's supplied data. 
	 *	
	 *	N.B. In some cases the user will have to create the form themselves where the 
	 *	data isn't so easy to manage
	 */
	public function generateFields(){
		
		// the string that all the data will go into;
		$this->_allFieldElements = '';
		
		
		// status hack
		// if we're looking at the status drop down - here's a quickhack to 
		// make sure it starts off as 1 
		$status = ($_SERVER['REQUEST_METHOD'] != 'POST') ? 1 : $this->getValue('status');

		// loop through all database columns
		foreach($this->_fields as $field){ 
		
		if(!in_array($field, $this->_ignoredFields)){
			
				// Create form label
				// generate label tag
				$label = $this->label($field);
	
	
				// Start form errors
				// Start with general error: required field is empty
				// Invalid dates: e.g. 31st February
				// Wrong data formats (COMING SOON)
								
				$error = $this->getError($field);
	
				// Create form input element (eg input, textarea
	
				// check field type
				$field_type = $this->fieldType($field);
				
				// show a different input element for the different data types;
				switch($field_type){
						
					// Textarea
					case 'tinytext':
					case 'longtext':
					case 'mediumtext':
					case 'text';
						$input = $this->textarea($field, $this->getValue($field));
						break;
					
					// Standard input
					default:
					case 'char':
					case 'varchar':
						$input = $this->input($field, $this->getValue($field));
						break;
					
					// Date and time
					case 'datetime':
						$label = ''; // remove label
						$input = $this->dateTime($field, $this->getValue($field));
						break;
					
					// Date
					case 'date':
						$label = ''; // remove label
						$input = $this->inputDate($field, $this->getValue($field));
						break;
						
					// Int - could be a foreign key
					case 'int':	
					case 'float':
					case 'double':
					case 'decimal':
						if($field  == 'status'){
							$label = ''; // remove label
						}
						$input = $this->inputInt($field, $this->getValue($field));
						break;
					
					// Enum - 1 option from a selection
					case 'enum':
					case 'set':
						$input = $this->enum($field, $this->getValue($field));
						// bit messy, the following snippet to rid the label :(
						$options = explode('(', $this->_fieldsDetails[$field]['Type']);
						$options = (!empty($options[1])) ? str_replace(')', '', $options[1]) : $options[0];
						$options_array = explode(",",str_replace("'", '', $options));
						if($options_array == array('Y', 'N')){
							$label = '';
						}
						// end mess
						break;
						
				}
				
				
				// Put it all together
				$field_element = $this->fieldElement($label, $input, $error);
				
				// add form elements to the rest of the other form elements
				$this->_allFieldElements .=  $field_element;
				
			}
			
		 	
		}
		
		
		// What about 3rd party relationships? 
	 	// How will be add those?
	 	 

		// Foreign keys and 3rd party many-to-many 
		// relationships exist so loop through all these 
		// and add drop downs/checkboxes
		if(!empty($this->_foreignKeys)){
			foreach($this->_foreignKeys as $foreign_key){				
				// add element to the rest of the other elements
				$this->_allFieldElements .=  $this->fieldElementForeignKey($foreign_key);
			}
			
		}
		
		
	}
	
	/**
	 *	setRequired()
	 *	@param 	string 	$field
	 *	@param 	boolean	$force_required
	 *	@return string	$required (HTML)
	 */
	public function setRequired($field, $force_required = false){
	
		$required = '';
		
		if(
			$force_required === true 
			|| (
				isset($this->_fieldsDetails[$field]['Null']) 
				&& $this->_fieldsDetails[$field]['Null'] == 'NO'
			)
		){
			$required = ' class="required" required="required" aria-required="true"';
		}
			
		return $required;
	}	
	
	/**
	 *	setRequiredLabel()
	 *	@param 	string 	$field
	 *	@param 	boolean	$force_required
	 *	@return string	$required (HTML)
	 */
	public function setRequiredLabel($field, $force_required = false){
	
		$required = '';
		
		if(
			$force_required === true 
			|| (
				isset($this->_fieldsDetails[$field]['Null']) 
				&& $this->_fieldsDetails[$field]['Null'] == 'NO'
			)
		){
			$required = ' <span class="required" title="' . $this->labelText($field) . ' is required">*</span>';
		}
		
		return $required;
	}
	
	/**
	 *	labelText()
	 *	@param string $field
	 *	@return string
	 */
	public function labelText($field){
		$label = ucfirst(str_replace('_', ' ', $field));
		
		
		$find = array('vat');
		$replacements = array('VAT');
		
		$label = str_replace($find, $replacements, $label);
		return $label;
	}
	
	/**
	 *	label()
	 *	@param string $field
	 *	@param string $text
	 *	@param boolean $force_required
	 *	@return string
	 */
	public function label($field, $text = false, $force_required = false){
	
		if($text == false){
			$text = $this->labelText($field);
		}
	
		
		$label = '<label for="' . $field . '"' . $this->setRequired($field, $force_required) . '>' . $text . $this->setRequiredLabel($field, $force_required) . '</label>' . "\n";
		return  $label;
		
	}
	
	
	/**
	 *	getValue()
	 *	@param 	string $field
	 *	@return string $value
	 */
	public function getValue($field){
	
		$value = read($_POST, $field, '');

		if(
			$this->_action == 'edit' 
			|| $this->_action == 'duplicate' 
			|| substr($this->_action, 0, strlen('edit')) == 'edit'
		){
			$value =  read($_POST, $field, read($this->_properties, $field, ''));
		}
		
		return stripslashes(urldecode($value));
		
	}
	
	
	
	/**
	 *	fieldType()
	 *	@param string $field
	 *	@return string $field_type
	 */
	public function fieldType($field){
		
		if(strpos($this->_fieldsDetails[$field]['Type'], '(') !== false){
			$field_type = substr($this->_fieldsDetails[$field]['Type'], 0, (strpos($this->_fieldsDetails[$field]['Type'], '(')));
		} else{ 
			$field_type = $this->_fieldsDetails[$field]['Type'];
		}
		
		return $field_type;
	}
	
	/**
	 *	getError()
	 *	@param string $field
	 *	@return string $error
	 */
	public function getError($field){
	
		$error = '';
		
		$maxlength = (int)$this->getMaxlength($this->_fieldsDetails[$field]['Type']);
		$field_type = $this->_fieldsDetails[$field]['Type'];
		
		if(
			isset($_POST['action']) 
			&& empty($_POST[$field]) 
			&& $this->_fieldsDetails[$field]['Null'] == 'NO'
		){
			if($_POST['action'] == 'edit' && $field == 'password'){
				// don't a show an error for the missing password in edit mode
				// because passwords are in a separate form :)
			}  else if(
				isset($_POST[$field]) 
				&& (
					$_POST[$field] == '0' 
					|| $_POST[$field] == '0.00'
				)  
				&& (
					$field_type == 'int' 
					|| $field_type == 'float'  
					|| substr($field_type, 0, 7) == 'decimal'
				)
			){
			
				/*
				this field is integer/float/decimal set as zero
				php's empty() brings back false on a zero even though it's 
				fine for the database
				*/
			
			} else if($field_type == 'datetime'){
				
				// Date time passed in a unrequired field isn't legal e.g. 30th February :(
				$objCalendar = new Calendar(read($_POST, $field . '_month', ''), read($_POST, $field . '_year', ''));									
				$validDate = $objCalendar->validDate(read($_POST, $field . '_day', ''));
				if($validDate !== true){
					$error  .= '<span class="error">' . $this->labelText($field) . ' must be a valid date</span>';	
				}
				
			} else{
				// General error: required field is empty
				$error  .= '<span class="error">' . $this->labelText($field) . ' is required</span>';
			}
		} else if(
			isset($_POST['action']) 
			&& $this->_fieldsDetails[$field]['Type'] == 'datetime'
		){
			
			// this is a date/time field so is the date legitimate?
			// check it with the calendar object
			$objCalendar = new Calendar($_POST[$field . '_month'], $_POST[$field . '_year']);										$validDate = $objCalendar->validDate($_POST[$field . '_day']);
			
			if($validDate !== true){
				$error  .= '<span class="error">' . $this->labelText($field) . ' requires a valid date</span>';
			}
			
		} else if(
			isset($_POST['action']) 
			&& !empty($_POST[$field]) 
			&& $maxlength > 0 
			&& strlen(trim($_POST[$field])) > $maxlength
		){
			// field is too long
			$error  .= '<span class="error">' . $this->labelText($field) . ' is too long. It can only be a maximum of ' . $maxlength . ' characters</span>';
		}
		
		return $error;
	}
	
	/**
	 *	input()		 
	 *	@param string $field
	 *	@param string $value
	 *	@return string $input
	 */
	public function input($field, $value = ''){
	
		$maxlength = $this->getMaxlength($this->_fieldsDetails[$field]['Type']);
		
		// if this is a password field - make it a type 
		// password e.g. ******
		$type = ($field == 'password') ? 'password' : 'text';
		
		switch($field){
		
			default:
				$type = 'text';	
				break;
				
			case 'password':
				$type = 'password';	
				break;
				
			case 'email':
			case 'email_address':
				$type = 'email';
				break;
				
			case 'telephone':
			case 'mobile':
				$type= 'tel';
				break;
		}
		
		if($this->_action == 'edit' && $field == 'password'){
			// If this is edit mdoe and this is a password field then don't show it
			// because we change passwords on a separate form.
			$input = '<a href="' . str_replace('/edit/', '/password/', $_SERVER['REQUEST_URI']) . '">Change password</a>' . "\n";
		} else{
			$input = '<input type="' . $type . '" value="' . stripslashes(htmlentities($value)) . '" id="' . $field . '" name="' . $field . '" maxlength="' . $maxlength . '" />' . "\n";
		}
		
		// URL values
		if(strtolower($field)  == 'url'){
			$input = '<span class="pre-input">http:// ' . $this->_application->getFrontendSiteUrl() . '/</span>' . $input;
		}
		
		return $input;
		
	}
	
	/**
	 *	inputHidden()		 
	 *	@param string $field
	 *	@param string $value
	 *	@return string $input
	 */
	public function inputHidden($field, $value = ''){
		
		$input = '<input type="hidden" value="' . stripslashes(htmlentities($value)) . '" id="' . $field . '" name="' . $field . '" />' . "\n";
		
		return $input;
	}
	
	/**
	 *	inputPassword()		 
	 *	@param string $field
	 *	@param string $value
	 *	@return string $input
	 */
	public function inputPassword($field, $value = ''){
		
		$input = '<input type="password" value="' . stripslashes(htmlentities($value)) . '" id="' . $field . '" name="' . $field . '" />' . "\n";
		
		return $input;
	}
	
	/**
	 *	inputDisplay()		 
	 *	@param string $field
	 *	@param string $value
	 *	@return string $input
	 */
	public function inputDisplay($field, $value = ''){
	
		$maxlength = $this->getMaxlength($this->_fieldsDetails[$field]['Type']);
		
		// if this is a password field - make it a type 
		// password e.g. ******
		$type = ($field == 'password') ? 'password' : 'text';
		
		if($this->_action == 'edit' && $field == 'password'){
			$input = '<a href="' . str_replace('/edit/', '/password/', $_SERVER['REQUEST_URI']) . '">Change password</a>' . "\n";
		} else{
			$input = '<div class="input" id="' . $field . '-display">' . stripslashes(htmlentities($value)) . "\n" . $this->inputHidden($field, $value) . '</div>' . "\n";
		}
		
		return $input;
		
	}
	
	
	/**
	 *	dateTime()		 
	 *	@param string $field
	 *	@param string $value
	 *	@return string $input
	 */
	public function dateTime($field, $value = ''){

		$years_to_show = 50;
	
		$input = '<fieldset class="date">' . "\n";
		$input .= '<legend>' . $this->labelText($field) . '</legend>' . "\n";
		$input .= FormDate::getDay($field, read($_POST, $field . '_day', read($this->_properties, $field, date('d'))));
		$input .= FormDate::getMonth($field, read($_POST, $field . '_month', read($this->_properties, $field, date('m'))));
		$input .= FormDate::getYear($field, read($_POST, $field . '_year', read($this->_properties, $field, date('Y'))), $years_to_show, 'both');
		$input .= FormDate::getHour($field, read($_POST, $field . '_hour', read($this->_properties, $field, '')));
		$input .= FormDate::getMinute($field, read($_POST, $field . '_minute', read($this->_properties, $field, '')), 15);
		$input .= '</fieldset>' . "\n";
		
		
		return $input;
	}
	
	
	/**
	 *	inputDate()		 
	 *	@param string $field
	 *	@param string $value
	 *	@return string $input
	 */
	public function inputDate($field, $value = ''){
	
		$years_to_show = 10;
	
		$input = '<fieldset class="date">' . "\n";
		$input .= '<legend>' . $this->labelText($field) . '</legend>' . "\n";
		$input .= FormDate::getDay($field, read($_POST, $field . '_day', read($this->_properties, $field, date('d'))));
		$input .= FormDate::getMonth($field, read($_POST, $field . '_month', read($this->_properties, $field, date('m'))));
		$input .= FormDate::getYear($field, read($_POST, $field . '_year', read($this->_properties, $field, date('Y'))), $years_to_show, 'both');
		$input .= '</fieldset>' . "\n";
		
		
		return $input;
	}
	
	
	/**
	 *	textarea()		 
	 *	@param string $field
	 *	@param string $value
	 *	@return string $input
	 */
	public function textarea($field, $value = ''){
	
		$input = '<textarea name="' . $field . '" id="' . $field . '" rows="5" cols="10">' . stripslashes(htmlentities($value)).'</textarea>' . "\n";
		
		return $input;
	}
	
	
	
	/**
	 *	inputInt()		 
	 *	@param string $field
	 *	@param string $value
	 *	@return string $input
	 */
	public function inputInt($field, $value = ''){
	
		$field_type = $this->fieldType($field);
		
		// This field has a table of the same name - which 
		// *should* hold extra details for it						
		if(in_array($field, $this->_tables)){
		
			// but if it's status - just make it a hidden field
			if($field  == 'status'){
				$label = ''; // remove label
				$input = '<input type="hidden" name="' . $field . '" id="' . $field . '" value="1" />' . "\n";
			} else{				
				
				$objInflector = new Inflector();
									
				$input = '<select name="' . $field . '" id="' . $field.'">' . "\n";
				$input .= drawDropDown(getDropDownOptions($field, 'Choose'), $value);
				$input .= '</select>';
				// AJAX add new / update links
				if(file_exists(APPLICATION_PATH . '/ajax/' . $field . '_dropdown.php')){
					$input .= '<span class="help"><a href="/' . $objInflector->pluralise($field) . '/add/?mode=popup" class="button positive add add_new_link" id="add_new_' . $field . '_link" title="Add new ' . $field . '"></a> <a href="/ajax/' . $field . '_dropdown.php?mode=popup" class="refresh_link button refresh" id="refresh_' . $field . '_link" title="Refresh ' . $field . ' options"></a></span>';
				}
				$input .= "\n";
			}
		} else{
			// element doesn't have a corresponding 'extra details' table
			// so just draw up an input element
			$input = '<input type="text" value="' . stripslashes(htmlentities($value)) . '" id="' . $field . '" name="' . $field . '" class="int" />' . "\n";
			
			
			
			// Floats are most likely money so
			// add a currency symbol in front :)
			if($field_type == 'decimal' && $field != 'vat_rate'){
				$input = '<span class="pre-input">' . CURRENCY . '</span>' . $input;
			}
			
			if($field_type == 'decimal' && $field == 'vat_rate'){
				$input .= ' <span class="post-input">%</span>';
			}
			
			
			
		}
		
		return $input;
	}
	
	/**
	 *	enum()		 
	 *	@param string $field
	 *	@param string $value
	 *	@return string $input
	 */
	public function enum($field, $value = '', $label_text = false){
		$options = explode('(', $this->_fieldsDetails[$field]['Type']);
		$options = (!empty($options[1])) ? str_replace(')', '', $options[1]) : $options[0];
		$options_array = explode(",",str_replace("'", '', $options));
		
		// options are Y and N e.g. Yes and No
		// so show a checkbox
		if($options_array == array('Y', 'N')){
		
			$label = $this->labelText($field);
			
			if(!empty($label_text)){
				$label = $label_text;
			}
			
			//echo 'Yes or No';
			$input = '<input name="' . $field . '" id="' . $field . '_no" type="hidden" value="N" />' . "\n";
			$input .= '<input name="' . $field . '" id="' . $field . '" type="checkbox" class="checkbox" value="Y" ' . isChecked('Y', $value) . ' />' . "\n";
			$input .= '<label for="' . $field . '" class="checklabel">' . $label . '?</label>' . "\n";
			
			$label = '';
		} else{
			foreach($options_array as $options){
				$options_array2[$options] = $options;
			}
			//$input = $options;
			$input = '<select name="' . $field . '" id="' . $field . '">' . "\n";
			$input .= drawDropDown($options_array2, $value);
			$input .= '</select>' . "\n";
		}
		
		return $input;
	}
	
	
	/**
	 *	fieldElement()		 
	 *	@param string $label
	 *	@param string $input
	 *	@param string $error
	 *	@return string $field_element
	 */
	public function fieldElement($label, $input, $error = false){
		
		// has there been an error? if so create a class
		$field_element_error = ($error) ? ' error' : '';
		// start field element (put in a <div> so we have more control over it)
		$field_element = '<div class="field' . $field_element_error . '">' . "\n";
		// label
		$field_element .= $label . "\n";
		// input/textarea/select/etc
		$field_element .= $input . "\n";
		// error (if there is one)
		$field_element .= $error;
		// end element
		$field_element .= '</div>' . "\n";
		
		return $field_element;
	}
	
	
	/**
	 *	fieldElement()		 
	 *	@param string $field
	 *	@return string $field_element
	 */
	public function fieldElementForeignKey($field){
	
		$field_element =  '<fieldset id="' . $field . '" class="checkboxes">' . "\n". '<legend>' . $this->labelText($field) . '</legend>' . "\n";
		
		// loop through and find assign all current values to an array
		$current_{$field} = array();
		
		// foreign key values exist
		if(!empty($this->_properties[$field])){
			// loop through foreign keys values
			foreach($this->_properties[$field] as $foreign_key_data){
				// add to an array storing all current foreign key data
				$current_{$field}[] = $foreign_key_data['title'];
			}
		}
		// create helpful instructions for user
		if(empty($current_{$field})){
			$current = 'None selected';
		} else{
			$current = join(', ', $current_{$field});
		}
		$field_element .= '<p class="instructions"><strong>Current:</strong> ' . $current . '</p>' . "\n";
		
		// loop through and show all items
		$fk_options = $this->setForeignKeyOptions($field);
		
		if(!empty($fk_options)){
		
			$fk_options_size = sizeof($fk_options);
			$i = 0; // counter - used to make unique id attributes
			
			foreach($fk_options as $fk_option){
				// is this foreign key already selected or not?
				$checked = (in_array($fk_option['title'], $current_{$field})) ? ' checked="checked"' : '';
				// checkbox
				$field_element .= '<div class="field ' . assignOrderClass($i, $fk_options_size) . '">' . "\n\t";
				$field_element .= '<input type="checkbox" name="' . $field . '[]" id="' . $field . '_' . $i . '" value="' . $fk_option['id'] . '" class="checkbox"' . $checked . ' />' . "\n";
				// label
				$field_element .= '<label for="' . $field . '_' . $i . '" class="checklabel">' . stripslashes($fk_option['title']) . '</label></div>' . "\n";
				$i++; // increment counter
			}
			
		}
		
		// close off element
		$field_element .= '</fieldset>' . "\n";
		
		return $field_element;
	}
	
	
	/**
	 *	setForeignKeyOptions
	 *	@param string $field - table name
	 *	@return array $options
	 */
	protected function setForeignKeyOptions($field){
		$options = array();
	
		// CACHE
		$objCache = new Cache('dropdown.cache', 24, $field);
		
		if($objCache->getCacheExists() === true){
			$options = $objCache->getCache();
		} else{
			$query = "SELECT * FROM `{$field}` WHERE 1 AND `status` = '1' LIMIT 0, 250;";
			$options = $this->_db->get_results($query, "ARRAY_A");
			$objCache->createCache($options);
		}
		// END CACHE
		
		return $options;
		
		
		//return getFieldsFromTable($field);

	}
	
	
	/**
	 *	setTables()
	 *	Foreign keys are stored as ints in a table and they usually reference another 
	 *	table of the same name
	 *	e.g. 
	 *	status may be a 1 or a 0 in a table but the status table will 
	 *	tell us what 1 and 0 refer to.
	 */
	public function setTables(){
	
		$results = $this->_site->getTables();
		
		foreach($results as $table){
			$this->_tables[] = $table->{'Tables_in_' . $this->_db->dbname};
		} 
		
	}
	
	/**
	 *	getMaxlength
	 */	
	protected function getMaxlength($field_type){
		$maxlength = explode('(', $field_type);
		$maxlength = (!empty($maxlength[1])) ? str_replace(')', '', $maxlength[1]) : $maxlength[0];	
		
		return $maxlength;
	}
	
	/**
	 *	getAllFieldElements()
	 */
	public function getAllFieldElements(){
		return $this->_allFieldElements;
	}
}

?>