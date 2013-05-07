<?php
/**
 *	=========================================================================
 *	
 *	Install class
 *	-------------------------------------------------------------------------
 *	
 *	Create (or drop) the Bean Counter database
 *	
 *	=========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 		2008-2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version		1.0	
 *	@author			philthompson.co.uk
 *	@since			16/06/2008
 *	
 *	@lastmodified	30/04/2013
 *	
 *	=========================================================================
 *	
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
 *		void		
 *		createDatabaseTables			
 *		setConfig			
 *		dropAll
 *	
 *	=========================================================================
 *
 */

define('APPLICATION_PRICE', 15.00);

class Install{


	// Variables
	/**
	 *	@var object
	 */
	protected $_db;
	
	/**
	 *	@var string
	 */
	protected $_database;
	
	/**
	 *	@var boolean
	 */
	private $databaseWorks = false;
	
	/**
	 *	@var object
	 */
	private $_error;
	
	/**
	 *	@var object
	 */
	private $_application;		
	
	/**
	 *	@var object
	 */
	private $_site;
	
	/**
	 *	@var object
	 */
	private $_authorise;
	
	/**
	 *	@var array
	 */
	public $_databaseErrors = array();
	
	/**
	 *	Constructor
	 */
	public function __construct($db, $database, $objApplication, $objError, $objSite, $objAuthorise = false){
	

		$this->_db = $db;
		$this->_database = $database;
		
		$this->_application = $objApplication;
		
		$this->_error = $objError;
		$this->_site = $objSite;
		$this->_authorise = $objAuthorise;
		
		// Is the database setup?
		$this->_databaseErrors = array(
			"mysql_connect() [function.mysql-connect]: Access denied for user",
			"mysql_connect() [function.mysql-connect]: Unknown MySQL server host",
			"Error establishing mySQL database connection. Correct user/password? Correct hostname? Database server running?",
			"mySQL database connection is not active",
			"No database selected"
		);
		
		//niceError($this->_error->properties['string']);
		
		// Is the database setup?
		$this->_databaseWorks = ( empty($this->_error->properties['string']) || (substr($this->_error->properties['string'], 0, 16) != 'Unknown database' && !in_array($this->_error->properties['string'], $this->_databaseErrors) && is_object($this->_db))) ? true : false;	
		
		// Check that this database exists in the information schema
		$databaseQuery = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = %s;";			
		if($result = $this->_db->get_var(@$this->_db->prepare($databaseQuery, $this->_database))){
			$this->_databaseWorks = true;
		} else{
			$this->_databaseWorks = false;
		}	
		
		// work out whether to prevent an installation
		if($this->_databaseWorks === true){
			$this->void();
		} // end if
		
		if(!empty($this->_authorise->name)){
			$this->_authorise->Logout();
		} // end if
	
	}
	
	// Methods
	
	/**
	 *	void()
	 */
	public function void(){
		
		if(isset($this->_site->tables) && sizeof($this->_site->tables) == 26){
			// print a page with a link to homepage
			exit("<html><head><title>Error</title></head><body><style>@import url(\"/style/global.css\");@import url(\"/style/basic.css\");h1{font-size: 200%;text-align: center;} div.buttons{float: none !important;margin: 60px 280px;text-align: center;}div.buttons a.button{float: none; font-size: 200%;}
</style><div id=\"Holder\"><h1>" . $this->_application->getApplicationName() . " already installed</h1><div class=\"buttons clearfix\"><a href=\"/\" class=\"button\">Home page</a></div></div></body></html>");
		} // end if
		
	} // end void method
	
