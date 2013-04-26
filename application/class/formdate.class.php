<?php
/**
 *	=========================================================================
 *
 *	FormDate Class
 *	-------------------------------------------------------------------------
 *	
 *	Creates <select> tags for a user to pick a date.
 *	
 *	=========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 		2008-2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license		n/a
 *	@version		1.0	
 *	@author			philthompson.co.uk
 *	@since			2007 
 *	@lastmodified	12/12/2009
 *	
 *	=========================================================================
 *	
 *	
 *	Benefits: 
 *		#	Works out current time/date
 *		#	Pre-selects values if user has submitted the form previously
 *		#	Saves on wriitng out boring <select>s all day long:)
 *	
 *	Limitations: 
 *		#	Doesn't show errors is user select non-existent date 
 *			e.g. 30/02/09
 *		#	Doesn't work our current time in minutes and defaults them to 
 *			the closest interval 
 *			e.g. 13 minutes should be rounded up to 15 or 10
 *		#	Doesn't (yet) allow the user to set a default in the function
 *			age limiting is the best, it only works on year.
 *		#	No error reporting
 *	
 *		But you could argue these limitations belong elsewhere - e.g. in a
 *		JavaScript file or in a separate PHP error/exception class.			
 *
 *	
 *	=========================================================================
 *	
 *	Table of contents:
 *	-------------------------------------------------------------------------
 *	
 *	Variables
 *	Methods
 *		Constructor
 *		getYear
 *		getMonth
 *		getDay
 *		getHour
 *		getMinute
 *		createSelect
 *		getSelectId
 *				
 *	=========================================================================
 *
 */


class FormDate{

	// Variables	
	
	/**
	 *	Constructor
	 *	Not used
	 */
	public function __construct(){ }
	
