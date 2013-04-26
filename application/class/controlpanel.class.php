<?php
/**
 *	=========================================================================
 *	
 *	Control Panel Class
 *	-------------------------------------------------------------------------
 *	
 *	Generate links and options for users' control panels based on their 
 *	requirements/access levels etc
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
 *	@since		05/03/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	12/04/2009
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
 *		setIntroduction
 *		setCommonTasks
 *		
 *	=========================================================================
 *
 */
	
	




	class ControlPanel{
	
		// Variables
		
		/**
		 *	@var array
		 */
		public $properties = array();
		
		/**
		 *	@var object
		 */
		protected $_db;
		
		/**
		 *	@var object
		 */
		protected $_authorise;
		
		/**
		 *	@var array
		 */
		protected $_commonTasks = array();
		
		/**
		 *	@var string
		 */
		protected $_introduction;

	
		/**
		 *	Constructor
		 *	@param object $db
		 */
		public function __construct($db){	
		
			global $objAuthorise;
			
			// setup local database object
			$this->_db = $db;
			
			// setup local authorisation object
			$this->_authorise = $objAuthorise;
			
			$this->setIntroduction();
			
			// set common tasks - what will each type of user need to do most?
			$this->setCommonTasks();
		
		}
		
		// methods
		
		/**
		 *	setIntroduction
		 */
		public function setIntroduction(){
			switch($this->_authorise->getLevel()){
				default:
					$this->_introduction = '';
					break;
					
				// Superuser - Needs to see everything
				case 'Superuser':
					$this->_introduction = 'Manage your online work. Create and send quotes. File and send invoices.';
					break;
					
				// Account - needs to see accounts/expenditure and that's all
				case 'Accountant':
					$this->_introduction = 'Quickly review and download the accounts for ' . SITE_NAME;
					break;
					
				// Basic - this will be a client - they only need to 1: see 
				// their projects, 2: pay, and 3: leave notes behind
				case 'Basic':
					$this->_introduction = 'View quotes and invoices from ' . SITE_NAME . ' and quickly contact them too.';
					break;
				
			}
		}
		
		/**
		 *	setCommonTasks
		 */
		public function setCommonTasks(){
				
			
			switch($this->_authorise->getLevel()){
			
				default:
					break;
				
				// Superuser - Needs to see everything
				case 'Superuser':
					$this->_commonTasks['Add new project'] = array('area' => 'Projects', 'url' => '/projects/add/');
					$this->_commonTasks['View accounts'] = array('area' => 'Accounts', 'url' => '/accounts/?show=200');
					$this->_commonTasks['View projects'] = array('area' => 'Projects', 'url' => '/projects/');
					$this->_commonTasks['Add new outgoing'] = array('area' => 'Outgoings', 'url' => '/outgoings/add/');					
					$this->_commonTasks['Add a new user'] = array('area' => 'Users', 'url' => '/users/add/');
					$this->_commonTasks['View bookings'] = array('area' => 'Projects', 'url' => '/bookings/');
					break;
					
				// Account - needs to see accounts/expenditure and that's all
				case 'Accountant':
					$this->_commonTasks['View accounts'] = array('area' => 'Accounts', 'url' => '/accounts/?show=200');
					$this->_commonTasks['View outgoings'] = array('area' => 'Outgoings', 'url' => '/outgoings/');
					$this->_commonTasks['Contact '.SITE_NAME] = array('area' => 'Contact', 'url' => '/contact/');
					break;
					
				// Basic - this will be a client - they only need to 1: see their projects
				case 'Basic':
					$this->_commonTasks['View invoices and quotes'] = array('area' => 'Activity', 'url' => '/projects/');
					$this->_commonTasks['View your users'] = array('area' => 'Users', 'url' => '/users/');
					$this->_commonTasks['Add a new user'] = array('area' => 'Users', 'url' => '/users/add/');
					$this->_commonTasks['Contact '.SITE_NAME] = array('area' => 'Contact', 'url' => '/contact/');
					break;
					
			}
			
			
			if(!empty($this->_commonTasks )){
				
			}
			
		}
		
		
		/**
		 *	getIntroduction
		 */
		public function getIntroduction(){
			return $this->_introduction;
		}
		
		/**
		 *	getCommonTasks
		 */
		public function getCommonTasks(){
			return $this->_commonTasks;
		}
	
	}

?>