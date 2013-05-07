<?php
/**
 *
 *	=========================================================================
 *		
 *	Download Class
 *	-------------------------------------------------------------------------
 *		
 *	List downloadable files, forces individual downloads
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
 *	@since		27/02/2007
 *	
 *	edited by:  Phil Thompson
 *	@modified	26/04/2013
 *
 *	=========================================================================
 *		
 *	Table of Contents
 *		
 *	Variables
 *	Constructor
 *	Methods
 *		setFileHeader
 *		setFileFooter
 *		setFileBody
 *		setFileName
 *		setStyle
 *		getters
 *	=========================================================================
 *	
 */


class Download{

	// Variables
	
	/**
	 *	@var string
	 */
	protected $_mimeType;
	
	/**
	 *	@var string
	 */
	protected $_filename;
	
	/**
	 *	@var string
	 */
	protected $_type;
	
	/**
	 *	@var string
	 */
	protected $_fileHeader;
	
	/**
	 *	@var string
	 */
	protected $_fileFooter;
	
	/**
	 *	@var string
	 */
	protected $_style;	
	
	/**
	 *	@var object
	 */
	protected $_application;
	
	/**
	 *	@var array
	 */
	protected $_filter = array();
	
	/**
	 *	@var string or int
	 */
	private $_id;

	
	/**
	 *	construct
	 *	@param object $objApplication
	 */
	public function __construct($objApplication){

		$this->_application = $objApplication;
		
		$this->_id = $this->_application->getId();			
		$this->_filter = $this->_application->getFilters();
		
		// what type of file is it
		$this->type = read($this->_filter, 'type', 'invoice');
			
		$this->setMimeType();
		
		$this->setFileName();
		
		$this->setFileHeader();
		
		//$this->setFileBody();
		
		$this->setFileFooter();
		
		
	}
	
	// Methods
	
	/**
	 *	setMimeType()
	 */
	public function setMimeType(){
		
		switch($this->type){
		
			default:
			case 'project':
			case 'outgoing':
			case 'invoice':
			case 'quote':
			case 'remittance':
				$this->_mimeType = 'text/html; charset=utf-8';
				break;
				
		}
		
	}
	
	/**
	 *	setFileHeader()
	 */
	public function setFileHeader(){
		global $objTemplate, $objVcard;
		
		switch($this->type){
		
			default:
			case 'invoice':
			case 'quote':
				//$style = file_get_contents(APPLICATION_PATH."/compressor.php?type=css&files=reset.css,global.css,tables.css,projects.css,receipt.css");
				// Cached d/l file doesn't exist so create it
				if(!file_exists($this->_filename)){
				}
				$title = ucfirst($this->type);
				
				$permalink = str_replace(array('download', 'invoice', 'quote', 'remittance', $this->_id), array('pdf', 'pdf', 'pdf', 'pdf', MD5(SECRET_PHRASE . $this->_id)), $_SERVER['REQUEST_URI']);
				
				$permalink_full = 'http://' . $this->_application->getSiteUrl() . $permalink;
								
				$this->setStyle();

				$this->_fileHeader = '<!DOCTYPE html>
				<html dir="ltr" lang="en-GB">
				<head>
				<meta charset="UTF-8" />
				<meta name="robots" content="noindex,nofollow" />
				<title>' . SITE_NAME . ' - ' . $title . '</title>
				</head>
				<body class="download invoice">
				<STYLE>
					' . reduceFileSize($this->_style) . '						
				</STYLE>
				<div class="download-container">';
				
				if($this->type != 'remittance'){
					$this->_fileHeader .= '<div id="Download">
						<p><a href="' . $permalink_full . '">Download this document as a PDF (beta)</a></p>
					</div>';
				}
				
				$this->_fileHeader .= '<div class="site-container">
				<div class="group site-header">
				' . $objTemplate->getBranding() . '
				<div class="date">' . DateFormat::getDate('date', date('Y-m-d H:i:s')) . '</div>' . 
				$objVcard->getVcard() . '
				</div>
				<hr>
				<div class="group site-content">
				<div id="PrimaryContent" class="content-primary">
				';
				
				break;
				
		}
		
	}
	
	/**
	 *	setFileFooter()
	 */
	public function setFileFooter(){
		
		switch($this->type){
		
			default:
				$this->_fileFooter = '</div></div></div><div id="PoweredBy">Powered by <a href="' . $this->_application->getApplicationUrl() . '"><strong>' . $this->_application->getApplicationName() . '</strong></a> for ' . SITE_NAME . '</div></div></body></html>';
				break;
			
		} // end switch
		
	}
	
	/**
	 *	setFileBody()
	 */
	public function setFileBody(){
		switch($this->type){
			
			default:
			case 'invoice':
			case 'quote':
				// CURL the project
				$this->_fileBody = file_get_contents($this->_filename);	
				
				//$this->_fileBody = unserialize(base64_decode($this->_fileBody));
	
				// remove content between <!-- NOT IN INVOICE --> content <!-- END NOT IN INVOICE -->
				$pattern = "/<!-- NOT IN INVOICE -->[a-zA-Z0-9<>\"\n\r -_]+<!-- END NOT IN INVOICE -->/";
				$this->_fileBody = preg_replace($pattern, '', $this->_fileBody);
				
				//print_x($matches);
				break;
				
		} // end switch
		
		
	}
	
	
	/**
	 *	setFileName()
	 */
	public function setFileName(){
	
		// start download file
		$this->_filename = SITE_PATH . 'cache/';
		
		switch($this->type){
		
			default:
			case 'invoice':
			case 'quote':
				$this->_filename .= 'project/download-' . md5(SECRET_PHRASE . $this->_id) . '.html';
				break;
			
			case 'remittance':
				$this->_filename .= 'outgoing/download-' . md5(SECRET_PHRASE . $this->_id) . '.html';
				break;
				
		} // end switch
				
	}
	
	/**
	 *	setStyle()
	 */
	public function setStyle(){
		// Dreamhost file_get_contents (on an external URL) hack
		if(empty($this->_style)){
			$this->_style .= file_get_contents(SITE_PATH . '/style/global.css');
			$this->_style .= file_get_contents(SITE_PATH . '/style/tables.css');
			$this->_style .= file_get_contents(SITE_PATH . '/style/receipt.css');
		}
	}
	
	
	/**
	 *	getStyle()
	 *	@return string
	 */
	public function getStyle(){
		return $this->_style;
	}
	
	/**
	 *	getFilename()
	 *	@return string
	 */
	public function getFilename(){
		return $this->_filename;
	}
	
	/**
	 *	getFileHeader()
	 *	@return string
	 */
	public function getFileHeader(){
		return $this->_fileHeader;
	}
	
	/**
	 *	getFileFooter()
	 *	@return string
	 */
	public function getFileFooter(){
		return $this->_fileFooter;
	}
	
	/**
	 *	getFileBody()
	 *	@return string
	 */
	public function getFileBody(){
		return $this->_fileBody;
	}
	
	

}
