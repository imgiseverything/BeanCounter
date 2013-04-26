<?php
/**
 *	=========================================================================
 *	
 *	AppController Class	
 *	-------------------------------------------------------------------------
 *	
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
 *	@since		20/07/2009
 *	
 *	edited by:  Phil Thompson
 *	@modified	20/07/2009
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
 *	Methods
 *		variables
 *		constructor
 *		methods
 *			before
 *			after
 *			runController
 *			
 *
 */

	
	class AppController{
	
	
		/**
		 *	@var string
		 */
		protected $_controller;
		
		/**
		 *	@var string
		 *	What view to show e.g. view|all|add|edit|delete|duplicate|download|etc
		 */
		protected $_action;
		
		/**
		 *	@var object
		 *	Local application object
		 */
		protected $_application;
		
		/**
		 *	@var object
		 *	Local database object
		 */
		protected $_db;
		
		/**
		 *	@var object
		 */
		protected $_objData;
		
		/**
		 *	@var array
		 */
		protected $_data;
		
		/**
		 *	@var string
		 */
		protected $_view;
		
		/**
		 *	@var string
		 */
		protected $_form;
		 
		 
		
		/**
		 *	Constructor
		 *	@param object $db
		 *	@param string $controller
		 *	@param string $action
		 */
		public function __construct($db, $controller = 'scaffold', $action = 'all'){
		
			
			$this->_application = new Application();
			
			$this->_db = $db;
			
			$this->_controller = $controller;
			$this->_action = $action;
			
			if(empty($this->_controller)){
				$this->_controller = 'scaffold';
			}
			
			if(empty($this->_action)){
				$this->_action = 'all';
				
				if($this->_application->getId()){
					$this->_action = 'one';
				}
				
			}
			
			
			
			
			$this->setView();
				
			$this->before();

			$this->runController();
			
			$this->after();
			
			
		}
		
		
		
		
		/**
		 *	before
		 *	Prior to setting up all the view data this method runs
		 *	and it runs for all views in this controller
		 */
		protected function before(){
			
		}
		
		/**
		 *	after
		 *	After setting up all the view data this method runs
		 *	and it runs for all views in this controller
		 */
		protected function after(){
			
		}
		
		/**
		 *	runController
		 */
		protected function runController(){
			
			$method = 'view' . ucfirst($this->_action);

			if(method_exists($this, $method)){
				$this->{$method}();
			}
			
		}
		
		
		
		/**
		 *	setView
		 */
		protected function setView($view = false){
		
			if($view === false){
				$view = $this->_controller . '_' . $this->_action;
			}
			
			// /full/path/to/view/folder/controller_action.php
			$this->_view = $this->_application->getViewFolder() . $view . '.php';
			
			// remove $_controller and put 'scaffold' in if view file doesn't
			// exist as this means we need to show the default file
			if(file_exists($this->_view) === false){
				$this->_view = $this->_application->getViewFolder() . 'scaffold_' . $this->_action . '.php';
			}
			
		}
		
		
		/**
		 *	resetView()
		 *	@param string
		 *	@return void
		 */
		public function resetView($new_view){

			// append .php if misisng
			$new_view = str_replace('.php', '', $new_view);
			
			$this->setView($new_view);
		}
		
		
		/**
		 *	setForm()
		 *	check if supplied form file exists
		 *	if not try the default form file
		 * 	if not show error message
		 */
		protected function setForm(){
		
			// Default form view (a missing view)
			$this->_form = $this->_application->getViewFolder() . 'forms/missing.php';
		
			// Create the form field name
			// relies upon a naming convention
			// then see if it exists		
			$this->_action = ($this->_action == 'edit' || $this->_action == 'add') ? 'add_edit' : $this->_action;
			$form_filename = $this->_application->getViewFolder() . 'forms/' . strtolower($this->_controller) . '_' . $this->_action . '.php';
			
			if(file_exists($form_filename)){
				$this->_form = $form_filename;
			} else{
			
				// file doesn't exist try to use default form (scaffold)
				$form_filename =  $this->_application->getViewFolder() . 'forms/scaffold_' . $this->_action . '.php';
				
				// check if default form exists
				if($this->_controller != 'scaffold' && file_exists( $form_filename)){
					// include default form
					$this->_form = $form_filename;
				}
				
			}
			
		}
		
		
		
		/**
		 *	viewAll
		 */
		protected function viewAll(){
			
		}
		
		
		/**
		 *	viewOne
		 */
		protected function viewOne(){
			$this->resetView($this->_controller . '_view');
		}
		
		/**
		 *	viewAdd
		 */
		protected function viewAdd(){
			$this->setForm();
			$this->resetView($this->_controller . '_form');
		}
		
		/**
		 *	viewEdit
		 */
		protected function viewEdit(){
			$this->setForm();
			$this->resetView($this->_controller . '_form');
		}
		
		/**
		 *	viewDelete
		 */
		protected function viewDelete(){
			$this->setForm();
			$this->resetView($this->_controller . '_form');
		}
		
		/**
		 *	viewDuplicate
		 */
		protected function viewDuplicate(){
			$this->setForm();
			$this->resetView($this->_controller . '_form');
		}
		
		
		
		/**
		 *	getView
		 *	@return string $this->_view (scaffold_all.php)
		 */
		public function getView(){
			return $this->_view;
		}
		
		/**
		 *	getObjData
		 *	@return object
		 */
		public function getObjData(){
			return $this->_objData;
		}
		
		/**
		 *	getData
		 *	@return array
		 */
		public function getData(){
			return $this->_data;
		}
		
		
		
		/**
		 *	getForm
		 *	@return string
		 */
		public function getForm(){
			return $this->_form;
		}
		

	
	}

?>