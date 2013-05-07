<?php
/**
 *	 =================================================================================
 *	 
 *	 Bean Counter settings file
 *	 ---------------------------------------------------------------------------------
 *	 
 *	 This file is included at the top of every file.
 *	 It is essentially an initialisation file for the entire
 *	 Bean Counter system.
 *	 
 *	 =================================================================================
 *	
 */
 

 
 	// Include necessary common function files
	require_once(APPLICATION_PATH . '/inc/functions.php'); 
	// Application Class
	require_once(APPLICATION_PATH . '/class/application.class.php'); 
	$objApplication = new Application();

	
	// Maintenance object - set to true to turn the whole site 
	// into a holding page
	$objMaintenance = new Maintenance(false);


	// Grab website owner's configuration settings
	if(file_exists(BEANCOUNTER_PATH . '/config.php')){
		require_once(BEANCOUNTER_PATH . '/config.php');
	} else{
		// Config file is absent :(
		die("<html><head><title>" . $objApplication->getApplicationName() . " Error</title></head><body><style>body{background: #fff; color: #333; text-align: center; margin: 0 auto; width: 620px;}</style></head><body><div id=\"holder\"><h1>Configuration file (<var>" . BEANCOUNTER_PATH . "/config.php</var>) is missing</h1><h2>Please add it with the correct values in place</h2></div></body></html>");
	}

	/**
	 * Error object
	 * ---------------------------------------------------------------------------------
	 * Take errors and decide what to do with them. Needs the xhandler function 
	 * in the functions file (/inc/functions.php)  to exist in order to work
	 */		
	$objError = new Error($objApplication);
	set_error_handler('xhandler', E_ALL);

	/**
	 * Database
	 * ---------------------------------------------------------------------------------
	 * The whole site runs off a MySQL database. The settings for
	 * it are in the config.php file.	
	 */
	// Database connection
	require_once(LIBRARY_PATH . '/ezSQL/ezsql.class.php');

	
	// Initialise a database object
	$db = new ezSQL($username, $password, $database, $sqlserver);
	
	// Debugging mode - echo all queries - !!!DON'T ALLOW ON LIVE SITE!!!
	if(isset($_GET['DEBUG']) && MODE != 'live'){
		$db->debug_all = true;
	}
	
	/*
	 *	Initialise site configurations object 
	 *	based on a (hopefully, cached) object text file
	 */
	$objSite = new Site($db, $objApplication);

	// Website defining settings
	require_once(APPLICATION_PATH . '/inc/def.inc.php');

	
	/**
	 * User feedback
	 * ---------------------------------------------------------------------------------
	 * Setup user feedback values. The function drawFeedback($user_feedback)
	 * uses this data to provide messgaes to the user.
	 * Most objects that process data actually return these values
	 * e.g. $user_feedback = $objObject->doStuff();
	 */
	$user_feedback = $objApplication->getUserFeedback();
	
	
	/**
	 * Sessions
	 * ---------------------------------------------------------------------------------
	 * Start session - if not already started - avoid errors
	 * Sessions are like cookies, they keep track of important information around the 
	 * site e.g. usernames/passwords
	 */
	if(!isset($_SESSION)){
		session_start();
	}
	
	/**
	 * Authorisation
	 * ---------------------------------------------------------------------------------
	 * All areas, require users to be authorised to be able to view them
	 * (except the install page)
	 */	
	$objAuthorise = new Authorise($objApplication);

	
	/**
	 * Variables required by all objects
	 * ---------------------------------------------------------------------------------
	 * Nearly all objects require the same variables for filtering, sorting, showing the 
	 * results. These variables need default but can also be manipulated by the 
	 * URL ($_GET) or $_POST hence the use of $_REQUEST - but this may need rethinking
	 */
	
	/**
	 *	@var id: int or string
	 *	the id of a requested object - usually a primary key (int) 
	 *	in a table row
	 */
	$id = $objApplication->getId();
	
	/**
	 *	@var action: string
	 *	Are we doing something eg. adding, editing or deleting something
	 */
	$action = $objApplication->getAction();
	
	
	/**
	 * Object filters
	 * -----------------------------------------------------------------------------------
	 * All of bean counter's object's constructors use a $filter array to determine which 
	 * data to show
	 * e.g. are we showing all the rows in the database table or just 20 and how are they 
	 * ordered by date, etc?
	*/

	$objApplication->setFilters($objAuthorise);
	$filter = $objApplication->getFilters();
	
	/**
	 * Quick note
	 * ---------------------------------------------------------------------------------
	 * The whole $_REQUEST usage - I'm not too sure how good idea it was of mine to use 
	 * $_REQUEST so much... perhaps I should investigate looking for $_GET but then 
	 * overruling $_GET variables with identically named $_POSTs
	 * e.g. read($_POST,'var',read($_GET,'var','default value'))
	 */
	
	// Sort object data by values options: 
	// used to create a <select> drop down
	$sort_options = array(
		'newest'		=> 'Most recently added', 
		'oldest' 		=> 'Least recently added', 
		'last_edited' 	=> 'Most recently edited',
		'title_az'		=> 'Title (A-Z)', 
		'title_za' 		=> 'Title (Z-A)'
	);
	
	// Show data relating to set a timeframe:
	// values equal umber of days so 7 is a week
	// see $filter['timeframe']
	$timeframe_options = array(
		'1' 	=> 'Today',
		'7' 	=> 'This week',
		'30' 	=> 'This month',
		'90' 	=> 'Last 3 months',
		'183' 	=> 'Last 6 months',
		'365' 	=> 'Last 12 months',
		'' 		=> 'All time',
	);	

	$objDateFormat = new DateFormat();
	
	/**
	 * Initialise HTML template object
	 * ---------------------------------------------------------------------------------
	 * How the site will look, which area we're in e.g. admin area
	 * work out the appearance, the <title>s, CSS, JavaScript, and key page elements 
	 * e.g. header, footer.
	 */
	$objTemplate = new Template($objApplication);

	/**
	 * Initialise Menu object
	 * ---------------------------------------------------------------------------------
	 * Create website menus e.g.
	 * Navigation, Breadcrumbs, etc
	 */
	$objMenu = (substr($_SERVER['REQUEST_URI'], 0, 8) != '/install') ? new Menu($db, $objTemplate, $objApplication,$objTree) : '';
	
	/**
	 * Initialise Vcard object
	 * ---------------------------------------------------------------------------------
	 * Use for vcard (microformat) version of our name/address
	 */
	$objVcard = new Vcard();
	
	
	
	/**
	 * Textile - HTML formatting
	 */
	require_once(LIBRARY_PATH . '/textile/textile.class.php');
	$objTextile = new Textile();
	
	
	/**
	 *	Redirect user sin the admin area when they make an options selction e.g.
	 *	edit this item
	 */
	if($objApplication->getParameter('options_go')){
		redirect(urldecode($objApplication->getParameter('options_go')));
	}
	
/**
 * End of settings file, wasn't that fun?
 */
?>