<?php
/**
 *	DateFormat Class
 *	Create nice looking dates based on the given format. A bit over the top
 *	but it saves us littering code with strtotime() which look unsightly:)
 *	
 *	@package	bean counter	
 *	@copyright 	2008-2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@version	1.1	
 *	@author		philthompson.co.uk
 *	@since		02/02/2009
 *		
 *	Contents
 *
 *	Variables
 *	
 *	Methods
 *		Constructor
 *		getDate
 *		addDays
 *		removeDays
 *		howManyDays
 *		removeHours
 *		getRelativeTime
 *		
 */

class DateFormat{

	// Variables
	
	/**
	 *	@var int
	 */
	public static $secondsInADay = 86400;
	
	/**
	 *	@var int
	 */
	public static $secondsInAnHour = 3600;
	
	/**
	 *	Constructor
	 *	@param (bool|string) $timestamp
	 */
	public function __construct(){
		
	}
	
	
	/**
	 *	getDate()
	 *	@param string $format
	 *	@param string $timestamp (FORMAT: YYYY-MM-DD HH:mm:ss)
	 */
	public static function getDate($format, $timestamp){
	
		$formattedDate = '';

		if(!empty($timestamp) && $timestamp != '0000-00-00 00:00:00' && $timestamp != NULL){
		
			$date = strtotime($timestamp);
			

		
			switch(strtolower($format)){
				
				default:
				case 'datetime': // 1st January 2009 at 12:01
					$formattedDate = date('jS F Y \a\t H:i', $date);
					break;
				
				case 'ddmmyyyy': // UK 31/01/2009
					$formattedDate = date('d/m/Y', $date);
					break;
					
				case 'mmddyyyy': // USA 01/31/2009
					$formattedDate = date('m/d/Y', $date);
					break;
					
				case 'ddmmyy': // 28/02/04
					$formattedDate = date('d/m/y', $date);
					break;
				
				case 'date': // 1st January 2009
					$formattedDate = date('jS F Y', $date);
					break;
				
				case 'date-with-day': // Day 1st January 2009
					$formattedDate = date('D jS F Y', $date);
					break;
					
				case 'time': // 10:55
					$formattedDate = date('H:i', $date);
					break;
				
				case 'rss': // Tue, 10 Feb 2009 10:20:35 0000
					$formattedDate = date('D, j M Y H:i:s O', $date);
					break;
				
				case 'xml-sitemap': // 2008-04-07T18:49:04+00:00
					$formattedDate = date('Y-m-d\TH:i:s O', $date);
					break;
				
			}
		
		}
			
		return $formattedDate;
		
	}
	
	/**
	 *	addDays()
	 *	add a set number of days to a given date and 
	 *	return the new date		 
	 *	@param 	string 	$date
	 *	@param 	string 	$days_added
	 *	@return	string 	$new_date
	 */
	public static function addDays($date, $days_added = 0){
		
		$total_seconds = (self::$secondsInADay * $days_added);
		$date = strtotime($date);
		$new_date = ($date + $total_seconds);
		
		$new_date = date('Y\-m\-d \0\0\:\0\0\:\0\0', $new_date);
		
		return $new_date;
	}
	
	
	/**
	 *	removeDays()
	 *	subtract a set number of days to a given date and 
	 *	return the new date
	 *	@param 	string 	$date
	 *	@param 	string 	$days_removed
	 *	@return	string 	$new_date
	 */
	public static function removeDays($date, $days_removed){
		
		$total_seconds = (self::$secondsInADay * $days_removed);
		$date = strtotime($date);
		$new_date = ($date - $total_seconds);
		
		$new_date = date('Y\-m\-d \0\0\:\0\0\:\0\0', $new_date);
		
		return $new_date;
	}


	/**
	 *	howManyDays()
	 *	returns the number of days between 2 given dates
	 *	@param 	string 	$first_date
	 *	@param 	string 	$second_date
	 *	@return	string 	$new_date
	 */
	public static function howManyDays($first_date, $second_date = NULL){
		
		if($second_date == NULL){ // if no second date present
			$second_date = date('Y-m-d H:i:s'); // use today as the 2nd date
		}
	
		// convert format - if it needs it
		if(
			empty($first_date) 
			|| (
				!is_string($first_date) && !is_numeric($first_date)
			) 
			|| strlen($first_date) == 19
		){
			$first_date = strtotime($first_date);
		}
		
		if(
			empty($second_date) 
			|| (
			!	is_string($second_date) && !is_numeric($second_date)
			) 
			|| strlen($second_date) == 19
		){
			$second_date = strtotime($second_date);
		}
		

		
		$offset = ($second_date - $first_date);
		$offset_in_seconds = ($offset / self::$secondsInADay);
		
		$new_date = round($offset_in_seconds);
		$new_date .= ($new_date == 1) ? ' day' : ' days';
	
		return $new_date;
	}
	
	
	
	/**
	 *	removeHours()
	 *	return the date given but with the given number of hours removed
	 *	@param 	string 	$date
	 *	@param 	int 	$hours_added
	 *	@return	string 	$new_date
	 */
	public static function removeHours($date, $hours_removed){
	
		$total_seconds = (self::$secondsInAnHour * $hours_removed);
		$date = strtotime($date);
		$new_date = ($date - $total_seconds);
		$new_date = date('Y\-m\-d H\:i\:s', $new_date);
		
		return $new_date;
		
	}
	
	
	/**
	 *	getRelativeTime
	 *	@param	(int|boolean) 	$time	 
	 *	@param	int 			$limit
	 *	@param	string 			$format
	 *	@return string 			$relative;
	 *	@copyright	http://dev-tips.com/featured/creating-a-relative-time-function
	 */
	public static function getRelativeTime($time = false, $limit = 86400, $format = 'datetime') {
		
		// convert format to timestamp if it's needed
		if(empty($time) || (!is_string($time) && !is_numeric($time))){
			 $time = time();
		} else if(is_string($time)){
			$time = strtotime($time);
		}

		
		$now = time();
		$relative = '';
		
		// check supplied time against current time
		if($time === $now){
			$relative = 'now';
		} else if($time > $now){
			$relative = 'in the future';
		} else{
			$diff = $now - $time;
			
			if ($diff >= $limit){
				// this is over the limit so show days not hours
				$relative = self::howManyDays($time, $now) . ' ago';
			} else if($diff < 60){
				$relative = 'less than one minute ago';
			} else if(($minutes = ceil($diff/60)) < 60) {
				$relative = $minutes . ' minute' . (((int)$minutes === 1) ? '' : 's') . ' ago';
			} else{
				$hours = ceil($diff/3600);
				$relative = 'about ' . $hours . ' hour' . (((int)$hours === 1) ? '' : 's') . ' ago';
			}
			
		}
		
		return $relative;
		
	}

	


}
?>