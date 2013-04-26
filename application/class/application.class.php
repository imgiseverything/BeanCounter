<?php
/**
 *	=========================================================================
 *	
 *	Application Class
 *	-------------------------------------------------------------------------
 *	Manage the setup and parts of the application.
 *	Define constants and default settings
 *	
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
 *	@since		05/01/2009
 *	
 *	edited by:  Phil Thompson
 *	@modified	12/04/2009
 *	
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
 *		startTimer
 *		setSiteUrl
 *		setId
 *		setAction
 *		setFilters
 *		setFilter
 *		setUserFeedback
 *		setLocation
 *		setViewFolder
 *		getters
 *		getFilter
 *		getParameter
 *	
 *		
 *	
 *	=========================================================================
 */

	class Application{
	
		// Variables
		
		/**
		 *	@var string or int (int more likely)
		 *	the id of a requested object: 
		 *	usually a primary key (int) in a table row
		 */
		private $_id;
		 
		/**
		 *	@var string
		 *	Are we doing something e.g. adding, 
		 *	editing or deleting something
		 */
		private $_action;
		 
		/**
		 *	@var array
		 *	All of bean counter's object's constructors use a 
		 *	$this->_filter array to determine which data to show 
		 *	e.g. are we showing all the rows in the database table 
		 *	or just 20 and how are they ordered by date, etc?
		 */ 
		private $_filter = array();
		 
		/**
		 *	@var string
		 */
		private $_applicationName = 'Bean Counter';
		
		/**
		 *	@var string
		 */
		private $_applicationUrl = 'http://beancounterapp.com/';
		
		/**
		 *	@var string
		 */
		private $_siteUrl;
		
		/**
		 *	@var microtime()
		 *	Used to time page load
		 */
		private $_start;
		
		/**
		 *	@var array()
		 *	user friendly feedback
		 */
		private $_userFeedback = array();
		
		/**
		 *	@var string
		 */
		private $_location;
		
		/**
		 *	@var string
		 */
		private $_viewFolder;

		
				
		/**
		 *	Constructor
		 */
		public function __construct(){			
			$this->startTimer();
			$this->setSiteUrl();	
		
			$this->setId();
			$this->setAction();	
			//$this->setFilters();			
			$this->setUserFeeback();	
			
			// Normal, Mobile or admin
			$this->setLocation();
			
			$this->setViewFolder();
		}
		
		// Methods
		
		/**
		 *	startTimer()
		 */
		private function startTimer(){
			$this->_start =  microtime();
		}
		
		/**
		 *	setSiteUrl()
		 *	Put current URL into handy defined variable
		 */
		private function setSiteUrl(){
			$this->_siteUrl = $_SERVER["SERVER_NAME"];
		}
		
		/**
		 *	setId()
		 */
		public function setId($id = false){
			$this->_id = ($id === false) ? $this->getParameter('id') : $id;
		}	
		
		/**
		 *	setAction()
		 */
		public function setAction($action = false){
			$this->_action = ($action !== false) ? $action : $this->getParameter('action');
		}
		
		/**
		 *	setFilters()
		 */
		public function setFilters($objAuthorise){
			
			// Sort data by value
			$this->_filter['order_by'] = $this->getParameter('sort', 'date');
			
			// Search/query results - use to perform LIKE or 
			// FULLTEXT searches on the database table
			$this->_filter['search'] = clean_xss($this->getParameter('search')); 
			
			// What page are we on? - usually page 1
			$this->_filter['current_page'] = $this->getParameter('page', 1); 
			
			// How many items to show per page?
			$this->_filter['per_page'] = $this->getParameter('show', 20); 
			
			// if someone wants to show all - give them 10000 (at most)
			// any more than that will cause issues
			$this->_filter['per_page'] = ($this->_filter['per_page'] == 'all') ? 10000 : $this->_filter['per_page'];
			
			// Item status (optional) e.g. live, deleted, suspended? 
			// [See the status table in the database for more details]
			$this->_filter['status'] = $this->getParameter('status', 1);
			
			// show only published items ('past') or show all ('all') 
			// or show not yet published ('future')
			$this->_filter['tense'] = $this->getParameter('tense', 'past'); 
			
			// Timeframe (int) - only show data from predefined date ranges
			// e.g. 7 days
			$this->_filter['timeframe'] = $this->getParameter('timeframe');
			
			// Timeframes - only show data from a user 
			// specified start and end dates/times
			// see $timeframe_options below
			$this->_filter['timeframe_custom']['start'] = $this->getParameter('start_year') . '-' . $this->getParameter('start_month') . '-' . $this->getParameter('start_day') . ' 00:00:00';
			$this->_filter['timeframe_custom']['end'] = $this->getParameter('end_year', date('Y')) . '-' . $this->getParameter('end_month', date('m')) . '-' . $this->getParameter('end_day', date('d')) . ' 23:59:59';
			
			
			
			// Client
			$this->_filter['client'] = $this->getParameter('client');
			
			if(isset($objAuthorise) && $objAuthorise->getLevel() == 'Basic'){
				$this->_filter['client'] = $objAuthorise->getClient();
			}
			
			
		}
		
		/**
		 *	setFilter()
		 */
		public function setFilter($array_key, $array_value){
			
			if(is_array($array_key)){
				$key = $array_key[0];
				$childKey = $array_key[1];
				$this->_filter[$key][$childKey] = $array_value;
			} else{
				$this->_filter[$array_key] = $array_value;
			}
			
		}
		
		
		
		/**
		 *	setUserFeedback()
		 * 	Setup user feedback values. The function 
		 *	drawFeedback($user_feedback)
		 *	uses this data to provide messgaes to the user.
		 *	Most objects that process data actually return these values
		 *	e.g. $user_feedback = $objObject->doStuff();
		 */
		public function setUserFeeback($user_feedback = array()){
		
			if(empty($user_feedback)){
				$this->_userFeedback['type'] = $this->getParameter('type');
				$this->_userFeedback['content'] = $this->getParameter('content');
			} else{
				$this->_userFeedback['type'] = $user_feedback['type'];
				$this->_userFeedback['content'] = $user_feedback['content'];
			}
		}
		
		/**
		 *	setLocation()
		 *	determine website location
		 *	either: 'admin', 'mobile' or 'site'
		 */
		private function setLocation(){
		
			$arrDomain = explode('.', $_SERVER['HTTP_HOST']);
			$subdomain = $arrDomain[0];
			
			switch($subdomain){
				
				case 'admin':
					$this->_location = 'admin';
					break;
				
				case 'i':
				case 'iphone':
				case 'm':
				case 'mobile':
					$this->_location = 'mobile';
					break;
				
				default:	
				case 'site':
					$this->_location = 'site';
					break;
			}
		
		}
		
		
		/**
		 *	setViewFolder()
		 *	Work out which view folder to use based on location
		 */
		private function setViewFolder(){
		
			$this->_viewFolder = APPLICATION_PATH . '/views/';
			
			switch($this->_location){
			
				/*case 'admin':
					$this->_viewFolder .= 'admin/';
					break;*/
				
				case 'mobile':
					$this->_viewFolder .= 'mobile/';
					break;
				
				default:
				case 'site':
					$this->_viewFolder .= '';
					break;
			}
			
			
		}
		
		
		
		/**
		 *	getStartTime()
		 */
		public function getStartTime(){
			return $this->_start;
		}
		
		/**
		 *	getApplicationName()
		 */
		public function getApplicationName(){
			return $this->_applicationName;
		}
		
		/**
		 *	getApplicationUrl()
		 */
		public function getApplicationUrl(){
			return $this->_applicationUrl;
		}
		
		/**
		 *	getSiteUrl()
		 */
		public function getSiteUrl(){
			return $this->_siteUrl;
		}		
		
		
		/**
		 *	getId()
		 */
		public function getId(){
			return $this->_id;
		}	
		
		/**
		 *	getAction()
		 */
		public function getAction(){
			return $this->_action;
		}
		
		/**
		 *	getFilters()
		 */
		public function getFilters(){
			return $this->_filter;
		}
		
		/**
		 *	getFilter()
		 */
		public function getFilter($array_key){
			
			if(array_key_exists($array_key, $this->_filter) === true){
				return $this->_filter[$array_key];
			}
			
		}
		
		/**
		 *	getParameter()
		 *	Grab an element from the $_POST or $_GET array
		 *	$_POST trumps $_GET
		 *	@param string $key e.g. $_POST[$key]
		 *	@param string $default (NULL)
		 */
		public function getParameter($key, $default = ''){
			
			return read($_POST, $key, read($_GET, $key, $default));
			
		}
		
		/**
		 *	getUserFeedback()
		 */
		public function getUserFeedback(){
			return $this->_userFeedback;
		}
		
		/**
		 *	getLocation()
		 */
		public function getLocation(){
			return $this->_location;
		}
		
		/**
		 *	getViewFolder()
		 */
		public function getViewFolder(){
			return $this->_viewFolder;			
		}
		
	
	
	}
?>