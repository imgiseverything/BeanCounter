<?php
/**
 *	=========================================================================
 *	
 *	HTML Template Class
 *	-------------------------------------------------------------------------
 *	
 *	This class takes variables set out for each view and converts the 
 *	into meaningful data values for inclusion in HTML templates.
 *	Date includes e.g. page <title>s or which CSS to show on each page.
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
 *	@since		10/01/2008
 *	
 *	edited by: 	Phil Thompson
 *	@modified	31/03/2009
 *	
 *	=========================================================================
 * 
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *	
 *	Variables
 *	Constructor
 *	Methods	
 *		startTimeer
 *		getSpeed
 *
 *
 */


	class Timer{
	
		/**
		 *	@var string
		 */
		private $_start;
	
		/**
		 *	Constructor
		 */
		public function __construct(){
			$this->startTimer();
		}
		
		/**
		 *	startTimer()
		 */
		private function startTimer(){
			$this->_start = microtime();
		}
		
		/**
		 *	getSpeed()
		 */
		public function getSpeed($finish_time){
		
			$startTime = $this->_start;

			list($secs, $usecs) = explode(' ', $this->_start);
			$start = $secs + $usecs;
			
			list($secs, $usecs) = explode(' ', $finish_time);
			$finish = $secs + $usecs;
			$time = $finish - $start;
			
			return $time . ' seconds';
		}
	
	}

?>