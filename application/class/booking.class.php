<?php
/**
 *	=========================================================================
 *	
 *	Booking Class
 *	-------------------------------------------------------------------------
 *	
 *	Add/Edit/Delete/View Bookings from database
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
 *	@since		01/12/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	13/04/2009
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
 *		customQueryFilters()			
 *		duplicate()			
 *		setDateArray()
 *		getCSSHeight
 *
 *  =========================================================================
 *
 */
	
	


	class Booking extends Scaffold{
	
		
		// Variables		
		protected $_name;
		protected $propertiesDate;
	
		// construct
		public function __construct($db, $filter, $id = false){
		
			$this->_id = $id;
		
			// Object naming conventions
			$this->_name = 'booking';		
		
			// SQL - Database related namings
			$this->_sql['main_table'] =  'booking';
			
			
			// Timeframe
			// show data from a user defined timeframe 
			// e.g. from one date to another
			$this->_timeframeCustom = (isset($this->_timeframeCustom)) ? $this->_timeframeCustom : read($filter, 'timeframe_custom', ''); 

			
			parent::__construct($db, $filter, $this->_id);

			
			// Format the object name so it looks better in the view
			$this->_name = 'booking';
			
			// Calendar format
			if(!$this->_id){
				$this->setDateArray();
			}

		}
		
		
		/**
		 *	customQueryFilters
		 *	@return	void
		 */
		public function customQueryFilters(){
		
			$this->_queryFilter['custom'] = '';
			
			// Use our custom timeframe
			if(!$this->_id){
				// Custom timeframe filter
				if($this->_timeframeCustom && strlen($this->_timeframeCustom['start']) == 19 && strlen($this->_timeframeCustom['end']) == 19){				
					$this->_queryFilter['timeframe'] = " AND (t.date_started BETWEEN '{$this->_timeframeCustom['start']}' AND '{$this->_timeframeCustom['end']}' OR t.date_ended BETWEEN '{$this->_timeframeCustom['start']}' AND '{$this->_timeframeCustom['end']}') ";
					
				}
			} else{
				$this->_queryFilter['timeframe'] = ' AND 1 ';
			}
			
			
			// Always order by date started
			$this->_queryFilter['order_by'] = 't.date_started ASC';
			
		}
		
		/**
		 *	duplicate
		 */
		public function duplicate(){
			return $this->add();
		}
		
		/**
		 *	setDateArray()
		 *	Put in a date array
		 */
		public function setDateArray(){
			
			$this->_propertiesDate = array();
			
			
			
			if(!empty($this->_properties) && !$this->_id){
				foreach($this->_properties as $property){
					$start_day = date('j', strtotime($property['date_started']));
					$start_month = date('m', strtotime($property['date_started']));
					$start_year = date('Y', strtotime($property['date_started']));
					$end_day = date('j', strtotime($property['date_ended']));
					$end_month = date('m', strtotime($property['date_ended']));
					$end_year = date('Y', strtotime($property['date_ended']));
					
					
					
					if(
						$start_day ==  $end_day
						&& $start_month == $end_month
						&& $start_year == $end_year
					){
						$property['class'] = 'single';
					} else{
						$property['class'] = 'multiple';
					}
					
					if($start_month == date('m', strtotime($this->_timeframeCustom['start'])) ){
						$this->_propertiesDate[$start_day][] = $property;
					} 
					
					// Add booking to every day it spans
					if(strtotime($property['date_ended']) > strtotime($property['date_started'])){
						$total_days = ($end_day - $start_day);

						// Both days in the same month
						if($start_month == $end_month){
						
							for($i = ($start_day + 1); $i <= ($start_day + $total_days); $i++){
							
								$current_day = date('N', strtotime($start_year . '-' . $start_month . '-' . $i));
								
								if($property['include_weekends'] == 'Y'){
									$this->_propertiesDate[$i][] = $property;
								} else if($property['include_weekends'] == 'N' && $current_day < 6){
									$this->_propertiesDate[$i][] = $property;
								}

							}
							
							//print_r($this->_propertiesDate);
							
						} else if($start_month != $end_month){ 
							
							// start date is in month(s) previous to end date
							// end month is this month showing
							if($end_month == date('m', strtotime($this->_timeframeCustom['start']))){
							
								for($i = $end_day; $i > 0; $i--){
								
									$current_day = date('N', strtotime($end_year . '-' . $end_month . '-' . $i));
									
									if($property['include_weekends'] == 'Y'){
										$this->_propertiesDate[$i][] = $property;
									} else if($property['include_weekends'] == 'N' && $current_day < 6){
										$this->_propertiesDate[$i][] = $property;
									}
									
								}
								
							} else if(
								/*$end_month < date('m', strtotime($this->_timeframeCustom['start'])) 
								&& */
								$end_year > date('Y', strtotime($this->_timeframeCustom['start']))
							){	
								// How many days in total?
								$days_in_start_month = cal_days_in_month(CAL_GREGORIAN, $start_month, $start_year); // 31
								
								$total_days = ($days_in_start_month - ($start_day-1));
							
								// end month is in a future month next year
								for($i = $start_day; $i <= $days_in_start_month; $i++){
									if($i != $start_day){
										$current_day = date('N', strtotime($start_year . '-' . $start_month . '-' . $i));
									
										if($property['include_weekends'] == 'Y'){
											$this->_propertiesDate[$i][] = $property;
										} else if($property['include_weekends'] == 'N' && $current_day < 6){
											$this->_propertiesDate[$i][] = $property;
										}
										
									}	
								
								}
								
							} else if(
								$end_month > date('m', strtotime($this->_timeframeCustom['start'])
								&& $end_year == date('Y', strtotime($this->_timeframeCustom['start'])))
							){
								// end month is in a future month
								
								for($i = $start_day; $i < 32; $i++){
								
									$current_day = date('N', strtotime($start_year . '-' . $start_month . '-' . $i));
									if($i != $start_day){

										if($property['include_weekends'] == 'Y'){
											$this->_propertiesDate[$i][] = $property;
										} else if($property['include_weekends'] == 'N' && $current_day < 6){
											$this->_propertiesDate[$i][] = $property;
										}
										
									}
									
									
								}
								
							}
							
						}
						
					}
				}
			}
			
			
		}
		
		
		/**
		 *	getCSSHeight()
		 *	Work out how long each booking was in hours and make the block fatter if needs be
		 *	@param	array 	booking details
		 *	@param	int		the height in pixels that a one hour booking should be
		 *	@return	int
		 */
		public function getCSSHeight($booking, $default_height = 15){
		
			$end = strtotime($booking['date_ended']);
			$start = strtotime($booking['date_started']);
		
			if(date('Ymd', $end) > date('Ymd', $start)){
				$hours = 7; // booking ends another day therefore this is an all day booking.
			} else{
				$hours = ceil(($end-$start)/3600);
			}
			
			return ($hours * $default_height);	      
		
		}
		
		
		/**
		 *	getPropertiesDate()
		 */
		public function getPropertiesDate(){
			return $this->_propertiesDate;
		}
		
		
		/**
		 *	ical
		 */
		public function ical(){
			$objIcal = new Ical($this->_properties);
			$objIcal->showIcal();
		
		}
	
	}