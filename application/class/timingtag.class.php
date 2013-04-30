<?php
/*
 *	=========================================================================
 *	
 *	TimingTag Class
 *	-------------------------------------------------------------------------
 *	
 *	Add/edit/delete/view timing tags
 *	
 *	=========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 	2008-2013 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.0	
 *	@author		philthompson.co.uk
 *	@since		30/04/2013
 *	
 *	edited by:  Phil Thompson
 *	@modified	30/04/2013
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
 *		
 *	=========================================================================
 *	
 */
	

class TimingTag extends Scaffold{

	// Variables

	/**
	 *	construct
	 *	@param object $db
	 *	@param array $filter
	 *	@param int|boolean $id
	 */
	public function __construct($db, $filter = array(), $id = false){		
	
		$this->_name = 'timing tag';
		$this->_namePlural = 'timing tags';
		$this->_folder = '/timings/tags';
		$this->_sql['main_table'] = 'timing_tag';
		
		$this->_filter = $filter;
		
		parent::__construct($db, $this->_filter, $id);	
		
	}
	
		
}
