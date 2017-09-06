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
 *	@modified	06/09/2017
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

	class AccountOverview Extends Account{

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

			$this->_folder = '/' . $this->_namePlural . '/';

		}

		// Methods


		/**
		 *	setData
		 *	@see	Account::setData
		 */
		public function setData($filters){

			parent::setData($filters);
			$this->setMonthlyFigures($filters);

		}

		/**
		 *	setMonths()
		 */
		protected function setMonths($filters){

			if(!empty($this->_properties)){

				$properties_size = sizeof($this->_properties);

				for($i = 0; $i < $properties_size; $i++){

					// get year then month from transaction date
					$year = date('Y', $this->_properties[$i]['timestamp']);
					$month = date('m', $this->_properties[$i]['timestamp']);

					// Only show months in the present/past
					if(strtotime($year . '-' . $month . '-01 00:00:00') <= strtotime(date('Y-m-d'))){
						$this->_months[$year . '-' . $month][] = $this->_properties[$i];
					}

				}

			}

			// If the month hasn't got any data in it then we still
			// need them as array keys to show empty months in the view

			$start_date_x = date('Ym', strtotime($filters['timeframe_custom']['start']));
			$end_date_x = date('Ym', strtotime($filters['timeframe_custom']['end']));

			for($i = min($end_date_x, date('Ym')); $i >= $start_date_x; $i--){

				$year = substr($i, 0, 4);
				$month_digits = substr($i, 4);
				$month = date('m', strtotime($year . '-' . $month_digits . '-01 00:00:00'));

				if(((int)$month_digits < 12 && (int)$month_digits > 0) && empty($this->_months[$year . '-' . $month])){
					array_unshift_associative($this->_months, $year . '-' . $month, array());
				}

				krsort($this->_months);

			}

		}

		/**
		 *	setMonthlyFigures()
		 */
		protected function setMonthlyFigures($filters){

			$this->setMonths($filters);

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
			return read($this->_monthlyProfit, $month, 0);
		}

		/**
		 *	getMonthlyLoss()
		 *	@param string $month
		 */
		public function getMonthlyLoss($month){
			return read($this->_monthlyLoss, $month, 0);
		}

		/**
		 *	getMonthlySubtotal()
		 *	@param string $month
		 */
		public function getMonthlySubtotal($month){
			return read($this->_monthlySubtotal, $month, 0);
		}

	}
?>