	/**
	 *	getMonth
	 *	Create a drop down menu to allow users to select a year within a 
	 *	certain range ($total_years) and based on a tense ($tense) of 
	 *	either future or past. 'Past' starts with this year and goes backwards
	 *	whereas 'future' starts with this date and goes forward
	 
	 * 	The currently selected year (via $current_value) should be
	 *	pre-selected also.
	 *		
	 *	@param 	string 	$element_name = name/id of the <select element>
	 *	@param 	int		$total_years = how many year you want in the form 
	 					e.g. 3
	 *	@param 	string 	$tense = is the form to show years in the 'future', 'past', or 'both'. 
	 *			 		Future is the default.
	 *	@param 	int		$age_limit = does the user have to be over a certain age 
	 					e.g. 18 or 21?
	 *	@return string
	 */
	public static function getYear(
		$element_name, 
		$current_value = '', 
		$total_years = 3, 
		$tense = 'future', 
		$age_limit = NULL
	){

		
		$element_name = $element_name . '_year';
		
		$current_value = (empty($current_value)) ? date('Y') : $current_value;
		
		//$current_value = (!$current_value) ? read($_REQUEST,$element_name,date('Y')) : date('Y',strtotime($current_value));	
					
		$current_value = (!$current_value) ? read($_REQUEST, $element_name, date('Y')) :  (strlen($current_value) > 4 ) ? date('Y', strtotime($current_value)) : $current_value;	
		
		// start by opening the <select> element
		$year = '<label for="' . self::createSelectId($element_name) . '" class="hide">Year</label>' . "\n\t";
		$year .= '<select name="' . $element_name . '" id="' . self::createSelectId($element_name) . '" class="date year">' . "\n\t";
		
		
		// work if we're looking dates in the future
		switch($tense){
		
			default:
			case 'future':
				$start_year = date('Y');
				$end_year = (date('Y') + $total_years);
				break;
			
			// or dates in the past
			case 'past':
				$start_year = (date('Y') - $total_years);
				$end_year = date('Y');
				// check for an age limit & only show years that are within the allowed range
				if($age_limit != NULL){
					$end_year =  ($end_year - $age_limit);
				}
				break;
			
			// or dates in the past and the future
			case 'both':
				$start_year = (date('Y') - $total_years);
				$end_year = (date('Y') + $total_years);
				break;
				
		}
		
		/* if the current year is less than the start year (e.g. we're only showing 5 years and the current year is 6 years ago) add it to the beginning pre selected */
		$year .= (!empty($current_value) && $current_value < $start_year) ?  ' <option value="' . $current_value . '" selected="selected">' . $current_value . '</option>' . "\n\t" : '';
		
		// then create the <option>s
		for($i = $start_year; $i <= $end_year; $i++){
			unset($selected);
			// check if the form has been posted and set the selected <option> to the user selection
			$selected = ($current_value == $i) ? ' selected="selected"' : '';
			// create <option>
			$year .= ' <option value="' . $i . '"' . $selected . '>' . $i . '</option>' . "\n\t";
		} // end for		
		
		$year .= "</select>\n";
		
		return $year;
	} // end getYear method
	
	
	/**
	 *	getMonth
	 *	Create a drop down menu to allow users to select a month of the year. 
	 * 	The currently selected month (via $current_value) should be
	 *	pre-selected also.
	 *	
	 *	@param 	string 	$element_name
	 *	@param 	int 	$current_value
	 *	@return string 	$month
	 */
	public static function getMonth($element_name, $current_value = ''){
		
		$element_name = $element_name . '_month';
		
		$current_value = (empty($current_value)) ? date('m') : $current_value;
		
		//$current_value = (!$current_value) ? read($_REQUEST,$element_name,date('m')) : date('m',strtotime($current_value));
		
		$current_value = (!$current_value) ? read($_REQUEST, $element_name, date('m')) :  (strlen($current_value) > 2) ? date('m', strtotime($current_value)) : $current_value;	
	

		// start by opening the <select> element
		$month = '<label for="' . self::createSelectId($element_name) . '" class="hide">Month</label>' . "\n\t";
		$month .= '<select name="' . $element_name . '" id="' . self::createSelectId($element_name) . '" class="date month">' . "\n\t";
		for($i = 1; $i <= 12; $i++){
			unset($selected);
			
			// values
			$month_name = date('F', mktime(12, 0, 0, $i, 1, date('Y')));
			$month_value = date('m', mktime(12, 0, 0, $i, 1, date('Y')));
			
			// work out which month is pre selected
			$selected = ($current_value == $month_value || (!$current_value && date('m') == $month_value)) ? ' selected="selected"': '';
		
			// create <option>
			$month .= '  <option value="' . $month_value . '"' . $selected . '>' . $month_name . '</option>' . "\n\t";
		} // end for
		
		
		$month .= "</select>\n";
		
		return $month;
	} // end getMonth method
	
	
	/**
	 *	getDay()
	 *	Create a drop down menu to allow users to select a day of the month. 
	 * 	The currently selected day (via $current_value) should be
	 *	pre-selected also.
	 *
	 *	@param 	string 	$element_name
	 *	@param 	int 	$current_value
	 *	@return string 	$day
	 */
	public static function getDay($element_name, $current_value = ''){
		
		$element_name = $element_name . '_day';
		
		$current_value = (empty($current_value)) ? date('d') : $current_value;
		
		//$current_value = (!$current_value) ? read($_REQUEST,$element_name,date('d')) :  date('d',strtotime($current_value));
		
		$current_value = (!$current_value) ? read($_REQUEST, $element_name, date('d')) :  (strlen($current_value) > 2) ? date('d', strtotime($current_value)) : $current_value;			
		
	
		// start by opening the <select> element
		$day = '<label for="' . self::createSelectId($element_name).'" class="hide">Day</label>' . "\n\t";
		$day .= '<select name="' . $element_name . '" id="' . self::createSelectId($element_name) . '" class="date day">' . "\n\t";

		// create all <option>s
		for($i = 1; $i <= 31; $i++){
			unset($selected);
			// day value = add leading zero if digit is only 1-9
			$day_value = (strlen($i) == 1) ? '0' . $i : $i;

			// work out which item is pre selected (if any)
			$selected = ($current_value == $day_value) ? ' selected="selected"' : '';

			// create <option>
			$day .= '  <option value="' . $day_value . '"' . $selected . '>' . $day_value . '</option>' . "\n\t";
		}
		
		
		$day .= "</select>\n";
		
		return $day;
	} // end getDay method
	
	
	/**
	 *	getHour
	 *	Create a drop down menu to allow users to select an hour of the day. 
	 * 	The currently selected hour (via $current_value) should be
	 *	pre-selected also.
	 
	 *	@param 	string 	$element_name
	 *	@param 	int  	$current_value either a double digit number or a MySQL timestamp
	 *	@return string 	$hour
	 */
	public static function getHour($element_name, $current_value = ''){			
		

		$element_name = $element_name . '_hour';
		
		if(empty($current_value)){
			$current_value = read($_POST, $element_name, read($_GET, $element_name, '00'));
		}
		
		// current value is a timestamp so separate out the hour figure
		if(strlen(trim($current_value)) == 19){
			$current_value = substr($current_value, 11, 2);
		}
		
		// start by opening the <select> element
		$hour = '<label for="' . self::createSelectId($element_name) . '" class="hide">Hour</label>' . "\n\t";
		$hour .= '<select name="' . $element_name . '" id="' . self::createSelectId($element_name) . '" class="time">' . "\n\t";
		// create all <option>s
		for($i = 0; $i <= 23; $i++){
			unset($selected);
			//hour value add leading zero if digit is only 1-9
			$hour_value = str_pad($i, 2, 0, STR_PAD_LEFT);
			
			// check if the form has been posted and set the selected <option> to the user selection
			$selected = ($current_value == $hour_value) ? ' selected="selected"' : '';
			
			$hour .= ' <option value="' . $hour_value . '"' . $selected . '>' . $hour_value . '</option>' . "\n\t";
		} // end for
		
		
		$hour .= "</select>\n";
		return $hour;
		
	} // end getHour method
	
	
	/**
	 *	getMinute
	 *	Create a drop down menu to allow users to select a minute of the hour. 
	 *	Minutes are displayed by interval ($interval) so users don't get
	 *	60 drop down options if it isn't necessary:)
	 * 	The currently selected minute (via $current_value) should be
	 *	pre-selected also.
	 *
	 *	@param 	string $element_name
	 *	@param 	string $current_value either a double digit number or a MySQL timestamp
	 *	@return string $minute
     */
	public static function getMinute($element_name, $current_value, $interval = 15){
		
		
		$element_name = $element_name . '_minute';
		
		//$current_value = (!$current_value) ? read($_REQUEST,$element_name,date('i')) :  date('i',strtotime($current_value));
		
		if(empty($current_value)){
			$current_value = read($_POST, $element_name, read($_GET, $element_name, '00'));
		}
		
		
		// current value is a timestamp so separate out the hour figure
		if(strlen(trim($current_value)) == 19){
			$current_value = substr($current_value, 11, 2);
		}
		

		// start by opening the <select> element
		$minute = '<label for="' . self::createSelectId($element_name) . '" class="hide">Minute</label>' . "\n\t";
		$minute .= '<select name="' . $element_name . '" id="' . self::createSelectId($element_name) . '" class="time">' . "\n\t";
		
		
		// Do we need to round/up down current value to the closest interval?
		
		//create <option>s
		for($i = 0; $i <= 59; $i += $interval){
			unset($selected);
			
			// add a preceding zero if the minute is 0-9
			$minute_value = str_pad($i, 2, 0, STR_PAD_LEFT);
			
			// work out which item is to be pre selected
			$selected = ($current_value == $minute_value) ? ' selected="selected"' : ''; // Needs improvement
			
			// create <option>
			$minute .= ' <option value="' . $minute_value . '"' . $selected . '>' . $minute_value . '</option>' . "\n\t";

			
		} // end for
		
		$minute .= "</select>\n";
		return $minute;
		
	} // end getMinute method
	
	
	/**
	 *	createSelect()
	 *	draw drop down menu
	 *
	 *	@param 	array 	$options
	 *	@param 	int 	$element_name
	 *	@return string
	 */
	public static function createSelect($options, $element_name){
		
		// start by opening the <select> element
		$select = '<select name="' . $element_name . '" id="' . self::createSelectId($element_name) . '" class="date">' . "\n\t";
		// mix in the <option>s
		$select .= $options;
		$select .= "</select>\n";			
		return $select;
		
	} // end createSelect method
	
	
	/**
	 *	createSelectId()
	 *	id="" cannot have illegal characters so remove them
	 *	@param 	string	$element_name
	 *	@return	string	$clean_name
	 */
	public static function createSelectId($element_name){
		$clean_name = str_replace(']', '', str_replace('[', '_', $element_name));
		return $clean_name;
	} // end createSelectId method
	
}


?>