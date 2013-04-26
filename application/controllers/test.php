<?php

	/**
	 *	Test controller
	 *
	 */
	 
	 
	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php"); 


	class Test extends AppController{
	
	
		/**
		 *	Constructor
		 *	@param string $controller
		 *	@param string $action
		 */
		public function __construct($db, $controller, $action){
			parent::__construct($db, $controller, $action);
		}
		
		
		/**
		 *	before
		 */
		protected function before(){
		
			global $objAuthorise;
	
			// Project object filter
			$project_stage = (!$this->_application->getId()) ? $this->_application->getParameter('project_stage', array(2, 3, 4)) : array();
	
			if($objAuthorise->getLevel() == 'Basic'){
				$project_stage = (!$this->_application->getId()) ? $this->_application->getParameter('project_stage', array(1, 2, 3, 4)) : $project_stage;
			}
			
			$this->_application->setFilter('project_stage', $project_stage);
			
			// Initialise Object
			$this->_objData = new Project($this->_db, $this->_application->getFilters(), $this->_application->getId());
			$this->_data = $this->_objData->getProperties();
			
		}
		
		/**
		 *	viewOne
		 */
		protected function viewOne(){
			parent::viewOne();
		}
	
	
	}
	

	$objView = new Test($db, 'projects', $objApplication->getAction());
	
	if($objView->getView()){
	
		$objScaffold = $objView->getObjData();
		$properties = $objView->getData();
		$properties_size = sizeof($properties);

		// Pagination e.g. Previous 1 2 3 4 5 Next
		$objPagination = new Pagination($objScaffold->getTotal(), $objScaffold->getPerPage(), $objScaffold->getCurrentPage());
		
		// Process data and set up user feedback
		$user_feedback = $objScaffold->processData();
		$objFeedback = new Feedback($user_feedback);
		
		$form = $objView->getForm();
				
		include($objView->getView());
		exit;
	} 


?>