<?php
/**
 *	=========================================================================
 *	
 *	Menu (Navigation) Class
 *	-------------------------------------------------------------------------
 *	
 *	This class creates navigational menus for pages based on user settings
 *	and pages in a database
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
 *	@since		18/01/2008
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
 *		setBreadcrumb
 *	
 *	=========================================================================
 *
 */


	class Menu{
	
		// Variables
				
		/**
		 *	@var string
		 */
		protected $_breadcrumb;	
		
		/**
		 *	@var string
		 */	
		protected $_breadcrumbSeparator;
		
		/**
		 *	@var object
		 */
		protected $_template;
		
		/**
		 *	@var object
		 */
		protected $_rewrite;
		
		/**
		 *	@var string
		 */
		private $_url;
		
		
		
		
		/**
		 *	constructor
		 */
		public function __construct($db, $objTemplate, $objApplication, $objTree = false){
			
			$this->_db = $db;
			$this->_template = $objTemplate;
			$this->_application = $objApplication;
	
			$this->_breadcrumbSeparator = '::'; 

			$this->setBreadcrumb();
			
		}
		
		/**
		 *	setShortcutsMenu
		 */
		public function setShortcutsMenu($value = false){
			$this->_shortcuts = '<ul id="shortcuts" class="hide">
			<li><a href="#PrimaryContent" accesskey="2">Skip to content</a></li>
			<li><a href="#menu">Skip to main menu</a></li>
			<li><a href="/accessibility/" accesskey="0">Accessibility Statement</a></li>
			<li><a href="/sitemap/">Sitemap</a></li>
			</ul>' . "\n";
		}
		
				
		/**
		 *	setBreadcrumb
		 *	@param: string $page_title
		 */
		public function setBreadcrumb($page_title = false){
			global $objRewrite, $action;
			
			$id = $this->_application->getParameter('id');
			
			$this->_breadcrumb = '<div class="group breadcrumb">' . "\n\t";
			$this->_breadcrumb .= 'You are here: ';
			$this->_breadcrumb .= ($this->_template->getHomepage() !== true) ? '<a href="/">' . SITE_NAME . '</a> ' . "\n" : SITE_NAME . ' home';
			
			// rewrite class isn't initialised so create it
			if(!isset($objRewrite) && $this->_template->getMode() != 'popup'){
				$this->_rewrite = new Rewrite();
				// could this have reprecussions?
			} else{
				$this->_rewrite = $objRewrite;
			}
			
			
			$this->_url = $this->_rewrite->getURL();
			
			// insert optional add-ons;
			if(!empty($this->_url) && $this->_template->getMode() != 'popup'){
				$link = '/'; // breadcrumb link
				
				$trailSize = ($page_title === false) ? sizeof($this->_url) : (sizeof($this->_url) - 1);

				for($i = 0; $i < $trailSize; $i++){
					// remove dashes and make the first character uppercase
					$clean_crumb = str_replace('-', ' ', ucfirst($this->_url[$i]));
					
					// clean crumb is a month
					if(strlen($clean_crumb) == 2 && is_numeric($clean_crumb) && $clean_crumb < 13 && strlen($this->_url[($i-1)]) == 4 && is_numeric($this->_url[($i-1)])){
						$clean_crumb = date('F', strtotime(date('Y') . '-' . $clean_crumb . '-01 00:00:00'));	
					}
					
					// build up link
					$link .= $this->_url[$i] . '/';
					
					$this->_breadcrumb .= "\t";

					if($clean_crumb != $id && !in_array(strtolower($clean_crumb), $this->_rewrite->getActions())){					
						$this->_breadcrumb .= ($i != sizeof($this->_url)) ? $this->_breadcrumbSeparator . ' <a href="' . $link . '">' . ucfirst($clean_crumb) . '</a> ' . "\n" : ucfirst($clean_crumb) . "\n";
					}
					
					
				}
				
			}
			
			//$this->_breadcrumb .=  (!empty($action)) ? $this->_breadcrumbSeparator . ' ' . ucfirst($action) : '';
			$this->_breadcrumb .= ($page_title) ? ' ' . $this->_breadcrumbSeparator . ' ' . $page_title : '';
			

			// end breadcrumb trail
			$this->_breadcrumb .= "\t" . '</div>' . "\n";
		}
		
		/**
		 *	getBreadcrumb()
		 */
		public function getBreadcrumb(){
			return $this->_breadcrumb;		
		}
			
	}

?>