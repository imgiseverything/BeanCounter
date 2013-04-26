<?php
/**
 *	=========================================================================
 *		
 *	Scaffold Class
 *	-------------------------------------------------------------------------
 *	Extension of the CRUD class to add in extras like
 *	page title, bread crumb trails title, page descriptions
 *	=========================================================================
 *		
 *	Copyright:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright Phil Thompson
 *	 
 *	This class was written by Phil Thompson
 *	http://imgiseverything.co.uk/
 *	hello@philthompson.co.uk
 *		
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *		
 *	@copyright 	2008-2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.0	
 *	@author		philthompson.co.uk
 *	@since		02/02/2009
 *		
 *	edited by:  Phil Thompson
 *	@modified	18/03/2009
 *		
 *		
 *	=========================================================================
 *		
 *	=========================================================================
 *		
 *		
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *	
 *		variables
 *		
 *		construct
 *		
 *		methods
 *			Setters
 *				setPageTitle
 *				setPageDescription
 *				setBreadcrumb
 *
 *			Getters
 *				getPageTitle
 *				getPageDescription
 *				getBreadcrumb				
 *			
 *	=========================================================================
 *		
 */
	

	class Scaffold extends CRUD{
	
		
		// Variables
		
		/**
		 * @var constant
		 */
		const TITLE_SEPARATOR = ' &lt; ';		
				
		/**
		 *	$var string
		 */
		protected $_pageTitle;
		
		/**
		 *	$var string
		 */
		protected $_pageDescription;
		
		/**
		 *	$var string
		 */
		protected $_breadcrumb;
		
		/**
		 *	$var string
		 */
		protected $_sectionTitle;
		
		
	
		/**
		 *	construct()
		 *	@param object $db
		 *	@param array $filter
		 *	@param int $id (FALSE)
		 */
		public function __construct($db, $filter = array(), $id = false){		
			parent::__construct($db, $filter, $id);	
			
			$this->setSectionTitle();	
		}
		

		
		/**
		 *	setPageTitle()
		 */
		protected function setPageTitle(){
		
			global $action;

			if($this->_id){
				$this->_pageTitle = stripslashes(read($this->_properties, 'title', ''));
				if(!empty($this->_pageTitle)){
					$this->_pageTitle .= self::TITLE_SEPARATOR . ucfirst($this->_namePlural);
				}
			} else{
				$this->_pageTitle = ucfirst($this->_namePlural);
			}
			
			if(!empty($action)){
				$this->_pageTitle = ($action == 'add') ? 'Add new ' . ucfirst($this->_name) : ucwords($action) . " " . $this->_pageTitle;
			}
	
			// Add page number if it we're not on page 1
			if($this->_currentPage > 1){
				$this->_pageTitle .= Template::TITLE_SEPARATOR . 'Page ' . $this->_currentPage;
			}
			
		}
		
		/**
		 *	setPageDescription()
		 */
		protected function setPageDescription(){
			if($this->_id){
				$this->_pageDescription = stripslashes(strip_tags(read($this->_properties, 'description', $this->_name)));
			} else{
				$this->_pageDescription = ucfirst($this->_namePlural);
			}
			
			$this->_pageDescription = str_replace('"', '', $this->_pageDescription);
		}
		
		/**
		 *	setBreadcrumb
		 */
		protected function setBreadcrumb(){
			global $action;
			$this->_breadcrumb = ucfirst(stripslashes(read($this->_properties, 'title', $this->_namePlural)));
			
			if($action){
				$this->_breadcrumb = ($action == 'add') ? 'Add new ' . ucfirst($this->_name) : ucwords($action) . " " . $this->_breadcrumb;
			}
		}
		
		
		/**
		 *	setSectionTitle
		 */
		protected function setSectionTitle(){
			$this->_sectionTitle = ucfirst($this->_name);
			
			if(empty($this->_id)){
				$this->_sectionTitle = ucfirst($this->_namePlural);
			}
			
		}
		
		
		/**
		 *	getPageTitle()
		 *	@return string
		 */
		public function getPageTitle(){
			return $this->_pageTitle;
		}
		
		/**
		 *	getPageDescription()
		 *	@return string
		 */
		public function getPageDescription(){
			return $this->_pageDescription;
		}
		
		/**
		 *	getBreadcrumb()
		 *	@return string
		 */
		public function getBreadcrumb(){
			return $this->_breadcrumb;
		}
		
		/**
		 *	getSectionTitle()
		 *	@return string
		 */
		public function getSectionTitle(){
			return $this->_sectionTitle;
		}	
		
		protected function purge(){}

}
?>