	/**
	 *	createDatabaseTables()
	 */
	public function createDatabaseTables(){

		$table_counter = 0;
		
	//	ini_set('display_errors', 1);
	//	error_reporting(E_ALL);
	
		// Ensure database table don't exist before running this method
		$query = "SHOW FIELDS FROM config;";
		if($results = @@$this->_db->query(@$this->_db->prepare($query))){
			$user_feedback['type'] = 'error';
			$user_feedback['content'] = 'All your database tables are already set-up; running this install might break your site.';
			return $user_feedback;
		} // end if
				
		/*
		 Table structure for table `access_level`
		*/			
		$query = "CREATE TABLE `access_level` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(25) NOT NULL,
		  `description` mediumtext NOT NULL,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		/*
		 Content for `access_level`
		*/
		$query = "INSERT INTO `access_level` (`id`, `title`, `description`, `status`, `date_added`, `date_edited`) VALUES
		(1, 'Superuser', 'The owner of this site.', 1, '2008-05-03 00:00:00', NULL),
		(3, 'Accountant', 'Can log into system and see accounts and outgoings', 1, '2008-05-03 15:25:28', NULL),
		(2, 'Basic', 'A basic user is usally a client but could be a supplier', 1, '2008-05-03 15:26:06', NULL);
		";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
		  Table structure for `booking`
		*/
		$query = "CREATE TABLE `booking` (
		  `id` int(11) NOT NULL auto_increment,
		  `client` int(11) NOT NULL COMMENT 'FK on client table',
		  `booking_type` int(11) NOT NULL COMMENT 'FK on booking_type table',
		  `title` varchar(255) NOT NULL,
		  `description` MEDIUMTEXT NULL,
		  `date_started` datetime NOT NULL,
		  `date_ended` datetime NOT NULL,
		  `include_weekends` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N', 
		  `status` int(11) NOT NULL default '1' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `client` (`client`),
		  KEY `booking_type` (`booking_type`)
		) ENGINE=MyISAM COMMENT='Stores bookings';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
		  Table structure for table `booking_type`
		*/
		$query = "CREATE TABLE `booking_type` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `title` varchar(150) NOT NULL,
		  `description` mediumtext,
		  `status` int(11) NOT NULL DEFAULT '1',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
		  Content for table `booking_type`
		*/
		$query = "INSERT INTO `booking_type` (`id`, `title`, `description`, `status`, `date_added`, `date_edited`) VALUES
		(1, 'Meeting', 'A meeting whether in person or on the phone. Not necessarily paid for.', 1, '2010-08-10 09:34:45', NULL),
		(2, 'Job', 'A working paying job.', 1, '2010-08-10 09:34:45', NULL),
		(3, 'Personal', 'Non work related', 1, '2010-08-10 09:34:45', NULL),
		(4, 'Holiday', 'Time booked as holiday', 1, '2010-08-10 09:34:45', NULL),
		(5, 'Training', 'Time spent learning or at a conference.', 1, '2010-08-10 09:34:45', NULL),
		(6, 'Administration', 'Time spent doing admin eg accounts, marketing, CRM etc.', 1, '2010-08-10 09:34:45', NULL);";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		
		/*
		  Content for table `charity`
		*/
		$query = "CREATE TABLE `charity` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `title` varchar(150) NOT NULL,
		  `description` mediumtext,
		  `status` int(11) NOT NULL DEFAULT '1',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Charities any donations have been given to';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		/*
			Table structure for table `client`
		*/
		$query = "CREATE TABLE `client` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(50) NOT NULL,
		  `description` mediumtext,
		  `main_contact` varchar(255) NOT NULL,
		  `address1` varchar(255) NOT NULL,
		  `address2` varchar(255) default NULL,
		  `address3` varchar(255) default NULL,
		  `address4` varchar(255) default NULL,
		  `postal_code` varchar(15) NOT NULL,
		  `country` varchar(255) NOT NULL,
		  `email` varchar(255) NOT NULL,
		  `telephone` varchar(25) default NULL,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `status` (`status`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
		 Table structure for table `config`
		*/			
		$query = "
		CREATE TABLE `config` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(255) NOT NULL,
		  `description` mediumtext NOT NULL,
		  `max_length` int(11) NOT NULL default '50',
		  `value` mediumtext default NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Website configuration settings';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		

		/*
		 Table structure for table `currency`
		*/			
		$query = "CREATE TABLE `currency` (
		  `id` int(11) NOT NULL auto_increment,
		  `value` varchar(255) NOT NULL,
		  `title` varchar(255) NOT NULL, 
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		niceError($query);
		
		
		/*
		 Content for currency table
		*/
		$query = "INSERT INTO `currency` VALUES
		(1, '&#163;', 'GBP', 1, '2010-03-06 18:09:22', NULL),
		(2, '&#8364;', 'EUR', 1, '2010-03-06 18:09:22', NULL),
		(3, '&#165;', 'YEN', 1, '2010-03-06 18:09:22', NULL),
		(4, '$', 'USD', 1, '2010-03-06 18:09:22', NULL),
		(5, '$', 'AUD', 1, '0000-00-00 00:00:00', NULL),
		(6, '$', 'CAD', 1, '0000-00-00 00:00:00', NULL),
		(7, '$', 'HKD', 1, '0000-00-00 00:00:00', NULL),
		(8, '&#164;', 'SEK', 1, '0000-00-00 00:00:00', NULL),
		(9, '&#164;', 'NOK', 1, '0000-00-00 00:00:00', NULL),
		(10, '$', 'NZD', 1, '0000-00-00 00:00:00', NULL),
		(11, '&#165;', 'CNY', 1, '0000-00-00 00:00:00', NULL);";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		niceError($query);
		
		
		/*
		 Table structure for table `donation`
		*/			
		$query = "CREATE TABLE `donation` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `charity` int(11) NOT NULL,
		  `title` varchar(150) NOT NULL,
		  `description` mediumtext,
		  `status` int(11) NOT NULL DEFAULT '1',
		  `amount` decimal(10,2) NOT NULL,
		  `gift_aid` decimal(10,2) NULL,
		  `transaction_date` date NOT NULL,
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Charitable donations made';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);

		
		/*
		 Table structure for table `errors`
		*/			
		$query = "CREATE TABLE `errors` (
		  `id` int(11) NOT NULL auto_increment,
		  `errors_level` int(11) NOT NULL COMMENT 'FK on errors_level table',
		  `string` mediumtext NOT NULL,
		  `file` mediumtext NOT NULL,
		  `line` mediumtext NOT NULL,
		  `date_added` datetime NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `errors_level` (`errors_level`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Log of all PHP errors that happen';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
		 Table structure for table `errors_level`
		*/			
		$query = "CREATE TABLE `errors_level` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(25) NOT NULL,
		  `description` mediumtext NOT NULL,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
		 Content for errors_level
		*/			
		$query = "INSERT INTO `errors_level` (`id`, `title`, `description`, `status`, `date_added`, `date_edited`) VALUES
		(1, '1', 'E_ERROR', 1, '2008-03-20 14:03:35', NULL),
		(2, '2', 'E_WARNING', 1, '2008-03-20 14:03:35', NULL),
		(3, '4', 'E_PARSE', 1, '2008-03-20 14:03:35', NULL),
		(4, '8', 'E_NOTICE', 1, '2008-03-20 14:03:35', NULL),
		(5, '16', 'E_CORE_ERROR', 1, '2008-03-20 14:03:35', NULL),
		(6, '32', 'E_CORE_WARNING', 1, '2008-03-20 14:03:35', NULL),
		(7, '64', 'E_COMPILE_ERROR', 1, '2008-03-20 14:03:35', NULL),
		(8, '128', 'E_COMPILE_WARNING', 1, '2008-03-20 14:03:35', NULL),
		(9, '256', 'E_USER_ERROR', 1, '2008-03-20 14:03:35', NULL),
		(10, '512', 'E_USER_WARNING', 1, '2008-03-20 14:03:35', NULL),
		(11, '1024', 'E_USER_NOTICE', 1, '2008-03-20 14:03:35', NULL),
		(12, '6143', 'E_ALL', 1, '2008-03-20 14:03:35', NULL),
		(13, '2048', 'E_STRICT', 1, '2008-03-20 14:03:35', NULL),
		(14, '4096', 'E_RECOVERABLE_ERROR', 1, '2008-03-20 14:03:35', NULL);";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
		 Content for errors_level
		*/			
		$query = "CREATE TABLE `interest` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` varchar(150) NOT NULL,
		`description` mediumtext,
		`status` int(11) NOT NULL DEFAULT '1',
		`amount` decimal(10,2) NOT NULL,
		`transaction_date` date NOT NULL,
		`date_added` datetime NOT NULL,
		`date_edited` datetime DEFAULT NULL,
		 PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Log of interest payments from bank accounts';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		
		/**
		 *	Table structure for table `lead`
		 */
		$query = "CREATE TABLE `lead` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `client` int(11) NOT NULL,
		  `title` varchar(150) NOT NULL,
		  `description` mediumtext,
		  `status` int(11) NOT NULL DEFAULT '1',
		  `lead_type` int(11) NOT NULL COMMENT 'FK on lead_type table',
		  `likelihood` int(3) DEFAULT NULL COMMENT 'How likely percentage-wise is this to come off',
		  `job_value` decimal(10,2) NOT NULL,
		  `first_contact_date` date NOT NULL COMMENT 'What date was first contact received?',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `client` (`client`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		/*
			Table structure for table `lead_type`
		 */
		$query = "CREATE TABLE `lead_type` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `title` varchar(25) NOT NULL,
		  `description` mediumtext NOT NULL,
		  `status` int(11) DEFAULT '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		
		/*
			Content for table `lead_type`
		 */
		$query = "INSERT INTO `lead_type` (`id`, `title`, `description`, `status`, `date_added`, `date_edited`) VALUES
		(1, 'Cold call/email', 'Job came out of nowhere', 1, '2011-10-04 00:00:00', NULL),
		(2, 'Referral', 'Job was referred from a client/competitor', 1, '2011-10-04 00:00:00', NULL),
		(3, 'Existing client', 'Job is from a client you have worked with before.', 1, '2011-10-04 00:00:00', NULL);";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
			Table structure for table `outgoing`
		*/
		$query = "CREATE TABLE `outgoing` (
		  `id` int(11) NOT NULL auto_increment,
		  `outgoing_supplier` int(11) NOT NULL COMMENT 'FK on outgoing_supplier table',
		  `outgoing_category` int(11) NOT NULL COMMENT 'FK on outgoing_category table',
		  `outgoing_payment` int(11) NOT NULL COMMENT 'FK on outgoing_payment table',
		  `title` varchar(100) NOT NULL,
		  `description` mediumtext,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `transaction_id` varchar(100) NULL COMMENT 'Receipt/invoice number',
		  `transaction_date` datetime NOT NULL,
		  `price` decimal(5,2) NOT NULL,
		  `claimable_price` decimal(5,2) default '0.00' COMMENT 'Amount of the price which can be claimed against income tax',
		  `vat` decimal(5,2) default '0.00' COMMENT 'Amount of price of which is VAT',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `outgoing_supplier` (`outgoing_supplier`),
		  KEY `outgoing_category` (`outgoing_category`),
		  KEY `outgoing_payment` (`outgoing_payment`),
		  KEY `status` (`status`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Record all business expenses';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
		 Table structure for table `outgoing_documentation`
		*/
		$query = "CREATE TABLE IF NOT EXISTS `outgoing_documentation` (
		  `id` int(11) NOT NULL auto_increment,
		  `outgoing` int(11) NOT NULL,
		  `title` varchar(75) NOT NULL,
		  `description` mediumtext,
		  `filename` varchar(255) NOT NULL,
		  `filesize` int(11) NOT NULL,
		  `mimetype` varchar(255) NOT NULL,
		  `status` int(11) NOT NULL default '1' COMMENT 'Is it live?',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `outgoing` (`outgoing`),
		  KEY `status` (`status`),
		  FULLTEXT KEY `title` (`title`),
		  FULLTEXT KEY `description` (`description`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Documentation as files/images for outgoings eg PDF receipts';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		

		/*
		 Content for `outgoing_category`
		*/
		$query = "CREATE TABLE `outgoing_category` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(100) NOT NULL,
		  `description` mediumtext,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		/*
		 Content for `outgoing_category`
		*/
		$query = "INSERT INTO `outgoing_category` (`id`, `title`, `description`, `status`, `date_added`, `date_edited`) VALUES
		(1, 'Goods used (bought for resale)', 'Cost of goods bought for resale or goods used', 1, '0000-00-00 00:00:00', '2008-05-04 18:05:06'),
		(2, 'Office costs', 'Telephone, fax, stationery and other office costs', 1, '0000-00-00 00:00:00', '2008-05-04 18:02:35'),
		(3, 'Subcontractor', 'Part of a job was completed by another supplier', 1, '0000-00-00 00:00:00', NULL),
		(4, 'Advertising and business entertainment costs', NULL, 1, '0000-00-00 00:00:00', '2008-05-04 18:06:10'),
		(6, 'Car, van and travel expenses', NULL, 1, '0000-00-00 00:00:00', '2008-05-04 18:01:54'),
		(8, 'Wages, salaries and other staff costs', '', 1, '2008-05-04 17:53:40', NULL),
		(9, 'Interest on bank and other loans', '', 1, '2008-05-04 18:02:52', NULL),
		(10, 'Bank, credit card and other financial charges', '', 1, '2008-05-04 18:03:11', NULL),
		(11, 'Irrecoverable debts written off', '', 1, '2008-05-04 18:03:28', NULL),
		(12, 'Accountancy, legal, and other professional fees', '', 1, '2008-05-04 18:03:52', NULL),
		(13, 'Depreciation and loss/profit on sale of assetss', '', 1, '2008-05-04 18:04:12', NULL),
		(14, 'Other business expenses', '', 1, '2008-05-04 18:04:19', NULL),
		(15, 'Construction industry - payments to subcontractors', '', 1, '2008-05-04 18:05:21', NULL),
		(16, 'Rents, rates, power and insurance costs  ', '', 1, '2008-05-04 18:05:37', '2008-06-16 13:38:17'),
		(17, 'Repairs and renewals of property and equipment', '', 1, '2008-05-04 18:06:30', NULL);";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		/*
			Table structure for table `outgoing_payment`
		*/
		$query = "CREATE TABLE `outgoing_payment` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(100) NOT NULL,
		  `description` mediumtext,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		

		/*
		 Content for `outgoing_payment`
		*/
		$query = "INSERT INTO `outgoing_payment` (`id`, `title`, `description`, `status`, `date_added`, `date_edited`) VALUES
		(1, 'Paypal', NULL, 1, '0000-00-00 00:00:00', NULL),
		(2, 'Bank transfer', 'Electronic payment from one bank account to another e.g. BACS, CHAPS or Faster Payments', 1, '0000-00-00 00:00:00', NULL),
		(3, 'Direct debit', NULL, 1, '0000-00-00 00:00:00', NULL),
		(4, 'Credit card', NULL, 1, '0000-00-00 00:00:00', NULL),
		(5, 'Debit card', NULL, 1, '0000-00-00 00:00:00', NULL),
		(6, 'Cash', NULL, 1, '0000-00-00 00:00:00', NULL),
		(7, 'Standing order', NULL, 1, '0000-00-00 00:00:00', NULL);";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		/*
			Table structure for table `outgoing_supplier`
		*/
		$query = "CREATE TABLE `outgoing_supplier` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(155) NOT NULL,
		  `description` mediumtext,
		  `main_contact` varchar(255) default NULL,
		  `address1` varchar(255) default NULL,
		  `address2` varchar(255) default NULL,
		  `address3` varchar(255) default NULL,
		  `address4` varchar(255) default NULL,
		  `postal_code` varchar(15) default NULL,
		  `country` varchar(255) default NULL,
		  `email` varchar(255) default NULL,
		  `telephone` varchar(25) default NULL,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Suppliers - people who you have bought from';
		";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		

		
		/*
			Table structure for table `payment_method`
		*/
		$query = "CREATE TABLE `payment_method` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(25) NOT NULL,
		  `description` mediumtext NOT NULL,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		

		/*
		 Content for `payment_method`
		*/
		$query = "
		INSERT INTO `payment_method` (`id`, `title`, `description`, `status`, `date_added`, `date_edited`) VALUES
		(1, 'Paypal', NULL, 1, '0000-00-00 00:00:00', NULL),
		(2, 'Bank transfer', 'Electronic payment from one bank account to another e.g. BACS, CHAPS or Faster Payments', 1, '0000-00-00 00:00:00', NULL),
		(3, 'Direct debit', NULL, 1, '0000-00-00 00:00:00', NULL),
		(4, 'Credit card', NULL, 1, '0000-00-00 00:00:00', NULL),
		(5, 'Debit card', NULL, 1, '0000-00-00 00:00:00', NULL),
		(6, 'Cash', NULL, 1, '0000-00-00 00:00:00', NULL),
		(7, 'Standing order', NULL, 1, '0000-00-00 00:00:00', NULL);";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		/*
			Table structure for table `project`
		*/
		$query = "
		CREATE TABLE `project` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `client` int(11) NOT NULL COMMENT 'FK on client table',
		  `title` varchar(50) NOT NULL,
		  `sender_address` mediumtext NOT NULL COMMENT 'Name/Address of invoice sender',
		  `clients_reference_number` varchar(255) DEFAULT NULL COMMENT 'What is the project known as by the client?',
		  `for_the_attention_of` mediumtext NOT NULL,
		  `description` mediumtext COMMENT 'Details that precede the itemised costs in the invoice/quote',
		  `appendix` mediumtext COMMENT 'Details that go after the itemised costs in the invoice/quote',
		  `project_stage` int(11) DEFAULT NULL COMMENT 'FK on project_stage table',
		  `charge_vat` enum('Y','N') DEFAULT 'N' COMMENT 'Does this invoice include VAT?',
		  `vat_rate` decimal(5,2) NOT NULL COMMENT 'The VAT rate at the time of invoicing',
		  `status` int(11) DEFAULT '0' COMMENT 'FK on status table',
		  `payment_required` datetime DEFAULT NULL COMMENT 'Date when payment due?',
		  `payment_expected` date DEFAULT NULL COMMENT 'When do you expect to be paid',
		  `transaction_date` datetime DEFAULT NULL COMMENT 'Date when payment was made',
		  `invoice_date` date NOT NULL COMMENT 'Date the invoice was sent',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `client` (`client`),
		  KEY `requires_deposit` (`requires_deposit`),
		  KEY `project_stage` (`project_stage`),
		  KEY `status` (`status`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Projects are invoices or proposals. They contain tasks (line items) and/or discounts.';
		";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		

		/*
		 Content for `project`
		*/
		$query = "INSERT INTO `project` 
		(`client`, `title`, `clients_reference_number`, `description`, `appendix`, `requires_deposit`, `project_stage`, `status`, `payment_required`, `transaction_date`, `date_added`) VALUES
		(1, 'Example project', '0001EXAMPLE', 'An example project.', NULL, 'Y', 4, 1, NULL, Now(), Now());";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		/*
			Table structure for table `project_discount`
		*/
		$query = "CREATE TABLE `project_discount` (
		  `id` int(11) NOT NULL auto_increment,
		  `project` int(11) NOT NULL COMMENT 'FK on project table id',
		  `title` varchar(255) NOT NULL,
		  `description` mediumtext,
		  `price` decimal(10,2) NOT NULL,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `project` (`project`),
		  KEY `status` (`status`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='An invoice may need a discount';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
			Table structure for table `project_payment`
		*/
		$query = "CREATE TABLE `project_payment` (
		  `id` int(11) NOT NULL auto_increment,
		  `project` int(11) NOT NULL COMMENT 'FK on project table id',
		  `payment_method` int(11) NOT NULL COMMENT 'FK on payment_method table id',
		  `title` varchar(255) NOT NULL,
		  `description` mediumtext,
		  `price` decimal(10,2) NOT NULL,
		  `transaction_date` date NOT NULL,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `project` (`project`),
		  KEY `payment_method` (`payment_method`),
		  KEY `status` (`status`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Record of payments for an invoice';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);

		
		/*
			Table structure for table `project_stage`
		*/
		$query = "CREATE TABLE `project_stage` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(100) NOT NULL,
		  `description` mediumtext NOT NULL,
		  `status` int(11) NOT NULL,
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `status` (`status`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Where is this project up to? About to start? Invoiced?';
		";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		

		/*
		 Content for `project_stage`
		*/
		$query = "
		INSERT INTO `project_stage` (`id`, `title`, `description`, `status`, date_added) VALUES
		(1, 'Proposal', 'Quote sent to client. No work started yet.', 1, Now()),
		(2, 'Started', 'The green light has been given and work has started (or is slated to start) on this project.', 1, Now()),
		(3, 'Invoiced', 'Invoice has been sent but has not been paid yet', 1, Now()),
		(4, 'Completed', 'Project is finished and the invoice has been paid in full.', 1, Now()),
		('5', 'Green lit', 'This project has been agreed that it will definitely happen but has yet to actually start.', '1', Now());
		";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		/*
			Table structure for table `project_task`
		*/
		$query = "
		CREATE TABLE `project_task` (
		  `id` int(11) NOT NULL auto_increment,
		  `project` int(11) NOT NULL COMMENT 'FK on project table id',
		  `title` varchar(255) NOT NULL,
		  `description` mediumtext,
		  `price` decimal(10,2) NOT NULL,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `project` (`project`),
		  KEY `status` (`status`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Individual task (line item) for invoices/proposals.';
		";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		

		/*
		 Content for `project_task`
		*/
		$query = "
		INSERT INTO `project_task` (`project`, `title`, `description`, `price`, `status`, `date_added`) VALUES
		(1, 'Example task', 'This is an example task', 100.00, 1, Now());
		";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);


		/*
		 Table structure for table `status`
		*/			
		$query = "
		CREATE TABLE `status` (
		  `id` int(11) NOT NULL auto_increment,
		  `title` varchar(25) NOT NULL,
		  `description` mediumtext NOT NULL COMMENT 'Explain the status level',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Determines whether something appears on the site' ;
		";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		

		/*
			Content for  status
		*/			
		$query ="
		INSERT INTO `status` (`id`, `title`, `description`) VALUES
		(1, 'Active', 'available or live'),
		('2','To be approved','Item has not yet been approved'),
		('0', 'Inactive', 'Not available, e.g. deleted, or hidden or not live');
		";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		$query = "UPDATE `status` SET `id` = '0' WHERE `status`.`id` = 3 LIMIT 1";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		

		/* Timings */
		$query = "CREATE TABLE IF NOT EXISTS `timing` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `project` int(11) NOT NULL,
		  `title` varchar(150) NOT NULL,
		  `description` mediumtext,
		  `start_date` date NOT NULL,
		  `duration` float NOT NULL,
		  `billable` enum('Y','N') NOT NULL COMMENT 'Is this piece of work billable?',
		  `status` int(11) NOT NULL DEFAULT '1',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `project` (`project`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Record of time spent on a project (in hours).';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);

		/* Timing tags */
		$query = "CREATE TABLE IF NOT EXISTS `timing_tag` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `title` varchar(150) NOT NULL,
		  `description` mediumtext,
		  `status` int(11) NOT NULL DEFAULT '1',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8  COMMENT='Tags for Timings - to see what type of work is most popular.';";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);

		
		/* Timing tag matrix */
		$query = "CREATE TABLE IF NOT EXISTS `timing_tag_matrix` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `timing_id` int(11) NOT NULL,
		  `timing_tag_id` int(11) NOT NULL,
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);

		
		/*
			Table structure for table `user_client`
		*/
		$query = "CREATE TABLE `user_client` (
		`id` int(11) NOT NULL auto_increment,
		`firstname` varchar(155) NOT NULL,
		`surname` varchar(155) NOT NULL,
		`email` varchar(255) NOT NULL,
		`password` varchar(50) NOT NULL,
		`client` int(11) NOT NULL COMMENT 'FK on client table',
		`access_level` int(11) NOT NULL COMMENT 'FK on access_level table',
		`status` int(11) default '0' COMMENT 'FK on status table',
		`date_added` datetime NOT NULL,
		`date_edited` datetime default NULL,
		`date_last_login` datetime default NULL,
		PRIMARY KEY  (`id`),
		KEY `client` (`client`),
		KEY `status` (`status`),
		KEY `access_level` (`access_level`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		


		/*
		Structure for view `view_errors`
		*/			
		$query = "CREATE VIEW `view_errors` AS 
		SELECT DISTINCT `e`.`string` AS `string`, count(`e`.`string`) AS `COUNT( e.string )`, 
		`l`.`description` AS `description`, `e`.`file` AS `file`, `e`.`line` AS `line` 
		FROM `errors` `e` 
		LEFT JOIN `errors_level` `l`  ON `l`.`title` = `e`.`errors_level`
		GROUP BY `e`.`string`;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		return $this->setConfig();
		
	} // end createDatabaseTables method
	
	
	/**
	 *	setConfig()
	 */
	public function setConfig(){
		
		$error = 0;
		$success = 0;
		$all_data_present = false;
		
		// what fields are needed/might be submitted?
		$fields = array('business_name', 'firstname', 'surname', 'email', 'password_x');
		
		// clean data: make nice for data input
		// turn field name into easy to use variable names: e.g. $email
		extract(cleanFields($fields));
		
		if($business_name && $email && $password_x && $firstname && $surname){		
			$all_data_present = true;
		}

		if($all_data_present === true){
			
			$query = "INSERT INTO config (title, description, max_length, value) VALUES
			('Business name', 'The business name you trade under (which may be your own name or your company name)', 50, %s),
			('Address line 1', 'First line of your business address', 100, 'Address line 1'),
			('Address line 2', 'Second line of your business address', 100, ''),
			('Address line 3', 'Third line of your business address', 100, ''),
			('City/Town', 'The city/town where this business is based', 100, 'City/Town'),
			('County/State', 'The county/state that this business is in.', 100, 'County'),
			('Postal code', 'The postal code of this business', 15, ''),
			('Country', 'The country you are based in.', 255, 'UK'),
			('Main telephone number', 'The telephone number for your business', 50, ''),
			('Email address', 'The email address that invoices are sent from', 99, %s),
			('Main currency', 'The default currency that your business trades in', 50, 'GBP'),
			('Bank account number','', '8', '12345678'), 
			('Bank sort code','', '8','12-34-56'),
			('IBAN', 'International bank account number', '30', 'GBkkBBBBSSSSSSCCCCCCCC'),
			('Income tax rate', 'Percentage of each incoming fee that goes towards your income tax payment.', '50', '0'), 
			('National insurance', 'Percentage of each incoming fee that goes towards your UK national insurance payment.', '50', '0'),
			('VAT rate', 'Value added tax. Percentage of each incoming fee that is VAT. Leave blank if you do not pay VAT.', '50', '0'),
			('Start of financial year', 'Upon which date (DD/MM) does your financial year start?', '4', '0604'),
			('Invoice appendix', 'Default details about payment which appear at the bottom of invoices/proposals.', 20000, ' ')
			;";
			niceError($query);
			
			// Run query
			//Query worked
			if($results = @@$this->_db->query(@$this->_db->prepare($query, $business_name, $email, $email))){ 
				$success++; 
				$user_feedback['content'][] = $this->_application->getApplicationName().' database successfully set up and configurated';
				
				/* Add you as client */
				$password_x_hash = Authorise::generateHash($password_x);
				$query = "INSERT INTO `user_client` (`id`, `firstname`, `surname`, `email`, `password`, `client`, `access_level`, `status`, `date_added`) VALUES(1, %s, %s, %s, %s, 1, 1, 1, Now())";
				niceError($query);
				$prepared_query = @$this->_db->prepare($query, $firstname, $surname, $email, $password_x_hash);
				@$this->_db->query($prepared_query);
				
				$fullname = $firstname . ' ' . $surname;
				
				/*
				 Content for `client`
				*/
				$query = "INSERT INTO `client` 
				(`title`, `description`, `main_contact`, `address1`, `address2`, `address3`, `address4`,
				 `postal_code`, `country`, `email`, `telephone`, `status`, `date_added`) 
				VALUES 
				(%s, 'Me', %s, '1 Example Road', NULL, 'Example town', 
				'Example city', 'EX1 1AA', 'United Kingdom', %s, '01234567890', 1, Now())";
				niceError($query);
				
				// Run query
				$prepared_query = @$this->_db->prepare($query, $business_name, $fullname, $email);
				@$this->_db->query($prepared_query);

				//niceError($query);
				$user_feedback['content'][] = 'Hello ' . $fullname . ', you can <a href="/login/">Log-in here</a>';
				
				
				
				// Clear cache
				$objCacheSettings = new Cache('settings.cache', 1);			
				$objCacheSettings->delete('folder');
				
				
				// Initialise new website object
				$objNewWebsite = new Website($this->_db, array(), '');
				$objNewWebsite->setAll();
				
				// Clear cache
				$objCacheSettings = new Cache('settings.cache', 1, 'model');			
				$objCacheSettings->delete('folder');
				
				// Clear cache
				//$objCacheSettings = new Cache('settings.cache', 1);			
				//$objCacheSettings->delete('folder');
	
				
			} else{
				// Query failed
				$error++;
			} // end else
		
		} else{
			// data missing
			$error++;
			$user_feedback['content'][] = 'This step failed because you missed the following information out:';
			
			// No website name
			if(!$business_name){
				$user_feedback['content'][] = 'Business name. Call this the name you trade under which may be your own name or your company name';
			} //end if
			
			// No firstname
			if(!$firstname){
				$user_feedback['content'][] = 'Your first name';
			} //end if
			
			// No surname
			if(!$surname){
				$user_feedback['content'][] = 'Your surname';
			} //end if
			
			// No email
			if(!$email){
				$user_feedback['content'][] = 'Your email address';
			} //end if
			
			// No password
			if(!$password_x){
				$user_feedback['content'][] = 'Your password. We need a password so you can securely access this system';
			} //end if
			
		} // end else
		
		// redirect user & give feedback
		$user_feedback['type'] = ($error > 0) ? 'error' : 'success';
		
		return $user_feedback;
		
	} // end setConfig method
	
	
	/**
	 *	dropAll()
	 */
	public function dropAll(){
	
		// DROP TABLES
		$query = "DROP TABLE `access_level`, `booking`, `booking_type`, `charity`, `client`, `config`, `currency`, `donation`, `errors`, `errors_level`, `interest`, `lead`, `lead_type`, `outgoing`, `outgoing_category`, `outgoing_documentation`, `outgoing_payment`, `outgoing_supplier`, `payment_method`, `project`, `project_discount`, `project_payment`, `project_stage`, `project_task`, `status`, `timing`, `timing_tag`, `timing_tag_matrix`, `user_client`;";
		$results = $prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		
		
		// DROP VIEWS
		$query = "DROP VIEW `view_errors`;";
		$results = $prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);

		
		// redirect user & give feedback
		$user_feedback['content'] = 'Database wiped';
		$user_feedback['type'] = 'success';
		
		return $user_feedback;
		
	} // end dropAll method
	
	/**
	 *	getDatabaseWorks()
	 */
	public function getDatabaseWorks(){
		return $this->_databaseWorks;
	}
	
}
	
?>