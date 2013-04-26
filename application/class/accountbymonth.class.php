<?php
/**
 *	=========================================================================
 *	
 *	AccountByMonth Class
 *	-------------------------------------------------------------------------
 *	Extends Account class to work out the incomings, outogings, and
 *	profit/loss for each month in the supplied timeframe.
 *	
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
 *	@since		2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	12/04/2009
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
 *		setMonths
 *		setMonthlyFigures
 *		getters...
 *	
 *	=========================================================================
 */

	class AccountByMonth Extends Account{
	
		// Variables
		
		/**
		 *	@var array
		 */
		protected $_months = array();
		
		/**
		 *	@var array
		 */
		protected $_monthlyProfit = array();
		
		/**
		 *	@var array
		 */
		protected $_monthlyLoss = array();
		
		/**
		 *	@var array
		 */
		protected $_monthlySubtotal = array();

		
		// Constructor
		public function __construct($db){
			
			parent::__construct($db);
			$this->_folder .= 'bymonth/';
		}
		
		// Methods	
		
		
		/**
		 *	setData
		 *	@see	Account::setData
		 */
		public function setData($filters){
			
			parent::setData($filters);
			$this->setMonthlyFigures();
		}
		
		/**
		 *	setMonths()
		 */
		protected function setMonths(){
		
			
		
			if(!empty($this->_properties)){
			
				$properties_size = sizeof($this->_properties);
			
				for($i = 0; $i < $properties_size; $i++){
					
					// get year then month from transaction date
					$year = date('Y', $this->_properties[$i]['timestamp']);
					$month = date('F', $this->_properties[$i]['timestamp']);
					
					$this->_months[$month . ' ' . $year][] = $this->_properties[$i];
					
				}
				
			}
			

		}
		
		/**
		 *	setMonthlyFigures()
		 */
		protected function setMonthlyFigures(){
			
			
			$this->setMonths();
			
			if(!empty($this->_months)){
				foreach($this->_months as $key => $value){
				
					$this->_monthlyProfit[$key] = 0;
					$this->_monthlyLoss[$key] = 0;
					$this->_monthlySubtotal[$key] = 0;
	
					foreach($value as $subkey => $subvalue){
		
						if($subvalue['type'] == 'positive'){
							$this->_monthlyProfit[$key] += $subvalue['price'];
						} else{
							$this->_monthlyLoss[$key] += $subvalue['price'];
						}
						
					}
					
					$this->_monthlySubtotal[$key] = ($this->_monthlyProfit[$key] + $this->_monthlyLoss[$key]);
				
				}
			}
			
		}
		
		
		/**
		 *	getMonths()
		 */
		public function getMonths(){
			return $this->_months;
		}
		
		/**
		 *	getMonthlyProfit()
		 *	@param string $month
		 */
		public function getMonthlyProfit($month){
			return $this->_monthlyProfit[$month];
		}
		
		/**
		 *	getMonthlyLoss()
		 *	@param string $month
		 */
		public function getMonthlyLoss($month){
			return $this->_monthlyLoss[$month];
		}
		
		/**
		 *	getMonthlySubtotal()
		 *	@param string $month
		 */
		public function getMonthlySubtotal($month){
			return $this->_monthlySubtotal[$month];
		}
		
	
	
	}
?>