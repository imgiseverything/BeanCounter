<?php
/*
 *	=========================================================================
 *	
 *	BookingType Class
 *	-------------------------------------------------------------------------
 *	
 *	Add/edit/delete/view booking types
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
 *	@since		09/05/2013
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
 *	methods
 *	
 *		
 *	=========================================================================
 *	
 */
	

class BookingType extends Scaffold{

	// Variables

	/**
	 *	construct
	 *	@param object $db
	 *	@param array $filter
	 *	@param int|boolean $id
	 */
	public function __construct($db, $filter = array(), $id = false){		
	
		$this->_name = 'booking type';
		$this->_namePlural = 'booking types';
		$this->_folder = '/bookings/types/';
		$this->_sql['main_table'] = 'booking_type';
		
		$this->_filter = $filter;
		
		parent::__construct($db, $this->_filter, $id);	
		
	}
	
		
}
