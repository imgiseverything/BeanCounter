<?php
/**
 *	=========================================================================
 *	
 *	Model Class
 *	-------------------------------------------------------------------------
 *	Database model, workout the data model.
 *
 *	Thanks to MySQL queries like SHOW TABLES; and SHOW FIELDS FROM `x`
 *	we can pretty much automate a lot of things like
 *	# data validation: are all NOT NULL fields present? is a int field an int?
 *	# automatic form creation: <input>s for VARCHAR and <textarea>s for TEXT
 *	# find foreign keys: and grab their data/update them etc
 *
 *	Automation:
 *	The reason we automate this is so that if a new column/field etc is added
 *	or removed from the database we don't need to rewrite the code (99% of 
 *	time anyway) but we could simply add this data as text and remove the
 *	MySQL automation if it is required.
 *
 *	Caching:
 *	If this data isn't cached it means we need to go to the Database way too
 *	many times so caching is important.
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
 *	@since		01/01/2009
 *	
 *	edited by:  Phil Thompson
 *	@modified	12/04/2009
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
 *		sets:
 *			setFieldsExempt
 *			setFields
 *			setTables
 *			setFieldsDetails
 *			setFieldsRequired
 *			setForeignKeys
 *		gets:
 *			getFields
 *			getTables
 *			getFieldsDetails
 *			getFieldsRequired
 *			getForeignKeys
 *			getForeignKeysManyToMany
 *	
 *	=========================================================================
 */

	class Model{
	
		// Variables
		
		/**
		 *	@var array
		 */
		protected $_db;
		
		/**
		 *	@var array
		 */
		protected $_tableName;
		
		
		/**
		 *	@var array
		 */
		protected $_tables = array();
		
		/**
		 *	@var array
		 */		
		protected $_fields = array();
		
		/**
		 *	@var array
		 */
		protected $_fieldsDetails = array();
		
		/**
		 *	@var array
		 */
		protected $_FieldsRequired = array();
		
		/**
		 *	@var array
		 */
		protected $_fieldsExempt = array();
		
		/**
		 *	@var array
		 */
		protected $_foreignKeys = array();
		
		/**
		 *	@var array
		 */
		protected $_foreignKeysManyToMany = array();
		
		
		
		/**
		 *	construct
		 *	@param object $db
		 *	@param string $tableName
		 */
		public function __construct($db, $tableName){
			//echo $tableName . '<br>' . print_x($db). '<hr>';
			// Local variable objects
			$this->_db = $db;			
			$this->_tableName = $tableName;
			
			
			// Set relevant data
			$this->setFieldsExempt();
			$this->setFields();
			$this->setTables();
			$this->setForeignKeys();
			
		}
		
		/**
		 *	setFieldsExempt()
		 *	chose the fields that shouldn't be included in the fields array 
		 *	should include fields which are automatically added 
		 *	by MySQL e.g. an auto-incremented primary key
		 *
		 */
		protected function setFieldsExempt(){
			$this->_fieldsExempt = array('id', 'date_added', 'date_edited');
		}	
		
		
		/**
		 *	setFields()
		 *	What fields (columns) are in this table?
		 *	NOTE: don't put in exempt fields or else they'll
		 *	appear in auto-generated forms
		 */
		protected function setFields(){			
			
			// Get this tables fields
			$query = "SHOW FIELDS FROM `{$this->_tableName}`;";
			
			niceError($query);
			
			// Cache data
			$objCache = new Cache($this->_tableName . '_fields_data.cache', 24, $this->_tableName);
			if($objCache->getCacheExists() === true){
				$results = $objCache->getCache();
			} else{
				$results = $this->_db->get_results($query, "ARRAY_A");
				$objCache->createCache($results);
			}
			// End cache
			
			// Setup a simple array of field names for $_fields
			if($results){
				foreach($results as $result){
					if(!in_array($result['Field'], $this->_fieldsExempt)){
						$this->_fields[] = $result['Field'];
					}
				} 			
			}
			
			// Setup Details + Required fields
			$this->setFieldsDetails($results);
			$this->setFieldsRequired($results);


		}
		
		/**
		 *	setTables()
		 *	Get the database tables - for finding related tables
		 */
		protected function setTables(){
		
			// SQL query
			$query = "SHOW TABLES;";
			
			// Cache data
			$objCache = new Cache('tables.cache', 24, 'model');
			if($objCache->getCacheExists() === true){
				$results = $objCache->getCache();
			} else{
				$results = $this->_db->get_results($query);
				$objCache->createCache($results);
			}
			// End cache
			
			if(!empty($results)){
				foreach($results as $result){
					// database name needs to be got from 
					// $this->_db (so it must be set in '/inc/settings.inc.php')
					$this->_tables[] = $result->{'Tables_in_' . $this->_db->dbname}; 
				}
			}
			
		}
	
		
		/**
		 *	setFieldsDetails()
		 *	populate the _fieldsDetails variable so we
		 *	know how each field should be which is useful
		 *	for data validation, form helpers and much more :)
		 *	@param array $fields
		 */
		protected function setFieldsDetails($fields){

			if($fields){
				foreach($fields as $result){
					$this->_fieldsDetails[$result['Field']] = $result;
				} 			
			}

		}
		
		/**
		 *	setFieldsRequired()
		 *	Go through all the fields and see which
		 *	ones are required i.e. NOT NULL
		 * 	make sure to ignore some fields ($_fieldsExempt)
		 *	@param array $fields
		 */
		protected function setFieldsRequired($fields){
		
			if($fields){
				foreach($fields as $result){
					if(!in_array($result['Field'], $this->_fieldsExempt)){
						// if the field can't be null add it to the 
						// required fields
						if($result['Null'] == 'NO'){
							$this->_FieldsRequired[] = $result['Field'];
						} 
					} 
				} 			
			}

		}	
		
		
			
		/**
		 *	setForeignKeys()
		 *	Go through all tables and find out if there are any
		 *	tables which are foreign keys (one-to-many) or
		 *	many-to-many relationship tables.
		 *	This all works on a naming convention
		 *	Should populate:
		 *		$_foreignKeys
		 *		$_foreignKeysManyToMany
		 */
		protected function setForeignKeys(){

			if($this->_tables){	
				foreach($this->_tables as $table){
					// split table into array by _ underscores
					$table_array = explode('_', $table);

					// if a table's name looks like this: maintablename_2ndtablename 
					// it may be a one-to-many table
					$is_a_fk = (sizeof($table_array) > 1 && $table_array[0] == $this->_tableName && substr($table, -7) != '_matrix' && in_array($table.'_matrix', $this->_tables)) ? true : false;
					
					// If a table's name looks like this: tablename_2ndtablename_matrix
					// it is probably a 'many-to-many' relationship table
					$is_a_m2m_fk = ((sizeof($table_array) > 1 && $table_array[0] == $this->_tableName && substr($table, -7) != '_matrix' && !in_array($table.'_matrix', $this->_tables)) || in_array($table, $this->_fields)) ? true : false;				
					
					
					// Add the table to the relevant array e.g.
					// $_foreignKeys or $_foreignKeysOneToMany
					if($is_a_fk === true){
						$this->_foreignKeys[] = $table;
					} else if($is_a_m2m_fk === true){
						$this->_foreignKeysManyToMany[] = $table;						
					}
				}
			}
			
		}
		
		
		
		/**
		 *	setFields()
		 *	What fields (columns) are in this table?
		 *	NOTE: don't put in exempt fields or else they'll
		 *	appear in auto-generated forms
		 */
		public function getForeignKeysFields($foreign_key){			
		
		
			$fields = array();
			
			// Get this tables fields
			$query = "SHOW FIELDS FROM `{$foreign_key}`;";
			
			niceError($query);
			
			// Cache data
			$objCache = new Cache($foreign_key . '_fields_data.cache', 24, $foreign_key);
			if($objCache->getCacheExists() === true){
				$results = $objCache->getCache();
			} else{
				$results = $this->_db->get_results($query, "ARRAY_A");
				$objCache->createCache($results);
			}
			// End cache
			
			// Setup a simple array of field names for $_fields
			if($results){
				foreach($results as $result){
					if(!in_array($result['Field'], $this->_fieldsExempt)){
						$fields[] = "`fk` . " . $result['Field'];
					}
				} 			
			}
			
			
			$fields = join(',', $fields);
			
			
			return $fields;


		}
		
		
		
		
		/**
		 *	getFieldsExempt()
		 */
		public function getFieldsExempt(){
			return $this->_fieldsExempt;
		}
		
		
		/**
		 *	getFields()
		 */
		public function getFields(){
			return $this->_fields;
		}
		
		/**
		 *	getTables()
		 */
		public function getTables(){
			return $this->_tables;
		}
		
		/**
		 *	getFieldsDetails()
		 */
		public function getFieldsDetails(){
			return $this->_fieldsDetails;
		}
		
		/**
		 *	getFieldsRequired()
		 */
		public function getFieldsRequired(){
			return $this->_FieldsRequired;
		}
		
		/**
		 *	getForeignKeys()
		 */
		public function getForeignKeys(){
			return $this->_foreignKeys;
		}
		
		/**
		 *	getForeignKeysManyToMany()
		 */
		public function getForeignKeysManyToMany(){
			return $this->_foreignKeysManyToMany;
		}
		
	
	
	}
?>