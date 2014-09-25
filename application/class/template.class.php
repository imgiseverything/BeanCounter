<?php
/**
 *	=========================================================================
 *	
 *	HTML Template Class
 *	-------------------------------------------------------------------------
 *	
 *	This class takes variables set out for each view and converts the 
 *	into meaningful data values for inclusion in HTML templates.
 *	Date includes e.g. page <title>s or which CSS to show on each page.
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
 *	@since		10/01/2008
 *	
 *	edited by: 	Phil Thompson
 *	@modified	24/09/2014
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
 *		setMode
 *		setHeaderHTML
 *		setFooterHTML
 *		setTitle
 *		setDescription
 *		setBodyClass
 *		setStyle
 *		setExtraStyle
 *		setBehaviour
 *		setExtraBehaviour
 *		setCurrentURL
 *		setDoctype
 *		setRobots
 *		setLanguage
 *		setRSSFeed
 *		setStats
 *		setCopyright
 *		setWelcomeNote
 *		setBranding
 *		setSearchForm
 *		setForm
 *		getForm
 *		
 *	=========================================================================
 *
 */


	class Template{
	
		// Variables
		
		const TITLE_SEPARATOR = ' | ';		
		
		/**
		 *	@var string
		 */
		protected $_mode;
		
		/**
		 *	@var string
		 */
		protected $_title;
		
		/**
		 *	@var string
		 */
		protected $_description;
		
		/**
		 *	@var string
		 */
		protected $_rssFeed;	
		
		/**
		 *	@var string
		 */
		protected $_bodyClass;
		
		/**
		 *	@var string
		 */
		protected $_style;
		
		/**
		 *	@var string
		 */
		protected $_extraStyle;
		
		/**
		 *	@var string
		 */
		protected $_behaviour;
		
		/**
		 *	@var string
		 */
		protected $_extraBehaviour;
		
		/**
		 *	@var string
		 */
		protected $_currentUrl;
		
		/**
		 *	@var string
		 */
		protected $_menu;
		
		/**
		 *	@var string
		 */
		public $_submenu;
		
		/**
		 *	@var string
		 */
		protected $_bodyId;
		
		/**
		 *	@var string
		 */
		protected $_doctype;
		
		/**
		 *	@var string
		 */
		protected $_robots;
		
		/**
		 *	@var string
		 */
		protected $_charset;
		
		/**
		 *	@var string
		 */
		protected $_language;
		
		/**
		 *	@var string
		 */
		protected $_stats;
		
		/**
		 *	@var string
		 */
		protected $_Branding;
		
		/**
		 *	@var string
		 */
		protected $_welcomeNote;
		
		/**
		 *	@var string
		 */
		protected $_copyright;
		
		/**
		 *	@var boolean
		 */
		protected $_homepage = false;
		
		/**
		 *	@var boolean
		 */
		protected $_adminArea = false;
		
		/**
		 *	@var string
		 */
		protected $_templateFolder;
		
		/**
		 *	@var object
		 */
		private $authorise;	
		
		/**
		 *	@var object
		 */
		protected $_application;

		/**
		 *	@var string
		 */
		protected $_form;
		
		/**
		 *	@var string
		 */
		protected $_favicon;
		
		
		/**
		 *	Constructor
		 *	@param object $objApplication
		 */
		public function __construct($objApplication){
		
			$page_details = '';
			
			$this->_application = $objApplication;
			
			$this->setMode(read($_GET, 'mode', NULL)); // set mode, normal, mobile, iphone, popup, print etc
			
	
			// Page specific variables
			$this->setTitle(); // Page title used for SEO, usability, accessibility
			$this->setDescription(); // Meta description used in SEO
			$this->setFavicon();
			$this->setBodyClass(); // Class attribute for body used for CSS
			$this->setExtraStyle(); // CSS (on page within <style> tags)
			$this->setStyle(); // CSS (imported)
			$this->setExtraBehaviour();  // JavaScript (on page within <script> tags)
			$this->setBehaviour(); // JavaScript (externally linked)
			$this->setCurrentURL(); // what page re we on - used for pagination/links
			$this->setMenu(); // main menu (navigation)
			$this->setSubmenu(); // submenu (navigation)
			$this->setStats(); // website's statistics tracking
			
			// is this the homepage? It may need different things applied
			$this->_homepage = ($this->_bodyClass == 'home') ? true : false;
			
			// are we in the admin area? Instructions in forms will be different	
			$this->_adminArea = ($this->_application->getLocation() == 'admin') ? true : false;
			
			$this->setTemplateFolder();	
			$this->setHeaderHTML();
			$this->setFooterHTML();
			
	
			// Website specific variables - these values will be the same on all pages
			$this->_bodyId = str_replace('.', '-', $this->_application->getSiteUrl()); // id attribute for body tag used for user stylesheets/CSS
			$this->setRobots(); // should the page be visible to search engines?
			$this->setCharset();// what chracter set is the page? depends on what the user has set as language in settings.xml
			$this->setLanguage(); // what language is the page? depends on what the user has set in settings.xml
			$this->setBranding(); // website name + Logo
			$this->setCopyright();
			
			$this->setWelcomeNote(); // welcome note for front-end saying hello to custoemr or providing login links
			
			
			
		}
		
		/**
		 *	setMode
		 *	set a website mode (normal|mobile|iphone|popup|print|etc)
		 *	we use this to detemrine which templates to alter appearance
		 *	or show/hide items e.g. if it's AJAX we hide a lot of stuff :)
		 */
		public function setMode($value = false){
		
			$this->_mode = (!empty($value)) ? $value : 'normal';

			// An AJAX request has been performed
			if( 
				(isset($_SERVER['X-Requested-With']) && $_SERVER['X-Requested-With'] == 'XMLHttpRequest') 
				|| 
				(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest') 
				&& $this->_mode != 'ajax'
			){
				$this->_mode = 'ajax';
			}
			
		}
		
		/**
		 *	setTemplateFolder
		 *	Set the folder (directory) where templates are stored
		 */
		public function setTemplateFolder(){
			global $objSite, $action;
			
			$this->_templateFolder = $this->_application->getViewFolder() . 'layout/';
			
			if($this->_adminArea !== true || (($action == 'invoice' || $action == 'dispatch') && $_SERVER['REQUEST_METHOD'] == 'POST')){
				$this->_templateFolder .= ((!empty($objSite->config['Theme']) && $objSite->config['Theme'] == 'default') || empty($objSite->config['Theme'])) ? '' : 'themes/' . $objSite->config['Theme'] . '/';
			}
			
			
		}
		
		/**
		 *	setHeaderHTML
		 */
		public function setHeaderHTML(){
			
			switch($this->_mode){
			
				default:
					$this->_headerHTML = $this->_templateFolder . 'header.php';
					break;
					
				case 'popup':
					$this->_headerHTML = $this->_templateFolder . 'header_popup.php';
					break;
					
				case 'ajax':
					$this->_headerHTML = $this->_templateFolder . 'header_ajax.php';
					break;
					
			}
			
		}
		
		/**
		 *	setFooterHTML
		 */
		public function setFooterHTML(){
			
			switch($this->_mode){
			
				default:
					$this->_footerHTML = $this->_templateFolder . 'footer.php';
					break;
					
				case 'popup':
					$this->_footerHTML = $this->_templateFolder . 'footer_popup.php';
					break;
					
				case 'ajax':
					$this->_footerHTML = $this->_templateFolder . 'footer_ajax.php';
					break;
					
			}
		}
		
		/**
		 *	setTitle
		 *	set the page title: no title present, show the site's tag line.
		 *	this value is ***so*** important for SEO
		 */
		public function setTitle($value = false){

			$this->_title = ($value) ? $value : TAG_LINE;
			$this->_title .= ($this->_adminArea === true) ? self::TITLE_SEPARATOR . SITE_NAME . ' Admin area' : self::TITLE_SEPARATOR . SITE_NAME;
			
		}
		
		/**
		 *	setDescription
		 *	set a META description (used as site description in Google) 
		 *	if it has been set on the view page	
		 */
		public function setDescription($value = false){	
		
			$this->_description = ($value) ? '<meta name="description" content="' . $value . '" />' . "\n" : '';
			
		}	
		
		/**
		 *	setBodyClass
		 *	create a class name for the HTML <body> tag for CSS styling &
		 * 	javaScript hooks
		 *
		 *	@param $value - predefined class name (string)
		 *
		 *	if a defined value is missing, we calculate it from the URL e.g.
		 *	example.com/folder/sub-folder/page/id/
		 *	should be <body class="folder sub-folder page">
		 */
		public function setBodyClass($value = false){
		
			global $objRewrite; // sorry mum :(

			if(!$value && $this->_mode != 'popup'){
			
				// get the URL
				//$URL = explode('/',str_replace('?','',$_SERVER['REQUEST_URI']));
				if(!$objRewrite){
					$objRewrite = new Rewrite();
				} // end if
				
				// get the php page name e.g. 'products' or 'page' or 'basket'
				$url_prefix = ($_SERVER['PHP_SELF'] == '/rewrite.php') ? $objRewrite->getIncludeFile() : $_SERVER['PHP_SELF'];
				$url_prefix = str_replace(array('/', '.php', '.html'), '', $url_prefix);
				
				$url = $objRewrite->getURL();
				// add all array items together, separate with a space and remove starting and ending spaces
				$this->_bodyClass = trim(join(' ', $url));
				
				
				// is this the homepage?
				$this->_bodyClass = (!$this->_bodyClass && $url_prefix == 'index') ? 'home' : $this->_bodyClass;
				
				// is this the admin homepage?
				$this->_bodyClass .= ($this->_bodyClass == 'admin') ? ' home' : '';
				// add url prefix
				$this->_bodyClass = ($url_prefix != 'index' && $url_prefix != $this->_bodyClass) ? $url_prefix.' '.$this->_bodyClass : $this->_bodyClass;
				
				// add query string values
				$query_String = $objRewrite->getQueryString();
				if(!empty($query_string)){
				
					// loop through all URL variables
					foreach($_GET as $key => $value){
					
						// add QS value if it doesn't already exist in the body class
						$this->_bodyClass .= ((!in_array($key, $url)) && !in_array($value, $url) && !empty($value)) ? ' ' . $value : '';
						
					}
					
				}
				
				
			} elseif($this->_mode == 'popup'){
				// we're in popup mode so append it to value
				$this->_bodyClass = (substr($_SERVER['REQUEST_URI'], 0, 7) == '/admin/') ? 'admin popup' : 'popup';
			}  else{
				// a class has been pre-defined by the view
				$this->_bodyClass = $value;
			} // end else
			
		}
		
		/**
		 *	setStyle()
		 *	Create the the CSS <link />s or @imports to incldue the CSS
		 *	on a template. If we're in lvie mode -> send the CSS
		 *	to be minfied and chained via the compressor controller
		 *	@see /application/controllers/compressor.php
		 *	
		 *	@param array $value
		 */
		public function setStyle($value = array()){
		
			global $objSite; // sorry mum :(
		
			$value = (!$value) ? array() : $value;
			
			$value = (!is_array($value)) ? explode(',', $value) : $value;
			
			$action = $this->_application->getAction();
			
			$style_folder = '/style/';
			
			if($this->_adminArea !== true || (($action == 'invoice' || $action == 'dispatch') && $_SERVER['REQUEST_METHOD'] == 'POST')){
				$this->_styleFolder = ((!empty($objSite->config['Theme']) && $objSite->config['Theme'] == 'default') || empty($objSite->config['Theme'])) ? '/style/' : '/templates/themes/' . $objSite->config['Theme'] . '/style/';
			}
			
			// include default CSS that all pages must have			
			//array_unshift($value, 'main.min');
			
			// add mode (e.g. popup ) CSS
			if($this->_mode != 'normal'){
				$value[] = $this->_mode;
			}
			
			// compressed style array variable
			$compressed_style = array();
			
			
			if(is_array($value)){
				
				$this->_style = '';;
				
				// loop through items
				foreach($value as $item){
					// script is external e.g. begins with http or https
					if(strpos($item, 'http') !== false){
						$this->_style .= '<link rel="stylesheet" href="' . trim($item) . '" />' . "\n";
					} else{
						// if we're in live mode, create an array of items - 
						// because we'll join them together to save HTTP requests
						/*if(MODE == 'live'){
							$compressed_style[] = trim($item) . '.css';
						} else{*/
							// we're not live so just list them as normal
							$this->_style .= '<link rel="stylesheet" href="' . $style_folder . trim($item) . '.css" />' . "\n";
						//}
					}
				}
				// if we're in live mode, join all these files and import them in one go
				/*if(MODE == 'live'){
					$this->_style .= '<link rel="stylesheet" href="/compressor/?type=css&amp;files=' . join(',', $compressed_style) . '" />;' . "\n";
				}*/
				
				//$this->_style .= '</style>' . "\n";
			}
			

			// append extra style (if any) to the end
			//$this->_style .= $this->_extraStyle;
			
			/**
			 *	SUGGESTED IMPROVEMENTS:
			 *	The CSS could be minified and/or chained together on the fly on the live server
			 *	to increase server performance and user experience
			 */
			
		}
		
		/**
		 *	setExtraStyle
		 *	on page CSS - CSS is 100% specific to this individual page and doesn't 
		 *	warrant its own external stylesheet or to be in the global CSS
		 *	@param (string|boolean) $value (FALSE)
		 */
		public function setExtraStyle($value = false){
			
			if(!empty($value)){
				// value exists create HTML tags
				$this->_extraStyle = '<style>' . "\n";
				$this->_extraStyle .= "\t" . reduceFileSize($value) . "\n";
				$this->_extraStyle .= '</style>' . "\n";
			} else{
				// no value == no extra style
				$this->_extraStyle .= '';
			}
			
			// append extra style (if any) to the end
			$this->_style .= $this->_extraStyle;
		}
		
		/**
		 *	setBehaviour
		 *	JavaScript of page: include at bottom of page in footer (just before </body> tag )
		 *	should be an array of 0 to many scripts
		 *
		 *	@param array $value
		 */
		public function setBehaviour($value = false){
			// 
			
			// include default JavaScript that all pages must have			
			//array_unshift($value, "jquery");
		
			$this->_behaviour = '';
			
			$behaviours_joined = '';
			
			// compressed behaviour array variable
			$compressed_behaviour = array();
			
			if($value){
			
				$value = (!is_array($value)) ? explode(',', $value) : $value;
				
				if(is_array($value)){
				
					foreach($value as $item){
						// script is external e.g. begins with http or https
						if(strpos($item, 'http') !== false){
							$this->_behaviour .= '<script src="' . trim($item) . '"></script>' . "\n";
						} else{
							// script is local
							// if we're in live mode, create an array of items - 
							// because we'll join them together to save HTTP requests
							if(MODE == 'livex' && strpos($item, 'tiny_mce') === false){
								$compressed_behaviour[] = trim($item) . '.js';
								
							} else{
								// normal mode include scripts individually
								$this->_behaviour .= '<script src="/behaviour/' . trim($item) . '.js"></script>' . "\n";
							}
						}
					}
					
					// if we're in live mode, join all these files and import them in one go
					if(!empty($compressed_behaviour)){
						$this->_behaviour = '<script src="/compressor/?type=javascript&amp;files=' . join(',' , $compressed_behaviour) . '"></script>' . "\n" . $this->_behaviour;
					}
					
				}
				
			}
		
		}
		
		/**
		 *	setExtraBehaviour
		 *	on page JavaScript - this must be 100% specific to this individual page
		 *	@param (string|boolean) $value (FALSE)
		 */
		public function setExtraBehaviour($value = false){

			if($value){
				$this->_extraBehaviour = '<script type="text/javascript">' . "\n";
            	$this->_extraBehaviour .= "\t" . reduceFileSize($value, 'javascript') . "\n";
            	$this->_extraBehaviour .= '</script>' . "\n";
			} else{
				// no value: no extra behaviour
				$this->_extraBehaviour = '';
			}
			
			// now add extra behaviour (if it exists) to the value
			// for extra behaviour to work it has to go underneath 
			// because it may be reliant upon scripts called in setBehaviour
			$this->_behaviour .= $this->_extraBehaviour;
			
		}
		
		/**
		 *	setCurrentURL
		 *	@param (string|boolean) $value (FALSE)
		 */
		public function setCurrentURL($value = false){
			$this->_currentUrl = ($value) ? $value : '';
						
			//	if this is missing, should we workout the current url 
			// form request_URI or from URL?
			
		}
		
		/**
		 *	setMenu
		 *	if an alternative menu has been set, then this variable's value 
		 *	will be menu.section.php otherwise, by default, it'll be just menu.php
		 *	@param (string|boolean) $value (FALSE)
		 */
		public function setMenu($value = false){
			// 
			$this->_menu = 'layout/menu';
			$this->_menu .= ($value) ? '.' . $value : '';
			$this->_menu .= '.php';
			
		}
		
		/**
		 *	setSubmenu
		 *	if an alternative submenu has been set, then this variable's 
		 *	value will be submenu.sectionname.php
		 *	otherwise, by default, it'll be just submenu.php
		 *	@param (string|boolean) $value (FALSE)
		 */
		public function setSubmenu($value = false){
			$this->_submenu = 'layout/submenu';
			$this->_submenu .= ($value) ? '_' . $value : '';
			$this->_submenu .= '.php';
		}
		
		/**
		 *	setRobots
		 *	should a page be indexed or not?
		 *	the test server shouldn't be so add the robots meta tag that 
		 *	indicates not to index neither should the admin area
		 */
		public function setRobots(){
 
			$robots_value = (MODE == 'live') ? 'noindex,nofollow' : 'noindex,nofollow';
			$robots_value = ($this->_mode == 'popup') ? 'noindex,follow' : $robots_value;
			$this->_robots = '<meta name="robots" content="' . $robots_value . '" />' . "\n";
			
		}
		
		/**
		 *	setCharset
		 *	set the character set for the page: 
		 *	should be different (from default 'utf-8')  if the language is different
		 *	$charset_code = 'utf-8';
		 */
		public function setCharset(){
		
			$charset_code = 'utf-8';
			$this->charset = '<meta http-equiv="Content-Type" content="text/html; charset=' . $charset_code . '" />' . "\n";
			
		}
		
		/**
		 *	setLanguage
		 *	set the page language for the page: 
		 *	should be different (from default : en-gb) if the language is different
		 */
		public function setLanguage(){
			
			$language_code = 'en-gb';
			
			$this->_language  = ' lang="' . $language_code . '"';
		}
		
		/**
		 *	setRSSFeed
		 *	location of website's RSS feed - all sites should have one
		 *	create HTML tag - for auto discovery in address bar of browser
		 *	@param string $value 
		 */
		public function setRSSFeed($value = ''){

			$this->_rssFeed = '';
			
			if(!empty($value)){
				if(is_array($value)){
					
					foreach($value as $feed){
						$this->_rssFeed .= '<link href="http://' . $this->_application->getSiteUrl() . $feed . '" rel="alternate" type="application/rss+xml" title="RSS feed" />'."\n";
					}
					
				} else{
					$this->_rssFeed = "\n" . '<link href="http://' . $this->_application->getSiteUrl() . $value . '" rel="alternate" type="application/rss+xml" title="RSS feed" />' . "\n";
				}
			}
		}
		
		/**
		 *	setStats
		 *	WEBSITE TRACKING: WITH GOOGLE ANALYTICS
		 *	Only show on live server 
		 *	Note: Echo out just before </body> as GA sometimes doesn't load too quickly
		 *	@param (string|boolean) $value (FALSE)
		 */
		public function setStats($value = false){
			
			// We're live so track stats
			if(MODE == 'live' && GOOGLE_ANALYTICS_ID != ''){
			
				$gaJsHost = (!empty($_SERVER['HTTPS'])) ? 'https://ssl.' : 'http://www.';

				$this->_stats .= '<script src="' . $gaJsHost . 'google-analytics.com/ga.js" type="text/javascript"></script>'."\n";
				$this->_stats .= '<script type="text/javascript">' . "\n";
				$this->_stats .= 'if(_gat){' . "\n";
				$this->_stats .= 'var pageTracker = _gat._getTracker("' . GOOGLE_ANALYTICS_ID . '");' . "\n";
				$this->_stats .= 'pageTracker._initData();' . "\n";
				$this->_stats .= 'pageTracker._trackPageview();' . "\n";
				
				// spit out extra tracking code if it exists: for extra tracking e.g. 404 errors, goals, etc
				$this->_stats .= ($value) ? $value . "\n" : '';
				$this->_stats .= '}' . "\n";
				$this->_stats .= '</script>' . "\n";
				
			} else{
			
				// we're not live don't show stats (as it would wreck their integrity)
				// but leave reminder
				$this->_stats = '<!--THIS SITE USES GOOGLE ANALYTICS BUT STATS ARE NOT TRACKED ON TESTING SERVERS -->' . "\n";
				
			}
			
			/** 
			 *	SUGGESTED IMPROVEMENTS:
			 *	we could also ban a select list of IP addresses:
			 *	set by the user e.g the user's office IP
			 */
		
		}
		
		/**
		 *	setWelcomeNote()
		 */
		public function setWelcomeNote(){
			global $objAuthorise;
			
			// user is logged in or not
			$status = (isset($objAuthorise) && $objAuthorise->getStatus()) ? $objAuthorise->getStatus() : 'logged-out';
			
			if($status == 'logged-out'){
				$this->_welcomeNote = 'common/welcome_loggedout.php';
			} else{
				$this->_welcomeNote = 'common/welcome_loggedin.php';
			}
			
			
		}
		
		/**
		 *	setBranding()
		 */
		public function setBranding(){
		
			$this->_Branding = '<div id="Branding">' . "\n\t\t";
			
			
			if($this->_homepage === true) {
				// this is the homepage - use a <h1> and no link
				$this->_Branding .= '<h1 id="Logo">' . SITE_NAME . '</h1>' . "\n";
			} else { 
				// not the homepage, use a span and a link back to the homepage
				$this->_Branding .= '<span id="Logo"><a href="http://' . $_SERVER['HTTP_HOST'] . '/">' . SITE_NAME . '</a></span>' . "\n\t";
			}
			
			$logo_path = SITE_PATH . LOGO;
			
			if($logo_path != SITE_PATH && file_exists(SITE_PATH . LOGO) === true){
				$this->_Branding = str_replace(SITE_NAME, '<img src="' . str_replace('/images/', '/images/medium/', LOGO) . '" alt="' . SITE_NAME . '" title="' . SITE_NAME . '" />', $this->_Branding);
				
				
			}
			
			
			
			$this->_Branding .= '</div>' . "\n";
			
		}
		
		/**
		 *	setCopyright()
		 */
		public function setCopyright(){
		
			/*if($this->_application->getLocation() == 'admin'){*/
				$this->_copyright = '&#0169; ' . date('Y') . ' Powered by <strong><a href="' . $this->_application->getApplicationUrl() . '">' . $this->_application->getApplicationName() . '</a></strong>';
			/*} else{
				$this->_copyright = '&#0169; ' . date('Y') . ' ' . SITE_NAME;
			}*/
		}
		
		/**
		 *	setForm()
		 *	check if supplied form file exists
		 *	if not try the default form file
		 * 	if not show error message
		 *	@param string $objectName
		 *	@param string $action
		 */
		public function setForm($objectName, $action){
		
			// Default form view (a missing view)
			$this->_form = $this->_application->getViewFolder() . 'forms/missing.php';
			
			$objectName = strtolower(str_replace(' ', '_', $objectName));
		

			// Create the form field name
			// relies upon a naming convention
			// then see if it exists		
			$form_action = ($action == 'edit' || $action == 'add') ? 'add_edit' : $action;
			$form_filename = $this->_application->getViewFolder() . 'forms/' . $objectName . '_' . $form_action . '.php';
			
			if(file_exists($form_filename)){
				$this->_form = $form_filename;

			} else{
			
				// file doesn't exist try to use default form (scaffold)
				$form_filename =  $this->_application->getViewFolder() . 'forms/scaffold_' . $form_action . '.php';
				
				// check if default form exists
				if($objectName != 'scaffold' && file_exists( $form_filename)){
					// include default form
					$this->_form = $form_filename;
				}
				
			}
			
		}
		
		
		
		/**
		 *	setFavicon()
		 *	@param string $value ('favicon.ico')
		 */
		protected function setFavicon($value = 'favicon.ico'){
			$this->_favicon = '<link rel="Shortcut Icon" href="/' . $value . '" type="image/x-icon" />';
		}
		
		/**
		 *	getHomepage()
		 */
		public function getHomepage(){		 
			return $this->_homepage;			
		}
		
		/**
		 *	getBranding()
		 */
		public function getBranding(){		 
			return $this->_Branding;			
		}
		
		/**
		 *	getDoctype()
		 */
		public function getDoctype(){		 
			return $this->_doctype;			
		}
		
		/**
		 *	getLanguage()
		 */
		public function getLanguage(){		 
			return $this->_language;			
		}
		
		/**
		 *	getCharset()
		 */
		public function getCharset(){		 
			return $this->_charset;			
		}
		
		/**
		 *	getMode()
		 */
		public function getMode(){		 
			return $this->_mode;			
		}
		
		/**
		 *	getStyle()
		 */
		public function getStyle(){		 
			return $this->_style;			
		}
		
		/**
		 *	getBehaviour()
		 */
		public function getBehaviour(){		 
			return $this->_behaviour;			
		}
		
		/**
		 *	getBodyId()
		 */
		public function getBodyId(){		 
			return $this->_bodyId;			
		}
		
		/**
		 *	getBodyClass()
		 */
		public function getBodyClass(){		 
			return $this->_bodyClass;			
		}
		
		/**
		 *	getAdminArea()
		 */
		public function getAdminArea(){		 
			return $this->_adminArea;			
		}
		
		/**
		 *	getTemplateFolder()
		 */
		public function getTemplateFolder(){		 
			return $this->_templateFolder;			
		}
		
		/**
		 *	getCopyright()
		 */
		public function getCopyright(){		 
			return $this->_copyright;			
		}
		
		/**
		 *	getStats()
		 */
		public function getStats(){		 
			return $this->_stats;			
		}
		
		/**
		 *	getTitle()
		 */
		public function getTitle(){		 
			return $this->_title;			
		}
		
		/**
		 *	getDescription()
		 */
		public function getDescription(){		 
			return $this->_description;			
		}
		
		/**
		 *	getExtraStyle()
		 */
		public function getExtraStyle(){		 
			return $this->_extraStyle;			
		}
		
		/**
		 *	getExtraBehaviour()
		 */
		public function getExtraBehaviour(){		 
			return $this->_extraBehaviour;			
		}
		
		/**
		 *	getRobots()
		 */
		public function getRobots(){		 
			return $this->_robots;			
		}
		
		/**
		 *	getRssFeed()
		 */
		public function getRssFeed(){		 
			return $this->_rssFeed;			
		}
		
		/**
		 *	getCurrentUrl()
		 */
		public function getCurrentUrl(){		 
			return $this->_currentUrl;			
		}
		
		/**
		 *	getMenu()
		 */
		public function getMenu(){		 
			return $this->_menu;			
		}
		
		/**
		 *	getSubmenu()
		 */
		public function getSubmenu(){		 
			return $this->_submenu;			
		}
		
		/**
		 *	getHeaderHTML()
		 */
		public function getHeaderHTML(){		 
			return $this->_headerHTML;			
		}
		
		/**
		 *	getfooterHTML()
		 */
		public function getFooterHTML(){		 
			return $this->_footerHTML;			
		}
		
		/**
		 *	getFavicon()
		 */
		public function getFavicon(){		 
			return $this->_favicon;			
		}
		
		
		/**
		 *	getSpeed()
		 *	@param int $finish_time
		 *	@return float
		 */
		public function getSpeed($finish_time){
		
			$startTime = $this->_application->getStartTime();

			list($secs, $usecs) = explode(' ', $startTime);
			$start = $secs + $usecs;
			
			list($secs, $usecs) = explode(' ', $finish_time);
			$finish = $secs + $usecs;
			$time = $finish - $start;
			if(MODE != 'live'){
				return "<p id=\"Timer\">It took {$time} seconds to generate this page</p>";
			}
		}


		
		/**
		 *	getForm()
		 */
		public function getForm(){		 
			return $this->_form;			
		}
		
		/**
		 *	getWelcomeNote()
		 */
		public function getWelcomeNote(){		 
			return $this->_welcomeNote;			
		}
		
		
		
	
	}

?>