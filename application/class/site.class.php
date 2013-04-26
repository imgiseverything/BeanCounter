<?php
/**
 *	=========================================================================
 *	
 *	Site Class
 *	-------------------------------------------------------------------------
 *	
 *	This class takes and XML file and uses it to populate key variables
 *	for this website e.g. website name, 
 *	
 *	We use an XML file, so the site is calling the database and getting 
 *	these variables for every page
 *	request - that would be cause too much database stress
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
 *	@since		22/02/2008
 *	
 *	edited by: 	Phil Thompson
 *	@modified	31/07/2009
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
 *	Methods
 *		loadSettings
 *		createXMLCache
 *		loadSettings
 *		setConfig
 *		createCache
 *		setTables
 *		resetTables
 *		getTables
 *
 *	-------------------------------------------------------------------------
 *	@copyright 2009 Phil Thompson http://philthompson.co.uk	
 *	=========================================================================
 *
 */

	class Site{
	
		// Variables

		/**
		 *	@var string
		 */
		private $_cacheFilenameSettings;
		
		/**
		 *	@var string
		 */
		private $_cacheFilenameTables;
		
		
		/**
		 *	@var array
		 */
		private $_tables;
		
		/**
		 *	@var object
		 */
		private $_error;
		
		/**
		 *	@var object
		 */
		public $_db;
		
		/**
		 *	@var object
		 */
		private $_application;
		
		/**
		 *	@var array
		 */
		public $config = array();
		
		
		/**
		 *	Constructor
		 *	@param object $db
		 *	@param object $objApplication
		 */
		public function __construct($db, $objApplication){
		
			global $objError; // sorry mum :(
			
			// Local object variables
			$this->_db = $db;
			$this->_application = $objApplication;
			$this->_error = $objError;
		
			// Setup cache filenames
			$this->_cacheFilenameSettings = 'settings.cache';
			$this->_cacheFilenameTables = 'tables.cache';
		
			// Get settings/tables (from file or cache)
			$this->loadSettings();
			$this->setTables();			
			
		} // end __construct()
		
		/**
		 *	Destruct()
		 *	overwite CRUD::__destruct so purge doesn't run
		 */
		public function __destruct(){}

		
		/**
		 *	loadSettings()
		 *	Check the cache for settings if it doesn't exist
		 * 	create it then set up the config variable
		 */
		private function loadSettings(){
		
			$objCache = new Cache($this->_cacheFilenameSettings, 1, 'model');
			
			if($objCache->getCacheExists() === true){
				$this->setConfig($objCache->getCache());
			} else{
				$this->createCache();
			}
			
		} // end loadSettings()
		
		/**
		 *	setConfig()
		 *	@var $content array
		 *	Loop through contents and create an array called $this->config
		 */
		private function setConfig($contents = array()){ 
			if(!empty($contents)){
				foreach($contents as $config){
					   $this->config[(string)$config['title']] = (string)$config['value'];
				}
			}
		}
		
		
		/**
		 *	createCache()
		 *	If the cache doesn't exist create it
		 *	If the local databae object doesn't exist create one. 
		 *	this is bad practice using these globals.
		 */
		private function createCache(){
		
		
	
			global $username, $password, $database, $sqlserver; // sorry mum :(
		
			// Local database object is AWOL
			if(!$this->_db){			
				// Database connection
				require_once(APPLICATION_PATH . '/class/ezSQL.class.php');				
				// Initialise database object
				$this->_db = new ezSQL($username, $password, $database, $sqlserver);
			}			
			
			// Initialise website object
			$objWebsite = new Website($this->_db, array(), false);
			if($this->_error->db_working === true){
				// This doesn't seem to work...
				// it should only try to query the database
				// if the database is working.
			}
			
			
			$website_properties = $objWebsite->getProperties();


			
			// Create a new cache object
			// and cache file then
			// set up the config variable
			$objCache = new Cache($this->_cacheFilenameSettings, 1, 'model');
			$objCache->createCache($objWebsite->getProperties());	
			
			$cached_config = $objCache->getCache();

			
			if(empty($cached_config)){
				$this->setConfig($objWebsite->getProperties());
			} else{
				$this->setConfig($cached_config);
			}
			
						
			
			
		} // end createXMLCache()
		
		
		/**
		 *	setTables()
		 *	Grab MySQL table data from the cache or get it
		 *	from MySQL itself.
		 */
		public function setTables(){
			
			// Cache data
			$objCache = new Cache($this->_cacheFilenameTables, 24, 'model');
			if($objCache->getCacheExists() === true){
				$this->_tables = $objCache->getCache();
			} else{
			
				$query = "SHOW TABLES;";
				$this->_tables = $this->_db->get_results($query);
				
				
				// Cache new data
				$objCache->createCache($this->_tables);								
				if($this->_error->db_working === true){
					// This doesn't seem to work...
					// it should only try to query the database
					// if the database is working.
				}				
				//$objCache->createCache($this->_tables);
				
			}
			

		} 
		
		/**
		 *	resetTables()
		 *	used in install script this returns an empty array
		 *	@return array	
		 */
		public function resetTables(){
			$this->_tables = array();
		}
		
		/**
		 *	getTables()
		 *	@return array
		 */
		public function getTables(){
			return $this->_tables;
		}
		
	}

?>