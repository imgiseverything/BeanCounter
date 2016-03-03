<?php

/**
 *	Timing Class
 *	
 *	@package		Bean Counter
 *	@copyright 		2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@author			philthompson.co.uk
 *	@since			20/02/2012
 *	@lastmodified	03/02/2016
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

class Timing extends Scaffold{


	/** 
	 *	@var mixed (int or array of ints)
	 */
	 protected $_project;
	 
	/** 
	 *	@var int
	 */
	 protected $_totalHours = false;
	 
	 /** 
	  *	@var int
	  */
	 protected $_firstYear = 0;

	/**
	 *	Constructor
	 *	@param	object local database object
	 *	@param	array 	data options for SQL
	 *	@param	mixed (int|boolean)	ID
	 */
	public function __construct($db, $filter, $id = false){

		// Object naming conventions
		$this->_name = 'timing';
		
		// Always order leads by the start_date field
		$this->_queryFilter['order_by'] = 't.start_date DESC';
		
		$this->_project = read($filter, 'project', false);
		$this->_client = read($filter, 'client', false);
		

		// Run parent's constructor
		parent::__construct($db, $filter, $id);

		$this->setTotalHours();

		if(!empty($this->_project)){
			$this->setTotalProjectHours();
		}
		
	}
	
	
	/**
	 *	customQueryFilters()
	 */
	protected function customQueryFilters(){
	
		$this->_queryFilter['custom'] = '';
		
		if($this->_project){
			//exit($this->_project);
			$this->_queryFilter['custom'] = " AND t.project = '{$this->_project}' ";
		}
		
		if($this->_client){
			//exit($this->_project);
			$this->_queryFilter['custom'] = " AND client = '{$this->_client}' ";
		}
		
		
		// get client details
		$this->_sql['select'] .= ", `c`.`title` AS client_title";
		$this->_sql['joins'] .= " LEFT JOIN `client` `c` ON `c`.`id` = `t1`.`client`";
		
		
	}
	
	
	
	/**
	 *	setTotal
	 *	Add up all the hours in a data set to get the cumulative total
	 */
	public function setTotalHours(){
		
		
		if(empty($this->_id) && !empty($this->_properties)){
			foreach($this->_properties as $timing){
				$this->_totalHours += (float)$timing['duration'];
			}
		} else{
			$this->_totalHours = $this->_properties['duration'];
		}
		
		
	}
	
	/**
	 *	setProjectTotal
	 *	Add up all the hours in a data set to get the cumulative total
	 */
	public function setTotalProjectHours(){

		$value = $this->_db->escape($this->_project);
		$query = "SELECT SUM(`duration`) AS total_hours FROM `{$this->_sql['main_table']}` WHERE `project` = '{$value}';";

		$result = $this->_db->get_row($query);

		$this->_totalProjectHours = $result->total_hours;
		
	}
	
	
	/**
	 *	getTotalHours()
	 *	@return int
	 */
	public function getTotalHours(){
		return $this->_totalHours;
	}
	
	
	/**
	 *	getTotalProjectHours()
	 *	@return int
	 */
	public function getTotalProjectHours(){
		return $this->_totalProjectHours;
	}
	
	
	
	/**
		 *	setFirstYear
		 *	grab the first ever project and use that as the 
		 *	(glorious) first trading date
		 */
		public function setFirstYear(){
			// 
			$query = "SELECT `start_date` FROM `{$this->_sql['main_table']}` t WHERE 1 ORDER BY `start_date` ASC LIMIT 1;";
					
			niceError($query); // Debugging echo SQL
			
			$objCache = new Cache($this->_name . '_first_date.cache', 1, 'account');
			if($objCache->getCacheExists() === true){
				$this->_firstYear = $objCache->getCache();
			} else{

				if($result = $this->_db->get_var($query)){
					$this->_firstYear = $result;
				} else{
					// no results, so first year is this year
					$this->_firstYear = date('Y-m-d H:i:s');
				}
				
				$objCache->createCache($this->_firstYear);
			}
			// End cache
		}
	
	
	
	/**
	 *	getFirstYear()
	 *	@return int
	 */
	public function getFirstYear(){
		return $this->_firstYear;
	}

	


}
	
?>