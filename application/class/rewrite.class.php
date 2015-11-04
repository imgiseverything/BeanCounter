<?php
/**
 *  =========================================================================
 *		
 *	Rewrite Class
 *	-------------------------------------------------------------------------
 *		
 *	Take URL and inlcude relevant PHP page to gain effect of
 *	clean URLS e.g. www.example.com/clean/edit/45/ would include
 *		
 *	$_GET['action'] = 'edit';
 *	$_GET['id'] = 45;
 *	include('clean.php');
 *		
 *	but done with regular expressions - much like mod_rewrite would
 *		
 *	if a page doesn't exist feed it an error header, an error number via 
 *	$_GET and include the error.php page
 *	
 *	=========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 	2008-2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version		1.0	
 *	@author			philthompson.co.uk
 *	@since			25/01/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified		04/11/2015
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
 *	methods
 *		
 *		badURL
 *		setActions
 *		setIncludeFile
 *		getIncludeFile
 *		getURL
 *		getURLSize
 *		setQueryString
 *		getQueryString
 *		setHTTPHeader
 *		
 *	=========================================================================
 *	
 */
	
	
	class Rewrite{
	
		// Variables
		
		/**
		 * @var string
		 */
		protected $_includeFile;
		
		/**
		 * @var string
		 */
		protected $_url;
		
		/**
		 * @var string
		 */
		protected $_queryString;
		
		/**
		 * @var string
		 */
		protected $_urlSize = 0;
		
		/**
		 * @var array
		 */
		protected $_actions = array();
		
		/**
		 * @var string
		 */
		protected $_httpHeader = '';
		
		/**
		 *	Constructor
		 */
		public function __construct(){
			
			$this->badURL();
			
			$this->setActions();
			
			$this->setURL();
			$this->setIncludeFile();
			$this->setQueryString();
			$this->setHTTPHeader();
			
		}
		
		/** 
		 *	badURL()
		 *	recognise a bad (malformed) url and redirect (with correct headers) 
		 *	to the good alternative
		 */			
		public function badURL(){
			$error = 0; // error counter
			
			// double trailing slash
			if(strpos($_SERVER['REQUEST_URI'], '//') !== false){
				$error++;
				$redirect = str_replace('//', '/', $_SERVER['REQUEST_URI']);
			}
			
			// Error exists
			if($error > 0 ){
				header("HTTP/1.1 301 Moved Permanently");
				header('Location: ' . $redirect);
				exit;
			}
			
		}
		
		/**
		 *	setActions()
		 *	Create an array of 'actions' which are usually methods within an object
		 * 	so our URL can have example.com/section/optimise/id/
		 *	If 'optimise' is in the actions we now we need to do something with it
		 * 	If the term doesn't appear in this array then the URL is trying to view
		 * 	 a subfolder most probably
		 */
		protected function setActions(){
	
			$this->_actions = array(
				'add', 'approve', 'checkout', 'confirm', 'completed', 'delete', 'dispatch', 
				'download', 'duplicate', 'edit', 'ical', 'images', 'invoice', 'mark', 
				'password', 'pdf', 'quote', 'remittance', 'vat'
			);
		}
		
		/**
		 *	setURL()
		 *	break apart requested URL into composite parts
		 */
		protected function setURL(){
		
			// get URL (URI) from actual URI but remove query string elements
			// we'll add them back later
			$place_of_qs = strpos($_SERVER['REQUEST_URI'], '?');
			$url = ($place_of_qs !== false) ? substr($_SERVER['REQUEST_URI'], 0, $place_of_qs) : $_SERVER['REQUEST_URI'];
	
			// and split it up into its component parts
			$this->_url = explode('/', $url);
			
			// remove 1st empty field
			array_shift($this->_url);
		
		
			$i = 0; // counter
			
			// loop through URI components
			foreach($this->_url as $url_section){
		
				// remove file extensions
				$url_section = str_replace('.php', '', $url_section);
				
				// Check if this is a query string
				if(strpos($url_section,'=') !== false){
					// remove the query string form the URL array
					unset($this->_url[$i]);
				}
				$i++; // increment counter
				
			}
			
			
			// Reorder array removing empty fields
			array_values($this->_url);
			
			// how big is the URL array now? (FYI: Its size determines 
			// what we do with it)
			$this->_urlSize = sizeof($this->_url);

			//$last_url_item = array_pop($this->_url);
			// get last array value
			$last_url_item = $this->_url[($this->_urlSize-1)]; 
			
	
			// is the last value empty?
			if(empty($last_url_item)){
				// Yes the last value is empty so remove it
				//unset($this->_url[$this->_urlSize]);
				unset($this->_url[($this->_urlSize-1)]);
				// how big is the URL array now?
				$this->_urlSize = sizeof($this->_url);
			}
			
			// Is this is a paginated page?
			// Check to see if the last URL value starts with 'page' 
			// if it does we're paginating baby!
			if(!empty($this->_url[(sizeof($this->_url) - 1)]) && substr($this->_url[(sizeof($this->_url) - 1)], 0, 4) == 'page'){
				// create a page $_GET value based on the last value 
				// minus the  word 'page'
				$_GET['page'] = str_replace('page', '', $this->_url[(sizeof($this->_url) - 1)]);
				// remove the value from the url
				unset($this->_url[(sizeof($this->_url) - 1)]);
				// how big is the URL array now?
				$this->_urlSize = sizeof($this->_url);
			}
		
		}		
			
		/**
		 *	setIncludeFile()
		 *	Based on the URL, work out which file to include
		 *	this file will probably be a controller
		 */
		protected function setIncludeFile(){	
			
			// include different files depending on the size of the URL 
			switch($this->_urlSize){
				
				// Homepage
				default:
				case 0:
					$this->_includeFile = 'controllers/index.php';
					break;					
				// page.php aka /page/
				case 1:
				
					// does page.php exist? if so include it
					if(!empty($this->_url[0]) && file_exists(APPLICATION_PATH.'/controllers/' . $this->_url[0].'.php')){
						$this->_includeFile = 'controllers/' . $this->_url[0] . '.php';
					} else{
						// otherwise include category name/page name thats in use
						$_GET['error'] = 404;
						$this->_includeFile = 'controllers/error.php';						
					}
					
					break;
				// folder.page.php aka /folder/page/
				case 2:
					
					// check for category name/page name that is in use
					// otherwise include page.page.php
					if($this->_url[0] == 'ajax'){
						$this->_includeFile = 'ajax/'. $this->_url[1];
					} elseif($this->_url[0] == 'news'){
						$this->_includeFile = 'controllers/news.php';
					}
					elseif($this->_url[0] == 'accounts' && !in_array($this->_url[1], $this->_actions) && $this->_url[1] != 'details'){	
						$_GET['type'] = $this->_url[1];		
						$this->_includeFile = 'controllers/accounts.php';
					}
					// folder.page.php exists
					elseif(file_exists(APPLICATION_PATH . '/controllers/' . $this->_url[0] . '.' . $this->_url[1] . '.php')){
						$this->_includeFile = 'controllers/' . $this->_url[0] . '.' . $this->_url[1] . '.php';
					}
					// this URL contains an action e.g. add or edit
					elseif(in_array($this->_url[1], $this->_actions)){
						$_GET['action'] = $this->_url[1];
						$this->_includeFile = 'controllers/' . $this->_url[0] . '.php';
					}
					// this URL contains an id as the 2nd variable must be viewing an object by ID
					else if(is_numeric($this->_url[1])){
						$_GET['id'] = $this->_url[1];
						$this->_includeFile = 'controllers/' . $this->_url[0] . '.php';
					} else if(file_exists(APPLICATION_PATH . '/' . $this->_url[0] . '/' . $this->_url[1])){
						$this->_includeFile = $this->_url[0] . '/' . $this->_url[1];
					} else if(file_exists(APPLICATION_PATH . '/controllers/' . '/' . $this->_url[0] . '/' . $this->_url[1])){
						$this->_includeFile = 'controllers/' . '/' . $this->_url[0] . '/' . $this->_url[1];
					} else{
							$_GET['error'] = 404;
							$this->_includeFile = 'controllers/error.php';
					}
					break;
				// folder.subfolder.page.php aka /folder/subfolder/page/
				case 3:
				
					// check for category name/page name thats in use
					// otherwise include page.php
					//news
					if($this->_url[0] == 'news'){
						$this->_includeFile = 'controllers/news.php';
					}
					// Downloads
					elseif($this->_url[0] == 'download'){
						# Downloads
						$_GET['type'] = $this->_url[1];
						$_GET['id'] = $this->_url[2];
						$this->_includeFile = 'controllers/download.php';
					}
					elseif(in_array($this->_url[1], $this->_actions) && is_numeric($this->_url[2])){
						$_GET['action'] = $this->_url[1];
						$_GET['id'] = $this->_url[2];
						$this->_includeFile = 'controllers/' . $this->_url[0]. '.php';
					}	
					// this URL contains an action e.g. add or edit
					elseif(in_array($this->_url[1], $this->_actions) && ($this->_url[1] == 'pdf' || $this->_url[1] == 'download') && !is_numeric($this->_url[2])){
						$_GET['action'] = $this->_url[1];
						$_GET['id'] = $_GET['id_hash'] = $this->_url[2];
						$this->_includeFile = 'controllers/' . $this->_url[0]. '.php';
					}					
					// Bookings - view by year e g /bookings/YYYY/MM/
					else if($this->_url[0] == 'bookings' && is_numeric($this->_url[1]) &&  strlen($this->_url[1]) == 4 && is_numeric($this->_url[2]) && strlen($this->_url[2]) == 2){
						$_GET['start_year'] = $this->_url[1];
						$_GET['start_month'] = $this->_url[2];
						$this->_includeFile = 'controllers/bookings.php';
					}
					// this URL contains an action e.g. add or edit
					elseif(in_array($this->_url[2], $this->_actions)){
						$_GET['action'] = $this->_url[2];
						$this->_includeFile = 'controllers/' . $this->_url[0] . '.' . $this->_url[1].'.php';
					}
					// this is just a normal folder/page structure
					else{
						// folder.subfolder.page.php exists
						if(file_exists(APPLICATION_PATH.'/controllers/' . $this->_url[0]. '.' . $this->_url[1] . '.' . $this->_url[2].'php')){
							$this->_includeFile = $this->_url[0] . '.' . $this->_url[1] . '.' . $this->_url[2].'php';
						}
						// folder/subfolder/id
						elseif(file_exists(APPLICATION_PATH.'/controllers/' . $this->_url[0] . '.' . $this->_url[1].'.php') && is_numeric($this->_url[2])){
							$_GET['id'] = $this->_url[2];
							$this->_includeFile = 'controllers/' . $this->_url[0] . '.' . $this->_url[1].'.php';
						} else {
							$_GET['error'] = 404;
							$this->_includeFile = 'controllers/error.php';
						}
						
					}
					break;
				// folder.subfolder.page.page.php aka /folder/subfolder/page/page/ or /news/YYYY/MM/title/
				case 4:
					// this URL contains an action e.g. add or edit
					if(in_array($this->_url[2], $this->_actions) && is_numeric($this->_url[3])){
						$_GET['action'] = $this->_url[2];
						$_GET['id'] = $this->_url[3];	
						$this->_includeFile = 'controllers/' . $this->_url[0] . '.' . $this->_url[1] . '.php';
					} else{
						$_GET['year'] = $this->_url[1];
						$_GET['month'] = $this->_url[2];
						$this->_includeFile = 'controllers/news.php';
					}
					break;
			
			}
			// we're looking at the rewrite.php page - which isn't allowed
			if($this->_includeFile == 'rewrite.php'){
				$_GET['error'] = 403;
				$this->_includeFile = 'controllers/error.php';
			}
			
		}
		
		
		/**
		 *	setQueryString()
		 *	recreate the Query String as a $_GET array because boot strapping
		 * the app ahs killed the query string variables :(
		 */ 
		public function setQueryString(){
		
			 $this->_queryString = $_SERVER['QUERY_STRING'];
			 
			// Query string
			if($this->_queryString){
				$query_string = explode('&', $this->_queryString);
				foreach($query_string as $_get){
					// item is a query string so split it up and work out its values
					$_get_array = explode('=',$_get);
					if(!empty($_get_array[1])){
						$_GET[$_get_array[0]] = $_get_array[1];
					}
				}
			}
			
		}
		
		/**
		 *	setHTTPHeader()
		 *	set different HTTP headers for different situations
		 *	e.g. 404 for a page not found.
		 */
		public function setHTTPHeader(){

			// page error exist so set a HTTP header
			if(!empty($_GET['error'])){
			
				switch($_GET['error']){
				
					default:
					case '200':
						header("HTTP/1.0 200 OK");
						break;

					case '404':
						header("HTTP/1.0 404 Not Found");
						break;

					case 403:
						header("HTTP/1.0 403 Unauthorized");
						break;
						
				}
				
			} 
		}
		
		
		
		/**
		 *	getIncludeFile()
		 */
		public function getIncludeFile(){
			return $this->_includeFile;
		}
		
		/**
		 *	getURL()
		 */
		public function getURL(){
			return $this->_url;
		}
		
		/**
		 *	getURLSzie()
		 */
		public function getURLSize(){
			return $this->_urlSize;
		}
		
		/**
		 *	getActions()
		 */
		public function getActions(){
			return $this->_actions;
		}
		
		/**
		 *	getQueryString()
		 */
		public function getQueryString(){
			return $this->_queryString;
		}
		
		
		
	
		
	}

?>