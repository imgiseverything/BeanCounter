<?php

/**
 *	Lead Class
 *	
 *	@package		Bean Counter
 *	@copyright 		2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@author			philthompson.co.uk
 *	@since			04/09/2011
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

class Lead extends Scaffold{


	/** 
	 *	@var mixed (int or array of ints)
	 */
	 public $_leadType;

	/**
	 *	Constructor
	 *	@param	object local database object
	 *	@param	array 	data options for SQL
	 *	@param	mixed (int|boolean)	ID
	 */
	public function __construct($db, $filter, $id = false){

		// Object naming conventions
		$this->_name = 'lead';
		
		// Always order leads by the first_contact_date field
		$this->_queryFilter['order_by'] = 't.first_contact_date DESC';
		
		$this->_leadType = read($filter, 'lead_type', null);

		// Run parent's constructor
		parent::__construct($db, $filter, $id);
		
		
		
		
		
	}
	
	
	
	
	/**
		 *	customQueryFilters
		 */
		public function customQueryFilters(){
			
			$this->_queryFilter['custom'] = '';
			
			// filter by project_stage e.g. completed/invoiced/etc
			if($this->_leadType){
				if(is_array($this->_leadType)){
					$this->_queryFilter['custom'] .= " AND `t`.`lead_type` IN(" . join(',', $this->_lead_type) . ") ";
				} else{
					$this->_queryFilter['custom'] .= " AND `t`.`lead_type` = '{$this->_leadType}' ";
				}
			}
			

		}
	
	


}
	
?>