<?php
/**
 *	=========================================================================
 *	
 *	Maintenance class
 *	-------------------------------------------------------------------------
 *	
 *	Allows easy turning on of a maintenance/holding page for the site for 
 *	updating.
 *	Simply setting the line on setting.inc.php to 
 *	$objMaintenance = new Maintenance(true); will trigger
 *	the maintenance page.
 *	
 *	You can add your IP address to the $ip_addresses array to make sure you 
 *	don't see the holding page but everyone else does. This is necessary when 
 *	making site updates.
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
 *	@since		12/06/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	12/04/2009
 *	
 *	=========================================================================
 *	
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *	
 *	Variables
 *	
 *	Constructor
 *	
 *	Methods
 *		showHoldingPage()
 *		
 *		
 *	=========================================================================
 *
 */
	
	class Maintenance{
	
		// variables
		
		/**
		 *	@var string
		 */
		public $url;
		
		/**
		 *	@var array
		 */
		public $ip_addresses = array();
		
		/**
		 *	@var array
		 */
		public $exempt_urls = array();
		
		/**
		 * construct
		 */
		public function __construct($maintenance = false){
		
			// a list of IP adddress which never see the holding page. Add your IP address to this array
			$this->ip_addresses = array($_SERVER['SERVER_ADDR'], '79.66.111.123');
			// a list of URLs that are not affected by the redirect
			$this->exempt_urls = array('/settings.xml', '/images/', '/templates/');
			
			// If the maintenance variables has been set to true, then show the holding page
			if($maintenance === true){
				$this->showHoldingPage();
			}
			
		}
		
		// Methods
		
		/**
		 *	showHoldingPage()
		 *	if all conditions are met - redirect to the maintenance page
		 */
		protected function showHoldingPage(){
		
			$perfect_conditions = (!in_array($_SERVER['REQUEST_URI'], $this->exempt_urls) && !in_array($_SERVER['REMOTE_ADDR'], $this->ip_addresses)) ? true : false;
			
			// 
			if($perfect_conditions === true){
				header("Location: /maintenance.html", TRUE, 302);
				exit();
			} // end if
			
		}
		
	}

?>