<?php
/**
 *
 *	=========================================================================
 *
 *	Ical Class
 *	-------------------------------------------------------------------------
 *	
 *	Format a single event into an iCal friendly text file 
 *	@usage
 *	$ical = new Ical($properties)
 *	$ical->showIcal();
 *	
 *	=========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 		2008-2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version		1.0	
 *	@author			philthompson.co.uk
 *	@since			13/04/2009
 *	
 *	@lastmodified	13/03/2010
 *	
 *	=========================================================================

 *	
 *	=========================================================================
 *	
 *	Table of contents:
 *	-------------------------------------------------------------------------
 *	
 *	Variables
 *	Methods
 *	Constructor
 *		build
 *		showIcal			
 *	
 *	=========================================================================
 *
 */


	class Ical{
	
	
		/**
		 *	@var string
		 */
		protected $_ical = '';
		
		/**
		 *	@var array
		 */
		protected $_properties = array();
	
		/**
		 *	Constructor
		 */
		public function __construct($properties){
		
			$this->_properties = $properties;
			
			$this->build();
			
		}
		
		
		/**
		 *	build
		 */
		protected function build(){
		
			// Timezone - where in the world are we?
			$timezone = 'Europe/London';
			
			// Summary (title) of event
			$summary = trim(read($this->_properties, 'title', ''));
			
			// Start date - format YYYYMMDDTHHMMSS
			$dtstart = date('Ymd', strtotime($this->_properties['date_started'])) . 'T' . date('His', strtotime($this->_properties['date_started']));
			
			//Date Stamp (not sure what for) format YYYYMMDDTHHMMSSZ
			$dtstamp = $dtstart . 'Z';
			
			// End date - format YYYYMMDDTHHMMSS
			$dtend = date('Ymd', strtotime($this->_properties['date_ended'])) . 'T' . date('His', strtotime($this->_properties['date_ended']));
			
			// Unqiue ID
			$uid = $this->_properties['id'];
			
			// Calendar name 
			$calName = trim(SITE_NAME);
			
			// Event description
			$description  = trim(strip_tags(str_replace(array("\n", "\r"), '', read($this->_properties, 'description', ''))));
			
			// Build the Ical file
			$this->_ical = 'BEGIN:VCALENDAR
			CALSCALE:GREGORIAN
			X-WR-TIMEZONE;VALUE=TEXT:' . $timezone . '
			METHOD:PUBLISH
			PRODID:-//Apple Computer\, Inc//iCal 1.0//EN
			X-WR-CALNAME;VALUE=TEXT:' . $calName . '
			VERSION:2.0
			BEGIN:VEVENT
			SEQUENCE:0
			DTSTART;TZID=' . $timezone . ':' . $dtstart . '
			DTSTAMP:' . $dtstamp .' 
			SUMMARY:' . $summary. '
			UID:' . $uid . '
			DTEND;TZID=' . $timezone . ':
			DESCRIPTION:' . $description . '
			END:VEVENT
			END:VCALENDAR';
			
			
			$this->_ical = str_replace("\t", '', $this->_ical);
			
		}
		
		
		/**
		 *	showICal()
		 */
		public function showIcal(){
			// Define the file as an iCalendar file
			header("Content-Type: text/Calendar");
			// Give the file a name and force download
			header("Content-Disposition: inline; filename=calendar.ics");
			echo $this->_ical;
			exit;
		}
	
	
	}
	
?>