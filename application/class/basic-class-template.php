<?php
/**
 *	Class
 *	
 *	@package		Bean Counter
 *	@copyright 		2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@author			philthompson.co.uk
 *	@since			
 *	@lastmodified	
 *	@version		1	
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	Variables
 *	Methods
 *		Constructor
 *	
 */

	class BasicTemplate{
	
		// Variables

		
		/**
		 *	$var object
		 *	System application object
		 */
		protected $_application;
		
		/**
		 *	$var object
		 *	Database connection object
		 */
		protected $_db;
		
		// Constructor
		public function __construct($objApplication, $db){
			
			// Local variable objects
			$this->_db = $db;
			$this->_application = $objApplication;
		}
		
		// Methods	
		
		/*public/private/protected function(){
		
		}*/
		
	
	
	}
?>