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
 *	@modified	04/05/2013
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

		$this->setBreadcrumb();
		
	}
	
			
	/**
	 *	setBreadcrumb
	 *	@param: string $page_title
	 */
	public function setBreadcrumb($page_title = false){
		global $objRewrite, $action;
		
		$id = $this->_application->getParameter('id');
		
		$this->_breadcrumb = '<div class="group breadcrumb">';
		$this->_breadcrumb .= 'You are here: ';
		//$this->_breadcrumb .= ($this->_template->getHomepage() !== true) ? '<span><a href="/">Dashboard</a></span> ' . "\n" : '<span>' . SITE_NAME . ' home</span>';
		
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
	
				// build up link
				$link .= $this->_url[$i] . '/';

				if($clean_crumb != $id && !in_array(strtolower($clean_crumb), $this->_rewrite->getActions()) && is_numeric($clean_crumb) !== true){					
					$this->_breadcrumb .= ($i != sizeof($this->_url)) ? ' <span><a href="' . $link . '">' . ucfirst($clean_crumb) . '</a></span> ' : ucfirst($clean_crumb);
				}
			
			}
			
		}
		
		$this->_breadcrumb .= ($page_title) ? ' ' . $page_title : '';
		

		// end breadcrumb trail
		$this->_breadcrumb .= '</div>';
	}
	
	/**
	 *	getBreadcrumb()
	 */
	public function getBreadcrumb(){
		return $this->_breadcrumb;		
	}
		
}
