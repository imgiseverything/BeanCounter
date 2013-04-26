<?php

/**
 *	DataFilter class
 *
 *	Set/get variables used to filter data in the CRUD object
 *	Used for pagination, sorting & searching through data
 *	
 *	@package		bean counter
 *	@since			27/01/2010
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
 *		setId
 *		setAction
 *		setFilters
 *		setFilter
 */
 
 
class DataFilter{

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
	 *	constructor
	 */
	public function __construct(){
		
		$this->setId();
		$this->setAction();	
		$this->setFilters();	
		
	}
	
	/**
	 *	setId()
	 *	Get the id (unique identifier) of a current element 
	 *	if there is one - usually this is in the url e.g.
	 *	example.com/section/8/
	 *	here '8' is the id. The Rewrite class sets this up as a
	 *	$_GET['id'] value
	 */
	public function setId($id = false){
		$this->_id = ($id === false) ? Application::getParameter('id') : $id;
	}	
	
	/**
	 *	setAction()
	 *	Work out what the suer is trying to do - this is usaully from a
	 *	form submission but may be from a URL value.
	 *	A list of actions can be found in Rewrite:_actions
	 *	most oftne it's add|delete|edit|etc
	 */
	public function setAction($action = false){
		$this->_action = ($action !== false) ? $action : Application::getParameter('action');
	}
	
	/**
	 *	setFilters()
	 *	initialise all the $_filter values so our Scaffold based
	 *	objects have default data to use.
	 */
	public function setFilters(){
		
		// Sort data by value
		$this->_filter['order_by'] = Application::getParameter('sort', 'date');

		
		// Search/query results - use to perform LIKE or 
		// FULLTEXT searches on ta given database table
		$this->_filter['search'] = Application::getParameter('search'); 
		
		// What page are we on? - usually page 1
		$this->_filter['current_page'] = Application::getParameter('page', 1); 
		
		// How many items to show per page?
		$this->_filter['per_page'] = Application::getParameter('show', 20); 
		
		// if someone wants to show all - give them 1000 (at most)
		// any more than that will cause issues
		$this->_filter['per_page'] = ($this->_filter['per_page'] == 'all' || $this->_filter['per_page'] > 1000) ? 1000 : $this->_filter['per_page'];
		
		// Item status (optional) e.g. live, deleted, suspended? 
		// [See the status table in the database for more details]
		$this->_filter['status'] = Application::getParameter('status', 'Active');
		
		// Show only published items ('past') or show all ('all') 
		// or show not yet published ('future')
		$this->_filter['tense'] = Application::getParameter('tense', 'past'); 
		
		// Timeframe (int) - only show data from predefined date ranges
		// e.g. 7 days
		$this->_filter['timeframe'] = Application::getParameter('timeframe');
		
		// Timeframes - custom:
		// only show data from a user 
		// specified start and end dates/times
		// see $timeframe_options below		
		
		$this->_filter['timeframe_custom']['start'] = sprintf("%04d-%02d-%02d 00:00:00", Application::getParameter('start_year'), Application::getParameter('start_month'), Application::getParameter('start_day'));
		
		$this->_filter['timeframe_custom']['end'] = sprintf("%04d-%02d-%02d 00:00:00", Application::getParameter('start_year'), Application::getParameter('start_month'), Application::getParameter('start_day'));
		
		
	}
	
	/**
	 *	setFilter()
	 *	Override/Create a $_filter value
	 *	@param	mixed	$array_key
	 *	@param	string	$array_value
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
	 *	getId()
	 *	@return int|string	$_id
	 */
	public function getId(){
		return $this->_id;
	}	
	
	/**
	 *	getAction()
	 *	@return string	$_action
	 */
	public function getAction(){
		return $this->_action;
	}
	
	/**
	 *	getFilters()
	 *	@return array	$_filter
	 */
	public function getFilters(){
		return $this->_filter;
	}
	
	/**
	 *	getFilter()
	 *	@return	string	$_filter[$array_key]
	 */
	public function getFilter($array_key){
		
		if(array_key_exists($array_key, $this->_filter) === true){
			return $this->_filter[$array_key];
		}
		
	}
	
	
	/**
	 *	dateFormat
	 *	take user friendly (UK) dd/mm/yyyy dates
	 *	and turn them into database friendly
	 *	yyyy-mm-dd values
	 *	@param	string	$user_date
	 *	@param	bool	$reverse
	 *	@return	String	$system_date
	 */
	public static function dateFormat($user_date, $reverse = false){
	
		$user_separator = '/';
		$system_separator = '-';
		
		if($reverse === true){
			$user_separator = '-';
			$system_separator = '/';
			$user_date = str_replace('00:00:00', '' , $user_date);
		}
		
		$arrDate  = explode($user_separator, $user_date);
		$system_date = trim($arrDate[2]) . $system_separator . trim($arrDate[1]) . $system_separator . trim($arrDate[0]);
		
		if($reverse === false){
			$system_date .= ' 00:00:00';
		} 
		
		// get ride of that werid anomaly where an empty date produces
		// -- or //
		$system_date = str_replace(array('//', '--'), '', $system_date);
		
		return trim($system_date);
		
	}
	
}

?>