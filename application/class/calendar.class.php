<?php
/**
 *	=========================================================================
 *	
 *	Calendar Class
 *	-------------------------------------------------------------------------
 *	Work out different month related items e.g.
 *	hwo many days in this month? What day does it start on etc.
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
 *	@since		12/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	14/04/2009
 *	
 *  =========================================================================
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	Variables
 *	
 *	Constructor
 *	
 *	Methods
 *		setMonthName
 *		setWeeks
 *		setDays
 *		setStartWeekDay
 *		validDate
 *	
 *	=========================================================================
 */
	 
	 class Calendar{
	 
	 	// Variables
	 	
	 	/**
		 *	@var int
		 */
	 	protected $_year;
	 	
	 	/**
		 *	@var int
		 */
	 	protected $_month;
	 	
	 	/**
		 *	@var int	
		 */
	 	protected $_monthName;
	 	
	 	/**
		 *	@var int	
		 */
	 	protected $_days = 0;
	 	
	 	/**
		 *	@var int	
		 */
	 	protected $_weeks = 0;
	 	
	 	/**
		 *	@var int	
		 */
	 	protected $_startWeekday;
	 	
	 	/**
		 *	Constructor
		 *	@param int $month 09
		 *	@param int $year 2009
		 */
	 	public function __construct($month, $year){
	 	
	 		$this->_month = (int)$month;
	 		$this->_year = (int)$year;
	 		
	 		$this->setDays();
	 		$this->setStartWeekDay();
	 		$this->setWeeks();	 		
	 		$this->setMonthName();
	 	}
	 	
	 	// Methods
	 	
	 	/**
	 	 *	setMonthName
	 	 */
	 	protected function setMonthName(){
	 		$this->_monthName = date('F', strtotime($this->_year . '-' . $this->_month . '-01 00:00:00'));
	 	}
	 	
	 	
	 	/**
	 	 *	setWeeks
	 	 *	how many weeks are in the given month
	 	 */
	 	protected function setWeeks(){

	 		$this->_weeks = ceil(($this->_startWeekDay + $this->_days) / 7);
	 		
	 		// FYI When certain months start on certain days we 
	 		// need to round up slightly differently
	 		// e.g. 
	 		// # a 31 day month starting on a Friday (Day 5)
	 		// # a 30 day month starting on a Saturday (Day 6)
	 		// # a 29 day month starting on a Sunday (Day 7)
	 		// # a 28 day month starting on a Monday (Day 1)
	 		if( ($this->_startWeekDay == 5 && $this->_days == 31) || ($this->_startWeekDay == 6 && $this->_days == 30) || ($this->_startWeekDay == 7 && $this->_days == 29) || ($this->_startWeekDay == 1 && $this->_days == 28) ){
	 			$this->_weeks = ceil($this->_days / 7);
	 		}
	 		
	 	}
	 	
	 	/**
	 	 *	setDays
	 	 *	how many days are in the given month?
	 	 *	31, 30, 29 or 28
	 	 */
	 	protected function setDays(){
	 		
	 		switch($this->_month){
	 		
	 			default:
	 			case 1:
	 			case 3:
	 			case 5:
	 			case 7:
	 			case 8:
	 			case 10:
	 			case 12:
	 				$this->_days = 31;
	 				break;
	 				
	 			case 4:
	 			case 6:
	 			case 9:
	 			case 11:
	 				$this->_days = 30;
	 				break;
	 				
	 			case 2:
	 				// Default of 28 days but check to see if
	 				// we're in a leap year and if so, make it 29 days
	 				$this->_days = 28;
	 				
	 				$leap_year = date('L', strtotime($this->_year . '-01-01 00:00:00'));
	 				if($leap_year == 1){
	 					$this->_days = 29; 
	 				}
	 				
	 				break;	
	 		}
	 		
	 	}
	 	
	 	/**
	 	 *	setStartWeekDay()
	 	 *	What day does this month start on?
	 	 */
	 	protected function setStartWeekDay(){
	 	
	 		// Create a timestamp for the first day of this month
	 		// then work out what the day was from that.
	 		$day = $this->_year . '-' . $this->_month . '-01 00:00:00';
	 		$day = strtotime(date($day));
	 		$this->_startWeekDay = date('N', $day);
	 		
	 	}
	 	
	 	/**
	 	 *	validDate
	 	 *	Is the supplied date a valid one
	 	 *	e.g. 31st February isn't :)
	 	 *	@param $day (int)
	 	 *	@retrun boolean
	 	 */
	 	public function validDate($day){
	 	
	 		$valid = true;
	 		
	 		if($day > $this->_days){
	 			$valid = false;
	 		}
	 		
	 		return $valid;
	 		
	 	}
	 	
	 	
	 	
	 	/**
		 *	getYear()
		 *	@return string $_year
		 */
		public function getYear(){
			return $this->_year;
		}
	 	
	 	/**
		 *	getMonth()
		 *	@return string $_month
		 */
		public function getMonth(){
			return $this->_month;
		}
	 	
	 	/**
		 *	getDays()
		 *	@return string $_days
		 */
		public function getDays(){
			return $this->_days;
		}
	 	
	 	/**
		 *	getStartWeekDay()
		 *	@return string $_
		 */
		public function getStartWeekDay(){
			return $this->_startWeekDay;
		}
	 	
	 	/**
		 *	getWeeks()
		 *	@return string $_weeks
		 */
		public function getWeeks(){
			return $this->_weeks;
		}
		
		/**
		 *	getMonthName()
		 *	@return string $_monthName
		 */
		public function getMonthName(){
			return $this->_monthName;
		}
	 	
	 	
	 
	 }


?>