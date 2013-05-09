<?php
/**
 *	=========================================================================
 *	
 *	File Class	
 *	-------------------------------------------------------------------------
 *	
 *	Upload file to file system
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
 *	@since		23/01/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	09/05/2013
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
 *	=========================================================================
 */
	



	class File extends Upload{
		
		/**
		 *	construct
		 */
		public function __construct($db, $filter = array(), $id = false){
		
			parent::__construct($db, $filter, $id);
		
			$this->_sql['main_table'] = 'files';
			
			$this->_extensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'mp3', 'wav', 'aac', 'html', 'txt', 'sql', 'gz', 'tar.gz');
			
			$this->setMaxSize(2);

		}
		
			
			
		
		
	}
