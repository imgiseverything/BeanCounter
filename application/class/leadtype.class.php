<?php
/*
 *	=========================================================================
 *	
 *	LeadType Class
 *	-------------------------------------------------------------------------
 *	
 *	Add/edit/delete/view lead types
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
	

class LeadType extends Scaffold{

	// Variables

	/**
	 *	construct
	 *	@param object $db
	 *	@param array $filter
	 *	@param int|boolean $id
	 */
	public function __construct($db, $filter = array(), $id = false){		
	
		$this->_name = 'lead type';
		$this->_namePlural = 'lead types';
		$this->_folder = '/leads/types/';
		$this->_sql['main_table'] = 'lead_type';
		
		$this->_filter = $filter;
		
		parent::__construct($db, $this->_filter, $id);	
		
	}
	
		
}
