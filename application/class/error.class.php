<?php
/**
 *	=========================================================================
 *
 *	Error Class
 *	-------------------------------------------------------------------------
 *
 *	Takes errors and logs (in a database or file) them/displays them.
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
 *	@since		04/06/2008
 *
 *	edited by:  Phil Thompson
 *	@modified	12/04/2009
 *
 *	=========================================================================
 *
 *	Table of contents:
 *	-------------------------------------------------------------------------
 *
 *	variables
 *
 *	constructor
 *
 *	methods
 *
 *		setErrorReporting()
 *		setProperties()
 *		setDatabaseErrors()
 *		logError()
 *		displayError()
 *
 *
 *
 *	=========================================================================
 *
 */

	class Error{

		// Variables

		/**
		 *	@var array
		 */
		public $properties = array();

		/**
		 *	@var boolean
		 */
		public $db_working = false;

		/**
		 *	@var object
		 */
		public $db;

		/**
		 *	@var array
		 */
		public $database_errors = array();

		/**
		 *	@var object
		 */
		private $_application;


		/**
		 *	Constructor
		 */
		public function __construct($objApplication){

			$this->_application = $objApplication;

			$this->setErrorReporting();
			$this->setDatabaseErrors();


		}

		// Methods

		/**
		 *	setErrorReporting()
		 */
		public function setErrorReporting(){

			// check which server we're on and decide which error reporting levels to show based on that information
			switch(MODE){

				// Live website e.g. www.example.com
				case 'live';
					// What type of error messages whould we show ? on the live site, none!
					ini_set("display_errors", "no");
					error_reporting(0);
					break;

				// Test website e.g. test.example.com
				case 'test';
					// What type of error messages whould we show ? ALL of them
					ini_set("display_errors", "yes");
					error_reporting(E_ALL);
					break;

				// Local Testing website e.g. http://localhost/example/
				case 'local':
				default:
					// What type of error messages whould we show ? ALL of them
					ini_set("display_errors", "yes");
					error_reporting(E_ALL);
					break;

			} // end switch

			/* use this error for ALL (E_ALL) errors */
			set_error_handler('xhandler', E_ALL);

		} // end setErrorReporting method

		/**
		 *	setProperties()
		 *	@param string $number
		 *	@param string $string
		 *	@param string $file
		 *	@param string $file
		 *	@param string $context
		 */
		public function setProperties($number, $string, $file, $line, $context){

			$this->_properties['number'] = $number;
			$this->_properties['string'] = $string;
			$this->_properties['line'] = $line;
			$this->_properties['file'] = $file;
			$this->_properties['context'] = $context;

		} // end setProperties method


		/**
		 *	setDatabaseErrors()
		 */
		public function setDatabaseErrors(){
			global $database;

			$this->database_errors = array(
				"Table '{$database}.config' doesn't exist",
				"Table '{$database}.errors' doesn't exist",
				"mysql_connect() [function.mysql-connect]: Access denied for user",
				"mysql_connect() [function.mysql-connect]: Unknown MySQL server host",
				"Error establishing mySQL database connection. Correct user/password? Correct hostname? Database server running?",
				"mySQL database connection is not active",
				"No database selected"
			);

		}


		/**
		 *	logError()
		 *	@param string $number
		 *	@param string $string
		 *	@param string $file
		 *	@param string $file
		 *	@param string $context
		 */
		public function logError($number, $string, $file, $line, $context){
			global $db, $database, $i;

			// Local database object
			$this->db = $db;

			$this->db_working = true;

			// set Properties array
			$this->setProperties($number, $string, $file, $line, $context);

			//niceError($this->_properties['string'].'<br>'.$this->_properties['line']);

			// Is the database setup?
			if(
				substr($this->_properties['string'], 0, 16) == 'Unknown database'
				|| in_array($this->_properties['string'], $this->database_errors)
				|| is_object($this->db) !== true
			){

			exit;
				// Database isn't working/not setup
				// Depending on what pages we're on either show the error
				switch($_SERVER['REQUEST_URI']){

					case '/install':
					case '/install/':
					case '/install.php':
						// stop all errors showing on the install screen about a lack of database.
						return;
						break;

					default:

						// Display install link
						$button = (file_exists(APPLICATION_PATH . '/controllers/install.php')) ? "<div class=\"buttons clearfix\"><a href=\"/install/\" class=\"button\">Install " . $this->_application->getApplicationName() . "</a></div>" : "";

						// print a page with a link to install cart45
						exit("<html><head><title>" . $this->_application->getApplicationName() . "</title></head><body><style>@import url(\"/style/reset.css\");@import url(\"/style/global.css\");@import url(\"/style/basic.css\"); h1{font-size: 42px;} div.buttons a.button{font-size:36px; padding: 40px 0;}</style><div id=\"BeanCounter\"><div class=\"inner\"><span class=\"logo\">{$this->_application->getApplicationName()}</span></div>
</div>
<div id=\"Holder\"><div class=\"inner\"><h1>What are you waiting for?</h1>{$button}</div></div></body></html>");
						break;

				} // end switch

			} else if($this->db_working === true && substr($_SERVER['REQUEST_URI'], 0, 8) != '/install'){
				// SQL
				//$query = "INSERT INTO errors (errors_level, string, file, line, date_added) VALUES ('{$this->db->escape($this->_properties['number'])}', '{$this->db->escape($this->_properties['string'])}', '{$this->db->escape($this->_properties['file'])}', '{$this->db->escape($this->_properties['line'])}', Now());";
				// Run query
				//$results = $this->db->query($query);
			}


			// Display errors on screen
			$this->displayError();

		} // end logError method


		/**
		 *	displayError()
		 */
		public function displayError(){


			$message = '<b>Error:</b> ' . $this->_properties['string'] . '<br />
				<b>File:</b> ' . str_replace(APPLICATION_PATH, '', $this->_properties['file']) . '<br />
				<b>Line:</b> ' . $this->_properties['line'];

			// Not live: display error (for easier debugging)
			// constructor should decide whether to run this method.
			if(MODE == 'local' && $this->db_working === true){
				niceError($message, true);
			} else if(MODE == 'live'){
				niceError($message);
				/*
				switch($this->_properties['number']){

					default:
					case E_USER_ERROR:
						// print an error page
						exit("<html><head><title>Error | " . $this->_application->getApplicationName() . "</title></head><body><style>@import url(\"/style/reset.css\");@import url(\"/style/global.css\");@import url(\"/style/basic.css\");</style><div id=\"holder\"><div id=\"header\"><div id=\"Branding\"><span id=\"Logo\"><a href=\"/\" title=\"homepage\"> " . $this->_application->getApplicationName() . "</a></span></div></div><h1>Error</h1><p>An error has occurred</p></div></body></html>");
						break;

				}
				*/
			}

		} // end displayError method


		/**
		 *	throw404()
		 *	set 404 HTTP error and show error view
		 *	make sure $objTemplate && $objMenu are passed to view the view
		 * 	@param object $objTemplate
		 * 	@param object $objMenu
		 *	@param object $objVcard
		 *	@param object $objAuthorise
		 */
		public function throw404($objTemplate, $objMenu, $objVcard, $objAuthorise = false){
			header("HTTP/1.0 404 Not Found");
			$_GET['error'] = '404';
			$objApplication = $this->_application;
			include(APPLICATION_PATH . "/controllers/error.php");
			exit();
		}

		/**
		 *	throw500()
		 *	set 500 HTTP error and show error view
		 * 	@param object $objTemplate
		 * 	@param object $objMenu
		 *	@param object $objVcard
		 *	@param object $objAuthorise
		 */
		public function throw500($objTemplate, $objMenu, $objVcard, $objAuthorise = false){
			header("HTTP/1.0 500 Server Error");
			$_GET['error'] = '500';
			$objApplication = $this->_application;
			include(APPLICATION_PATH . "/controllers/error.php");
			exit();
		}


	}

?>