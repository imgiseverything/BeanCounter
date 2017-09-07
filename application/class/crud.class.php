<?php
/**
 *	=========================================================================
 *
 *	CRUD Class [CREATE REPLACE UPDATE DELETE]
 *	-------------------------------------------------------------------------
 *
 *	Add/Edit/Delete/View Items from database
 *	Validate/Sanitise data for data input
 *	Provide user feedback (warning/success) based on outcomes
 *
 *	=========================================================================
 *
 *	Copyright:
 *	-------------------------------------------------------------------------
 *
 *	@copyright Phil Thompson
 *
 *	This class was written by Phil Thompson
 *	http://imgiseverything.co.uk/
 *	hello@philthompson.co.uk
 *
 *	If you want to use it. Go for it but it's your responsibility.
 *	Phil Thompson accepts no liability for any mishaps that arise from using
 *	this code.
 *
 *	If your girlfriend leaves you because this class gave you the free-time
 *	to sleep with your neighbour's wife, don't blame me.
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
 *	edited by: 	Phil Thompson
 *	@modified	31/07/2009
 *
 *
 *	=========================================================================
 *
 *	Brief usage instructions
 *	-------------------------------------------------------------------------
 *
 *	This beauty of this class is that it can make your MySQL database
 *	completely self automated.
 *	Add a new field to your table and the query that gets the data will
 *	automatically add it, the method that validates the data will check if
 *	that new field needs to be required and gives warning messages if a
 *	user tries to add a new item without that giving that field a value.
 *
 *	If you create a new many-to-many relationship, there is a method that
 *	will allow those relationships to  be automatically added/edited
 *	(provided those options are present to be selected via the add/edit form)
 *
 *	For this class to work as expected you need to follow a few conventions:
 *
 *		> table names are singular e.g. client as opposed to clients
 *		> many-to-many relationship tables must have the following naming
 *		  format:
 *		  	# main table: page
 *		  	# secondary_table: page_catgeory
 *		  	# relationship table: page_category_matrix
 *
 *	=========================================================================
 *
 *
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *		variables
 *
 *		construct

 *		destruct
 *
 *		methods
 *
 *			Naming Conventions
 *				setNamingConventions
 *
 *			Cache
 *				setCachename()
 *
 *			Data Validation
 *				allDataPresent()
 *				hasDataChanged()
 *				allDataCorrect()
 *				getErrors()
 *				isRecordUnique()
 *				formatData()
 *
 *			Form processing/Database interaction
 *				add()
 *				delete() - set item to inactive
 *				edit()
 *				trash() - permanently delete
 *				purge()
 *				createMany()
 *
 *			Object Population
 *				setAll()
 *				setById()
 *				setTotal()
 *				getMany()
 *
 *				getFKTitles()
 *				queryFilters()
 *				customQueryFilters()
 *				setModel()
 *
 *			Misc gets/sets
 *				getX
 *				setExists()
 *
 *	=========================================================================
 *
 */




	// Enough chat; start the class

	class CRUD{


		// Variables

		/**
		 *	@var (int|string)
		 */
		protected $_id;

		// Basic data filters

		/**
		 *	@var int
		 */
		protected $_currentPage;

		/**
		 *	@var int
		 */
		protected $_perPage;

		/**
		 *	@var string
		 */
		protected $_orderBy;


		/**
		 *	@var string
		 */
		protected $_search;

		/**
		 *	@var string
		 *	past or future
		 */
		protected $_tense;

		/**
		 *	@var int
		 */
		protected $_status;

		/**
		 *	@var int
		 *	number of days
		 */
		protected $_timeframe;

		/**
		 *	@var array
		 */
		protected $_timeframeCustom;


		/**
		 *	@var string
		 *	Allows a child class ot override the date_added field when filtering within a timeframe
		 */
		protected $_dateOrderField;

		/**
		 *	@var array
		 */
		protected $_group;

		/**
		 *	@var array
		 */
		protected $_filter;

		/**
		 *	@var string
		 */
		protected $_cacheFilename;

		// Errors

		/**
		 *	@var array()
		 *	List of fields which are NOT NULL but which have been left
		 *	as empty in an attmepted database INSERT/UPDATE
		 */
		protected $missingFields = array();


		/**
		 *	@var array()
		 */
		protected $missingData = array();
		/**
		 *	@var array()
		 */
		protected $invalidData = array();

		// Object data

		/**
		 *	@var array()
		 */
		protected $_queryFilter;

		/**
		 *	@var array()
		 */
		protected $_properties = array();

		/**
		 *	@var int
		 */
		protected $_propertiesSize = 0;

		/**
		 *	@var int
		 */
		protected $_total;

		/**
		 *	@var boolean
		 */
		protected $_exists;

		// Object naming conventions

		/**
		 *	@var string
		 */
		protected $_name;

		/**
		 *	@var string
		 */
		protected $_namePlural;

		/**
		 *	@var string
		 */
		protected $_folder;

		// Database details

		/**
		 *	@var array()
		 */
		protected $_fields;

		/**
		 *	@var array()
		 */
		protected $_fieldsRequired;

		/**
		 *	@var array()
		 */
		protected $_fieldsDetails;

		/**
		 *	$var array
		 *	Database settings
		 */

		protected $_sql;
		/**
		 *	$var object
		 *	Database connection
		 */
		protected $_db;

		/**
		 *	$var object
		 *	HTML template object
		 */
		protected $_template;

		/**
		 *	$var object
		 *	Database model
		 */
		protected $_model;

		/**
		 *	$var object
		 *	System application object
		 */
		protected $_application;

		/**
		 *	@var object
		 */
		protected $_dateFormat;


		/**
		 *	@var int
		 *	Number of days items stay in the trash before
		 *	they are permanently deleted
		 */
		protected $_daysInTrash = 10;

		/**
		 *	Constructor
		 *	@param object $db
		 *	@param array $filter - data options for SQL
		 *	@param (id|bool) $id
		 */
		public function __construct($db, $filter, $id = false){
			global $objTemplate, $objApplication; // Sorry mum :(

			// Local application object
			$this->_application = $objApplication;

			// Setup local database object
			$this->_db = $db;

			// Setup local template object -
			// this is so we now where we are, what mode we're in etc.
			$this->_template = $objTemplate;

			// Setup local date formatting object for easy pretty dates
			$this->_dateFormat = new DateFormat();

			// Object naming conventions
			// unless a child object have themselves set some defaults
			$this->setNamingConventions();

			// prefix the folder with the admin url if we're in the admin area
			//$this->_folder = ($this->_template->getAdminArea() === true) ? '/admin' . $this->_folder : $this->_folder;
			// not relevant because we should have subdomains e.g. admin.example.com :)


			// Setup DataModel
			$this->setModel();

			// Object Population Filters
			// using these filters for different options
			// we can query the database and bring back
			// only the datasets we care about and not all of them
			// thus minimising database calls :)
			$this->_filter = $filter;
			// what page of results are we on
			// (e.g. usually 1 unless there are lots of results)
			$this->_currentPage = (isset($this->_currentPage)) ?  (int)$this->_currentPage : (int)read($filter, 'current_page', 1);
			//  how many results do we show per page?
			$this->_perPage = (isset($this->_perPage)) ? (int)$this->_perPage : (int)read($filter, 'per_page', 20);
			// how shall we order the results (if more than one exists)?
			$this->_orderBy = (isset($this->_orderBy)) ? $this->_orderBy : read($filter, 'order_by', 'date');
			// are we showing all results are just those of a specific status
			// e.g. active or inactive
			$this->_status = (isset($this->_status)) ? (int)$this->_status : (int)read($filter, 'status', 1);
			// are we showing all results or just ones that have been
			// explicitly published
			$this->_tense = (isset($this->_tense)) ? $this->_tense : read($filter, 'tense', 'past');
			// don't show explicitly 'hidden' data
			$this->_hidden = (is_object($this->_template) && $this->_template->getAdminArea() === true) ?  false : true ;
			// is someone searching the object for specific keywords?
			$this->_search = (isset($this->_search)) ? $this->_search : read($filter, 'search', '');
			// show data from a set timeframe e.g. last 7 days
			$this->_timeframe = (isset($this->_timeframe)) ? $this->_timeframe : read($filter, 'timeframe', '');
			// show data from a user defined timeframe e.g. from one date to another
			$this->_timeframeCustom = (isset($this->_timeframeCustom)) ? $this->_timeframeCustom : read($filter, 'timeframe_custom', '');
			// a group of ids to be selected
			$this->_group = (isset($this->_group)) ? $this->_group : read($filter, 'group', array());

			$this->_dateOrderField = (isset($this->_dateOrderField)) ? $this->_dateOrderField : read($filter, 'date_order_field', 'date_added');

			// what fields/columns do we want to see, what tables do we
			// want to join to?
			$this->queryOptions();

			// used to filter the SQL query based upon the $filter
			// variables above
			$this->queryFilters();

			// Custom query filters - used by child classes to
			// extend MySQL queries
			$this->customQueryFilters();

			// Cache settings
			$this->setCachename($id);

			// Object population methods
			// IF and `id` is present: means we must only want one item
			// ELSE // no `id` means we must want all results

			if($id != ''){
				$this->_id = $id;
				// load a specific item by on the given ID
				$this->setById();
			} else{
				// get all the results
				// (based upon the $filter variables above)
				$this->setAll();
				// get all the total results
				// (based upon the $filter variables above - so we
				// can paginate)
				$this->setTotal();
			}

			// get foreign keys values
			$this->getMany();

			$this->setExists();


			$this->setPageTitle();
			$this->setPageDescription();
			$this->setBreadcrumb();

			$this->_propertiesSize = sizeof($this->_properties);

			//print_x($this->_properties); // DEBUGGING :(

		}

		/**
		 *	Desctructor
		 */
		public function __destruct(){


			$this->purge();
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

			// The name = <h1> on pages in <title>s - usually the database table too
			$this->_name = ($this->_name) ? $this->_name : 'scaffold';

			// Plural-a-fied name - where English is not your friend
			// @todo work out plural naming from word ending
			// e.g. ies or s or
			$this->_namePlural = ($this->_namePlural) ? $this->_namePlural : Inflector::pluralise($this->_name);

			// Folder name - where your object will reside in the site structure
			// this is whatever you'll call your controller and you may decide
			// upon different folder names for different modes e.g. admin area
			$this->_folder = ($this->_folder) ? $this->_folder : '/' . $this->_namePlural . '/';


			// SQL - Database related namings
			$this->_sql['main_table'] =  ($this->_sql['main_table']) ? $this->_sql['main_table'] : $this->_name;


		}



		/**
		 *	setCachename()
		 *	Create a unique filename for the cache
		 *  we'll be caching different data sets based upon different
		 *  $filter values so we need different filenames that represent
		 *	those values
		 *	@param int $id
		 */
		protected function setCachename($id = false){

			if(empty($id) || $id === false){
				// Create a cache filename that contains all the
				// $_filter values so we never get the wrong cached version
				// if a user seraches or looks at page 2 of results

				$filter = $this->_filter;

				// remove some values otherwsie we could end up with
				// unique caches for everyone - which defeats the purpose!
				$filter['basket_user'] = '';

				$this->_cacheFilename = $this->_name . '_' . md5(serialize($this->_filter)) . '.cache';
			} else{
				// If we're looking at a one item (which having an
				// id would presume we are) we don't all the filter junk
				// because we just want one item's data
				$this->_cacheFilename = $this->_name . '_' . $id . '.cache';
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
		 *		allDataPresent()
		 *		hasDataChanged()
		 *		allDataCorrect()
		 *		getErrors()
		 *		isRecordUnique()
		 */

		/**
		 *	allDataPresent()
		 *	loop through all required fields and ensure required fields are present
		 *	(FYI: these are database field names e.g. id, title, etc)
		 *	@return bool	whether the data is all preent or not
		 */
		protected function allDataPresent(){



			// set intitial value to be 1 just in case
			// data is missing completely
			$missing_fields = 1;


			// this object has fields
			if(!empty($this->_fields)){

				// fields array exist so set value to none
				$missing_fields = 0;

				// loop through all fields
				foreach($this->_fields as $field){

					$field_type = $this->_fieldsDetails[$field]['Type'];


					// if the field is present in the required array
					if(in_array($field, $this->_fieldsRequired)){

						// is that required field present as a post variable?
						if(empty($_POST[$field]) && $field != 'status'){

							// is it a datetime field?
							if($field_type == 'datetime'){

								// the required datetime values aren't present
								if(
									empty($_POST[$field . '_year'])
									|| empty($_POST[$field . '_month'])
									|| empty($_POST[$field . '_day'])
									|| empty($_POST[$field . '_hour'])
								){
									$missing_fields++;
									$this->_missingFields[$field] = $field;
								} else{
									// is date valid ?
									$objCalendar = new Calendar($_POST[$field . '_month'], $_POST[$field . '_year']);
									$validDate = $objCalendar->validDate($_POST[$field . '_day']);
									if($validDate !== true){
										$missing_fields++;
										$this->_missingFields[$field] = $field;
									}
								}

							} else if($field_type == 'date'){

									// the required date values aren't present
									if(empty($_POST[$field . '_year']) || empty($_POST[$field . '_month']) || empty($_POST[$field . '_day'])){

										$missing_fields++;
										$this->_missingFields[$field] = $field;
									}

							} else if(
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

							} else{

								// this is edit mode and the password is missing - we don't need it
								if($field  == 'password' && !empty($_POST['action']) && $_POST['action'] == 'edit'){
									/*
									make sure the password field in edit mode doesn't
									throw errors because the password field is in another form :)
									*/
								} else{
									// if not, increment the number of missing fields
									$missing_fields++;
									$this->_missingFields[$field] = $field;
								}

							}

						}
					} else if($field_type == 'datetime' || $field_type == 'date'){

						// Date time passed in a unrequired field isn't legal e.g. 30th February :(
						$objCalendar = new Calendar(read($_POST, $field . '_month', ''), read($_POST, $field . '_year', ''));
						$validDate = $objCalendar->validDate(read($_POST, $field . '_day', ''));
						if($validDate !== true){
							$missing_fields++;
							$this->_missingFields[$field] = $field;
						}

					}

				} // end foreach

			} // end if



			// if there are no missing fields (count = 0) then all
			// data is present (true) otherwise data is absent (flase)
			$all_data_present = ($missing_fields == 0) ? true : false;

			// Now check all the data is correct
			// if it isn't return false
			$all_data_correct = $this->allDataCorrect();
			if($all_data_correct !== true){
				$all_data_present = false;
			}

			return $all_data_present;

		}


		/**
		 *	dataHasChanged
		 *	check to see if any data has changed when an edit
		 *	has been requested if no field shave changed then
		 *	the edit method shouldn't let a query be sent to
		 *	the database AND the query should only update those
		 *	fields were changes have been made
		 *	@return bool $dat_has_changed
		 */
		protected function dataHasChanged(){

			// set intitial value to be 0 - because if no
			// fields exist then data can't have been changed
			// (techncially it can but we don't want to
			// allow *all* NULL values
			$changed_fields = 0;

			if(!empty($this->_fields)){
				// loop through all fields
				foreach($this->_fields as $field){
					// if the field exists and is different to the object value
					// and is present as a post variable?
					if(!empty($_POST[$field]) && $this->_db->escape($_POST[$field]) != $this->_db->escape($this->_properties[$field]) && $field != 'password'){
						// if not, increment the number of changed fields
						$changed_fields++;
					} // end if
					// field is mising from the post but it currently has a value
					else if(empty($_POST[$field]) && $this->_db->escape($this->_properties[$field]) != '' ){
						// if not, increment the number of changed fields
						$changed_fields++;
					} // end elseif
				} // end foreach
			} // end if

			// now check for many-to-many relationship fields

			// this object does have many-to-many Foreign keys
			if(!empty($this->_sql['foreign_keys'])){
				// loop through these foreign keys
				foreach($this->_sql['foreign_keys'] as $fk){

					// turn foreign key data into an array of just id values e.g. array(0,1,2,3);
					if(!empty($this->_properties[$fk])){

						$fk_array = array(); // set up array variable to avoid PHP error

						// loop through all value and create a new array
						foreach($this->_properties[$fk] as $fk_item){
							$fk_array[] = $fk_item['id'];
						} // end foreach

						// if the foreign key field exists and is different
						// to the object value in the newly created array
						// and is also present as a post variable?
						if(!empty($_POST[$fk]) && $_POST[$fk] != $fk_array){
							// if not, increment the number of changed fields
							$changed_fields++;
						} // end if

					} // end if

				} // end foreach

			} // end if

			// if there are no missing field then all data is present
			$data_has_changed = ($changed_fields > 0) ? true : false;

			return $data_has_changed;

		}

		/**
		 *	allDataCorrect()
		 *	loop through all fields
		 *	(FYI: are database field names e.g. id, title, etc)
		 *	and ensure dat is the right for,at e.g. ints are
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
					if(
						!empty($_POST[$field])
						&& $field != 'status'
						&& $fieldType != 'datetime'
						||
						(
							isset($_POST[$field])
							&& (
								$fieldType == 'decimal'
								|| $fieldType == 'float'
							)
						)

					){


						switch($fieldType){

							case 'int':
								// This field should be numeric but isn't :(
								$cleanField = preg_replace("/[^0-9]/", "", trim($_POST[$field]));

								if($_POST[$field] != $cleanField){
									$incorrect_fields++;
									$this->_invalidData[$field] = '<a href="#"' . $field . '">' . $fieldLabel . '</a> must be a number';
								}
								break;

							case 'float':
							case 'decimal':
								// This field should be numeric but isn't :(
								$cleanField = preg_replace("/[^0-9.,]/", "", trim($_POST[$field]));

								if($cleanField != $_POST[$field]){
									$incorrect_fields++;
									$this->_invalidData[$field] = '<a href="#"' . $field . '">' . $fieldLabel . '</a> must be a number';
								}
								break;

							case 'varchar':
								// Check for string length
								// (supplied in Type array_key like so varchar(100)
								$maxLength = preg_replace("/[^0-9]/", "", trim($this->_fieldsDetails[$field]['Type']));

								if(strlen($_POST[$field]) > $maxLength){
									$incorrect_fields++;
									$this->_invalidData[$field] = '<a href="#"' . $field . '">' . $fieldLabel . '</a> is too long. It can only be ' . $maxLength . ' character(s) long.';
								}

								break;

							default:
								break;

						} // end switch

					} else if($fieldType == 'datetime' || $fieldType == 'date'){

						// Date time passed in a unrequired field
						// isn't legal e.g. 30th February :(
						$objCalendar = new Calendar(read($_POST, $field . '_month', ''), read($_POST,$field . '_year', ''));					$validDate = $objCalendar->validDate(read($_POST, $field . '_day', ''));

						if($validDate !== true){
							$incorrect_fields++;
							$this->_invalidData[$field] = '<a href="#"' . $field . '">' . $fieldLabel . '</a> must be a valid date.';
						}
					}
				} // end foreach
			} // end if

			// if there are no incorrect fields (count = 0) then all
			// data is correct (true) otherwise data is incorrect (false)
			$all_data_correct = ($incorrect_fields == 0) ? true : false;

			return $all_data_correct;

		}

		/**
		 *	getErrors
		 *	If required $_POST values are missing then
		 *	create 'user-friendly' error messages e.g.
		 *	`Field name` was missing
		 *	@return array $errors
		 */
		public function getErrors(){

			// setup (error as array now to avoid PHP errors)
			// it will store all text error messages
			$errors = array();




			// loop through all fields
			/*foreach($this->_fields as $field){
				// check to see if the field is required
				// then check it's not a datetime field and check whether
				// a $_POST value exist for it
				if(in_array($field, $this->_fieldsRequired)){

					$field_label = ucfirst(str_replace('_', ' ', $field));

					$field_type = $this->_fieldsDetails[$field]['Type'];

					if(
						$field  == 'password'
						&& !empty($_POST['action'])
						&& $_POST['action'] == 'edit'
					){
						// make sure the password field in edit mode doesn't throw errors
						// because the password field is in another form :)
					} else if(
						$field_type != 'datetime'
						&& $field_type != 'date'
						&& $field_type != 'float'
						&& substr($field_type, 0, 7) != 'decimal'
						&& empty($_POST[$field])
					){
						// this field is required but the field doesn't exist
						// create (hopefully) user friendly error message
						$errors[] = '<a href="#' . $field . '">' . $field_label . '</a> is missing';
					} // end if

				}

			} // end foreach
			*/

			// go through all misisng fields and create errors with description and links
			if(!empty($this->_missingFields)){
				foreach($this->_missingFields as $field){

					$field_label = ucfirst(str_replace('_', ' ', $field));
					$errors[$field] = '<label class="incognito" for="' . $field . '">' . $field_label . ' is missing</label>';
				}
			}

			// Add in the invalid data messages to the array
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



		/**
		 *	formatData()
		 *	Ensure 'odd' data is correctly formatted for database insertion
		 * 	datetimes must be correctly YYYY-MM-DD HH:MM:SS
		 *	passwords must be md5()'d
		 *	@param string $field
		 *	@param string $field_value ($_POST'd value)
		 *	@return string
		 */
		protected function formatData($field, $field_value){

			if($this->_fieldsDetails[$field]['Type'] == 'datetime'){

				// field is a date and time so generate the right date/time stamp.
				$value = (!empty($_POST[$field . '_year']) && !empty($_POST[$field . '_month']) && !empty($_POST[$field . '_day']) && !empty($_POST[$field . '_hour']) && !empty($_POST[$field . '_minute'])) ? $_POST[$field . '_year'] . '-' . $_POST[$field . '_month'] . '-' . $_POST[$field . '_day'] . ' ' . $_POST[$field .'_hour'] . ':' . $_POST[$field . '_minute'] . ':00': 'Now()';
			} else if($this->_fieldsDetails[$field]['Type'] == 'date'){
				// field is a date and time so generate the right date.
				$value = (!empty($_POST[$field . '_year']) && !empty($_POST[$field . '_month']) && !empty($_POST[$field . '_day'])) ? $_POST[$field . '_year'] . '-' . $_POST[$field . '_month'] . '-' . $_POST[$field . '_day'] : date('Y-m-d');
			} else{
				// all other data types
				// check to see if the field is a password field
				// and md5 encrypt it if it is
				if($field  == 'password'){
					$value = Authorise::generateHash($field_value);
				} else{
					// just a normal field
					$value = $field_value;
				}
			}

			niceError($field . ' : ' . read($this->_properties, $field, '') . ' : ' . $value);

			return $value;
		}

		/**
		 *
		 *	Database Interaction
		 *
		 *	Based upon user input add/update or delete items from the database
		 *	Only allow database interaction if data passes validation see Data Validation methods above.
		 *
		 *	A note about item deletion:
		 *	We don't delete items - just deactivate them (set status to 0 or 'inactive') because
		 *	deleting items could break data integrity
		 *
		 *	Methods:
		 *		processData()
		 *		add()
		 *		delete()
		 *		edit()
		 *		createMany()
		 *
		 */


		/**
		 *	processData()
		 *	If a form has been POSTED (NOTE: not $_GET nor $_REQUEST) then check to see if
		 *	the action matches a method found in this class.
		 *	e.g. $_POST['action'] == 'add' should call the add() method
		 *	and $_POST['action'] == 'donkey' should call nothing
		 *	@return array $user_feedback
		 */
		public function processData(){

			// User feedback variables - all our methods should return these

			// run the requested method and return values into the user
			// feedback variable which will tell people what has happened
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				if(!empty($_POST['action'])){
					if(method_exists($this, $_POST['action'])){
						$_SESSION['feedback'] = $this->{$_POST['action']}();
					}
				}
			}



			// Send the user on/Provide feedback

			if(!empty($_SESSION['feedback']['type']) && $_SESSION['feedback']['type'] == 'success' && !empty($_POST['action'])){
				if($_POST['action'] == 'delete' && empty($_POST['redirect'])){
					// item deleted so direct to listings page
					redirect($this->_folder);
				} else{
					// item added/updated/etc so send user to details page - if we're

					if(!empty($_POST['popup']) && $_POST['popup'] == 'true'){
						return $_SESSION['feedback'];
					} else{

						if(!empty($_POST['redirect'])){
							redirect($_POST['redirect']);
						} else{
							redirect($this->_folder . $this->_id);
						}

					}



				}
			} else{
				// Something went wrong (or nothing happened) so send the user back to the same page (the form)
				//return $user_feedback;

			}
		}

		/**
		 *	add
		 *	add a *new* item to the database (using MySQL INSERT)
		 *	@return array $user_feedback
		 */
		protected function add(){

			// Error counter. Increment everytime there is an error.
			$error = 0;


			// clean data: make nice for data input then turn field name
			// into an easy to use variable name: e.g. $title
			extract(cleanFields($this->_fields));

			// Is all data present?

			// check for data if it exists
			// Build up query by
			// Looping through fields array to get column headings then
			// turn field headers into a comma separated list
			// add date added field for good measure so we have
			// INSERT INTO `{$this->_sql['main_table']}`
			//		(heading, heading2, date_added)
			// then loop through fields array to get insert values
			// IMPORTANT: make sure the data is correctly formatted
			// add data to insert array then
			// turn field values into a comma separated list and add
			// 'date_added' value for good measure

			if($this->allDataPresent() === true){

				$query = "INSERT INTO `{$this->_sql['main_table']}` (";

				foreach($this->_fields as $field){
					$query_headers[] = "$field";
				}

				$query .= join(', ', $query_headers);
				$query .= ",date_added) VALUES (";

				foreach($this->_fields as $field){
					$value = $this->formatData($field, $$field);
					$query_inserts[] = "'{$value}'";
				}

				$query .= join(', ', $query_inserts);
				$query .= ",Now());";

				niceError($query); // Debugging - echo SQL

				// run query and provide feedback
				if($results = $this->_db->query($query)){ /// success
					$user_feedback['content'] = 'You have successfully added a new ' . $this->_name;
					$user_feedback['id'] = mysql_insert_id();
					$this->_id = $user_feedback['id'];

					// Delete cached files as they are now out of date
					$this->deleteCache();

					// reload the object variables with new data
					$this->setById();

					// add many-to-many relationships
					$this->createMany();

				} else{
					// Query failed - database down or bad SQL statement
					$error++;
					$user_feedback['content'] = 'Due to a technical error, you have failed to add a new ' . $this->_name;
				}
			} else{
				 // vital data missing
				$error++;
				$user_feedback['content'][] = 'This ' . $this->_name . ' has not been added due to the following problems:';

				// missing form fields error messages
				$error_messages = $this->getErrors();
				$user_feedback['content'] = array_merge($user_feedback['content'], $error_messages);

			}

			// provide user with feedback
			$user_feedback['type'] = ($error > 0) ? 'error' : 'success';

			return $user_feedback;

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

			// check for ID - no ID; no database interaction
			if($this->_id){

				// query
				$query = "UPDATE `{$this->_sql['main_table']}` SET status = 0 WHERE id = '{$this->_id}' LIMIT 1;";

				//niceError($query); // Debugging - echo SQL

				// run query
				if($results = $this->_db->query($query)){ // Success
					$user_feedback['type'] = 'success';
					$user_feedback['content'] = 'You have successfully deleted that ' . $this->_name;

					// Delete cached files as they are now out of date
					$this->deleteCache();

				} else{
					// Query failed - database down or bad SQL statement
					$error++;
					$user_feedback['type'] = 'error';
					$user_feedback['content'] = 'This ' . $this->_name . ' hasn\'t been deleted. Please try again later.';
				}
			} else{
				// No id: cancel the delete process
				$error++;
				$user_feedback['content'] = 'Due to a technical error, you have failed to delete this ' . $this->_name;
			}

			// provide user with feedback
			$user_feedback['type'] = ($error > 0) ? 'error' : 'success';

			return $user_feedback;
		}

		/**
		 *	duplicate()
		 */
		protected function duplicate(){
			return $this->add();
		}

		/**
		 *	edit()
		 *	edit an item *already in the system*
		 *	@return $user_feedback - ARRAY
		 */
		protected function edit(){
			// Error counter.
			// Increment everytime there is an error.
			$error = 0;
			// clean data: make nice for data input and then turn field name
			// into easy to use variable names: e.g. $email
			extract(cleanFields($this->_fields));


			// check for ID - no ID = no database interaction

			if($this->_id){

				// check for data
				if(
					$this->allDataPresent() === true
					&& $this->dataHasChanged() === true
				){
					// start query
					$query = "UPDATE `{$this->_sql['main_table']}` SET ";
					$query_updates = array();// initialise updates array to avoid PHP error

					// build up query by looping through all fields
					foreach($this->_fields as $field){

						// update field (if it has changed)
						if($$field != $this->_properties[$field] && $field != 'password'){
							// make sure data is correctly formatted
							$value = $this->formatData($field, $$field);
							$query_updates[] = "`$field` = '" . $value . "'";
						}

					}
					//add a 'date_edited' timestamp
					$query_updates[] = "date_edited = Now() ";
					// and query fields updates to the query (if any exist)
					if(!empty($query_updates)){
						$query .= join(', ', $query_updates);
					}
					// now close the query;
					$query .= " WHERE `id` = '{$this->_id}' LIMIT 1;";

					niceError($query); // Debugging - echo SQL


					// run query
					if($results = $this->_db->query($query)){ /// success
						$user_feedback['content'] = 'You have successfully updated this ' . $this->_name;
						$user_feedback['id'] = $this->_id;

						// Delete cached files as they are now out of date
						$this->deleteCache();

						$this->setById();
						// add many-to-many relationships
						$this->createMany();
					} else{

						// Query failed - database down or bad SQL statement

						$error++;
						$user_feedback['content'] = 'Due to a technical error, you have failed to update this ' . $this->_name;
					}
				} else{
					 // vital data missing
					$error++;
					$user_feedback['content'][] = 'This ' . $this->_name . ' hasn\'t been updated due to the following problems:';

					// missing form fields error messages
					$error_messages = $this->getErrors();
					$user_feedback['content'] = array_merge($user_feedback['content'], $error_messages);

					// no data has changed
					if($this->dataHasChanged() === false){
						$user_feedback['content'][] = 'You have not changed any information';
					}
				}

			} else{
				// No id: cancel edit process
				$error++;
				$user_feedback['content'] = 'Due to a technical error, you have failed to update this ' . $this->_name;
			}

			// provide user with feedback
			$user_feedback['type'] = ($error > 0) ? 'error' : 'success';

			return $user_feedback;

		}

		/**
		 *	trash()
		 *	remove an item *permanently* from the system
		 *	@return array $user_feedback
		 */
		protected function trash(){
			// Error counter. Increment everytime there is an error.
			$error = 0;

			// check for ID - no ID, no database interaction
			if($this->_id){

				// query
				$query = "DELETE FROM {$this->_sql['main_table']}
				WHERE id = '{$this->_id}'
				LIMIT 1;";

				niceError($query); // Debugging - echo SQL

				// run query
				if($results = $this->_db->query($query)){ // Success

					// Delete cached files as they are now out of date
					$this->deleteCache();

					$user_feedback['type'] = 'success';
					$user_feedback['content'] = 'You have successfully permanently deleted this ' . $this->_name;
				} else{
					// Query failed - database down or bad SQL statement
					$error++;
					$user_feedback['type'] = 'error';
					$user_feedback['content'] = 'This ' . $this->_name . ' hasn\'t been permanently deleted. Please try again later.';
				}
			} else{
				// No id: cancel
				$error++;
				$user_feedback['content'] = 'Due to a technical error, you have failed to permanently delete this ' . $this->_name;
			}

			// provide user with feedback
			$user_feedback['type'] = ($error > 0) ? 'error' : 'success';

			return $user_feedback;
		}


		/**
		 *	purge()
		 *	remove all deleted items *permanently* from the system
		 *	that haven't been edited for 10 days
		 *	Limit deletion to 10 rows at a time just in case we randomly]
		 *	have 10000 rows to remove
		 */
		protected function purge(){


			$time_limit = DateFormat::removeDays(date('Y-m-d H:i:s'), $this->_daysInTrash);

			$query = "DELETE FROM `{$this->_sql['main_table']}`
			WHERE status = '0'
			AND (
				(
					`date_edited` < '{$time_limit}'
					AND `date_edited` IS NOT NULL
				)
				OR
				(
					`date_added` < '{$time_limit}'
					AND `date_edited` IS NULL
				)
			)
			LIMIT 10;";

			$this->_db->query($query);

			niceError($query);

		}



		/**
		 *	createMany()
		 *	Add many-to-many relationships to table (should end in _matrix)
		 *	do this for all tables in the $this->_sql['foreign_keys'] array.
		 *	If a form doesn't allow user to add these relationships,
		 *	then *all current relationships will be deleted*
		 *
		 *	This method works by deleting current relationships then adding
		 *	the new ones rather than checking existing relationships
		 *	and deleting and adding accordingly.
		 *
		 *	Version 1.0 idea:> We should really check if any data has
		 *	changed first!
		 */
		protected function createMany(){

			// ID is present to just do the one row
			if($this->_id && !empty($this->_sql['foreign_keys'])){
				// loop through all foreign keys
				foreach($this->_sql['foreign_keys'] as $foreign_key){

					$foreign_key_data = read($_POST, $foreign_key, '');

					$foreign_key_id = (substr($foreign_key, -1) != 's') ? $foreign_key . '_id' : substr($foreign_key, 0, -1) . '_id';

					// Query
					$query = "SELECT fk.id, fk.title, fk.description, fk.date_added, fk.date_edited
					FROM `{$foreign_key}_matrix x
					LEFT JOIN `{$foreign_key}` fk ON fk.id = x.{$foreign_key_id}
					LEFT JOIN `{$this->_sql['main_table']}` t ON t.id = x.{$this->_name}_id
					WHERE t.id = '{$this->_id}';";

					niceError($query); //- Debugging - echo SQL


					// remove existing relationships
					$query = "DELETE FROM `{$foreign_key}_matrix`
					WHERE {$this->_name}_id = '$this->_id';";

					// Run query
					$this->_db->query($query, true);

					// inititialise insert array to avoid PHP errors
					$query_inserts = array();

					if(!empty($foreign_key_data)){

						// start query
						$query = "INSERT INTO `{$foreign_key}_matrix` ({$foreign_key_id}, {$this->_name}_id) VALUES";
						// loop through categories
						foreach($foreign_key_data as $fk => $fk_id){
							// build up query
							$query_inserts[] = "\n('{$this->_db->escape($fk_id)}','{$this->_id}')";
						}
						// conclude query
						$query .= join(',', $query_inserts) . ";";

						// directly above is where we lose our great new mysql_insert_id()

						niceError($query); //- Debugging - echo SQL

						// Run query
						$this->_db->query($query);
					}
					/*
						Note: this query runs potentially a lot so if we were to
						check it worked each time we might be waiting a while.
						This should probably be a transaction.
					*/
				}

				// Delete cached files as they are now out of date
				$this->deleteCache();

			}
		}


		/**
		 *
		 *	Object population
		 *
		 *	A user may request to see all the rows* of a table or simply one.
		 *	*We never show all because there may be tens of thousands of rows and
		 *  that could put strain on the server.
		 *
		 *
		 *	getMany - goes through all the foreign keys (where a many-to-many relationship
		 *	is stored in a 3rd table)
		 *	and creates an array inside $this->_properties named after this foreign key
		 *
		 *	queryFilters - determines how many results from the table we see and in what order.
		 *
		 *	Methods:
		 *		setAll()
		 *		setById()
		 *		setTotal()
		 *		getMany
		 *		queryOptions
		 *		queryFilters()
		 *		setFields
		 *
		 */

		/**
		 *	setAll()
		 *	Load the $_properties with all the details
		 *	of all items in the database table
		 */
		protected function setAll(){

			$cache_used = 'No';
			$objTimer = new Timer();

			$query = "SELECT {$this->_sql['select']}
			FROM `{$this->_sql['main_table']}` t
			{$this->_sql['joins']}
			WHERE 1
			{$this->_queryFilter['search']}
			{$this->_queryFilter['status']}
			{$this->_queryFilter['hidden']}
			{$this->_queryFilter['tense']}
			{$this->_queryFilter['timeframe']}
			{$this->_queryFilter['group']}
			{$this->_queryFilter['custom']}
			ORDER BY {$this->_queryFilter['order_by']}
			LIMIT {$this->_queryFilter['limit']};";



			// Cache data
			$objCache = new Cache(str_replace($this->_name . '_', $this->_name . '_all_', $this->_cacheFilename), 1, $this->_name);
			if($objCache->getCacheExists() === true){
				$this->_properties = $objCache->getCache();
				$cache_used = 'Yes';
			} else{

				$this->_properties = $this->_db->get_results($query, "ARRAY_A");
				$objCache->createCache($this->_properties);
			}
			// End cache

			// Debugging - echo SQL
			$query_speed = $objTimer->getSpeed(microtime());
			niceError($query . '<br />Cache used: ' . (string)$cache_used . '<br />Speed: ' . $query_speed);



			// Run query
			if($this->_properties){ // worked
				return true;
			} else{ // failed
				$this->_exists = false;
				return false;
			}

		}

		/**
		 *	setById()
		 *	Load the $_properties with a specific item's
		 *	info from a supplied id ($this->_id)
		 */
		protected function setById(){

			// If id exists, go ahead with query
			if($this->_id){

				$cache_used = 'No';
				$objTimer = new Timer();

				$query = "SELECT {$this->_sql['select']}
				FROM `{$this->_sql['main_table']}` t
				{$this->_sql['joins']}
				WHERE t.id = '{$this->_id}'
				{$this->_queryFilter['tense']}
				{$this->_queryFilter['timeframe']}
				{$this->_queryFilter['custom']}";

				// Cache data
				$objCache = new Cache($this->_cacheFilename, 1, $this->_name);
				if($objCache->getCacheExists() === true){
					$this->_properties = $objCache->getCache();
					$cache_used = 'Yes';
				} else{
					$this->_properties = $this->_db->get_row($query, "ARRAY_A");
					$objCache->createCache($this->_properties);
				}
				// End cache


				// Debugging - echo SQL
				$query_speed = $objTimer->getSpeed(microtime());
				niceError($query . '<br />Cache used: ' . $cache_used . '<br />Speed: ' . $query_speed);

				// Run query
				if($this->_properties){ // worked
					return true;
				} else{

					// Query failed - database down or poorly formed query
					$this->_exists = false;
					return false;
				}
			} else{
				// No id: no go.
				return false;
			}

		}

		/**
		 *	setTotal()
		 *	get the total number of items in the system:
		 *	used for pagination and displaying
		 */
		protected function setTotal(){

			$cache_used = 'No';
			$objTimer = new Timer();

			// Query
			$query = "SELECT COUNT(t.id)
			FROM `{$this->_sql['main_table']}` t
			{$this->_sql['joins']}
			WHERE 1
			{$this->_queryFilter['search']}
			{$this->_queryFilter['status']}
			{$this->_queryFilter['hidden']}
			{$this->_queryFilter['tense']}
			{$this->_queryFilter['timeframe']}
			{$this->_queryFilter['group']}
			{$this->_queryFilter['custom']}";


			// Cache data
			$objCache = new Cache(str_replace($this->_name . '_', $this->_name . '_total_', $this->_cacheFilename), 1, $this->_name);
			if($objCache->getCacheExists() === true){
				$this->_total = $objCache->getCache();
				$cache_used = 'Yes';
			} else{
				$this->_total = $this->_db->get_var($query);
				$objCache->createCache($this->_total);
			}
			// End cache

			// Debugging - echo SQL
			$query_speed = $objTimer->getSpeed(microtime());
			niceError($query . '<br />Cache used: ' . $cache_used . '<br />Speed: ' . $query_speed);

		}

		/**
		 *	getMany()
		 *	get an object's normalised related items
		 *	e.g. a table might have a foreign key stored as an id
		 *	that relates to another table where more details are
		 *	stored. Our object may have many of these items
		 */
		protected function getMany(){

			// only bother if foreign keys even exist
			if(!empty($this->_sql['foreign_keys'])){
				// ID is present to just do the one row
				if($this->_id){
					// loop through all foreign keys
					foreach($this->_sql['foreign_keys'] as $foreign_key){

						$cache_used = 'No';
						$objTimer = New Timer();

						$foreign_key_id = (substr($foreign_key, -1) != 's') ? $foreign_key . '_id' : substr($foreign_key, 0, -1) . '_id';

						$fk_fields = $this->_model->getForeignKeysFields($foreign_key);

						// Query
						$query = "SELECT {$fk_fields}
						FROM `{$foreign_key}_matrix` x
						LEFT JOIN `{$foreign_key}` fk ON fk.id = x.{$foreign_key_id}
						LEFT JOIN `{$this->_sql['main_table']}` t ON t.id = x.{$this->_name}_id
						WHERE t.id = '{$this->_id}';";

						//echo $query;


						// CACHE
						$objCache = new Cache($this->_name . '_' . $foreign_key . '_' . $this->_id . '.cache', 24, $this->_name);

						if($objCache->getCacheExists() === true){
							$this->_properties[$foreign_key] = $objCache->getCache();
							$cache_used = 'Yes';
						} else{
							$this->_properties[$foreign_key] = $this->_db->get_results($query, "ARRAY_A");
							$objCache->createCache($this->_properties[$foreign_key]);
						}
						// END CACHE

						// Debugging - echo SQL
						$query_speed = $objTimer->getSpeed(microtime());
						niceError($query . '<br />Cache used: ' . $cache_used . '<br />Speed: ' . $query_speed);


					} // end foreach
				} else{
					// no id is present so add details for all rows in table

					foreach($this->_sql['foreign_keys'] as $foreign_key){
						$foreign_key_id = (substr($foreign_key,-1) != 's') ? $foreign_key . '_id' : substr($foreign_key,0, -1).'_id';

						$i = 0; // counter
						if(!empty($this->_properties)){
							foreach($this->_properties as $property){

								$cache_used = 'No';
								$objTimer = New Timer();

								$fk_fields = $this->_model->getForeignKeysFields($foreign_key);

								// Query
								$query = "SELECT {$fk_fields}
								FROM `{$foreign_key}_matrix` x
								LEFT JOIN `{$foreign_key}` fk ON fk.id = x.{$foreign_key_id}
								LEFT JOIN `{$this->_sql['main_table']}` t ON t.id = x.{$this->_name}_id
								WHERE t.id = '{$property['id']}';";

								//echo $query;

								// CACHE
								$objCache = new Cache($this->_name . '_' . $foreign_key . '_' . $this->_properties[$i]['id'] . '.cache', 24, $this->_name);

								if($objCache->getCacheExists() === true){
									$this->_properties[$i][$foreign_key] = $objCache->getCache();
									$cache_used = 'Yes';
								} else{
									$this->_properties[$i][$foreign_key] = $this->_db->get_results($query, "ARRAY_A");
									$objCache->createCache($this->_properties[$i][$foreign_key]);
								}
								// END CACHE

								// Debugging - echo SQL
								$query_speed = $objTimer->getSpeed(microtime());
								niceError($query . '<br />Cache used: ' . $cache_used . '<br />Speed: ' . $query_speed);


								$i++; // increment counter

							} // end foreach
						} // end if
					}// end foreach
				} // end else
			} // end if

		}


		/**
		 *	getFKTitles()
		 *	If another table exists in the database
		 *	with the same name as a column in this table
		 *	then we grab its details
		 */
		public function getFKTitles(){}

		/**
		 *	queryOptions()
		 *
		 *	Workout which fields to grab from the database and how to join the tables.
		 *	If we just do SELECT * FROM table t1 LEFT JOIN table2 t2 ON t1.fk = t2.id
		 *	Then we run into trouble if the joined table has any identically named
		 *	fields (which it undoubtedly will do)
		 *
		 *	So we want to join all one-to-many relationship tables (if they exist), and get
		 *	all field names from main table
		 *
		 *	Futhermore, it should be all be automatic so if a new one-to-many table is added
		 *	to the database, we won't have to change this code :)
		 */
		protected function queryOptions(){

			// grab all the fields and format them for the SQL query
			$all_fields = (!empty($this->_fields)) ? "t." . join(', t.', $this->_fields) . "," : "";

			// Select Fields e.g. * or t.title, t.id etc
			$this->_sql['select'] = "t.id, {$all_fields} t.date_added, t.date_edited";

			// Joins
			$this->_sql['joins'] = "";

			// If (1 to many) foreign keys exist add them to the select and joins parts of the query
			if(!empty($this->_sql['fk_one_to_many'])){
				$i = 1; // counter

				// loop through the 1-to-many tables (foreign keys) and
				// if the foreign key table name matches a field name in
				// the main table use it
				foreach($this->_sql['fk_one_to_many'] as $fk){
					if(in_array($fk, $this->_fields)){

						$cache_used = 'No';
						$objTimer = New Timer();

						// query database to get the foreign key's fields
						$fk_query = "SHOW FIELDS FROM `$fk`;";

						// Cache data
						$objCache = new Cache($fk . '_fields.cache', 24 , 'model');
						if($objCache->getCacheExists() === true){
							$fields = $objCache->getCache();
							$cache_used = 'Yes';
						} else{
							$fields = $this->_db->get_results($fk_query, "ARRAY_A");
							$objCache->createCache($fields);
						}
						// End cache

						$query_speed = $objTimer->getSpeed(microtime());

						niceError($fk_query . '<br />Cache used: ' . $cache_used . '<br />Speed: ' . $query_speed); // Debugging - echo SQL



						// does this FK table have fields? it bloody well
						// should do but let's just test to avoid error messages!
						if($fields){
							foreach($fields as $field){
								// add to the select statement (grab foreign keys'
								// field and prefix to avoid naming clashes)
								$this->_sql['select'] .= ", t$i.{$field['Field']} as {$fk}_{$field['Field']}";
							}

							// add to the join statement (link the 2 tables)
							$this->_sql['joins'] .= " LEFT JOIN {$fk} t{$i} ON t{$i}.id = t.{$fk} ";
						}
					} // end if
					$i++; // increment counter
				} // end foreach


			} // end if


		}

		/**
		 *	queryFilters()
		 *	Data queries *need* limits, ordering etc.
		 *	Multiple queries require identical data so all these variables are created in this function
		 *	and called in a query like so:
		 *	SELECT * FROM table WHERE 1 {$this->_queryFilter['search']}
		 *
		 *	We use 'WHERE 1' in all queries so all the different filters can start
		 *	with " AND" this way we'll never get query errors or have
		 *	to work out whether to show the AND if we're showing multiple filters.
		 *
		 *	Variables may have been set already be the child class so we only set
		 *	them if they don't already exist
		 */
		protected function queryFilters(){

			// Query ordering - chose a field to sort by and whether
			// to do it descending (high to low) or ascending (low to high)
			if(empty($this->_queryFilter['order_by'])){

				switch($this->_orderBy){

					// order by newest
					default:
						$this->_queryFilter['order_by'] = 't.id DESC';
						break;

					case 'newest';
					case 'latest';
					case 'date';
						//if(in_array('date_added', $this->_fields)){
							$this->_queryFilter['order_by'] = 't.' . $this->_dateOrderField . ' DESC';
						//}
						break;

					// order by oldest
					case 'oldest';
					case 'last';
						//if(in_array('date_added', $this->_fields)){
							$this->_queryFilter['order_by'] = 't.' . $this->_dateOrderField . ' ASC';
						//}
						break;

					// recently edited
					case 'last_edited';
						if(in_array('date_edited', $this->_fields)){
							$this->_queryFilter['order_by'] = 't.date_edited DESC';
						}
						break;

					// title A-Z
					case 'title_az':
						if(in_array('title', $this->_fields)){
							$this->_queryFilter['order_by'] = 't.title ASC';
						}
						break;

					// title Z-A
					case 'title_za':
						if(in_array('title', $this->_fields)){
							$this->_queryFilter['order_by'] =  't.title DESC';
						}
						break;

					// transaction_date
					case 'transaction_date':
						$this->_queryFilter['order_by'] = (in_array('transaction_date', $this->_fields)) ? 't.transaction_date DESC' : 't.id DESC';
						break;
				}

				// if it is still empty, apply the default
				if(empty($this->_queryFilter['order_by'])){
					$this->_queryFilter['order_by'] = 't.id DESC';
				}

			}


			// Search filter - has the user searched for a particular item?
			if(empty($this->_queryFilter['search'])){

				$this->_queryFilter['search'] = '';

				if(!empty($this->_search)){
					if(in_array('title', $this->_fields)){
						$this->_queryFilter['search'] = " AND (t.title LIKE '%{$this->_db->escape($this->_search)}%')";
					}
				}

			}

			// Status filter - live, suspended, all, pending, etc
			if(empty($this->_queryFilter['status'])){
				$this->_queryFilter['status'] = ($this->_status == 0 || $this->_status > 0) ? " AND t.status = '{$this->_status}' " : '';
			}

			// show items that have been published - if a date_published field exists
			if(empty($this->_queryFilter['tense'])){
				$this->_queryFilter['tense'] = (in_array('date_published', $this->_fields) && $this->_tense != 'future') ? " AND t.date_published < Now() AND t.date_published != '0000-00-00 00:00:00'" : '';
			}

			// Hidden filter - only show items which haven't been 'hidden'
			$this->_queryFilter['hidden'] = (in_array('hidden',$this->_fields) && $this->_hidden === true) ? "AND t.hidden = '0'" : "";



			// Custom timeframe filter
			if(empty($this->_queryFilter['timeframe'])){

				if(!$this->_timeframe && $this->_timeframeCustom && strlen($this->_timeframeCustom['start']) == 19 && strlen($this->_timeframeCustom['end']) == 19){

					$this->_queryFilter['timeframe'] = " AND t.{$this->_dateOrderField} BETWEEN '{$this->_timeframeCustom['start']}' AND '{$this->_timeframeCustom['end']}' ";


				} else if($this->_timeframe){

					// Timeframe filter
					$today = date('Y-m-d');
					$today = DateFormat::addDays($today, 1); // add a day to today so we can see items updated today
					$old_date = DateFormat::removeDays(date('Y-m-d 00:00:00'),$this->_timeframe); // create the from date
					$this->_queryFilter['timeframe'] = " AND t.{$this->_dateOrderField} BETWEEN '$old_date' AND '$today' ";

				} else{
					$this->_queryFilter['timeframe'] = '';
				}

			}



			// Group filter
			// get all
			if(!empty($this->_group)){
				$id_list = join(", ", $this->_group);
				$this->_queryFilter['group'] = " AND t.id IN({$this->_db->escape($id_list)}) ";
			} else{
				$this->_queryFilter['group'] = "";
			}

			/**
			 *	Allow child objects to have a say over the query filters
			 *	this variable can be set from the child object and can be quite big e.g.
			 *	" AND x = '$this->y' AND z = '1' "
			 *
			 *	if the child hasn't set it, wipe it clean - so we don't get no errors fool!
			 */

			// custom filter - child object can use this to affect the query
			if(empty($this->_queryFilter['custom'])){
				$this->_queryFilter['custom'] = '';
			}

			// Query limitations (Don't overload the Database)
			if(empty($this->_queryFilter['limit'])){
				$this->_queryFilter['limit'] = getQueryLimits($this->_perPage, $this->_currentPage);
			}


		}

		/**
		 *	customQueryFilters()
		 *	This method will appear in child classes
		 *	it's just here to remind you
		 *	that you can use it - what do you mean
		 *	"that's what interfaces are for"?
		 */
		protected function customQueryFilters(){}

		/**
		 *	setModel()
		 *	initialise a local data model object and grab
		 *	all the table/field data for this table
		 */
		public function setModel(){

			$this->_model = new Model($this->_db, $this->_sql['main_table']);
			$this->_fields = $this->_model->getFields();
			$this->_fieldsDetails = $this->_model->getFieldsDetails();
			$this->_fieldsRequired = $this->_model->getFieldsRequired();
			$this->_sql['tables'] = $this->_model->getTables();
			$this->_sql['foreign_keys'] = $this->_model->getForeignKeys();
			// $this->_sql['fk_one_to_many' is misnamed :( needs fixing
			$this->_sql['fk_one_to_many'] = $this->_model->getForeignKeysManyToMany();

		}

		/**
		 *	setExists()
		 *	does the ID given = a recognised item in the database?
		 *	if it doesn't we need to halt preceding 404/403/401 error
		 *	(but do that in the View/Constructor) for an error message
		 */
		public function setExists(){
			// Id exists - but no proeprties do must mean
			// we're on a non-existent item
			$this->_exists = (($this->_id && empty($this->_properties['title'])))? false : true;

			// We're on page 2 or 3 with no results
			if($this->_currentPage > 1 && empty($this->_properties)){
				$this->_exists = false;
			}
		}

		/**
		 *	getIdFromUrlValue()
		 *	set the $_id variable via a query to the Database
		 *	@see: checkValueForSpam() below
		 */
		protected function getIdFromUrlValue(){

			if(!empty($this->_filter['url']) && $this->checkValueForSpam($this->_filter['url']) === false){

				if(empty($this->_sql['main_table'])){
					$this->_sql['main_table'] = strtolower($this->_name);
				}

				$query = "SELECT id FROM `{$this->_sql['main_table']}` WHERE url = '" . $this->_filter['url'] . "' AND status = 1 LIMIT 1;";

				niceError($query); // DEBUGGING - echo SQL

				$this->_id = $this->_db->get_var($query);
			}



		}

		/**
		 *	checkValueForSpam()
		 *	@param string $value
		 *	@return boolean $spam
		 *
		 *	If supplied value is made up of only letters,
		 *	numbers and dashes return true.
		 */
		protected function checkValueForSpam($value){

			$spam = true;

			$cleanValue = preg_replace("/[^A-Za-z0-9-]/", "", trim($value));

			if($cleanValue == $value){
				$spam = false;
			}

			return $spam;
		}


		/**
		 *	deleteCache()
		 *	remove all cached files from the cache
		 *	public so other classes using this one can use
		 *	it's power.
		 */
		public function deleteCache(){
			$objCache = new Cache($this->_name, 1, $this->_name);
			$objCache->delete('folder');
		}



		/**
		 *	getId()
		 *	@return	int
		 */
		public function getId(){
			return $this->_id;
		}

		/**
		 *	getProperties()
		 *	@return	array
		 */
		public function getProperties(){
			return $this->_properties;
		}

		/**
		 *	getTotal()
		 *	@return	int
		 */
		public function getTotal(){
			return $this->_total;
		}

		/**
		 *	getName()
		 *	@return	string
		 */
		public function getName(){
			return $this->_name;
		}

		/**
		 *	getNamePlural()
		 *	@return	string
		 */
		public function getNamePlural(){
			return $this->_namePlural;
		}

		/**
		 *	getFolder()
		 *	@return	string
		 */
		public function getFolder(){
			return $this->_folder;
		}

		/**
		 *	getForeignKeys()
		 *	@return	array
		 */
		public function getForeignKeys(){
			return $this->_sql['foreign_keys'];
		}

		/**
		 *	getFields()
		 *	@return	array
		 */
		public function getFields(){
			return $this->_fields;
		}

		/**
		 *	getFieldsDetails()
		 *	@return	array
		 */
		public function getFieldsDetails(){
			return $this->_fieldsDetails;
		}

		/**
		 *	getFieldsRequired()
		 *	@return	array
		 */
		public function getFieldsRequired(){
			return $this->_fieldsRequired;
		}

		/**
		 *	getExists()
		 *	@return	boolean
		 */
		public function getExists(){
			return $this->_exists;
		}


		/**
		 *	getCurrentPage()
		 *	@return	int
		 */
		public function getCurrentPage(){
			return $this->_currentPage;
		}

		/**
		 *	getPerPage()
		 *	@return	int
		 */
		public function getPerPage(){
			return $this->_perPage;
		}

		/**
		 *	getOrderBy()
		 *	@return	string
		 */
		public function getOrderBy(){
			return $this->_orderBy;
		}

		/**
		 *	getStatus()
		 *	@return	string
		 */
		public function getStatus(){
			return $this->_status;
		}

		/**
		 *	getSearch()
		 *	@return	string
		 */
		public function getSearch(){
			return $this->_search;
		}

		/**
		 *	getTimeframe()
		 *	@return	string
		 */
		public function getTimeframe(){
			return $this->_timeframe;
		}

		/**
		 *	getTimeframeCustom()
		 *	@return	string
		 */
		public function getTimeframeCustom(){
			return $this->_timeframeCustom;
		}

		/**
		 *	getTense()
		 *	@return	string
		 */
		public function getTense(){
			return $this->_tense;
		}

		/**
		 *	getHidden()
		 *	@return	string
		 */
		public function getHidden(){
			return $this->_hidden;
		}


		/**
		 *	getFilter()
		 *	@return	string
		 */
		public function getFilter($key){
			return $this->_filter[$key];
		}


		/**
		 *	getMissingFields()
		 *	@return	array
		 */
		public function getMissingFields(){
			return $this->_missingFields;
		}


		/**
		 *	getTableName()
		 *	@return	string
		 */
		public function getTableName(){
			return $this->_sql['main_table'];
		}






	}
	/**
	 * and so ends the scaffold class.
	 * It's sad isn't it? I too, would have loved to have seen its methods extend to the
	 * stars and back, but that's (a little) unrealistic.
	 *
	 * Laters
	 *
	 * Phil
	 * xx
	 */
?>
