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
 *	@lastmodified	22/10/2014
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
		}
		
		if(!empty($this->_authorise->name)){
			$this->_authorise->Logout();
		}
	
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
		}
				
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
		(1, 'Superuser', 'The owner of this site.', 1, Now(), NULL),
		(3, 'Accountant', 'Can log into system and see accounts and outgoings', 1, Now(), NULL),
		(2, 'Basic', 'A basic user is usually a client but could be a supplier', 1, Now(), NULL);
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
		(1, 'Meeting', 'A meeting whether in person or on the phone. Not necessarily paid for.', 1, Now(), NULL),
		(2, 'Job', 'A working paying job.', 1, Now(), NULL),
		(3, 'Personal', 'Non work related', 1, Now(), NULL),
		(4, 'Holiday', 'Time booked as holiday', 1, Now(), NULL),
		(5, 'Training', 'Time spent learning or at a conference.', 1, Now(), NULL),
		(6, 'Administration', 'Time spent doing admin eg accounts, marketing, CRM etc.', 1, Now(), NULL);";
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
		  `country` int(11) NOT NULL,
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
		  Content for table `client
		*/
		$query = "INSERT INTO `client` (`id`, `title`, `main_contact`, `address1`, `email`, `postal_code`, `country`, `status`, `date_added`) VALUES
		(1, 'Example', 'John Smith', '123 Example Street', 'hello@example.com', 'AB1 2DE', 232,  1, Now());";
			
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
		 Table structure for table `country`
		*/			
		$query = "
		CREATE TABLE `country` (
		`id` int(11) NOT NULL auto_increment,
		`title` varchar(200) NOT NULL,
		`ccode` varchar(2) NOT NULL default '',
		`status` int(11) default '0' COMMENT 'FK on status table',
		`date_added` datetime NOT NULL,
		`date_edited` datetime default NULL,
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
		 Content for table `country`
		*/			
		$query = "
		INSERT INTO `country` (`id`, `title`, `ccode`, `status`, `date_added`, `date_edited`) VALUES
		(1, 'Afghanistan', 'AF', 1, Now(), NULL),
		(2, 'Åland Islands', 'AX', 1, Now(), NULL),
		(3, 'Albania', 'AL', 1, Now(), NULL),
		(4, 'Algeria', 'DZ', 1, Now(), NULL),
		(5, 'American Samoa', 'AS', 1, Now(), NULL),
		(6, 'Andorra', 'AD', 1, Now(), NULL),
		(7, 'Angola', 'AO', 1, Now(), NULL),
		(8, 'Anguilla', 'AI', 1, Now(), NULL),
		(9, 'Antarctica', 'AQ', 1, Now(), NULL),
		(10, 'Antigua and Barbuda', 'AG', 1, Now(), NULL),
		(11, 'Argentina', 'AR', 1, Now(), NULL),
		(12, 'Armenia', 'AM', 1, Now(), NULL),
		(13, 'Aruba', 'AW', 1, Now(), NULL),
		(14, 'Australia', 'AU', 1, Now(), NULL),
		(15, 'Austria', 'AT', 1, Now(), NULL),
		(16, 'Azerbaijan', 'AZ', 1, Now(), NULL),
		(17, 'Bahamas', 'BS', 1, Now(), NULL),
		(18, 'Bahrain', 'BH', 1, Now(), NULL),
		(19, 'Bangladesh', 'BD', 1, Now(), NULL),
		(20, 'Barbados', 'BB', 1, Now(), NULL),
		(21, 'Belarus', 'BY', 1, Now(), NULL),
		(22, 'Belgium', 'BE', 1, Now(), NULL),
		(23, 'Belize', 'BZ', 1, Now(), NULL),
		(24, 'Benin', 'BJ', 1, Now(), NULL),
		(25, 'Bermuda', 'BM', 1, Now(), NULL),
		(26, 'Bhutan', 'BT', 1, Now(), NULL),
		(27, 'Bolivia', 'BO', 1, Now(), NULL),
		(28, 'Bosnia and Herzegovina', 'BA', 1, Now(), NULL),
		(29, 'Botswana', 'BW', 1, Now(), NULL),
		(30, 'Bouvet Island', 'BV', 1, Now(), NULL),
		(31, 'Brazil', 'BR', 1, Now(), NULL),
		(32, 'British Indian Ocean Territory', 'IO', 1, Now(), NULL),
		(33, 'Brunei Darussalam', 'BN', 1, Now(), NULL),
		(34, 'Bulgaria', 'BG', 1, Now(), NULL),
		(35, 'Burkina Faso', 'BF', 1, Now(), NULL),
		(36, 'Burundi', 'BI', 1, Now(), NULL),
		(37, 'Cambodia', 'KH', 1, Now(), NULL),
		(38, 'Cameroon', 'CM', 1, Now(), NULL),
		(39, 'Canada', 'CA', 1, Now(), NULL),
		(40, 'Cape Verde', 'CV', 1, Now(), NULL),
		(41, 'Cayman Islands', 'KY', 1, Now(), NULL),
		(42, 'Central African Republic', 'CF', 1, Now(), NULL),
		(43, 'Chad', 'TD', 1, Now(), NULL),
		(44, 'Chile', 'CL', 1, Now(), NULL),
		(45, 'China', 'CN', 1, Now(), NULL),
		(46, 'Christmas Island', 'CX', 1, Now(), NULL),
		(47, 'Cocos (Keeling) Islands', 'CC', 1, Now(), NULL),
		(48, 'Colombia', 'CO', 1, Now(), NULL),
		(49, 'Comoros', 'KM', 1, Now(), NULL),
		(50, 'Congo', 'CG', 1, Now(), NULL),
		(51, 'Congo, The Democratic Republic of the', 'CD', 1, Now(), NULL),
		(52, 'Cook Islands', 'CK', 1, Now(), NULL),
		(53, 'Costa Rica', 'CR', 1, Now(), NULL),
		(54, 'Côte D''Ivoire', 'CI', 1, Now(), NULL),
		(55, 'Croatia', 'HR', 1, Now(), NULL),
		(56, 'Cuba', 'CU', 1, Now(), NULL),
		(57, 'Cyprus', 'CY', 1, Now(), NULL),
		(58, 'Czech Republic', 'CZ', 1, Now(), NULL),
		(59, 'Denmark', 'DK', 1, Now(), NULL),
		(60, 'Djibouti', 'DJ', 1, Now(), NULL),
		(61, 'Dominica', 'DM', 1, Now(), NULL),
		(62, 'Dominican Republic', 'DO', 1, Now(), NULL),
		(63, 'Ecuador', 'EC', 1, Now(), NULL),
		(64, 'Egypt', 'EG', 1, Now(), NULL),
		(65, 'El Salvador', 'SV', 1, Now(), NULL),
		(66, 'Equatorial Guinea', 'GQ', 1, Now(), NULL),
		(67, 'Eritrea', 'ER', 1, Now(), NULL),
		(68, 'Estonia', 'EE', 1, Now(), NULL),
		(69, 'Ethiopia', 'ET', 1, Now(), NULL),
		(70, 'Falkland Islands (Malvinas)', 'FK', 1, Now(), NULL),
		(71, 'Faroe Islands', 'FO', 1, Now(), NULL),
		(72, 'Fiji', 'FJ', 1, Now(), NULL),
		(73, 'Finland', 'FI', 1, Now(), NULL),
		(74, 'France', 'FR', 1, Now(), NULL),
		(75, 'French Guiana', 'GF', 1, Now(), NULL),
		(76, 'French Polynesia', 'PF', 1, Now(), NULL),
		(77, 'French Southern Territories', 'TF', 1, Now(), NULL),
		(78, 'Gabon', 'GA', 1, Now(), NULL),
		(79, 'Gambia', 'GM', 1, Now(), NULL),
		(80, 'Georgia', 'GE', 1, Now(), NULL),
		(81, 'Germany', 'DE', 1, Now(), NULL),
		(82, 'Ghana', 'GH', 1, Now(), NULL),
		(83, 'Gibraltar', 'GI', 1, Now(), NULL),
		(84, 'Greece', 'GR', 1, Now(), NULL),
		(85, 'Greenland', 'GL', 1, Now(), NULL),
		(86, 'Grenada', 'GD', 1, Now(), NULL),
		(87, 'Guadeloupe', 'GP', 1, Now(), NULL),
		(88, 'Guam', 'GU', 1, Now(), NULL),
		(89, 'Guatemala', 'GT', 1, Now(), NULL),
		(90, 'Guernsey', 'GG', 1, Now(), NULL),
		(91, 'Guinea', 'GN', 1, Now(), NULL),
		(92, 'Guinea-Bissau', 'GW', 1, Now(), NULL),
		(93, 'Guyana', 'GY', 1, Now(), NULL),
		(94, 'Haiti', 'HT', 1, Now(), NULL),
		(95, 'Heard Island and McDonald Islands', 'HM', 1, Now(), NULL),
		(96, 'Holy See (Vatican City State)', 'VA', 1, Now(), NULL),
		(97, 'Honduras', 'HN', 1, Now(), NULL),
		(98, 'Hong Kong', 'HK', 1, Now(), NULL),
		(99, 'Hungary', 'HU', 1, Now(), NULL),
		(100, 'Iceland', 'IS', 1, Now(), NULL),
		(101, 'India', 'IN', 1, Now(), NULL),
		(102, 'Indonesia', 'ID', 1, Now(), NULL),
		(103, 'Iran, Islamic Republic of', 'IR', 1, Now(), NULL),
		(104, 'Iraq', 'IQ', 1, Now(), NULL),
		(105, 'Ireland', 'IE', 1, Now(), NULL),
		(106, 'Isle of Man', 'IM', 1, Now(), NULL),
		(107, 'Israel', 'IL', 1, Now(), NULL),
		(108, 'Italy', 'IT', 1, Now(), NULL),
		(109, 'Jamaica', 'JM', 1, Now(), NULL),
		(110, 'Japan', 'JP', 1, Now(), NULL),
		(111, 'Jersey', 'JE', 1, Now(), NULL),
		(112, 'Jordan', 'JO', 1, Now(), NULL),
		(113, 'Kazakhstan', 'KZ', 1, Now(), NULL),
		(114, 'Kenya', 'KE', 1, Now(), NULL),
		(115, 'Kiribati', 'KI', 1, Now(), NULL),
		(116, 'Korea, Democratic People''s Republic of', 'KP', 1, Now(), NULL),
		(117, 'Korea, Republic of', 'KR', 1, Now(), NULL),
		(118, 'Kuwait', 'KW', 1, Now(), NULL),
		(119, 'Kyrgyzstan', 'KG', 1, Now(), NULL),
		(120, 'Lao People''s Democratic Republic', 'LA', 1, Now(), NULL),
		(121, 'Latvia', 'LV', 1, Now(), NULL),
		(122, 'Lebanon', 'LB', 1, Now(), NULL),
		(123, 'Lesotho', 'LS', 1, Now(), NULL),
		(124, 'Liberia', 'LR', 1, Now(), NULL),
		(125, 'Libyan Arab Jamahiriya', 'LY', 1, Now(), NULL),
		(126, 'Liechtenstein', 'LI', 1, Now(), NULL),
		(127, 'Lithuania', 'LT', 1, Now(), NULL),
		(128, 'Luxembourg', 'LU', 1, Now(), NULL),
		(129, 'Macao', 'MO', 1, Now(), NULL),
		(130, 'Macedonia, The Former Yugoslav Republic of', 'MK', 1, Now(), NULL),
		(131, 'Madagascar', 'MG', 1, Now(), NULL),
		(132, 'Malawi', 'MW', 1, Now(), NULL),
		(133, 'Malaysia', 'MY', 1, Now(), NULL),
		(134, 'Maldives', 'MV', 1, Now(), NULL),
		(135, 'Mali', 'ML', 1, Now(), NULL),
		(136, 'Malta', 'MT', 1, Now(), NULL),
		(137, 'Marshall Islands', 'MH', 1, Now(), NULL),
		(138, 'Martinique', 'MQ', 1, Now(), NULL),
		(139, 'Mauritania', 'MR', 1, Now(), NULL),
		(140, 'Mauritius', 'MU', 1, Now(), NULL),
		(141, 'Mayotte', 'YT', 1, Now(), NULL),
		(142, 'Mexico', 'MX', 1, Now(), NULL),
		(143, 'Micronesia, Federated States of', 'FM', 1, Now(), NULL),
		(144, 'Moldova, Republic of', 'MD', 1, Now(), NULL),
		(145, 'Monaco', 'MC', 1, Now(), NULL),
		(146, 'Mongolia', 'MN', 1, Now(), NULL),
		(147, 'Montenegro', 'ME', 1, Now(), NULL),
		(148, 'Montserrat', 'MS', 1, Now(), NULL),
		(149, 'Morocco', 'MA', 1, Now(), NULL),
		(150, 'Mozambique', 'MZ', 1, Now(), NULL),
		(151, 'Myanmar', 'MM', 1, Now(), NULL),
		(152, 'Namibia', 'NA', 1, Now(), NULL),
		(153, 'Nauru', 'NR', 1, Now(), NULL),
		(154, 'Nepal', 'NP', 1, Now(), NULL),
		(155, 'Netherlands', 'NL', 1, Now(), NULL),
		(156, 'Netherlands Antilles', 'AN', 1, Now(), NULL),
		(157, 'New Caledonia', 'NC', 1, Now(), NULL),
		(158, 'New Zealand', 'NZ', 1, Now(), NULL),
		(159, 'Nicaragua', 'NI', 1, Now(), NULL),
		(160, 'Niger', 'NE', 1, Now(), NULL),
		(161, 'Nigeria', 'NG', 1, Now(), NULL),
		(162, 'Niue', 'NU', 1, Now(), NULL),
		(163, 'Norfolk Island', 'NF', 1, Now(), NULL),
		(164, 'Northern Mariana Islands', 'MP', 1, Now(), NULL),
		(165, 'Norway', 'NO', 1, Now(), NULL),
		(166, 'Oman', 'OM', 1, Now(), NULL),
		(167, 'Pakistan', 'PK', 1, Now(), NULL),
		(168, 'Palau', 'PW', 1, Now(), NULL),
		(169, 'Palestinian Territory, Occupied', 'PS', 1, Now(), NULL),
		(170, 'Panama', 'PA', 1, Now(), NULL),
		(171, 'Papua New Guinea', 'PG', 1, Now(), NULL),
		(172, 'Paraguay', 'PY', 1, Now(), NULL),
		(173, 'Peru', 'PE', 1, Now(), NULL),
		(174, 'Philippines', 'PH', 1, Now(), NULL),
		(175, 'Pitcairn', 'PN', 1, Now(), NULL),
		(176, 'Poland', 'PL', 1, Now(), NULL),
		(177, 'Portugal', 'PT', 1, Now(), NULL),
		(178, 'Puerto Rico', 'PR', 1, Now(), NULL),
		(179, 'Qatar', 'QA', 1, Now(), NULL),
		(180, 'Reunion', 'RE', 1, Now(), NULL),
		(181, 'Romania', 'RO', 1, Now(), NULL),
		(182, 'Russian Federation', 'RU', 1, Now(), NULL),
		(183, 'Rwanda', 'RW', 1, Now(), NULL),
		(184, 'Saint Barthélemy', 'BL', 1, Now(), NULL),
		(185, 'Saint Helena', 'SH', 1, Now(), NULL),
		(186, 'Saint Kitts and Nevis', 'KN', 1, Now(), NULL),
		(187, 'Saint Lucia', 'LC', 1, Now(), NULL),
		(188, 'Saint Martin', 'MF', 1, Now(), NULL),
		(189, 'Saint Pierre and Miquelon', 'PM', 1, Now(), NULL),
		(190, 'Saint Vincent and the Grenadines', 'VC', 1, Now(), NULL),
		(191, 'Samoa', 'WS', 1, Now(), NULL),
		(192, 'San Marino', 'SM', 1, Now(), NULL),
		(193, 'Sao Tome and Principe', 'ST', 1, Now(), NULL),
		(194, 'Saudi Arabia', 'SA', 1, Now(), NULL),
		(195, 'Senegal', 'SN', 1, Now(), NULL),
		(196, 'Serbia', 'RS', 1, Now(), NULL),
		(197, 'Seychelles', 'SC', 1, Now(), NULL),
		(198, 'Sierra Leone', 'SL', 1, Now(), NULL),
		(199, 'Singapore', 'SG', 1, Now(), NULL),
		(200, 'Slovakia', 'SK', 1, Now(), NULL),
		(201, 'Slovenia', 'SI', 1, Now(), NULL),
		(202, 'Solomon Islands', 'SB', 1, Now(), NULL),
		(203, 'Somalia', 'SO', 1, Now(), NULL),
		(204, 'South Africa', 'ZA', 1, Now(), NULL),
		(205, 'South Georgia and the South Sandwich Islands', 'GS', 1, Now(), NULL),
		(206, 'Spain', 'ES', 1, Now(), NULL),
		(207, 'Sri Lanka', 'LK', 1, Now(), NULL),
		(208, 'Sudan', 'SD', 1, Now(), NULL),
		(209, 'Suriname', 'SR', 1, Now(), NULL),
		(210, 'Svalbard and Jan Mayen', 'SJ', 1, Now(), NULL),
		(211, 'Swaziland', 'SZ', 1, Now(), NULL),
		(212, 'Sweden', 'SE', 1, Now(), NULL),
		(213, 'Switzerland', 'CH', 1, Now(), NULL),
		(214, 'Syrian Arab Republic', 'SY', 1, Now(), NULL),
		(215, 'Taiwan, Province Of China', 'TW', 1, Now(), NULL),
		(216, 'Tajikistan', 'TJ', 1, Now(), NULL),
		(217, 'Tanzania, United Republic of', 'TZ', 1, Now(), NULL),
		(218, 'Thailand', 'TH', 1, Now(), NULL),
		(219, 'Timor-Leste', 'TL', 1, Now(), NULL),
		(220, 'Togo', 'TG', 1, Now(), NULL),
		(221, 'Tokelau', 'TK', 1, Now(), NULL),
		(222, 'Tonga', 'TO', 1, Now(), NULL),
		(223, 'Trinidad and Tobago', 'TT', 1, Now(), NULL),
		(224, 'Tunisia', 'TN', 1, Now(), NULL),
		(225, 'Turkey', 'TR', 1, Now(), NULL),
		(226, 'Turkmenistan', 'TM', 1, Now(), NULL),
		(227, 'Turks and Caicos Islands', 'TC', 1, Now(), NULL),
		(228, 'Tuvalu', 'TV', 1, Now(), NULL),
		(229, 'Uganda', 'UG', 1, Now(), NULL),
		(230, 'Ukraine', 'UA', 1, Now(), NULL),
		(231, 'United Arab Emirates', 'AE', 1, Now(), NULL),
		(232, 'United Kingdom', 'GB', 1, Now(), NULL),
		(233, 'United States', 'US', 1, Now(), NULL),
		(234, 'United States Minor Outlying Islands', 'UM', 1, Now(), NULL),
		(235, 'Uruguay', 'UY', 1, Now(), NULL),
		(236, 'Uzbekistan', 'UZ', 1, Now(), NULL),
		(237, 'Vanuatu', 'VU', 1, Now(), NULL),
		(238, 'Venezuela', 'VE', 1, Now(), NULL),
		(239, 'Viet Nam', 'VN', 1, Now(), NULL),
		(240, 'Virgin Islands, British', 'VG', 1, Now(), NULL),
		(241, 'Virgin Islands, U.S.', 'VI', 1, Now(), NULL),
		(242, 'Wallis And Futuna', 'WF', 1, Now(), NULL),
		(243, 'Western Sahara', 'EH', 1, Now(), NULL),
		(244, 'Yemen', 'YE', 1, Now(), NULL),
		(245, 'Zambia', 'ZM', 1, Now(), NULL),
		(246, 'Zimbabwe', 'ZW', 1, Now(), NULL);";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		niceError($query);


		

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
		(1, '&#163;', 'GBP', 1, Now(), NULL),
		(2, '&#8364;', 'EUR', 1, Now(), NULL),
		(3, '&#165;', 'YEN', 1, Now(), NULL),
		(4, '$', 'USD', 1, Now(), NULL),
		(5, '$', 'AUD', 1, Now(), NULL),
		(6, '$', 'CAD', 1, Now(), NULL),
		(7, '$', 'HKD', 1, Now(), NULL),
		(8, '&#164;', 'SEK', 1, Now(), NULL),
		(9, '&#164;', 'NOK', 1, Now(), NULL),
		(10, '$', 'NZD', 1, Now(), NULL),
		(11, '&#165;', 'CNY', 1, Now(), NULL);";
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
		(1, 'Cold call/email', 'Job came out of nowhere', 1, Now(), NULL),
		(2, 'Referral', 'Job was referred from a client/competitor', 1, Now(), NULL),
		(3, 'Existing client', 'Job is from a client you have worked with before.', 1, Now(), NULL);";
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
		  `payment_method` int(11) NOT NULL COMMENT 'FK on payment_method table',
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
		  KEY `payment_method` (`payment_method`),
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
		(1, 'Goods used (bought for resale)', 'Cost of goods bought for resale or goods used', 1, Now(), NULL),
		(2, 'Office costs', 'Telephone, fax, stationery and other office costs', 1, Now(), NULL),
		(3, 'Subcontractor', 'Part of a job was completed by another supplier', 1, Now(), NULL),
		(4, 'Advertising and business entertainment costs', NULL, 1, Now(), NULL),
		(6, 'Car, van and travel expenses', NULL, 1, Now(), NULL),
		(8, 'Wages, salaries and other staff costs', '', 1, Now(), NULL),
		(9, 'Interest on bank and other loans', '', 1, Now(), NULL),
		(10, 'Bank, credit card and other financial charges', '', 1, Now(), NULL),
		(11, 'Irrecoverable debts written off', '', 1, Now(), NULL),
		(12, 'Accountancy, legal, and other professional fees', '', 1, Now(), NULL),
		(13, 'Depreciation and loss/profit on sale of assets', '', 1, Now(), NULL),
		(14, 'Other business expenses', '', 1, Now(), NULL),
		(15, 'Construction industry - payments to subcontractors', '', 1, Now(), NULL),
		(16, 'Rents, rates, power and insurance costs  ', '', 1, Now(), NULL),
		(17, 'Repairs and renewals of property and equipment', '', 1, Now(), NULL);";
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
		  `country` int(11) default NULL COMMENT 'FK on country table',
		  `email` varchar(255) default NULL,
		  `telephone` varchar(25) default NULL,
		  `status` int(11) default '0' COMMENT 'FK on status table',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Suppliers - organisations who you have bought from';
		";
		// Run query
		$prepared_query = @$this->_db->prepare($query);
		@$this->_db->query($prepared_query);
		
		
		/*
		 Content for `outgoing_supplier`
		*/
		$query = "INSERT INTO `outgoing_supplier` (`id`, `title`, `description`, `status`, `date_added`, `date_edited`) VALUES
		(1, 'Mileage', 'Generic supplier used to quickly add mileage for travel by car/bike/foot', 1, Now(), NULL)";
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
		(1, 'Paypal', NULL, 1, Now(), NULL),
		(2, 'Bank transfer', 'Electronic payment from one bank account to another e.g. BACS, CHAPS or Faster Payments', 1, Now(), NULL),
		(3, 'Direct debit', NULL, 1, Now(), NULL),
		(4, 'Credit card', NULL, 1, Now(), NULL),
		(5, 'Debit card', NULL, 1, Now(), NULL),
		(6, 'Cash', NULL, 1, Now(), NULL),
		(7, 'Standing order', NULL, 1, Now(), NULL),
		(8, 'Cheque', NULL, 1, Now(), NULL);";
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
		  `vat_flat_rate_percentage` decimal(5,2) NOT NULL COMMENT 'The VAT flat rate percentage at the time of invoicing',
		  `status` int(11) DEFAULT '0' COMMENT 'FK on status table',
		  `payment_required` datetime DEFAULT NULL COMMENT 'Date when payment is due',
		  `payment_expected` date DEFAULT NULL COMMENT 'Date when you realistically expect to be paid',
		  `transaction_date` datetime DEFAULT NULL COMMENT 'Date when payment was made',
		  `invoice_date` date NOT NULL COMMENT 'Date the invoice was sent',
		  `date_added` datetime NOT NULL,
		  `date_edited` datetime DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `client` (`client`),
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
			('Invoice appendix', 'Default details about payment which appear at the bottom of invoices/proposals.', 20000, ' '),
			('VAT flat rate scheme percentage', 'If enrolled in the VAT Flat Rate Scheme then enter the percentage.', '50', '0'),
			('VAT flat rate scheme registration date', 'If enrolled in the VAT Flat Rate Scheme then enter the date you started (because first year is 1% less).', '50', '0')
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
				'Example city', 'EX1 1AA', 232, %s, '01234567890', 1, Now())";
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