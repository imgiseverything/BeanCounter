<?php
/**
 *	=========================================================================
 *	
 *	Controller Class
 *	-------------------------------------------------------------------------
 *	Work out which view to show for the controller.
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
 *	@since		2009
 *	
 *	edited by:  Phil Thompson
 *	@modified	20/07/2009
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
 *		checkExists
 *		setView
 *		getView
 *		resetView
 *		setSortLinks
 *		getSortLink
 *	
 *	=========================================================================
 */

	class Controller{
	
		// Variables
		
		/**
		 *	$var object
		 *	Database connection object
		 */
		protected $_db;
		
		/**
		 *	$var object
		 *	System application object
		 */
		protected $_application;
		
		/**
		 *	$var object
		 */
		protected $_template;
		
		/**
		 *	$var object
		 */
		protected $_menu;
		
		/**
		 *	$var object
		 */
		protected $_vcard;
		
		/**
		 *	$var object
		 */
		protected $_authorise;
		
		/**
		 *	$var object
		 */
		protected $_scaffold;
		
		
		/**
		 *	$var string
		 *	HTML 'view' file to be included
		 */
		protected $_view;
		
		/**
		 *	$var string
		 *	HTML 'view' default file to be included
		 */
		protected $_defaultView;
		
		/**
		 *	@var string
		 */
		protected $_action;
		
		/**
		 *	@var array
		 */
		protected $_sortLinks = array();
		
		/**
		 *	Constructor
		 *	@param object $db
		 *	@param object $objApplication
		 *	@param object $objTemplate
		 *	@param object $objMenu
		 *	@param object $objVcard
		 *	@param object $objAuthorise
		 *	@param object $objScaffold
		 */
		public function __construct($db, $objApplication, $objTemplate, $objMenu, $objVcard, $objAuthorise, $objScaffold){
			
			// Local variable objects
			$this->_db = $db;
			$this->_application = $objApplication;
			$this->_template = $objTemplate;
			$this->_menu = $objMenu;
			$this->_vcard = $objVcard;
			$this->_authorise = $objAuthorise;
			$this->_scaffold = $objScaffold;
			
			$this->_action = $this->_application->getAction();
			
			$this->checkExists();
			$this->setView();
			
			$this->setSortLinks();
		}
		
		// Methods	
		
		/*public/private/protected function(){
		
		}*/
		
		/**
		 *	checkExists()
		 *	If the scaffold doesn't exist give an error message (404)
		 */
		protected function checkExists(){
			if($this->_scaffold->getExists() === false && empty($this->_scaffold->search) && $this->_action != 'add'){
				$obj404 = new Error($this->_application);
				$obj404->throw404($this->_template, $this->_menu, $this->_vcard, $this->_authorise);
			}
		}
		
		/**
		 *	setView()
		 *	Work out which 'view' to show	
		 *	If $id exists either show individual object data or
		 *	the form to manipulate it
		 *	No id means show all objects or add a new item
		 */
		protected function setView(){
		
		
			$defaultName = 'scaffold';
			$scaffoldName = strtolower(str_replace(' ', '.', $this->_scaffold->getNamePlural()));
			
			$id = $this->_scaffold->getId();
	
			if($id){
			
					if(!$this->_action){
						// View an individual item
						$this->_view = $this->_application->getViewFolder() . $scaffoldName . '_view.php';				
					} else{
						// View a form edit/delete are most likely
						$this->_template->setForm($this->_scaffold->getName(), $this->_action);
						$this->_view = $this->_application->getViewFolder() . $scaffoldName . '_form.php';
					}
				
				} else{
					
					if($this->_action == 'add' || $this->_action == 'sort'){
						// Add a new item
						$this->_template->setForm($this->_scaffold->getName(), $this->_action);
						$this->_view = $this->_application->getViewFolder() . $scaffoldName . '_form.php';
					} else{
						// Show all items
						// but check data exists first
						
						$data = $this->_scaffold->getProperties();
						
						if(!empty($data)){
							$this->_view = $this->_application->getViewFolder() . $scaffoldName . '_all.php';
						} else{
							$this->_view = $this->_application->getViewFolder() . $scaffoldName . '_empty.php';
						}
					}
					
				}
				
				
				// If the view file doesn't exist, avoid errors by using 
				// the default 'scaffold' view
				if(!file_exists($this->_view)){
					$this->_view = str_replace($scaffoldName, 'scaffold', $this->_view);
				}
		}
		
		/**
		 *	getView()
		 */
		public function getView(){
			return $this->_view;
		}
		
		/**
		 *	resetView()
		 *	@param string
		 *	@return void
		 */
		public function resetView($new_view){
		
			// append .php if misisng
			if(strpos($new_view, '.php') === false){
				$new_view .= '.php';
			}
			
			$this->_view = $this->_application->getViewFolder() . $new_view;
		}
		
		
		
		/**
		 *	setSortLinks()
		 */
		protected function setSortLinks(){
		
			$arrLinks = array(
				'title' => array('default' => 'title_az', 'alternative' => 'title_za'),
				'name' => array('default' => 'name_az', 'alternative' => 'name_za'),
				'screenname' => array('default' => 'screenname_az', 'alternative' => 'screenname_za'),
				'date_added' => array('default' => 'newest', 'alternative' => 'oldest'),
				'date_edited' => array('default' => 'last_edited', 'alternative' => 'first_edited'),
				'last_login' => array('default' => 'last_login', 'alternative' => 'first_login'),
			
			);
		
			foreach($arrLinks as $key => $value){
				$this->_sortLinks[$key] = (!empty($_GET['sort']) && $_GET['sort'] == $value['default']) ? $value['alternative'] :  $value['default'];
			}
		}
		
		/**
		 *	getSortLink()
		 */
		public function getSortLink($key){
			
			if(empty($this->_sortLinks[$key])){
				$this->_sortLinks[$key] = '';
			}
			
			return $this->_sortLinks[$key];
		}
		
		
		
	
	
	}
?>