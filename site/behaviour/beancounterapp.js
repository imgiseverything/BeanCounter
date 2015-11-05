/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <jevin9@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return. Jevin O. Sewaruth
 * ----------------------------------------------------------------------------
 *
 * Autogrow Textarea Plugin Version v3.0
 * http://www.technoreply.com/autogrow-textarea-plugin-3-0
 * 
 * THIS PLUGIN IS DELIVERD ON A PAY WHAT YOU WHANT BASIS. IF THE PLUGIN WAS USEFUL TO YOU, PLEASE CONSIDER BUYING THE PLUGIN HERE :
 * https://sites.fastspring.com/technoreply/instant/autogrowtextareaplugin
 *
 * Date: October 15, 2012
 */

jQuery.fn.autoGrow = function() {
	return this.each(function() {

		var createMirror = function(textarea) {
			jQuery(textarea).after('<div class="autogrow-textarea-mirror"></div>');
			return jQuery(textarea).next('.autogrow-textarea-mirror')[0];
		}

		var sendContentToMirror = function (textarea) {
			mirror.innerHTML = String(textarea.value).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br />') + '.<br/>.';

			if (jQuery(textarea).height() != jQuery(mirror).height())
				jQuery(textarea).height(jQuery(mirror).height());
		}

		var growTextarea = function () {
			sendContentToMirror(this);
		}

		// Create a mirror
		var mirror = createMirror(this);
		
		// Style the mirror
		mirror.style.display = 'none';
		mirror.style.wordWrap = 'break-word';
		mirror.style.whiteSpace = 'normal';
		mirror.style.padding = jQuery(this).css('padding');
		mirror.style.width = jQuery(this).css('width');
		mirror.style.fontFamily = jQuery(this).css('font-family');
		mirror.style.fontSize = jQuery(this).css('font-size');
		mirror.style.lineHeight = jQuery(this).css('line-height');

		// Style the textarea
		this.style.overflow = "hidden";
		this.style.minHeight = this.rows+"em";

		// Bind the textarea's event
		this.onkeyup = growTextarea;

		// Fire the event for text already present
		sendContentToMirror(this);

	});
};;/*
 * Date prototype extensions. Doesn't depend on any
 * other code. Doens't overwrite existing methods.
 *
 * Adds dayNames, abbrDayNames, monthNames and abbrMonthNames static properties and isLeapYear,
 * isWeekend, isWeekDay, getDaysInMonth, getDayName, getMonthName, getDayOfYear, getWeekOfYear,
 * setDayOfYear, addYears, addMonths, addDays, addHours, addMinutes, addSeconds methods
 *
 * Copyright (c) 2006 Jörn Zaefferer and Brandon Aaron (brandon.aaron@gmail.com || http://brandonaaron.net)
 *
 * Additional methods and properties added by Kelvin Luck: firstDayOfWeek, dateFormat, zeroTime, asString, fromString -
 * I've added my name to these methods so you know who to blame if they are broken!
 * 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 */

/**
 * An Array of day names starting with Sunday.
 * 
 * @example dayNames[0]
 * @result 'Sunday'
 *
 * @name dayNames
 * @type Array
 * @cat Plugins/Methods/Date
 */
Date.dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

/**
 * An Array of abbreviated day names starting with Sun.
 * 
 * @example abbrDayNames[0]
 * @result 'Sun'
 *
 * @name abbrDayNames
 * @type Array
 * @cat Plugins/Methods/Date
 */
Date.abbrDayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

/**
 * An Array of month names starting with Janurary.
 * 
 * @example monthNames[0]
 * @result 'January'
 *
 * @name monthNames
 * @type Array
 * @cat Plugins/Methods/Date
 */
Date.monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

/**
 * An Array of abbreviated month names starting with Jan.
 * 
 * @example abbrMonthNames[0]
 * @result 'Jan'
 *
 * @name monthNames
 * @type Array
 * @cat Plugins/Methods/Date
 */
Date.abbrMonthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

/**
 * The first day of the week for this locale.
 *
 * @name firstDayOfWeek
 * @type Number
 * @cat Plugins/Methods/Date
 * @author Kelvin Luck
 */
Date.firstDayOfWeek = 1;

/**
 * The format that string dates should be represented as (e.g. 'dd/mm/yyyy' for UK, 'mm/dd/yyyy' for US, 'yyyy-mm-dd' for Unicode etc).
 *
 * @name format
 * @type String
 * @cat Plugins/Methods/Date
 * @author Kelvin Luck
 */
Date.format = 'dd/mm/yyyy';
//Date.format = 'mm/dd/yyyy';
//Date.format = 'yyyy-mm-dd';
//Date.format = 'dd mmm yy';

/**
 * The first two numbers in the century to be used when decoding a two digit year. Since a two digit year is ambiguous (and date.setYear
 * only works with numbers < 99 and so doesn't allow you to set years after 2000) we need to use this to disambiguate the two digit year codes.
 *
 * @name format
 * @type String
 * @cat Plugins/Methods/Date
 * @author Kelvin Luck
 */
Date.fullYearStart = '20';

(function() {

	/**
	 * Adds a given method under the given name 
	 * to the Date prototype if it doesn't
	 * currently exist.
	 *
	 * @private
	 */
	function add(name, method) {
		if( !Date.prototype[name] ) {
			Date.prototype[name] = method;
		}
	};
	
	/**
	 * Checks if the year is a leap year.
	 *
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.isLeapYear();
	 * @result true
	 *
	 * @name isLeapYear
	 * @type Boolean
	 * @cat Plugins/Methods/Date
	 */
	add("isLeapYear", function() {
		var y = this.getFullYear();
		return (y%4==0 && y%100!=0) || y%400==0;
	});
	
	/**
	 * Checks if the day is a weekend day (Sat or Sun).
	 *
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.isWeekend();
	 * @result false
	 *
	 * @name isWeekend
	 * @type Boolean
	 * @cat Plugins/Methods/Date
	 */
	add("isWeekend", function() {
		return this.getDay()==0 || this.getDay()==6;
	});
	
	/**
	 * Check if the day is a day of the week (Mon-Fri)
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.isWeekDay();
	 * @result false
	 * 
	 * @name isWeekDay
	 * @type Boolean
	 * @cat Plugins/Methods/Date
	 */
	add("isWeekDay", function() {
		return !this.isWeekend();
	});
	
	/**
	 * Gets the number of days in the month.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.getDaysInMonth();
	 * @result 31
	 * 
	 * @name getDaysInMonth
	 * @type Number
	 * @cat Plugins/Methods/Date
	 */
	add("getDaysInMonth", function() {
		return [31,(this.isLeapYear() ? 29:28),31,30,31,30,31,31,30,31,30,31][this.getMonth()];
	});
	
	/**
	 * Gets the name of the day.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.getDayName();
	 * @result 'Saturday'
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.getDayName(true);
	 * @result 'Sat'
	 * 
	 * @param abbreviated Boolean When set to true the name will be abbreviated.
	 * @name getDayName
	 * @type String
	 * @cat Plugins/Methods/Date
	 */
	add("getDayName", function(abbreviated) {
		return abbreviated ? Date.abbrDayNames[this.getDay()] : Date.dayNames[this.getDay()];
	});

	/**
	 * Gets the name of the month.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.getMonthName();
	 * @result 'Janurary'
	 *
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.getMonthName(true);
	 * @result 'Jan'
	 * 
	 * @param abbreviated Boolean When set to true the name will be abbreviated.
	 * @name getDayName
	 * @type String
	 * @cat Plugins/Methods/Date
	 */
	add("getMonthName", function(abbreviated) {
		return abbreviated ? Date.abbrMonthNames[this.getMonth()] : Date.monthNames[this.getMonth()];
	});

	/**
	 * Get the number of the day of the year.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.getDayOfYear();
	 * @result 11
	 * 
	 * @name getDayOfYear
	 * @type Number
	 * @cat Plugins/Methods/Date
	 */
	add("getDayOfYear", function() {
		var tmpdtm = new Date("1/1/" + this.getFullYear());
		return Math.floor((this.getTime() - tmpdtm.getTime()) / 86400000);
	});
	
	/**
	 * Get the number of the week of the year.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.getWeekOfYear();
	 * @result 2
	 * 
	 * @name getWeekOfYear
	 * @type Number
	 * @cat Plugins/Methods/Date
	 */
	add("getWeekOfYear", function() {
		return Math.ceil(this.getDayOfYear() / 7);
	});

	/**
	 * Set the day of the year.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.setDayOfYear(1);
	 * dtm.toString();
	 * @result 'Tue Jan 01 2008 00:00:00'
	 * 
	 * @name setDayOfYear
	 * @type Date
	 * @cat Plugins/Methods/Date
	 */
	add("setDayOfYear", function(day) {
		this.setMonth(0);
		this.setDate(day);
		return this;
	});
	
	/**
	 * Add a number of years to the date object.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.addYears(1);
	 * dtm.toString();
	 * @result 'Mon Jan 12 2009 00:00:00'
	 * 
	 * @name addYears
	 * @type Date
	 * @cat Plugins/Methods/Date
	 */
	add("addYears", function(num) {
		this.setFullYear(this.getFullYear() + num);
		return this;
	});
	
	/**
	 * Add a number of months to the date object.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.addMonths(1);
	 * dtm.toString();
	 * @result 'Tue Feb 12 2008 00:00:00'
	 * 
	 * @name addMonths
	 * @type Date
	 * @cat Plugins/Methods/Date
	 */
	add("addMonths", function(num) {
		var tmpdtm = this.getDate();
		
		this.setMonth(this.getMonth() + num);
		
		if (tmpdtm > this.getDate())
			this.addDays(-this.getDate());
		
		return this;
	});
	
	/**
	 * Add a number of days to the date object.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.addDays(1);
	 * dtm.toString();
	 * @result 'Sun Jan 13 2008 00:00:00'
	 * 
	 * @name addDays
	 * @type Date
	 * @cat Plugins/Methods/Date
	 */
	add("addDays", function(num) {
		//this.setDate(this.getDate() + num);
		this.setTime(this.getTime() + (num*86400000) );
		return this;
	});
	
	/**
	 * Add a number of hours to the date object.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.addHours(24);
	 * dtm.toString();
	 * @result 'Sun Jan 13 2008 00:00:00'
	 * 
	 * @name addHours
	 * @type Date
	 * @cat Plugins/Methods/Date
	 */
	add("addHours", function(num) {
		this.setHours(this.getHours() + num);
		return this;
	});

	/**
	 * Add a number of minutes to the date object.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.addMinutes(60);
	 * dtm.toString();
	 * @result 'Sat Jan 12 2008 01:00:00'
	 * 
	 * @name addMinutes
	 * @type Date
	 * @cat Plugins/Methods/Date
	 */
	add("addMinutes", function(num) {
		this.setMinutes(this.getMinutes() + num);
		return this;
	});
	
	/**
	 * Add a number of seconds to the date object.
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.addSeconds(60);
	 * dtm.toString();
	 * @result 'Sat Jan 12 2008 00:01:00'
	 * 
	 * @name addSeconds
	 * @type Date
	 * @cat Plugins/Methods/Date
	 */
	add("addSeconds", function(num) {
		this.setSeconds(this.getSeconds() + num);
		return this;
	});
	
	/**
	 * Sets the time component of this Date to zero for cleaner, easier comparison of dates where time is not relevant.
	 * 
	 * @example var dtm = new Date();
	 * dtm.zeroTime();
	 * dtm.toString();
	 * @result 'Sat Jan 12 2008 00:01:00'
	 * 
	 * @name zeroTime
	 * @type Date
	 * @cat Plugins/Methods/Date
	 * @author Kelvin Luck
	 */
	add("zeroTime", function() {
		this.setMilliseconds(0);
		this.setSeconds(0);
		this.setMinutes(0);
		this.setHours(0);
		return this;
	});
	
	/**
	 * Returns a string representation of the date object according to Date.format.
	 * (Date.toString may be used in other places so I purposefully didn't overwrite it)
	 * 
	 * @example var dtm = new Date("01/12/2008");
	 * dtm.asString();
	 * @result '12/01/2008' // (where Date.format == 'dd/mm/yyyy'
	 * 
	 * @name asString
	 * @type Date
	 * @cat Plugins/Methods/Date
	 * @author Kelvin Luck
	 */
	add("asString", function(format) {
		var r = format || Date.format;
		return r
			.split('yyyy').join(this.getFullYear())
			.split('yy').join((this.getFullYear() + '').substring(2))
			.split('mmmm').join(this.getMonthName(false))
			.split('mmm').join(this.getMonthName(true))
			.split('mm').join(_zeroPad(this.getMonth()+1))
			.split('dd').join(_zeroPad(this.getDate()))
			.split('hh').join(_zeroPad(this.getHours()))
			.split('min').join(_zeroPad(this.getMinutes()))
			.split('ss').join(_zeroPad(this.getSeconds()));
	});
	
	/**
	 * Returns a new date object created from the passed String according to Date.format or false if the attempt to do this results in an invalid date object
	 * (We can't simple use Date.parse as it's not aware of locale and I chose not to overwrite it incase it's functionality is being relied on elsewhere)
	 *
	 * @example var dtm = Date.fromString("12/01/2008");
	 * dtm.toString();
	 * @result 'Sat Jan 12 2008 00:00:00' // (where Date.format == 'dd/mm/yyyy'
	 * 
	 * @name fromString
	 * @type Date
	 * @cat Plugins/Methods/Date
	 * @author Kelvin Luck
	 */
	Date.fromString = function(s, format)
	{
		var f = format || Date.format;
		var d = new Date('01/01/1977');
		
		var mLength = 0;

		var iM = f.indexOf('mmmm');
		if (iM > -1) {
			for (var i=0; i<Date.monthNames.length; i++) {
				var mStr = s.substr(iM, Date.monthNames[i].length);
				if (Date.monthNames[i] == mStr) {
					mLength = Date.monthNames[i].length - 4;
					break;
				}
			}
			d.setMonth(i);
		} else {
			iM = f.indexOf('mmm');
			if (iM > -1) {
				var mStr = s.substr(iM, 3);
				for (var i=0; i<Date.abbrMonthNames.length; i++) {
					if (Date.abbrMonthNames[i] == mStr) break;
				}
				d.setMonth(i);
			} else {
				d.setMonth(Number(s.substr(f.indexOf('mm'), 2)) - 1);
			}
		}
		
		var iY = f.indexOf('yyyy');

		if (iY > -1) {
			if (iM < iY)
			{
				iY += mLength;
			}
			d.setFullYear(Number(s.substr(iY, 4)));
		} else {
			if (iM < iY)
			{
				iY += mLength;
			}
			// TODO - this doesn't work very well - are there any rules for what is meant by a two digit year?
			d.setFullYear(Number(Date.fullYearStart + s.substr(f.indexOf('yy'), 2)));
		}
		var iD = f.indexOf('dd');
		if (iM < iD)
		{
			iD += mLength;
		}
		d.setDate(Number(s.substr(iD, 2)));
		if (isNaN(d.getTime())) {
			return false;
		}
		return d;
	};
	
	// utility method
	var _zeroPad = function(num) {
		var s = '0'+num;
		return s.substring(s.length-2)
		//return ('0'+num).substring(-2); // doesn't work on IE :(
	};
	
})();;/**
 * Copyright (c) 2008 Kelvin Luck (http://www.kelvinluck.com/)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) 
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 * .
 * $Id: jquery.datePicker.js 108 2011-11-17 21:19:57Z kelvin.luck@gmail.com $
 **/

(function($){
    
	$.fn.extend({
/**
 * Render a calendar table into any matched elements.
 * 
 * @param Object s (optional) Customize your calendars.
 * @option Number month The month to render (NOTE that months are zero based). Default is today's month.
 * @option Number year The year to render. Default is today's year.
 * @option Function renderCallback A reference to a function that is called as each cell is rendered and which can add classes and event listeners to the created nodes. Default is no callback.
 * @option Number showHeader Whether or not to show the header row, possible values are: $.dpConst.SHOW_HEADER_NONE (no header), $.dpConst.SHOW_HEADER_SHORT (first letter of each day) and $.dpConst.SHOW_HEADER_LONG (full name of each day). Default is $.dpConst.SHOW_HEADER_SHORT.
 * @option String hoverClass The class to attach to each cell when you hover over it (to allow you to use hover effects in IE6 which doesn't support the :hover pseudo-class on elements other than links). Default is dp-hover. Pass false if you don't want a hover class.
 * @type jQuery
 * @name renderCalendar
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('#calendar-me').renderCalendar({month:0, year:2007});
 * @desc Renders a calendar displaying January 2007 into the element with an id of calendar-me.
 *
 * @example
 * var testCallback = function($td, thisDate, month, year)
 * {
 * if ($td.is('.current-month') && thisDate.getDay() == 4) {
 *		var d = thisDate.getDate();
 *		$td.bind(
 *			'click',
 *			function()
 *			{
 *				alert('You clicked on ' + d + '/' + (Number(month)+1) + '/' + year);
 *			}
 *		).addClass('thursday');
 *	} else if (thisDate.getDay() == 5) {
 *		$td.html('Friday the ' + $td.html() + 'th');
 *	}
 * }
 * $('#calendar-me').renderCalendar({month:0, year:2007, renderCallback:testCallback});
 * 
 * @desc Renders a calendar displaying January 2007 into the element with an id of calendar-me. Every Thursday in the current month has a class of "thursday" applied to it, is clickable and shows an alert when clicked. Every Friday on the calendar has the number inside replaced with text.
 **/
		renderCalendar  :   function(s)
		{
			var dc = function(a)
			{
				return document.createElement(a);
			};

			s = $.extend({}, $.fn.datePicker.defaults, s);
			
			if (s.showHeader != $.dpConst.SHOW_HEADER_NONE) {
				var headRow = $(dc('tr'));
				for (var i=Date.firstDayOfWeek; i<Date.firstDayOfWeek+7; i++) {
					var weekday = i%7;
					var day = Date.dayNames[weekday];
					headRow.append(
						jQuery(dc('th')).attr({'scope':'col', 'abbr':day, 'title':day, 'class':(weekday == 0 || weekday == 6 ? 'weekend' : 'weekday')}).html(s.showHeader == $.dpConst.SHOW_HEADER_SHORT ? day.substr(0, 1) : day)
					);
				}
			};
			
			var calendarTable = $(dc('table'))
									.attr(
										{
											'cellspacing':2
										}
									)
									.addClass('jCalendar')
									.append(
										(s.showHeader != $.dpConst.SHOW_HEADER_NONE ? 
											$(dc('thead'))
												.append(headRow)
											:
											dc('thead')
										)
									);
			var tbody = $(dc('tbody'));
			
			var today = (new Date()).zeroTime();
			today.setHours(12);
			
			var month = s.month == undefined ? today.getMonth() : s.month;
			var year = s.year || today.getFullYear();
			
			var currentDate = (new Date(year, month, 1, 12, 0, 0));
			
			
			var firstDayOffset = Date.firstDayOfWeek - currentDate.getDay() + 1;
			if (firstDayOffset > 1) firstDayOffset -= 7;
			var weeksToDraw = Math.ceil(( (-1*firstDayOffset+1) + currentDate.getDaysInMonth() ) /7);
			currentDate.addDays(firstDayOffset-1);
			
			var doHover = function(firstDayInBounds)
			{
				return function()
				{
					if (s.hoverClass) {
						var $this = $(this);
						if (!s.selectWeek) {
							$this.addClass(s.hoverClass);
						} else if (firstDayInBounds && !$this.is('.disabled')) {
							$this.parent().addClass('activeWeekHover');
						}
					}
				}
			};
			var unHover = function()
			{
				if (s.hoverClass) {
					var $this = $(this);
					$this.removeClass(s.hoverClass);
					$this.parent().removeClass('activeWeekHover');
				}
			};

			var w = 0;
			while (w++<weeksToDraw) {
				var r = jQuery(dc('tr'));
				var firstDayInBounds = s.dpController ? currentDate > s.dpController.startDate : false;
				for (var i=0; i<7; i++) {
					var thisMonth = currentDate.getMonth() == month;
					var d = $(dc('td'))
								.text(currentDate.getDate() + '')
								.addClass((thisMonth ? 'current-month ' : 'other-month ') +
													(currentDate.isWeekend() ? 'weekend ' : 'weekday ') +
													(thisMonth && currentDate.getTime() == today.getTime() ? 'today ' : '')
								)
								.data('datePickerDate', currentDate.asString())
								.hover(doHover(firstDayInBounds), unHover)
							;
					r.append(d);
					if (s.renderCallback) {
						s.renderCallback(d, currentDate, month, year);
					}
					// addDays(1) fails in some locales due to daylight savings. See issue 39.
					//currentDate.addDays(1);
					// set the time to midday to avoid any weird timezone issues??
					currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate()+1, 12, 0, 0);
				}
				tbody.append(r);
			}
			calendarTable.append(tbody);
			
			return this.each(
				function()
				{
					$(this).empty().append(calendarTable);
				}
			);
		},
/**
 * Create a datePicker associated with each of the matched elements.
 *
 * The matched element will receive a few custom events with the following signatures:
 *
 * dateSelected(event, date, $td, status)
 * Triggered when a date is selected. event is a reference to the event, date is the Date selected, $td is a jquery object wrapped around the TD that was clicked on and status is whether the date was selected (true) or deselected (false)
 * 
 * dpClosed(event, selected)
 * Triggered when the date picker is closed. event is a reference to the event and selected is an Array containing Date objects.
 *
 * dpMonthChanged(event, displayedMonth, displayedYear)
 * Triggered when the month of the popped up calendar is changed. event is a reference to the event, displayedMonth is the number of the month now displayed (zero based) and displayedYear is the year of the month.
 *
 * dpDisplayed(event, $datePickerDiv)
 * Triggered when the date picker is created. $datePickerDiv is the div containing the date picker. Use this event to add custom content/ listeners to the popped up date picker.
 *
 * @param Object s (optional) Customize your date pickers.
 * @option Number month The month to render when the date picker is opened (NOTE that months are zero based). Default is today's month.
 * @option Number year The year to render when the date picker is opened. Default is today's year.
 * @option String|Date startDate The first date date can be selected.
 * @option String|Date endDate The last date that can be selected.
 * @option Boolean inline Whether to create the datePicker as inline (e.g. always on the page) or as a model popup. Default is false (== modal popup)
 * @option Boolean createButton Whether to create a .dp-choose-date anchor directly after the matched element which when clicked will trigger the showing of the date picker. Default is true.
 * @option Boolean showYearNavigation Whether to display buttons which allow the user to navigate through the months a year at a time. Default is true.
 * @option Boolean closeOnSelect Whether to close the date picker when a date is selected. Default is true.
 * @option Boolean displayClose Whether to create a "Close" button within the date picker popup. Default is false.
 * @option Boolean selectMultiple Whether a user should be able to select multiple dates with this date picker. Default is false.
 * @option Number numSelectable The maximum number of dates that can be selected where selectMultiple is true. Default is a very high number.
 * @option Boolean clickInput If the matched element is an input type="text" and this option is true then clicking on the input will cause the date picker to appear.
 * @option Boolean rememberViewedMonth Whether the datePicker should remember the last viewed month and open on it. If false then the date picker will always open with the month for the first selected date visible.
 * @option Boolean selectWeek Whether to select a complete week at a time...
 * @option Number verticalPosition The vertical alignment of the popped up date picker to the matched element. One of $.dpConst.POS_TOP and $.dpConst.POS_BOTTOM. Default is $.dpConst.POS_TOP.
 * @option Number horizontalPosition The horizontal alignment of the popped up date picker to the matched element. One of $.dpConst.POS_LEFT and $.dpConst.POS_RIGHT.
 * @option Number verticalOffset The number of pixels offset from the defined verticalPosition of this date picker that it should pop up in. Default in 0.
 * @option Number horizontalOffset The number of pixels offset from the defined horizontalPosition of this date picker that it should pop up in. Default in 0.
 * @option (Function|Array) renderCallback A reference to a function (or an array of separate functions) that is called as each cell is rendered and which can add classes and event listeners to the created nodes. Each callback function will receive four arguments; a jquery object wrapping the created TD, a Date object containing the date this TD represents, a number giving the currently rendered month and a number giving the currently rendered year. Default is no callback.
 * @option String hoverClass The class to attach to each cell when you hover over it (to allow you to use hover effects in IE6 which doesn't support the :hover pseudo-class on elements other than links). Default is dp-hover. Pass false if you don't want a hover class.
 * @option String autoFocusNextInput Whether focus should be passed onto the next input in the form (true) or remain on this input (false) when a date is selected and the calendar closes
 * @type jQuery
 * @name datePicker
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('input.date-picker').datePicker();
 * @desc Creates a date picker button next to all matched input elements. When the button is clicked on the value of the selected date will be placed in the corresponding input (formatted according to Date.format).
 *
 * @example demo/index.html
 * @desc See the projects homepage for many more complex examples...
 **/
		datePicker : function(s)
		{			
			if (!$.event._dpCache) $.event._dpCache = [];
			
			// initialise the date picker controller with the relevant settings...
			s = $.extend({}, $.fn.datePicker.defaults, s);
			
			return this.each(
				function()
				{
					var $this = $(this);
					var alreadyExists = true;
					
					if (!this._dpId) {
						this._dpId = $.guid++;
						$.event._dpCache[this._dpId] = new DatePicker(this);
						alreadyExists = false;
					}
					
					if (s.inline) {
						s.createButton = false;
						s.displayClose = false;
						s.closeOnSelect = false;
						$this.empty();
					}
					
					var controller = $.event._dpCache[this._dpId];
					
					controller.init(s);
					
					if (!alreadyExists && s.createButton) {
						// create it!
						controller.button = $('<a href="#" class="dp-choose-date" title="' + $.dpText.TEXT_CHOOSE_DATE + '">' + $.dpText.TEXT_CHOOSE_DATE + '</a>')
								.bind(
									'click',
									function()
									{
										$this.dpDisplay(this);
										this.blur();
										return false;
									}
								);
						$this.after(controller.button);
					}
					
					if (!alreadyExists && $this.is(':text')) {
						$this
							.bind(
								'dateSelected',
								function(e, selectedDate, $td)
								{
									this.value = selectedDate.asString();
								}
							).bind(
								'change',
								function()
								{
									if (this.value == '') {
										controller.clearSelected();
									} else {
										var d = Date.fromString(this.value);
										if (d) {
											controller.setSelected(d, true, true);
										}
									}
								}
							);
						if (s.clickInput) {
							$this.bind(
								'click',
								function()
								{
									// The change event doesn't happen until the input loses focus so we need to manually trigger it...
									$this.trigger('change');
									$this.dpDisplay();
								}
							);
						}
						var d = Date.fromString(this.value);
						if (this.value != '' && d) {
							controller.setSelected(d, true, true);
						}
					}
					
					$this.addClass('dp-applied');
					
				}
			)
		},
/**
 * Disables or enables this date picker
 *
 * @param Boolean s Whether to disable (true) or enable (false) this datePicker
 * @type jQuery
 * @name dpSetDisabled
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('.date-picker').datePicker();
 * $('.date-picker').dpSetDisabled(true);
 * @desc Prevents this date picker from displaying and adds a class of dp-disabled to it (and it's associated button if it has one) for styling purposes. If the matched element is an input field then it will also set the disabled attribute to stop people directly editing the field.
 **/
		dpSetDisabled : function(s)
		{
			return _w.call(this, 'setDisabled', s);
		},
/**
 * Updates the first selectable date for any date pickers on any matched elements.
 *
 * @param String|Date d A Date object or string representing the first selectable date (formatted according to Date.format).
 * @type jQuery
 * @name dpSetStartDate
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('.date-picker').datePicker();
 * $('.date-picker').dpSetStartDate('01/01/2000');
 * @desc Creates a date picker associated with all elements with a class of "date-picker" then sets the first selectable date for each of these to the first day of the millenium.
 **/
		dpSetStartDate : function(d)
		{
			return _w.call(this, 'setStartDate', d);
		},
/**
 * Updates the last selectable date for any date pickers on any matched elements.
 *
 * @param String|Date d A Date object or string representing the last selectable date (formatted according to Date.format).
 * @type jQuery
 * @name dpSetEndDate
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('.date-picker').datePicker();
 * $('.date-picker').dpSetEndDate('01/01/2010');
 * @desc Creates a date picker associated with all elements with a class of "date-picker" then sets the last selectable date for each of these to the first Janurary 2010.
 **/
		dpSetEndDate : function(d)
		{
			return _w.call(this, 'setEndDate', d);
		},
/**
 * Gets a list of Dates currently selected by this datePicker. This will be an empty array if no dates are currently selected or NULL if there is no datePicker associated with the matched element.
 *
 * @type Array
 * @name dpGetSelected
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('.date-picker').datePicker();
 * alert($('.date-picker').dpGetSelected());
 * @desc Will alert an empty array (as nothing is selected yet)
 **/
		dpGetSelected : function()
		{
			var c = _getController(this[0]);
			if (c) {
				return c.getSelected();
			}
			return null;
		},
/**
 * Selects or deselects a date on any matched element's date pickers. Deselcting is only useful on date pickers where selectMultiple==true. Selecting will only work if the passed date is within the startDate and endDate boundries for a given date picker.
 *
 * @param String|Date d A Date object or string representing the date you want to select (formatted according to Date.format).
 * @param Boolean v Whether you want to select (true) or deselect (false) this date. Optional - default = true.
 * @param Boolean m Whether you want the date picker to open up on the month of this date when it is next opened. Optional - default = true.
 * @param Boolean e Whether you want the date picker to dispatch events related to this change of selection. Optional - default = true.
 * @type jQuery
 * @name dpSetSelected
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('.date-picker').datePicker();
 * $('.date-picker').dpSetSelected('01/01/2010');
 * @desc Creates a date picker associated with all elements with a class of "date-picker" then sets the selected date on these date pickers to the first Janurary 2010. When the date picker is next opened it will display Janurary 2010.
 **/
		dpSetSelected : function(d, v, m, e)
		{
			if (v == undefined) v=true;
			if (m == undefined) m=true;
			if (e == undefined) e=true;
			return _w.call(this, 'setSelected', Date.fromString(d), v, m, e);
		},
/**
 * Sets the month that will be displayed when the date picker is next opened. If the passed month is before startDate then the month containing startDate will be displayed instead. If the passed month is after endDate then the month containing the endDate will be displayed instead.
 *
 * @param Number m The month you want the date picker to display. Optional - defaults to the currently displayed month.
 * @param Number y The year you want the date picker to display. Optional - defaults to the currently displayed year.
 * @type jQuery
 * @name dpSetDisplayedMonth
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('.date-picker').datePicker();
 * $('.date-picker').dpSetDisplayedMonth(10, 2008);
 * @desc Creates a date picker associated with all elements with a class of "date-picker" then sets the selected date on these date pickers to the first Janurary 2010. When the date picker is next opened it will display Janurary 2010.
 **/
		dpSetDisplayedMonth : function(m, y)
		{
			return _w.call(this, 'setDisplayedMonth', Number(m), Number(y), true);
		},
/**
 * Displays the date picker associated with the matched elements. Since only one date picker can be displayed at once then the date picker associated with the last matched element will be the one that is displayed.
 *
 * @param HTMLElement e An element that you want the date picker to pop up relative in position to. Optional - default behaviour is to pop up next to the element associated with this date picker.
 * @type jQuery
 * @name dpDisplay
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('#date-picker').datePicker();
 * $('#date-picker').dpDisplay();
 * @desc Creates a date picker associated with the element with an id of date-picker and then causes it to pop up.
 **/
		dpDisplay : function(e)
		{
			return _w.call(this, 'display', e);
		},
/**
 * Sets a function or array of functions that is called when each TD of the date picker popup is rendered to the page
 *
 * @param (Function|Array) a A function or an array of functions that are called when each td is rendered. Each function will receive four arguments; a jquery object wrapping the created TD, a Date object containing the date this TD represents, a number giving the currently rendered month and a number giving the currently rendered year.
 * @type jQuery
 * @name dpSetRenderCallback
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('#date-picker').datePicker();
 * $('#date-picker').dpSetRenderCallback(function($td, thisDate, month, year)
 * {
 * 	// do stuff as each td is rendered dependant on the date in the td and the displayed month and year
 * });
 * @desc Creates a date picker associated with the element with an id of date-picker and then creates a function which is called as each td is rendered when this date picker is displayed.
 **/
		dpSetRenderCallback : function(a)
		{
			return _w.call(this, 'setRenderCallback', a);
		},
/**
 * Sets the position that the datePicker will pop up (relative to it's associated element)
 *
 * @param Number v The vertical alignment of the created date picker to it's associated element. Possible values are $.dpConst.POS_TOP and $.dpConst.POS_BOTTOM
 * @param Number h The horizontal alignment of the created date picker to it's associated element. Possible values are $.dpConst.POS_LEFT and $.dpConst.POS_RIGHT
 * @type jQuery
 * @name dpSetPosition
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('#date-picker').datePicker();
 * $('#date-picker').dpSetPosition($.dpConst.POS_BOTTOM, $.dpConst.POS_RIGHT);
 * @desc Creates a date picker associated with the element with an id of date-picker and makes it so that when this date picker pops up it will be bottom and right aligned to the #date-picker element.
 **/
		dpSetPosition : function(v, h)
		{
			return _w.call(this, 'setPosition', v, h);
		},
/**
 * Sets the offset that the popped up date picker will have from it's default position relative to it's associated element (as set by dpSetPosition)
 *
 * @param Number v The vertical offset of the created date picker.
 * @param Number h The horizontal offset of the created date picker.
 * @type jQuery
 * @name dpSetOffset
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('#date-picker').datePicker();
 * $('#date-picker').dpSetOffset(-20, 200);
 * @desc Creates a date picker associated with the element with an id of date-picker and makes it so that when this date picker pops up it will be 20 pixels above and 200 pixels to the right of it's default position.
 **/
		dpSetOffset : function(v, h)
		{
			return _w.call(this, 'setOffset', v, h);
		},
/**
 * Closes the open date picker associated with this element.
 *
 * @type jQuery
 * @name dpClose
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 * @example $('.date-pick')
 *		.datePicker()
 *		.bind(
 *			'focus',
 *			function()
 *			{
 *				$(this).dpDisplay();
 *			}
 *		).bind(
 *			'blur',
 *			function()
 *			{
 *				$(this).dpClose();
 *			}
 *		);
 **/
		dpClose : function()
		{
			return _w.call(this, '_closeCalendar', false, this[0]);
		},
/**
 * Rerenders the date picker's current month (for use with inline calendars and renderCallbacks).
 *
 * @type jQuery
 * @name dpRerenderCalendar
 * @cat plugins/datePicker
 * @author Kelvin Luck (http://www.kelvinluck.com/)
 *
 **/
		dpRerenderCalendar : function()
		{
			return _w.call(this, '_rerenderCalendar');
		},
		// private function called on unload to clean up any expandos etc and prevent memory links...
		_dpDestroy : function()
		{
			// TODO - implement this?
		}
	});
	
	// private internal function to cut down on the amount of code needed where we forward
	// dp* methods on the jQuery object on to the relevant DatePicker controllers...
	var _w = function(f, a1, a2, a3, a4)
	{
		return this.each(
			function()
			{
				var c = _getController(this);
				if (c) {
					c[f](a1, a2, a3, a4);
				}
			}
		);
	};
	
	function DatePicker(ele)
	{
		this.ele = ele;
		
		// initial values...
		this.displayedMonth		=	null;
		this.displayedYear		=	null;
		this.startDate			=	null;
		this.endDate			=	null;
		this.showYearNavigation	=	null;
		this.closeOnSelect		=	null;
		this.displayClose		=	null;
		this.rememberViewedMonth=	null;
		this.selectMultiple		=	null;
		this.numSelectable		=	null;
		this.numSelected		=	null;
		this.verticalPosition	=	null;
		this.horizontalPosition	=	null;
		this.verticalOffset		=	null;
		this.horizontalOffset	=	null;
		this.button				=	null;
		this.renderCallback		=	[];
		this.selectedDates		=	{};
		this.inline				=	null;
		this.context			=	'#dp-popup';
		this.settings			=	{};
	};
	$.extend(
		DatePicker.prototype,
		{	
			init : function(s)
			{
				this.setStartDate(s.startDate);
				this.setEndDate(s.endDate);
				this.setDisplayedMonth(Number(s.month), Number(s.year));
				this.setRenderCallback(s.renderCallback);
				this.showYearNavigation = s.showYearNavigation;
				this.closeOnSelect = s.closeOnSelect;
				this.displayClose = s.displayClose;
				this.rememberViewedMonth =	s.rememberViewedMonth;
				this.selectMultiple = s.selectMultiple;
				this.numSelectable = s.selectMultiple ? s.numSelectable : 1;
				this.numSelected = 0;
				this.verticalPosition = s.verticalPosition;
				this.horizontalPosition = s.horizontalPosition;
				this.hoverClass = s.hoverClass;
				this.setOffset(s.verticalOffset, s.horizontalOffset);
				this.inline = s.inline;
				this.settings = s;
				if (this.inline) {
					this.context = this.ele;
					this.display();
				}
			},
			setStartDate : function(d)
			{
				if (d) {
					if (d instanceof Date) {
						this.startDate = d;
					} else {
						this.startDate = Date.fromString(d);
					}
				}
				if (!this.startDate) {
					this.startDate = (new Date()).zeroTime();
				}
				this.setDisplayedMonth(this.displayedMonth, this.displayedYear);
			},
			setEndDate : function(d)
			{
				if (d) {
					if (d instanceof Date) {
						this.endDate = d;
					} else {
						this.endDate = Date.fromString(d);
					}
				}
				if (!this.endDate) {
					this.endDate = (new Date('12/31/2999')); // using the JS Date.parse function which expects mm/dd/yyyy
				}
				if (this.endDate.getTime() < this.startDate.getTime()) {
					this.endDate = this.startDate;
				}
				this.setDisplayedMonth(this.displayedMonth, this.displayedYear);
			},
			setPosition : function(v, h)
			{
				this.verticalPosition = v;
				this.horizontalPosition = h;
			},
			setOffset : function(v, h)
			{
				this.verticalOffset = parseInt(v) || 0;
				this.horizontalOffset = parseInt(h) || 0;
			},
			setDisabled : function(s)
			{
				$e = $(this.ele);
				$e[s ? 'addClass' : 'removeClass']('dp-disabled');
				if (this.button) {
					$but = $(this.button);
					$but[s ? 'addClass' : 'removeClass']('dp-disabled');
					$but.attr('title', s ? '' : $.dpText.TEXT_CHOOSE_DATE);
				}
				if ($e.is(':text')) {
					$e.attr('disabled', s ? 'disabled' : '');
				}
			},
			setDisplayedMonth : function(m, y, rerender)
			{
				if (this.startDate == undefined || this.endDate == undefined) {
					return;
				}
				var s = new Date(this.startDate.getTime());
				s.setDate(1);
				var e = new Date(this.endDate.getTime());
				e.setDate(1);
				
				var t;
				if ((!m && !y) || (isNaN(m) && isNaN(y))) {
					// no month or year passed - default to current month
					t = new Date().zeroTime();
					t.setDate(1);
				} else if (isNaN(m)) {
					// just year passed in - presume we want the displayedMonth
					t = new Date(y, this.displayedMonth, 1);
				} else if (isNaN(y)) {
					// just month passed in - presume we want the displayedYear
					t = new Date(this.displayedYear, m, 1);
				} else {
					// year and month passed in - that's the date we want!
					t = new Date(y, m, 1)
				}
				// check if the desired date is within the range of our defined startDate and endDate
				if (t.getTime() < s.getTime()) {
					t = s;
				} else if (t.getTime() > e.getTime()) {
					t = e;
				}
				var oldMonth = this.displayedMonth;
				var oldYear = this.displayedYear;
				this.displayedMonth = t.getMonth();
				this.displayedYear = t.getFullYear();

				if (rerender && (this.displayedMonth != oldMonth || this.displayedYear != oldYear))
				{
					this._rerenderCalendar();
					$(this.ele).trigger('dpMonthChanged', [this.displayedMonth, this.displayedYear]);
				}
			},
			setSelected : function(d, v, moveToMonth, dispatchEvents)
			{
				if (d < this.startDate || d.zeroTime() > this.endDate.zeroTime()) {
					// Don't allow people to select dates outside range...
					return;
				}
				var s = this.settings;
				if (s.selectWeek)
				{
					d = d.addDays(- (d.getDay() - Date.firstDayOfWeek + 7) % 7);
					if (d < this.startDate) // The first day of this week is before the start date so is unselectable...
					{
						return;
					}
				}
				if (v == this.isSelected(d)) // this date is already un/selected
				{
					return;
				}
				if (this.selectMultiple == false) {
					this.clearSelected();
				} else if (v && this.numSelected == this.numSelectable) {
					// can't select any more dates...
					return;
				}
				if (moveToMonth && (this.displayedMonth != d.getMonth() || this.displayedYear != d.getFullYear())) {
					this.setDisplayedMonth(d.getMonth(), d.getFullYear(), true);
				}
				this.selectedDates[d.asString()] = v;
				this.numSelected += v ? 1 : -1;
				var selectorString = 'td.' + (d.getMonth() == this.displayedMonth ? 'current-month' : 'other-month');
				var $td;
				$(selectorString, this.context).each(
					function()
					{
						if ($(this).data('datePickerDate') == d.asString()) {
							$td = $(this);
							if (s.selectWeek)
							{
								$td.parent()[v ? 'addClass' : 'removeClass']('selectedWeek');
							}
							$td[v ? 'addClass' : 'removeClass']('selected'); 
						}
					}
				);
				$('td', this.context).not('.selected')[this.selectMultiple &&  this.numSelected == this.numSelectable ? 'addClass' : 'removeClass']('unselectable');
				
				if (dispatchEvents)
				{
					var s = this.isSelected(d);
					$e = $(this.ele);
					var dClone = Date.fromString(d.asString());
					$e.trigger('dateSelected', [dClone, $td, s]);
					$e.trigger('change');
				}
			},
			isSelected : function(d)
			{
				return this.selectedDates[d.asString()];
			},
			getSelected : function()
			{
				var r = [];
				for(var s in this.selectedDates) {
					if (this.selectedDates[s] == true) {
						r.push(Date.fromString(s));
					}
				}
				return r;
			},
			clearSelected : function()
			{
				this.selectedDates = {};
				this.numSelected = 0;
				$('td.selected', this.context).removeClass('selected').parent().removeClass('selectedWeek');
			},
			display : function(eleAlignTo)
			{
				if ($(this.ele).is('.dp-disabled')) return;
				
				eleAlignTo = eleAlignTo || this.ele;
				var c = this;
				var $ele = $(eleAlignTo);
				var eleOffset = $ele.offset();
				
				var $createIn;
				var attrs;
				var attrsCalendarHolder;
				var cssRules;
				
				if (c.inline) {
					$createIn = $(this.ele);
					attrs = {
						'id'		:	'calendar-' + this.ele._dpId,
						'class'	:	'dp-popup dp-popup-inline'
					};

					$('.dp-popup', $createIn).remove();
					cssRules = {
					};
				} else {
					$createIn = $('body');
					attrs = {
						'id'		:	'dp-popup',
						'class'	:	'dp-popup'
					};
					cssRules = {
						'top'	:	eleOffset.top + c.verticalOffset,
						'left'	:	eleOffset.left + c.horizontalOffset
					};
					
					var _checkMouse = function(e)
					{
						var el = e.target;
						var cal = $('#dp-popup')[0];
						
						while (true){
							if (el == cal) {
								return true;
							} else if (el == document) {
								c._closeCalendar();
								return false;
							} else {
								el = $(el).parent()[0];
							}
						}
					};
					this._checkMouse = _checkMouse;
					
					c._closeCalendar(true);
					$(document).bind(
						'keydown.datepicker', 
						function(event)
						{
							if (event.keyCode == 27) {
								c._closeCalendar();
							}
						}
					);
				}
				
				if (!c.rememberViewedMonth)
				{
					var selectedDate = this.getSelected()[0];
					if (selectedDate) {
						selectedDate = new Date(selectedDate);
						this.setDisplayedMonth(selectedDate.getMonth(), selectedDate.getFullYear(), false);
					}
				}
				
				$createIn
					.append(
						$('<div></div>')
							.attr(attrs)
							.css(cssRules)
							.append(
//								$('<a href="#" class="selecteee">aaa</a>'),
								$('<h2></h2>'),
								$('<div class="dp-nav-prev"></div>')
									.append(
										$('<a class="dp-nav-prev-year" href="#" title="' + $.dpText.TEXT_PREV_YEAR + '">⏪</a>')
											.bind(
												'click',
												function()
												{
													return c._displayNewMonth.call(c, this, 0, -1);
												}
											),
										$('<a class="dp-nav-prev-month" href="#" title="' + $.dpText.TEXT_PREV_MONTH + '">◅</a>')
											.bind(
												'click',
												function()
												{
													return c._displayNewMonth.call(c, this, -1, 0);
												}
											)
									),
								$('<div class="dp-nav-next"></div>')
									.append(
										$('<a class="dp-nav-next-year" href="#" title="' + $.dpText.TEXT_NEXT_YEAR + '">⏩</a>')
											.bind(
												'click',
												function()
												{
													return c._displayNewMonth.call(c, this, 0, 1);
												}
											),
										$('<a class="dp-nav-next-month" href="#" title="' + $.dpText.TEXT_NEXT_MONTH + '">▻</a>')
											.bind(
												'click',
												function()
												{
													return c._displayNewMonth.call(c, this, 1, 0);
												}
											)
									),
								$('<div class="dp-calendar"></div>')
							)
							.bgIframe()
						);
					
				var $pop = this.inline ? $('.dp-popup', this.context) : $('#dp-popup');
				
				if (this.showYearNavigation == false) {
					$('.dp-nav-prev-year, .dp-nav-next-year', c.context).css('display', 'none');
				}
				if (this.displayClose) {
					$pop.append(
						$('<a href="#" id="dp-close">' + $.dpText.TEXT_CLOSE + '</a>')
							.bind(
								'click',
								function()
								{
									c._closeCalendar();
									return false;
								}
							)
					);
				}
				c._renderCalendar();

				$(this.ele).trigger('dpDisplayed', $pop);
				
				if (!c.inline) {
					if (this.verticalPosition == $.dpConst.POS_BOTTOM) {
						$pop.css('top', eleOffset.top + $ele.height() - $pop.height() + c.verticalOffset);
					}
					if (this.horizontalPosition == $.dpConst.POS_RIGHT) {
						$pop.css('left', eleOffset.left + $ele.width() - $pop.width() + c.horizontalOffset);
					}
//					$('.selectee', this.context).focus();
					$(document).bind('mousedown.datepicker', this._checkMouse);
				}
				
			},
			setRenderCallback : function(a)
			{
				if (a == null) return;
				if (a && typeof(a) == 'function') {
					a = [a];
				}
				this.renderCallback = this.renderCallback.concat(a);
			},
			cellRender : function ($td, thisDate, month, year) {
				var c = this.dpController;
				var d = new Date(thisDate.getTime());
				
				// add our click handlers to deal with it when the days are clicked...
				
				$td.bind(
					'click',
					function()
					{
						var $this = $(this);
						if (!$this.is('.disabled')) {
							c.setSelected(d, !$this.is('.selected') || !c.selectMultiple, false, true);
							if (c.closeOnSelect) {
								// Focus the next input in the formâ€¦
								if (c.settings.autoFocusNextInput) {
									var ele = c.ele;
									var found = false;
									$(':input', ele.form).each(
										function()
										{
											if (found) {
												$(this).focus();
												return false;
											}
											if (this == ele) {
												found = true;
											}
										}
									);
								} else {
									c.ele.focus();
								}
								c._closeCalendar();
							}
						}
					}
				);
				if (c.isSelected(d)) {
					$td.addClass('selected');
					if (c.settings.selectWeek)
					{
						$td.parent().addClass('selectedWeek');
					}
				} else  if (c.selectMultiple && c.numSelected == c.numSelectable) {
					$td.addClass('unselectable');
				}
				
			},
			_applyRenderCallbacks : function()
			{
				var c = this;
				$('td', this.context).each(
					function()
					{
						for (var i=0; i<c.renderCallback.length; i++) {
							$td = $(this);
							c.renderCallback[i].apply(this, [$td, Date.fromString($td.data('datePickerDate')), c.displayedMonth, c.displayedYear]);
						}
					}
				);
				return;
			},
			// ele is the clicked button - only proceed if it doesn't have the class disabled...
			// m and y are -1, 0 or 1 depending which direction we want to go in...
			_displayNewMonth : function(ele, m, y) 
			{
				if (!$(ele).is('.disabled')) {
					this.setDisplayedMonth(this.displayedMonth + m, this.displayedYear + y, true);
				}
				ele.blur();
				return false;
			},
			_rerenderCalendar : function()
			{
				this._clearCalendar();
				this._renderCalendar();
			},
			_renderCalendar : function()
			{
				// set the title...
				$('h2', this.context).html((new Date(this.displayedYear, this.displayedMonth, 1)).asString($.dpText.HEADER_FORMAT));
				
				// render the calendar...
				$('.dp-calendar', this.context).renderCalendar(
					$.extend(
						{},
						this.settings, 
						{
							month			: this.displayedMonth,
							year			: this.displayedYear,
							renderCallback	: this.cellRender,
							dpController	: this,
							hoverClass		: this.hoverClass
						})
				);
				
				// update the status of the control buttons and disable dates before startDate or after endDate...
				// TODO: When should the year buttons be disabled? When you can't go forward a whole year from where you are or is that annoying?
				if (this.displayedYear == this.startDate.getFullYear() && this.displayedMonth == this.startDate.getMonth()) {
					$('.dp-nav-prev-year', this.context).addClass('disabled');
					$('.dp-nav-prev-month', this.context).addClass('disabled');
					$('.dp-calendar td.other-month', this.context).each(
						function()
						{
							var $this = $(this);
							if (Number($this.text()) > 20) {
								$this.addClass('disabled');
							}
						}
					);
					var d = this.startDate.getDate();
					$('.dp-calendar td.current-month', this.context).each(
						function()
						{
							var $this = $(this);
							if (Number($this.text()) < d) {
								$this.addClass('disabled');
							}
						}
					);
				} else {
					$('.dp-nav-prev-year', this.context).removeClass('disabled');
					$('.dp-nav-prev-month', this.context).removeClass('disabled');
					var d = this.startDate.getDate();
					if (d > 20) {
						// check if the startDate is last month as we might need to add some disabled classes...
						var st = this.startDate.getTime();
						var sd = new Date(st);
						sd.addMonths(1);
						if (this.displayedYear == sd.getFullYear() && this.displayedMonth == sd.getMonth()) {
							$('.dp-calendar td.other-month', this.context).each(
								function()
								{
									var $this = $(this);
									if (Date.fromString($this.data('datePickerDate')).getTime() < st) {
										$this.addClass('disabled');
									}
								}
							);
						}
					}
				}
				if (this.displayedYear == this.endDate.getFullYear() && this.displayedMonth == this.endDate.getMonth()) {
					$('.dp-nav-next-year', this.context).addClass('disabled');
					$('.dp-nav-next-month', this.context).addClass('disabled');
					$('.dp-calendar td.other-month', this.context).each(
						function()
						{
							var $this = $(this);
							if (Number($this.text()) < 14) {
								$this.addClass('disabled');
							}
						}
					);
					var d = this.endDate.getDate();
					$('.dp-calendar td.current-month', this.context).each(
						function()
						{
							var $this = $(this);
							if (Number($this.text()) > d) {
								$this.addClass('disabled');
							}
						}
					);
				} else {
					$('.dp-nav-next-year', this.context).removeClass('disabled');
					$('.dp-nav-next-month', this.context).removeClass('disabled');
					var d = this.endDate.getDate();
					if (d < 13) {
						// check if the endDate is next month as we might need to add some disabled classes...
						var ed = new Date(this.endDate.getTime());
						ed.addMonths(-1);
						if (this.displayedYear == ed.getFullYear() && this.displayedMonth == ed.getMonth()) {
							$('.dp-calendar td.other-month', this.context).each(
								function()
								{
									var $this = $(this);
									var cellDay = Number($this.text());
									if (cellDay < 13 && cellDay > d) {
										$this.addClass('disabled');
									}
								}
							);
						}
					}
				}
				this._applyRenderCallbacks();
			},
			_closeCalendar : function(programatic, ele)
			{
				if (!ele || ele == this.ele)
				{
					$(document).unbind('mousedown.datepicker');
					$(document).unbind('keydown.datepicker');
					this._clearCalendar();
					$('#dp-popup a').unbind();
					$('#dp-popup').empty().remove();
					if (!programatic) {
						$(this.ele).trigger('dpClosed', [this.getSelected()]);
					}
				}
			},
			// empties the current dp-calendar div and makes sure that all events are unbound
			// and expandos removed to avoid memory leaks...
			_clearCalendar : function()
			{
				// TODO.
				$('.dp-calendar td', this.context).unbind();
				$('.dp-calendar', this.context).empty();
			}
		}
	);
	
	// static constants
	$.dpConst = {
		SHOW_HEADER_NONE	:	0,
		SHOW_HEADER_SHORT	:	1,
		SHOW_HEADER_LONG	:	2,
		POS_TOP				:	0,
		POS_BOTTOM			:	1,
		POS_LEFT			:	0,
		POS_RIGHT			:	1,
		DP_INTERNAL_FOCUS	:	'dpInternalFocusTrigger'
	};
	// localisable text
	$.dpText = {
		TEXT_PREV_YEAR		:	'Previous year',
		TEXT_PREV_MONTH		:	'Previous month',
		TEXT_NEXT_YEAR		:	'Next year',
		TEXT_NEXT_MONTH		:	'Next month',
		TEXT_CLOSE			:	'Close',
		TEXT_CHOOSE_DATE	:	'Choose date',
		HEADER_FORMAT		:	'mmmm yyyy'
	};
	// version
	$.dpVersion = '$Id: jquery.datePicker.js 108 2011-11-17 21:19:57Z kelvin.luck@gmail.com $';

	$.fn.datePicker.defaults = {
		month				: undefined,
		year				: undefined,
		showHeader			: $.dpConst.SHOW_HEADER_SHORT,
		startDate			: undefined,
		endDate				: undefined,
		inline				: false,
		renderCallback		: null,
		createButton		: true,
		showYearNavigation	: true,
		closeOnSelect		: true,
		displayClose		: false,
		selectMultiple		: false,
		numSelectable		: Number.MAX_VALUE,
		clickInput			: false,
		rememberViewedMonth	: true,
		selectWeek			: false,
		verticalPosition	: $.dpConst.POS_TOP,
		horizontalPosition	: $.dpConst.POS_LEFT,
		verticalOffset		: 0,
		horizontalOffset	: 0,
		hoverClass			: 'dp-hover',
		autoFocusNextInput  : false
	};

	function _getController(ele)
	{
		if (ele._dpId) return $.event._dpCache[ele._dpId];
		return false;
	};
	
	// make it so that no error is thrown if bgIframe plugin isn't included (allows you to use conditional
	// comments to only include bgIframe where it is needed in IE without breaking this plugin).
	if ($.fn.bgIframe == undefined) {
		$.fn.bgIframe = function() {return this; };
	};


	// clean-up
	$(window)
		.bind('unload', function() {
			var els = $.event._dpCache || [];
			for (var i in els) {
				$(els[i].ele)._dpDestroy();
			}
		});
		
	
})(jQuery);;/*!
 * jQuery Form Plugin
 * version: 3.32.0-2013.04.09
 * @requires jQuery v1.5 or later
 * Copyright (c) 2013 M. Alsup
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Project repository: https://github.com/malsup/form
 * Dual licensed under the MIT and GPL licenses.
 * https://github.com/malsup/form#copyright-and-license
 */
/*global ActiveXObject */
;(function($) {
"use strict";

/*
    Usage Note:
    -----------
    Do not use both ajaxSubmit and ajaxForm on the same form.  These
    functions are mutually exclusive.  Use ajaxSubmit if you want
    to bind your own submit handler to the form.  For example,

    $(document).ready(function() {
        $('#myForm').on('submit', function(e) {
            e.preventDefault(); // <-- important
            $(this).ajaxSubmit({
                target: '#output'
            });
        });
    });

    Use ajaxForm when you want the plugin to manage all the event binding
    for you.  For example,

    $(document).ready(function() {
        $('#myForm').ajaxForm({
            target: '#output'
        });
    });

    You can also use ajaxForm with delegation (requires jQuery v1.7+), so the
    form does not have to exist when you invoke ajaxForm:

    $('#myForm').ajaxForm({
        delegation: true,
        target: '#output'
    });

    When using ajaxForm, the ajaxSubmit function will be invoked for you
    at the appropriate time.
*/

/**
 * Feature detection
 */
var feature = {};
feature.fileapi = $("<input type='file'/>").get(0).files !== undefined;
feature.formdata = window.FormData !== undefined;

var hasProp = !!$.fn.prop;

// attr2 uses prop when it can but checks the return type for
// an expected string.  this accounts for the case where a form 
// contains inputs with names like "action" or "method"; in those
// cases "prop" returns the element
$.fn.attr2 = function() {
    if ( ! hasProp )
        return this.attr.apply(this, arguments);
    var val = this.prop.apply(this, arguments);
    if ( ( val && val.jquery ) || typeof val === 'string' )
        return val;
    return this.attr.apply(this, arguments);
};

/**
 * ajaxSubmit() provides a mechanism for immediately submitting
 * an HTML form using AJAX.
 */
$.fn.ajaxSubmit = function(options) {
    /*jshint scripturl:true */

    // fast fail if nothing selected (http://dev.jquery.com/ticket/2752)
    if (!this.length) {
        log('ajaxSubmit: skipping submit process - no element selected');
        return this;
    }

    var method, action, url, $form = this;

    if (typeof options == 'function') {
        options = { success: options };
    }

    method = this.attr2('method');
    action = this.attr2('action');

    url = (typeof action === 'string') ? $.trim(action) : '';
    url = url || window.location.href || '';
    if (url) {
        // clean url (don't include hash vaue)
        url = (url.match(/^([^#]+)/)||[])[1];
    }

    options = $.extend(true, {
        url:  url,
        success: $.ajaxSettings.success,
        type: method || 'GET',
        iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank'
    }, options);

    // hook for manipulating the form data before it is extracted;
    // convenient for use with rich editors like tinyMCE or FCKEditor
    var veto = {};
    this.trigger('form-pre-serialize', [this, options, veto]);
    if (veto.veto) {
        log('ajaxSubmit: submit vetoed via form-pre-serialize trigger');
        return this;
    }

    // provide opportunity to alter form data before it is serialized
    if (options.beforeSerialize && options.beforeSerialize(this, options) === false) {
        log('ajaxSubmit: submit aborted via beforeSerialize callback');
        return this;
    }

    var traditional = options.traditional;
    if ( traditional === undefined ) {
        traditional = $.ajaxSettings.traditional;
    }

    var elements = [];
    var qx, a = this.formToArray(options.semantic, elements);
    if (options.data) {
        options.extraData = options.data;
        qx = $.param(options.data, traditional);
    }

    // give pre-submit callback an opportunity to abort the submit
    if (options.beforeSubmit && options.beforeSubmit(a, this, options) === false) {
        log('ajaxSubmit: submit aborted via beforeSubmit callback');
        return this;
    }

    // fire vetoable 'validate' event
    this.trigger('form-submit-validate', [a, this, options, veto]);
    if (veto.veto) {
        log('ajaxSubmit: submit vetoed via form-submit-validate trigger');
        return this;
    }

    var q = $.param(a, traditional);
    if (qx) {
        q = ( q ? (q + '&' + qx) : qx );
    }
    if (options.type.toUpperCase() == 'GET') {
        options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + q;
        options.data = null;  // data is null for 'get'
    }
    else {
        options.data = q; // data is the query string for 'post'
    }

    var callbacks = [];
    if (options.resetForm) {
        callbacks.push(function() { $form.resetForm(); });
    }
    if (options.clearForm) {
        callbacks.push(function() { $form.clearForm(options.includeHidden); });
    }

    // perform a load on the target only if dataType is not provided
    if (!options.dataType && options.target) {
        var oldSuccess = options.success || function(){};
        callbacks.push(function(data) {
            var fn = options.replaceTarget ? 'replaceWith' : 'html';
            $(options.target)[fn](data).each(oldSuccess, arguments);
        });
    }
    else if (options.success) {
        callbacks.push(options.success);
    }

    options.success = function(data, status, xhr) { // jQuery 1.4+ passes xhr as 3rd arg
        var context = options.context || this ;    // jQuery 1.4+ supports scope context
        for (var i=0, max=callbacks.length; i < max; i++) {
            callbacks[i].apply(context, [data, status, xhr || $form, $form]);
        }
    };

    // are there files to upload?

    // [value] (issue #113), also see comment:
    // https://github.com/malsup/form/commit/588306aedba1de01388032d5f42a60159eea9228#commitcomment-2180219
    var fileInputs = $('input[type=file]:enabled[value!=""]', this);

    var hasFileInputs = fileInputs.length > 0;
    var mp = 'multipart/form-data';
    var multipart = ($form.attr('enctype') == mp || $form.attr('encoding') == mp);

    var fileAPI = feature.fileapi && feature.formdata;
    log("fileAPI :" + fileAPI);
    var shouldUseFrame = (hasFileInputs || multipart) && !fileAPI;

    var jqxhr;

    // options.iframe allows user to force iframe mode
    // 06-NOV-09: now defaulting to iframe mode if file input is detected
    if (options.iframe !== false && (options.iframe || shouldUseFrame)) {
        // hack to fix Safari hang (thanks to Tim Molendijk for this)
        // see:  http://groups.google.com/group/jquery-dev/browse_thread/thread/36395b7ab510dd5d
        if (options.closeKeepAlive) {
            $.get(options.closeKeepAlive, function() {
                jqxhr = fileUploadIframe(a);
            });
        }
        else {
            jqxhr = fileUploadIframe(a);
        }
    }
    else if ((hasFileInputs || multipart) && fileAPI) {
        jqxhr = fileUploadXhr(a);
    }
    else {
        jqxhr = $.ajax(options);
    }

    $form.removeData('jqxhr').data('jqxhr', jqxhr);

    // clear element array
    for (var k=0; k < elements.length; k++)
        elements[k] = null;

    // fire 'notify' event
    this.trigger('form-submit-notify', [this, options]);
    return this;

    // utility fn for deep serialization
    function deepSerialize(extraData){
        var serialized = $.param(extraData).split('&');
        var len = serialized.length;
        var result = [];
        var i, part;
        for (i=0; i < len; i++) {
            // #252; undo param space replacement
            serialized[i] = serialized[i].replace(/\+/g,' ');
            part = serialized[i].split('=');
            // #278; use array instead of object storage, favoring array serializations
            result.push([decodeURIComponent(part[0]), decodeURIComponent(part[1])]);
        }
        return result;
    }

     // XMLHttpRequest Level 2 file uploads (big hat tip to francois2metz)
    function fileUploadXhr(a) {
        var formdata = new FormData();

        for (var i=0; i < a.length; i++) {
            formdata.append(a[i].name, a[i].value);
        }

        if (options.extraData) {
            var serializedData = deepSerialize(options.extraData);
            for (i=0; i < serializedData.length; i++)
                if (serializedData[i])
                    formdata.append(serializedData[i][0], serializedData[i][1]);
        }

        options.data = null;

        var s = $.extend(true, {}, $.ajaxSettings, options, {
            contentType: false,
            processData: false,
            cache: false,
            type: method || 'POST'
        });

        if (options.uploadProgress) {
            // workaround because jqXHR does not expose upload property
            s.xhr = function() {
                var xhr = jQuery.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position; /*event.position is deprecated*/
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        options.uploadProgress(event, position, total, percent);
                    }, false);
                }
                return xhr;
            };
        }

        s.data = null;
            var beforeSend = s.beforeSend;
            s.beforeSend = function(xhr, o) {
                o.data = formdata;
                if(beforeSend)
                    beforeSend.call(this, xhr, o);
        };
        return $.ajax(s);
    }

    // private function for handling file uploads (hat tip to YAHOO!)
    function fileUploadIframe(a) {
        var form = $form[0], el, i, s, g, id, $io, io, xhr, sub, n, timedOut, timeoutHandle;
        var deferred = $.Deferred();

        if (a) {
            // ensure that every serialized input is still enabled
            for (i=0; i < elements.length; i++) {
                el = $(elements[i]);
                if ( hasProp )
                    el.prop('disabled', false);
                else
                    el.removeAttr('disabled');
            }
        }

        s = $.extend(true, {}, $.ajaxSettings, options);
        s.context = s.context || s;
        id = 'jqFormIO' + (new Date().getTime());
        if (s.iframeTarget) {
            $io = $(s.iframeTarget);
            n = $io.attr2('name');
            if (!n)
                 $io.attr2('name', id);
            else
                id = n;
        }
        else {
            $io = $('<iframe name="' + id + '" src="'+ s.iframeSrc +'" />');
            $io.css({ position: 'absolute', top: '-1000px', left: '-1000px' });
        }
        io = $io[0];


        xhr = { // mock object
            aborted: 0,
            responseText: null,
            responseXML: null,
            status: 0,
            statusText: 'n/a',
            getAllResponseHeaders: function() {},
            getResponseHeader: function() {},
            setRequestHeader: function() {},
            abort: function(status) {
                var e = (status === 'timeout' ? 'timeout' : 'aborted');
                log('aborting upload... ' + e);
                this.aborted = 1;

                try { // #214, #257
                    if (io.contentWindow.document.execCommand) {
                        io.contentWindow.document.execCommand('Stop');
                    }
                }
                catch(ignore) {}

                $io.attr('src', s.iframeSrc); // abort op in progress
                xhr.error = e;
                if (s.error)
                    s.error.call(s.context, xhr, e, status);
                if (g)
                    $.event.trigger("ajaxError", [xhr, s, e]);
                if (s.complete)
                    s.complete.call(s.context, xhr, e);
            }
        };

        g = s.global;
        // trigger ajax global events so that activity/block indicators work like normal
        if (g && 0 === $.active++) {
            $.event.trigger("ajaxStart");
        }
        if (g) {
            $.event.trigger("ajaxSend", [xhr, s]);
        }

        if (s.beforeSend && s.beforeSend.call(s.context, xhr, s) === false) {
            if (s.global) {
                $.active--;
            }
            deferred.reject();
            return deferred;
        }
        if (xhr.aborted) {
            deferred.reject();
            return deferred;
        }

        // add submitting element to data if we know it
        sub = form.clk;
        if (sub) {
            n = sub.name;
            if (n && !sub.disabled) {
                s.extraData = s.extraData || {};
                s.extraData[n] = sub.value;
                if (sub.type == "image") {
                    s.extraData[n+'.x'] = form.clk_x;
                    s.extraData[n+'.y'] = form.clk_y;
                }
            }
        }

        var CLIENT_TIMEOUT_ABORT = 1;
        var SERVER_ABORT = 2;
                
        function getDoc(frame) {
            /* it looks like contentWindow or contentDocument do not
             * carry the protocol property in ie8, when running under ssl
             * frame.document is the only valid response document, since
             * the protocol is know but not on the other two objects. strange?
             * "Same origin policy" http://en.wikipedia.org/wiki/Same_origin_policy
             */
            
            var doc = null;
            
            // IE8 cascading access check
            try {
                if (frame.contentWindow) {
                    doc = frame.contentWindow.document;
                }
            } catch(err) {
                // IE8 access denied under ssl & missing protocol
                log('cannot get iframe.contentWindow document: ' + err);
            }

            if (doc) { // successful getting content
                return doc;
            }

            try { // simply checking may throw in ie8 under ssl or mismatched protocol
                doc = frame.contentDocument ? frame.contentDocument : frame.document;
            } catch(err) {
                // last attempt
                log('cannot get iframe.contentDocument: ' + err);
                doc = frame.document;
            }
            return doc;
        }

        // Rails CSRF hack (thanks to Yvan Barthelemy)
        var csrf_token = $('meta[name=csrf-token]').attr('content');
        var csrf_param = $('meta[name=csrf-param]').attr('content');
        if (csrf_param && csrf_token) {
            s.extraData = s.extraData || {};
            s.extraData[csrf_param] = csrf_token;
        }

        // take a breath so that pending repaints get some cpu time before the upload starts
        function doSubmit() {
            // make sure form attrs are set
            var t = $form.attr2('target'), a = $form.attr2('action');

            // update form attrs in IE friendly way
            form.setAttribute('target',id);
            if (!method) {
                form.setAttribute('method', 'POST');
            }
            if (a != s.url) {
                form.setAttribute('action', s.url);
            }

            // ie borks in some cases when setting encoding
            if (! s.skipEncodingOverride && (!method || /post/i.test(method))) {
                $form.attr({
                    encoding: 'multipart/form-data',
                    enctype:  'multipart/form-data'
                });
            }

            // support timout
            if (s.timeout) {
                timeoutHandle = setTimeout(function() { timedOut = true; cb(CLIENT_TIMEOUT_ABORT); }, s.timeout);
            }

            // look for server aborts
            function checkState() {
                try {
                    var state = getDoc(io).readyState;
                    log('state = ' + state);
                    if (state && state.toLowerCase() == 'uninitialized')
                        setTimeout(checkState,50);
                }
                catch(e) {
                    log('Server abort: ' , e, ' (', e.name, ')');
                    cb(SERVER_ABORT);
                    if (timeoutHandle)
                        clearTimeout(timeoutHandle);
                    timeoutHandle = undefined;
                }
            }

            // add "extra" data to form if provided in options
            var extraInputs = [];
            try {
                if (s.extraData) {
                    for (var n in s.extraData) {
                        if (s.extraData.hasOwnProperty(n)) {
                           // if using the $.param format that allows for multiple values with the same name
                           if($.isPlainObject(s.extraData[n]) && s.extraData[n].hasOwnProperty('name') && s.extraData[n].hasOwnProperty('value')) {
                               extraInputs.push(
                               $('<input type="hidden" name="'+s.extraData[n].name+'">').val(s.extraData[n].value)
                                   .appendTo(form)[0]);
                           } else {
                               extraInputs.push(
                               $('<input type="hidden" name="'+n+'">').val(s.extraData[n])
                                   .appendTo(form)[0]);
                           }
                        }
                    }
                }

                if (!s.iframeTarget) {
                    // add iframe to doc and submit the form
                    $io.appendTo('body');
                    if (io.attachEvent)
                        io.attachEvent('onload', cb);
                    else
                        io.addEventListener('load', cb, false);
                }
                setTimeout(checkState,15);

                try {
                    form.submit();
                } catch(err) {
                    // just in case form has element with name/id of 'submit'
                    var submitFn = document.createElement('form').submit;
                    submitFn.apply(form);
                }
            }
            finally {
                // reset attrs and remove "extra" input elements
                form.setAttribute('action',a);
                if(t) {
                    form.setAttribute('target', t);
                } else {
                    $form.removeAttr('target');
                }
                $(extraInputs).remove();
            }
        }

        if (s.forceSync) {
            doSubmit();
        }
        else {
            setTimeout(doSubmit, 10); // this lets dom updates render
        }

        var data, doc, domCheckCount = 50, callbackProcessed;

        function cb(e) {
            if (xhr.aborted || callbackProcessed) {
                return;
            }
            
            doc = getDoc(io);
            if(!doc) {
                log('cannot access response document');
                e = SERVER_ABORT;
            }
            if (e === CLIENT_TIMEOUT_ABORT && xhr) {
                xhr.abort('timeout');
                deferred.reject(xhr, 'timeout');
                return;
            }
            else if (e == SERVER_ABORT && xhr) {
                xhr.abort('server abort');
                deferred.reject(xhr, 'error', 'server abort');
                return;
            }

            if (!doc || doc.location.href == s.iframeSrc) {
                // response not received yet
                if (!timedOut)
                    return;
            }
            if (io.detachEvent)
                io.detachEvent('onload', cb);
            else
                io.removeEventListener('load', cb, false);

            var status = 'success', errMsg;
            try {
                if (timedOut) {
                    throw 'timeout';
                }

                var isXml = s.dataType == 'xml' || doc.XMLDocument || $.isXMLDoc(doc);
                log('isXml='+isXml);
                if (!isXml && window.opera && (doc.body === null || !doc.body.innerHTML)) {
                    if (--domCheckCount) {
                        // in some browsers (Opera) the iframe DOM is not always traversable when
                        // the onload callback fires, so we loop a bit to accommodate
                        log('requeing onLoad callback, DOM not available');
                        setTimeout(cb, 250);
                        return;
                    }
                    // let this fall through because server response could be an empty document
                    //log('Could not access iframe DOM after mutiple tries.');
                    //throw 'DOMException: not available';
                }

                //log('response detected');
                var docRoot = doc.body ? doc.body : doc.documentElement;
                xhr.responseText = docRoot ? docRoot.innerHTML : null;
                xhr.responseXML = doc.XMLDocument ? doc.XMLDocument : doc;
                if (isXml)
                    s.dataType = 'xml';
                xhr.getResponseHeader = function(header){
                    var headers = {'content-type': s.dataType};
                    return headers[header];
                };
                // support for XHR 'status' & 'statusText' emulation :
                if (docRoot) {
                    xhr.status = Number( docRoot.getAttribute('status') ) || xhr.status;
                    xhr.statusText = docRoot.getAttribute('statusText') || xhr.statusText;
                }

                var dt = (s.dataType || '').toLowerCase();
                var scr = /(json|script|text)/.test(dt);
                if (scr || s.textarea) {
                    // see if user embedded response in textarea
                    var ta = doc.getElementsByTagName('textarea')[0];
                    if (ta) {
                        xhr.responseText = ta.value;
                        // support for XHR 'status' & 'statusText' emulation :
                        xhr.status = Number( ta.getAttribute('status') ) || xhr.status;
                        xhr.statusText = ta.getAttribute('statusText') || xhr.statusText;
                    }
                    else if (scr) {
                        // account for browsers injecting pre around json response
                        var pre = doc.getElementsByTagName('pre')[0];
                        var b = doc.getElementsByTagName('body')[0];
                        if (pre) {
                            xhr.responseText = pre.textContent ? pre.textContent : pre.innerText;
                        }
                        else if (b) {
                            xhr.responseText = b.textContent ? b.textContent : b.innerText;
                        }
                    }
                }
                else if (dt == 'xml' && !xhr.responseXML && xhr.responseText) {
                    xhr.responseXML = toXml(xhr.responseText);
                }

                try {
                    data = httpData(xhr, dt, s);
                }
                catch (err) {
                    status = 'parsererror';
                    xhr.error = errMsg = (err || status);
                }
            }
            catch (err) {
                log('error caught: ',err);
                status = 'error';
                xhr.error = errMsg = (err || status);
            }

            if (xhr.aborted) {
                log('upload aborted');
                status = null;
            }

            if (xhr.status) { // we've set xhr.status
                status = (xhr.status >= 200 && xhr.status < 300 || xhr.status === 304) ? 'success' : 'error';
            }

            // ordering of these callbacks/triggers is odd, but that's how $.ajax does it
            if (status === 'success') {
                if (s.success)
                    s.success.call(s.context, data, 'success', xhr);
                deferred.resolve(xhr.responseText, 'success', xhr);
                if (g)
                    $.event.trigger("ajaxSuccess", [xhr, s]);
            }
            else if (status) {
                if (errMsg === undefined)
                    errMsg = xhr.statusText;
                if (s.error)
                    s.error.call(s.context, xhr, status, errMsg);
                deferred.reject(xhr, 'error', errMsg);
                if (g)
                    $.event.trigger("ajaxError", [xhr, s, errMsg]);
            }

            if (g)
                $.event.trigger("ajaxComplete", [xhr, s]);

            if (g && ! --$.active) {
                $.event.trigger("ajaxStop");
            }

            if (s.complete)
                s.complete.call(s.context, xhr, status);

            callbackProcessed = true;
            if (s.timeout)
                clearTimeout(timeoutHandle);

            // clean up
            setTimeout(function() {
                if (!s.iframeTarget)
                    $io.remove();
                xhr.responseXML = null;
            }, 100);
        }

        var toXml = $.parseXML || function(s, doc) { // use parseXML if available (jQuery 1.5+)
            if (window.ActiveXObject) {
                doc = new ActiveXObject('Microsoft.XMLDOM');
                doc.async = 'false';
                doc.loadXML(s);
            }
            else {
                doc = (new DOMParser()).parseFromString(s, 'text/xml');
            }
            return (doc && doc.documentElement && doc.documentElement.nodeName != 'parsererror') ? doc : null;
        };
        var parseJSON = $.parseJSON || function(s) {
            /*jslint evil:true */
            return window['eval']('(' + s + ')');
        };

        var httpData = function( xhr, type, s ) { // mostly lifted from jq1.4.4

            var ct = xhr.getResponseHeader('content-type') || '',
                xml = type === 'xml' || !type && ct.indexOf('xml') >= 0,
                data = xml ? xhr.responseXML : xhr.responseText;

            if (xml && data.documentElement.nodeName === 'parsererror') {
                if ($.error)
                    $.error('parsererror');
            }
            if (s && s.dataFilter) {
                data = s.dataFilter(data, type);
            }
            if (typeof data === 'string') {
                if (type === 'json' || !type && ct.indexOf('json') >= 0) {
                    data = parseJSON(data);
                } else if (type === "script" || !type && ct.indexOf("javascript") >= 0) {
                    $.globalEval(data);
                }
            }
            return data;
        };

        return deferred;
    }
};

/**
 * ajaxForm() provides a mechanism for fully automating form submission.
 *
 * The advantages of using this method instead of ajaxSubmit() are:
 *
 * 1: This method will include coordinates for <input type="image" /> elements (if the element
 *    is used to submit the form).
 * 2. This method will include the submit element's name/value data (for the element that was
 *    used to submit the form).
 * 3. This method binds the submit() method to the form for you.
 *
 * The options argument for ajaxForm works exactly as it does for ajaxSubmit.  ajaxForm merely
 * passes the options argument along after properly binding events for submit elements and
 * the form itself.
 */
$.fn.ajaxForm = function(options) {
    options = options || {};
    options.delegation = options.delegation && $.isFunction($.fn.on);

    // in jQuery 1.3+ we can fix mistakes with the ready state
    if (!options.delegation && this.length === 0) {
        var o = { s: this.selector, c: this.context };
        if (!$.isReady && o.s) {
            log('DOM not ready, queuing ajaxForm');
            $(function() {
                $(o.s,o.c).ajaxForm(options);
            });
            return this;
        }
        // is your DOM ready?  http://docs.jquery.com/Tutorials:Introducing_$(document).ready()
        log('terminating; zero elements found by selector' + ($.isReady ? '' : ' (DOM not ready)'));
        return this;
    }

    if ( options.delegation ) {
        $(document)
            .off('submit.form-plugin', this.selector, doAjaxSubmit)
            .off('click.form-plugin', this.selector, captureSubmittingElement)
            .on('submit.form-plugin', this.selector, options, doAjaxSubmit)
            .on('click.form-plugin', this.selector, options, captureSubmittingElement);
        return this;
    }

    return this.ajaxFormUnbind()
        .bind('submit.form-plugin', options, doAjaxSubmit)
        .bind('click.form-plugin', options, captureSubmittingElement);
};

// private event handlers
function doAjaxSubmit(e) {
    /*jshint validthis:true */
    var options = e.data;
    if (!e.isDefaultPrevented()) { // if event has been canceled, don't proceed
        e.preventDefault();
        $(this).ajaxSubmit(options);
    }
}

function captureSubmittingElement(e) {
    /*jshint validthis:true */
    var target = e.target;
    var $el = $(target);
    if (!($el.is("[type=submit],[type=image]"))) {
        // is this a child element of the submit el?  (ex: a span within a button)
        var t = $el.closest('[type=submit]');
        if (t.length === 0) {
            return;
        }
        target = t[0];
    }
    var form = this;
    form.clk = target;
    if (target.type == 'image') {
        if (e.offsetX !== undefined) {
            form.clk_x = e.offsetX;
            form.clk_y = e.offsetY;
        } else if (typeof $.fn.offset == 'function') {
            var offset = $el.offset();
            form.clk_x = e.pageX - offset.left;
            form.clk_y = e.pageY - offset.top;
        } else {
            form.clk_x = e.pageX - target.offsetLeft;
            form.clk_y = e.pageY - target.offsetTop;
        }
    }
    // clear form vars
    setTimeout(function() { form.clk = form.clk_x = form.clk_y = null; }, 100);
}


// ajaxFormUnbind unbinds the event handlers that were bound by ajaxForm
$.fn.ajaxFormUnbind = function() {
    return this.unbind('submit.form-plugin click.form-plugin');
};

/**
 * formToArray() gathers form element data into an array of objects that can
 * be passed to any of the following ajax functions: $.get, $.post, or load.
 * Each object in the array has both a 'name' and 'value' property.  An example of
 * an array for a simple login form might be:
 *
 * [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
 *
 * It is this array that is passed to pre-submit callback functions provided to the
 * ajaxSubmit() and ajaxForm() methods.
 */
$.fn.formToArray = function(semantic, elements) {
    var a = [];
    if (this.length === 0) {
        return a;
    }

    var form = this[0];
    var els = semantic ? form.getElementsByTagName('*') : form.elements;
    if (!els) {
        return a;
    }

    var i,j,n,v,el,max,jmax;
    for(i=0, max=els.length; i < max; i++) {
        el = els[i];
        n = el.name;
        if (!n || el.disabled) {
            continue;
        }

        if (semantic && form.clk && el.type == "image") {
            // handle image inputs on the fly when semantic == true
            if(form.clk == el) {
                a.push({name: n, value: $(el).val(), type: el.type });
                a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
            }
            continue;
        }

        v = $.fieldValue(el, true);
        if (v && v.constructor == Array) {
            if (elements)
                elements.push(el);
            for(j=0, jmax=v.length; j < jmax; j++) {
                a.push({name: n, value: v[j]});
            }
        }
        else if (feature.fileapi && el.type == 'file') {
            if (elements)
                elements.push(el);
            var files = el.files;
            if (files.length) {
                for (j=0; j < files.length; j++) {
                    a.push({name: n, value: files[j], type: el.type});
                }
            }
            else {
                // #180
                a.push({ name: n, value: '', type: el.type });
            }
        }
        else if (v !== null && typeof v != 'undefined') {
            if (elements)
                elements.push(el);
            a.push({name: n, value: v, type: el.type, required: el.required});
        }
    }

    if (!semantic && form.clk) {
        // input type=='image' are not found in elements array! handle it here
        var $input = $(form.clk), input = $input[0];
        n = input.name;
        if (n && !input.disabled && input.type == 'image') {
            a.push({name: n, value: $input.val()});
            a.push({name: n+'.x', value: form.clk_x}, {name: n+'.y', value: form.clk_y});
        }
    }
    return a;
};

/**
 * Serializes form data into a 'submittable' string. This method will return a string
 * in the format: name1=value1&amp;name2=value2
 */
$.fn.formSerialize = function(semantic) {
    //hand off to jQuery.param for proper encoding
    return $.param(this.formToArray(semantic));
};

/**
 * Serializes all field elements in the jQuery object into a query string.
 * This method will return a string in the format: name1=value1&amp;name2=value2
 */
$.fn.fieldSerialize = function(successful) {
    var a = [];
    this.each(function() {
        var n = this.name;
        if (!n) {
            return;
        }
        var v = $.fieldValue(this, successful);
        if (v && v.constructor == Array) {
            for (var i=0,max=v.length; i < max; i++) {
                a.push({name: n, value: v[i]});
            }
        }
        else if (v !== null && typeof v != 'undefined') {
            a.push({name: this.name, value: v});
        }
    });
    //hand off to jQuery.param for proper encoding
    return $.param(a);
};

/**
 * Returns the value(s) of the element in the matched set.  For example, consider the following form:
 *
 *  <form><fieldset>
 *      <input name="A" type="text" />
 *      <input name="A" type="text" />
 *      <input name="B" type="checkbox" value="B1" />
 *      <input name="B" type="checkbox" value="B2"/>
 *      <input name="C" type="radio" value="C1" />
 *      <input name="C" type="radio" value="C2" />
 *  </fieldset></form>
 *
 *  var v = $('input[type=text]').fieldValue();
 *  // if no values are entered into the text inputs
 *  v == ['','']
 *  // if values entered into the text inputs are 'foo' and 'bar'
 *  v == ['foo','bar']
 *
 *  var v = $('input[type=checkbox]').fieldValue();
 *  // if neither checkbox is checked
 *  v === undefined
 *  // if both checkboxes are checked
 *  v == ['B1', 'B2']
 *
 *  var v = $('input[type=radio]').fieldValue();
 *  // if neither radio is checked
 *  v === undefined
 *  // if first radio is checked
 *  v == ['C1']
 *
 * The successful argument controls whether or not the field element must be 'successful'
 * (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
 * The default value of the successful argument is true.  If this value is false the value(s)
 * for each element is returned.
 *
 * Note: This method *always* returns an array.  If no valid value can be determined the
 *    array will be empty, otherwise it will contain one or more values.
 */
$.fn.fieldValue = function(successful) {
    for (var val=[], i=0, max=this.length; i < max; i++) {
        var el = this[i];
        var v = $.fieldValue(el, successful);
        if (v === null || typeof v == 'undefined' || (v.constructor == Array && !v.length)) {
            continue;
        }
        if (v.constructor == Array)
            $.merge(val, v);
        else
            val.push(v);
    }
    return val;
};

/**
 * Returns the value of the field element.
 */
$.fieldValue = function(el, successful) {
    var n = el.name, t = el.type, tag = el.tagName.toLowerCase();
    if (successful === undefined) {
        successful = true;
    }

    if (successful && (!n || el.disabled || t == 'reset' || t == 'button' ||
        (t == 'checkbox' || t == 'radio') && !el.checked ||
        (t == 'submit' || t == 'image') && el.form && el.form.clk != el ||
        tag == 'select' && el.selectedIndex == -1)) {
            return null;
    }

    if (tag == 'select') {
        var index = el.selectedIndex;
        if (index < 0) {
            return null;
        }
        var a = [], ops = el.options;
        var one = (t == 'select-one');
        var max = (one ? index+1 : ops.length);
        for(var i=(one ? index : 0); i < max; i++) {
            var op = ops[i];
            if (op.selected) {
                var v = op.value;
                if (!v) { // extra pain for IE...
                    v = (op.attributes && op.attributes['value'] && !(op.attributes['value'].specified)) ? op.text : op.value;
                }
                if (one) {
                    return v;
                }
                a.push(v);
            }
        }
        return a;
    }
    return $(el).val();
};

/**
 * Clears the form data.  Takes the following actions on the form's input fields:
 *  - input text fields will have their 'value' property set to the empty string
 *  - select elements will have their 'selectedIndex' property set to -1
 *  - checkbox and radio inputs will have their 'checked' property set to false
 *  - inputs of type submit, button, reset, and hidden will *not* be effected
 *  - button elements will *not* be effected
 */
$.fn.clearForm = function(includeHidden) {
    return this.each(function() {
        $('input,select,textarea', this).clearFields(includeHidden);
    });
};

/**
 * Clears the selected form elements.
 */
$.fn.clearFields = $.fn.clearInputs = function(includeHidden) {
    var re = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i; // 'hidden' is not in this list
    return this.each(function() {
        var t = this.type, tag = this.tagName.toLowerCase();
        if (re.test(t) || tag == 'textarea') {
            this.value = '';
        }
        else if (t == 'checkbox' || t == 'radio') {
            this.checked = false;
        }
        else if (tag == 'select') {
            this.selectedIndex = -1;
        }
		else if (t == "file") {
			if (/MSIE/.test(navigator.userAgent)) {
				$(this).replaceWith($(this).clone(true));
			} else {
				$(this).val('');
			}
		}
        else if (includeHidden) {
            // includeHidden can be the value true, or it can be a selector string
            // indicating a special test; for example:
            //  $('#myForm').clearForm('.special:hidden')
            // the above would clean hidden inputs that have the class of 'special'
            if ( (includeHidden === true && /hidden/.test(t)) ||
                 (typeof includeHidden == 'string' && $(this).is(includeHidden)) )
                this.value = '';
        }
    });
};

/**
 * Resets the form data.  Causes all form elements to be reset to their original value.
 */
$.fn.resetForm = function() {
    return this.each(function() {
        // guard against an input with the name of 'reset'
        // note that IE reports the reset function as an 'object'
        if (typeof this.reset == 'function' || (typeof this.reset == 'object' && !this.reset.nodeType)) {
            this.reset();
        }
    });
};

/**
 * Enables or disables any matching elements.
 */
$.fn.enable = function(b) {
    if (b === undefined) {
        b = true;
    }
    return this.each(function() {
        this.disabled = !b;
    });
};

/**
 * Checks/unchecks any matching checkboxes or radio buttons and
 * selects/deselects and matching option elements.
 */
$.fn.selected = function(select) {
    if (select === undefined) {
        select = true;
    }
    return this.each(function() {
        var t = this.type;
        if (t == 'checkbox' || t == 'radio') {
            this.checked = select;
        }
        else if (this.tagName.toLowerCase() == 'option') {
            var $sel = $(this).parent('select');
            if (select && $sel[0] && $sel[0].type == 'select-one') {
                // deselect all other options
                $sel.find('option').selected(false);
            }
            this.selected = select;
        }
    });
};

// expose debug var
$.fn.ajaxSubmit.debug = false;

// helper fn for console logging
function log() {
    if (!$.fn.ajaxSubmit.debug)
        return;
    var msg = '[jquery.form] ' + Array.prototype.join.call(arguments,'');
    if (window.console && window.console.log) {
        window.console.log(msg);
    }
    else if (window.opera && window.opera.postError) {
        window.opera.postError(msg);
    }
}

})(jQuery);
;/**
 *	AJAX filter
 *	use AJAX to filter results as the user is typing, selecting filter
 *	options
 * 
 *	@copyright 	2008-2013 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.1	
 *	@author		philthompson.co.uk
 *	@since		27/02/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	07/05/2013
 *
 */



(function ($) {

    beancounterAjaxFilter = {
    
    	config: {
    		container: 'PrimaryContent'
    	},

        // Run misc/generic functionality and call specific functions
        onReady: function () {
        
        	var self = this;
        	self.preFilter();
        	
        },
        

        ajaxFilter: function(URL, formData){
        
        
        	var self = this,
				$container = $('#' + self.config.container),
				loader = '<div id="Loading">Loading new content&hellip;</div>';
			
			if($container.find('.ajax-container').length === 0){
				$container.wrapInner('<div class="ajax-container" />');
			}
		
			/* Hide current content and show loading graphic */
			$container.find('.ajax-container').addClass('invisible').end().append(loader);		
			
			// Make AJAX call to set URL then if we're successful show the new content else unhide the old stuff
			//console.log(formData);
		
			var jqxhr = $.ajax({
				url: URL,
				context: $container.find('.ajax-container'),
				data: formData
			})
			.done(function(data) { 
		
				var ajaxData = $('<div>').html(data).find('#' + self.config.container).html();
				$(this).html(ajaxData);
		
			})
			.always(function() { 
				
				$('#Loading').remove();
				$container.find('.ajax-container').removeClass('invisible');
			
			});
		
		
		},
        

        preFilter: function(){
        
        	var self = this,
        		searchTimeout,
        		$form = $('#filterForm'),
        		button = '<div id="FilterButton"><a href="#" id="ShowHideFilterLink">Show filter options</a></div>';

        	$('.filter-form').prepend(button);
        	
        	$('#FilterButton').click(function(e){
        		e.preventDefault();
        		$form.slideToggle('slow', function() {
					// Animation complete.
				});
        	});
        		
        		
        	$form.submit(function(e){
        		e.preventDefault();
        		self.ajaxFilter($(this).attr('action'), $(this).serialize());
        	});

			$form.find('input').bind('keyup', function() {
		        if(searchTimeout !== undefined) {
		            clearTimeout(searchTimeout);
		        }
		        searchTimeout = setTimeout(function() {
	                searchTimeout = undefined;
	                // do stuff with $this here
	                $form.submit();
		        }, 300);
			});
			
			$form.find('select').change(function(){
				$form.submit();
			});
			
			$form.find('input:checkbox').click(function(){
				$form.submit();
			});
			
		}

	}


}(jQuery));


/**
 * 	document.ready (onload)
 *	initialise relevant functions
 */
$(document).ready(function(){
	beancounterAjaxFilter.onReady();
});




;/**
 *	AJAX form submit
 *	@copyright 	2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.0	
 *	@author		philthompson.co.uk
 *	@since		07/08/2009
 *	@modified	
 *
 *	@library	jQuery 1.3
 *	@plugin		jquery.form
 *
 *	@var ajaxFormOptions
 *	hideForm
 */
 


var ajaxFormOptions = {
   	target:        '#Content .inner',   // target element(s) to be updated with server response
  	beforeSubmit:  beforeAjaxFormSubmit,  // pre-submit callback
 	success:       ajaxFormSuccess,  // post-submit callback

   // other available options:
   //url:       url        // override for form's 'action' attribute
   //type:      type        // 'get' or 'post', override for form's 'method' attribute
   //dataType:  null        // 'xml', 'script', or 'json' (expected server response type)
   //clearForm: true        // clear all form fields after successful submit
   resetForm: true        // reset the form after successful submit

   // $.ajax options can be used here too, for example:
   //timeout:   3000
};

if(typeof ajaxForm == 'function'){
	//$('.content-primary form').ajaxForm(ajaxFormOptions);
}

$('form').bind('form-pre-serialize', function(e) {
    //tinyMCE.triggerSave();
});

function beforeAjaxFormSubmit(){
	
	var theForm = $('.content-primary form');

	theForm.height(theForm.height());
	theForm.find('div.field').hide();
	theForm.find('button[type="submit"]').html('Submitting please wait&hellip;');
}

function ajaxFormSuccess(){

	$('.content-primary form').ajaxForm(ajaxFormOptions);
	
	if(typeof beancounterColorbox == 'function'){
		beancounterColorbox();
	}
	
	if(typeof hideTime == 'function'){
		hideTime();
	}
		
	if(typeof showHideExtras == 'function'){
		showHideExtras();
	}
	
	if(typeof generateInvoiceNumber == 'function'){
		generateInvoiceNumber();
	}
	
	if(typeof initDatePicker == 'function'){
		initDatePicker();
	}
	
	if(typeof initUsableForms == 'function'){
		initUsableForms();
	}
	
	if(typeof addNewThenRefresh == 'function'){
		addNewThenRefresh();
	}
	

	
	
}

;/**
 *	ajaxPagination
 *	turn normal HTML pagination into AJAX so only the content
 *	affected by the pagination is updated.
 * 
 *	@copyright	2009-2013	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.1	
 *	@author		philthompson.co.uk
 *	@since		22/04/2009
 *	
 *	edited by:  Phil Thompson
 *	@modified	02/05/2013
 *
 */


(function ($) {

    beancounterAjaxPagination = {
      
    	config: {
    		container: 'PrimaryContent'
    	},

        // Run misc/generic functionality and call specific functions
        onReady: function () {
        
        	var self = this;
        	
        	
        	$('#' + self.config.container).on('click', '.pagination a', function(e){
        	
        		
				var	URL = $(this).attr("href"); // href value of clicked link
	
				e.preventDefault();
				
				
				if(URL.indexOf('?mode=ajax') === -1 && URL.indexOf('?') === -1){
					URL = URL + '?mode=ajax';
				} else if(URL.indexOf('?mode=ajax') === -1 && URL.indexOf('?') !== -1){
					URL = URL + '&mode=ajax';
				} 

				// Load in the new page
				self.loadByAjax(URL);
		
			});
        	
        	
        	self.loadFromHash();
        	
        },
        
        /**
		 *	loadByAjax
		 *	Use AJAX to grab new content and load it into place
		 *	@param string URL 	/news/2009/06
		 */
        loadByAjax: function(URL){
        
        	var self = this,
				$container = $('#' + self.config.container),
				loader = '<div id="Loading">Loading new content&hellip;</div>',
				jqxhr,
				ajaxData;
			
			if($container.find('.ajax-container').length === 0){
				$container.wrapInner('<div class="ajax-container" />');
			}
		
			/* Hide current content and show loading graphic */
			$container.find('.ajax-container').addClass('invisible').end().append(loader);
			
			// Update URL - not ready yet :(
			window.location.hash = self.urlHash(URL);
		
			// Make AJAX call to set URL then if we're successful show the new content else unhide the old stuff
			jqxhr = $.ajax({
				url: URL,
				context: $container.find('.ajax-container')
			})
			.done(function(data) { 
		
				ajaxData = $('<div>').html(data).find('#' + self.config.container).html();
				$(this).html(ajaxData);
				// Re-run function to allow future AJAX calls
		  		if(typeof styliseCalendar === 'function'){
		  			styliseCalendar();
		  		}
		
			})
			.always(function() { 
				
				$('#Loading').remove();
				$container.find('.ajax-container').removeClass('invisible');
			
			});
			
        },
        
        /**
		 *	loadFromHash()
		 *	if a hash is already set in the URL make sure the 
		 *	correct page is loaded from it.
		 *	if not - reload the correct page via AJAX
		 */
        loadFromHash: function(){
        	var self = this,
        		currentHash = window.location.hash.replace('#', '/'),
				selectedHref = $('.pagination').find('.selected').find('a').attr('href'),
				URLPartOne,
				URL,
				hash;
			
			if(currentHash.length > 0 && selectedHref.length > 0){
		
				// Does the selected href differ from the hash (to an extent)?
				if(selectedHref.indexOf(currentHash) === -1){
				
					// In the pagination the link to page 1 
					// will always be /section/ so grab it
					
					URLPartOne = $('.pagination').find('li:nth-child(2)').find('a').attr('href');
					
					// Now remove all the extra slashes
					URLPartOne = URLPartOne.replace('/', '');
					currentHash = currentHash.replace('/', '');
					
					// Rebuild the URL and hash and then 
					// grab the required page
					URL = '/' + URLPartOne + currentHash.replace('/', '') + '?mode=ajax';
					hash = self.urlHash(selectedHref);
					self.loadByAjax(URL);
				}
			}
        },
        
        
        /**
		 *	urlHash()
		 *	everytime a ajax link is clicked add a hash to the URL
		 *	this functions work out what that hash should be
		 *	Allows for bookmarking and browser history
		 */
        urlHash: function(href){
        	var hash = href.replace(href.split('/')[1] + '/', '');
	
			if(hash.indexOf('?') !== -1) { 
				hash = hash.split('?')[0]; 
			}
			
			return hash;
		}
	
	}


}(jQuery));


/**
 * 	document.ready (onload)
 *	initialise relevant functions
 */
$(document).ready(function(){
	beancounterAjaxPagination.onReady();
});
;/**
 *	Bean Counter 
 *	JavaScript
 *	
 *	@pages		global
 *	@author		philthompson.co.uk
 *	@since		03/03/2010
 *	@version 	1.0
 *	@package	jQuery
 *
 */
 
 




/**
 *	ajaxClientVcard
 *	When the user changes the client (drop down menu)
 *	change the client address field with AJAX
 */
function ajaxClientVcard(){
	
	if($('#client').length === 0 || $('#for_the_attention_of').length === 0){
		return;
	}	
	
	$('#client').change(function(){
		
		data = {
			id: $('#client').val()
		}
		
		$.ajax({
			url: '/ajax/client_vcard.php',
			data:  data,
			success: function(data){
		  		$('#for_the_attention_of').val(data);
			}
		});
		
	});
	
}




/**
 *	beancounterSettings
 *	Take drop down menus for start_of_financial values and merge them to gether to get a date string like
 *	3107 in a hidden field - whcih the database needs
 *	@return	void
 */
function beancounterSettings(){
	
	var $financialYear = $('#start_of_financial_year'),
		$financialYearDay = $('#start_of_financial_year_day'),
		$financialYearMonth = $('#start_of_financial_year_month');
	
	if($financialYear.length === 0){
		return;
	}
	
	$('#start_of_financial_year_day, #start_of_financial_year_month').change(function(e){
		$financialYear.val($financialYearDay.val() + $financialYearMonth.val());
	});
	
	
}





/**
 *	calculateVAT
 *	Work out how much VAT was paid from grand total
 *	@param	float	grand total e.g 100
 *	@param	float	VAT rate e.g. 17.5 or 20
 *	@return	float	
 */
function calculateVAT(total_paid, vat_rate){
	
	var minus_vat,
		vat_decimal,
		vat_paid = 0;

	if(vat_rate > 0){
		vat_decimal = (( 100 + parseFloat(vat_rate) ) / 100);
		minus_vat = (total_paid / vat_decimal);
		vat_paid = (total_paid - minus_vat);
		
		// Now round it up
		vat_paid = Math.round(vat_paid*100)/100 ;
	
	}
	
	return vat_paid;
}



/**
 *	closePopup
 *	@return	void
 */
function closePopup(){
	
	$(document).on('click', 'a.close-popup', function(e){
		e.preventDefault();
		parent.$.fn.colorbox.close();
	});
	
}


/**
 *	expensesVAT
 *	@return	void
 *
 */
function expensesVAT(){

	if($('#outgoing_supplier').length === 0 && $('#vat').length === 0){
		return;
	}
	
	if($('#vat').val().length === 0){
		$('#vat').parent().hide().before('<div class="field"><a href="#" id="AddExpensesVAT" class="button inline-button">Log VAT?</a></div>');
	}
	
	$('#AddExpensesVAT').click(function(e){
		e.preventDefault();
		$('#AddExpensesVAT').remove();
		$('#vat').parent().slideDown();
		$('#vat_rate').parent().slideDown();
		$('#price').blur();
	});	
	
	$('#price').blur(function(e){
		if($('#vat').parent().is(':visible') !== true){
			$('#vat').val(calculateVAT($('#price').val(), $('#vat_rate').val()));
		}
	});
	

}



/**
 *	projectsVAT
 *	@return	void
 *
 */
function projectsVAT(){

	var	$vatRate = $('#vat_rate'),
		$hiddenVatRate = $('#hidden_vat_rate'),
		$vatFlatRatePercentage = $('#vat_flat_rate_percentage'),
		$hiddenVatFlatRatePercentage = $('#hidden_vat_flat_rate_percentage');

	if($vatRate.length === 0 || $hiddenVatRate.length === 0){
		return;
	}
	
	if($hiddenVatRate.val().length === 0){
		return;
	}

	$('#charge_vat').click(function(e){
		//e.preventDefault();
		if($(this).is(':checked')){
			$vatRate.val($hiddenVatRate.val());
			if($hiddenVatFlatRatePercentage.length > 0){
				$vatFlatRatePercentage.val($hiddenVatFlatRatePercentage.val());
			}
		} else{
			$vatRate.val('0');
			if($hiddenVatFlatRatePercentage.length > 0){
				$vatFlatRatePercentage.val('0');
			}
		}
	});

}



/**
 *	formIframeCheck
 *	@return	void
 */
function formIframeCheck(){

	var	isInIFrame = (window.location != window.parent.location) ? true : false,
		$popupField = $('#popupfield');
		
	if($popupField.length === 0){
		return;
	}
	
	if(isInIFrame === true){
		$popupField.val('true');
	}
	
}








/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <jevin9@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return. Jevin O. Sewaruth
 * ----------------------------------------------------------------------------
 *
 * Autogrow Textarea Plugin Version v3.0
 * http://www.technoreply.com/autogrow-textarea-plugin-3-0
 * 
 * THIS PLUGIN IS DELIVERD ON A PAY WHAT YOU WHANT BASIS. IF THE PLUGIN WAS USEFUL TO YOU, PLEASE CONSIDER BUYING THE PLUGIN HERE :
 * https://sites.fastspring.com/technoreply/instant/autogrowtextareaplugin
 *
 * Date: October 15, 2012
 */

jQuery.fn.autoGrow = function() {
	return this.each(function() {

		var createMirror = function(textarea) {
			jQuery(textarea).after('<div class="autogrow-textarea-mirror"></div>');
			return jQuery(textarea).next('.autogrow-textarea-mirror')[0];
		}

		var sendContentToMirror = function (textarea) {
			mirror.innerHTML = String(textarea.value).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br />') + '.<br/>.';

			if (jQuery(textarea).height() != jQuery(mirror).height())
				jQuery(textarea).height(jQuery(mirror).height());
		}

		var growTextarea = function () {
			sendContentToMirror(this);
		}

		// Create a mirror
		var mirror = createMirror(this);
		
		// Style the mirror
		mirror.style.display = 'none';
		mirror.style.wordWrap = 'break-word';
		mirror.style.whiteSpace = 'normal';
		mirror.style.padding = jQuery(this).css('padding');
		mirror.style.width = jQuery(this).css('width');
		mirror.style.fontFamily = jQuery(this).css('font-family');
		mirror.style.fontSize = jQuery(this).css('font-size');
		mirror.style.lineHeight = jQuery(this).css('line-height');

		// Style the textarea
		this.style.overflow = "hidden";
		this.style.minHeight = this.rows+"em";

		// Bind the textarea's event
		this.onkeyup = growTextarea;

		// Fire the event for text already present
		sendContentToMirror(this);

	});
}; 
 
 

/**
 *	popularClients
 *	Use AJAX to get a list of popualr clients and append them 
 *	to the start of the #clients <select>
 *	@return	void
 */
function popularClients(){
	
	var ajaxUrl = '/ajax/popular_clients_dropdown.php',
		$clients = $('#client'),
		currentData;
	
	
	if($clients.length === 0){
		return;
	}
	
	
	currentData = $clients.html();
	
	
	$.ajax({
		url:	ajaxUrl,
		data:	'',
		success: function(data, textStatus, jqXHR){
		
			$clients.html(data + currentData);
		}
	});
	
	
	
}



/**
 *	refreshChoices
 *	Reload the <select> with AJAX and load in only the most
 *	recently added <option>
 *	@return	boolean
 */
function refreshChoices(element){

	var refreshButton, attrHref;

	$('#add_new_' + element + '_link', parent.document.body).hide();
	refreshButton = $('#refresh_' + element + '_link', parent.document.body);
	$(refreshButton).css('display', 'inline-block').click();

	// Load the ajax drop down menu into the drop down list
	attrHref = $(refreshButton).attr("href");
	// Put ajax content of file into the drop down id
	$('#' + element, parent.document.body).load(attrHref);
	$(refreshButton).hide();
	
	// Close this popup
	parent.$.fn.colorbox.close();

	return false;
	
}
 
/**
 *	styliseCalendar
 *	Make bookings which span multiple days the same colour as each other but
 *	different colours to other bookings.
 */
function styliseCalendar(){

	var	colours = new Array('#C33', '#6A0', '#36C', '#E80', '#D47', '#949', '#7083E8', '#63C'),
		bookings,
		attrClasses = new Array(),
		attrClass,
		i = 0,
		reservedClasses = new Array('single', 'multiple');
	
	if($('.calendar').length === 0){
		return;
	}
	
	
	// Grab all the bookings then process the list and 
	// create a new list of all the classes used on the bookings
	$('.calendar').each(function(){
		bookings = $(this).find('.bookings').find('li');
	});
	

	$(bookings).each(function(){

		attrClass = $(this).attr('class').split(' ');
		var x;
		for(x in attrClass){
			if(
				attrClass[x] != 'odd'
				&& attrClass[x] != 'even'
				&& attrClass[x] != 'first'
				&& attrClass[x] != 'even'
				&& attrClass[x] != 'single'
				&& attrClass[x] != 'multiple'
			){
				//console.log(attrClass[x]);
				attrClasses[i] = attrClass[x];
				i++;
			}
		}
	});
	
	//console.log(attrClasses);
	
	
	// Now make the array unique then we'll go through and assign colours
	// to each class of bookings e.g. $('li.booking-55') a gets red
    var uniques = [];
    for(var i = attrClasses.length; i--;){
        var val = attrClasses[i];  
        if($.inArray( val, uniques )===-1){
            uniques.unshift(val);
        }
    }

	//console.log(uniques);
	
	// now go through all the bookings with the same class and apply
	// a colour to them - should we apply a class for CSS instead?
	i = 0;
	var y;
	for(y in uniques){
		$('ul.bookings li.' + uniques[y] + ' a').css('background-color', colours[i]).css('color', '#FFF');
		i++;
		// reset counter so we don't run out of colours
		if(i >= colours.length){
			i = 0;
		}
	}
	
} 





/**
 *	timingClients
 *	@return	void
 */
function timingClients(){
	
	
	var projectUrl = '/ajax/project_client_dropdown.php',
		$client = $('#client'),
		$project = $('#project');
		
	if($client.length === 0 || $project.length === 0 || $('body.timings').length === 0){
		return;
	}

	$client.change(function(e){
		
		//
		$.ajax({
			url:	projectUrl,
			data:	'client=' + $client.val() + '&project=' + $project.val(),
			success: function(data, textStatus, jqXHR){
				$project.html(data);
			}
		});
		
	});
	
	
}
 

/**
 *	toggleProjectDetails
 */
function toggleProjectDetails(){

	var	showText = 'Show details',
		hideText = 'Hide details',
		toggleLink = ' <a href="#" id="ToggleProjectDetails" class="button inline-button"> ' + showText + '</a>',
		$projectDetails = $('#ProjectDetails').find('.details'),
		slideSpeed = 500;
	
	$projectDetails.hide();
	$('div#ProjectDetails h2:first').append(toggleLink);
	
	var $toggleLink = $('#ToggleProjectDetails');
	
	$toggleLink.click(function(e){
		$projectDetails.slideToggle(slideSpeed);
		if($toggleLink.text() == showText){
			$toggleLink.text(hideText);
		} else{
			$toggleLink.text(showText);
		}
	});
	
	
} 
 
/**
 *	toggleProjectTasks
 *	Show/hide extra project tasks when required in the project form
 */ 
function toggleProjectTasks(){

	var	toggleShowText = 'Edit line item',
		toggleHideText = 'Done editing this item',
		toggleLink = ' <a href="#" class="toggle-task button inline-button edit">' + toggleShowText + '</a>',
		addLink = ' <a href="#" class="add-task button inline-button positive add">Add another line item</a>',
		taskFieldset, 
		taskLegend, 
		taskFields, 
		addFieldset, 
		taskCount,
		slideSpeed = 500,
		$manageTasks = $('#ManageTasks');
	
	if($manageTasks.length == 0){
		return;
	}
	
	$manageTasks.find('.edit-task').find('.field').hide();
	$manageTasks.find('.add-new-task').hide();
	
	
	$manageTasks.find('.edit-task').each(function(){
	
		taskFieldset = $(this);
		taskLegend = $(taskFieldset).children('legend');
		$(taskLegend).append(toggleLink).show();
		
	})
	
	$manageTasks.find('.toggle-task').click(function(e){
		e.preventDefault();
		
		var $child = $(this).parent().parent().children('div.field');
		
		$child.slideToggle(slideSpeed);
		
		if($(this).text() == toggleShowText){
			$(this).text(toggleHideText);
		} else{
			$(this).text(toggleShowText);
		}
			
	});
	
	
	if($manageTasks.find('.edit-task').length === 0){
		$manageTasks.find('.add-new-task:first').show().append(addLink);
	} else{
		$manageTasks.find('.edit-task:last').append(addLink);
	}
	

	$(document).on('click', '#ManageTasks a.add-task', function(e){
		$(this).hide().parent().next('.add-new-task').show().append(addLink);
		$(this).remove();
		
		e.preventDefault();
	});
} 



// Allow a menu to be shown/hidden with the click of a button
function mobileMenu(){
	
	var navClass = 'site-nav',
		activeClass = 'active',
		$nav = $('.' + navClass),
		$button = $('.' + navClass + '__button');
		
	if($nav.length === 0 || $button.length === 0){
		return;
	}	
		
	$button.click(function(e){
		e.preventDefault();
		// Note: we're just gonna toggle classes with JS and we'll use CSS to display/animate stuff
		$nav.toggleClass(activeClass);
	});
	
}



 
 
/**
 *	beancounterInit
 */
function beancounterInit(){

	$('.hidden').hide().removeClass('hidden');
	
	toggleProjectDetails();
	
	toggleProjectTasks();
	
	styliseCalendar();	
	
	ajaxClientVcard();
		
	expensesVAT();
	
	projectsVAT();
	
	formIframeCheck();	
	
	timingClients();

	popularClients();
		
	closePopup();
	
	beancounterSettings();
		
	mobileMenu();
	
}


$(document).ready(function(){
	beancounterInit();
});;(function(root, factory) {
    if(typeof exports === 'object') {
        module.exports = factory();
    }
    else if(typeof define === 'function' && define.amd) {
        define([], factory);
    }
    else {
        root['Chartist'] = factory();
    }
}(this, function() {

  /* Chartist.js 0.1.11
   * Copyright © 2014 Gion Kunz
   * Free to use under the WTFPL license.
   * http://www.wtfpl.net/
   */
  /**
   * The core module of Chartist that is mainly providing static functions and higher level functions for chart modules.
   *
   * @module Chartist.Core
   */

  // This object is prepared for export via UMD
  var Chartist = {};
  Chartist.version = '0.1.11';

  (function (window, document, Chartist) {
    'use strict';

    // Helps to simplify functional style code
    Chartist.noop = function (n) {
      return n;
    };

    // Generates a-z from number
    Chartist.alphaNumerate = function (n) {
      // Limit to a-z
      return String.fromCharCode(97 + n % 26);
    };

    // Simple recursive object extend
    Chartist.extend = function (target, source) {
      target = target || {};
      for (var prop in source) {
        if (typeof source[prop] === 'object') {
          target[prop] = Chartist.extend(target[prop], source[prop]);
        } else {
          target[prop] = source[prop];
        }
      }
      return target;
    };

    // Get element height / width with fallback to svg BoundingBox or parent container dimensions
    // See https://bugzilla.mozilla.org/show_bug.cgi?id=530985
    Chartist.getHeight = function (svgElement) {
      return svgElement.clientHeight || Math.round(svgElement.getBBox().height) || svgElement.parentNode.clientHeight;
    };

    Chartist.getWidth = function (svgElement) {
      return svgElement.clientWidth || Math.round(svgElement.getBBox().width) || svgElement.parentNode.clientWidth;
    };

    // Create Chartist SVG element
    Chartist.createSvg = function (query, width, height, className) {
      // Get dom object from query or if already dom object just use it
      var container = query.nodeType ? query : document.querySelector(query),
        svg;

      // If container was not found we throw up
      if (!container) {
        throw 'Container node with selector "' + query + '" not found';
      }

      // If already contains our svg object we clear it, set width / height and return
      if (container._ctChart !== undefined) {
        svg = container._ctChart.attr({
          width: width || '100%',
          height: height || '100%'
        }).removeAllClasses().addClass(className);
        // Clear the draw if its already used before so we start fresh
        svg.empty();

      } else {
        // Create svg object with width and height or use 100% as default
        svg = Chartist.svg('svg').attr({
          width: width || '100%',
          height: height || '100%'
        }).addClass(className);

        // Add the DOM node to our container
        container.appendChild(svg._node);
        container._ctChart = svg;
      }

      return svg;
    };

    // Convert data series into plain array
    Chartist.getDataArray = function (data) {
      var array = [];

      for (var i = 0; i < data.series.length; i++) {
        // If the series array contains an object with a data property we will use the property
        // otherwise the value directly (array or number)
        array[i] = typeof(data.series[i]) === 'object' && data.series[i].data !== undefined ?
          data.series[i].data : data.series[i];
      }

      return array;
    };

    // Add missing values at the end of the arrays
    Chartist.normalizeDataArray = function (dataArray, length) {
      for (var i = 0; i < dataArray.length; i++) {
        if (dataArray[i].length === length) {
          continue;
        }

        for (var j = dataArray[i].length; j < length; j++) {
          dataArray[i][j] = 0;
        }
      }

      return dataArray;
    };

    Chartist.orderOfMagnitude = function (value) {
      return Math.floor(Math.log(Math.abs(value)) / Math.LN10);
    };

    Chartist.projectLength = function (svg, length, bounds, options) {
      var availableHeight = Chartist.getAvailableHeight(svg, options);
      return (length / bounds.range * availableHeight);
    };

    Chartist.getAvailableHeight = function (svg, options) {
      return Chartist.getHeight(svg._node) - (options.chartPadding * 2) - options.axisX.offset;
    };

    // Get highest and lowest value of data array
    Chartist.getHighLow = function (dataArray) {
      var i,
        j,
        highLow = {
          high: -Number.MAX_VALUE,
          low: Number.MAX_VALUE
        };

      for (i = 0; i < dataArray.length; i++) {
        for (j = 0; j < dataArray[i].length; j++) {
          if (dataArray[i][j] > highLow.high) {
            highLow.high = dataArray[i][j];
          }

          if (dataArray[i][j] < highLow.low) {
            highLow.low = dataArray[i][j];
          }
        }
      }

      return highLow;
    };

    // Find the highest and lowest values in a two dimensional array and calculate scale based on order of magnitude
    Chartist.getBounds = function (svg, normalizedData, options, referenceValue) {
      var i,
        newMin,
        newMax,
        bounds = Chartist.getHighLow(normalizedData);

      // Overrides of high / low from settings
      bounds.high = options.high || (options.high === 0 ? 0 : bounds.high);
      bounds.low = options.low || (options.low === 0 ? 0 : bounds.low);

      // Overrides of high / low based on reference value, it will make sure that the invisible reference value is
      // used to generate the chart. This is useful when the chart always needs to contain the position of the
      // invisible reference value in the view i.e. for bipolar scales.
      if (referenceValue || referenceValue === 0) {
        bounds.high = Math.max(referenceValue, bounds.high);
        bounds.low = Math.min(referenceValue, bounds.low);
      }

      bounds.valueRange = bounds.high - bounds.low;
      bounds.oom = Chartist.orderOfMagnitude(bounds.valueRange);
      bounds.min = Math.floor(bounds.low / Math.pow(10, bounds.oom)) * Math.pow(10, bounds.oom);
      bounds.max = Math.ceil(bounds.high / Math.pow(10, bounds.oom)) * Math.pow(10, bounds.oom);
      bounds.range = bounds.max - bounds.min;
      bounds.step = Math.pow(10, bounds.oom);
      bounds.numberOfSteps = Math.round(bounds.range / bounds.step);

      // Optimize scale step by checking if subdivision is possible based on horizontalGridMinSpace
      while (true) {
        var length = Chartist.projectLength(svg, bounds.step / 2, bounds, options);
        if (length >= options.axisY.scaleMinSpace) {
          bounds.step /= 2;
        } else {
          break;
        }
      }

      // Narrow min and max based on new step
      newMin = bounds.min;
      newMax = bounds.max;
      for (i = bounds.min; i <= bounds.max; i += bounds.step) {
        if (i + bounds.step < bounds.low) {
          newMin += bounds.step;
        }

        if (i - bounds.step > bounds.high) {
          newMax -= bounds.step;
        }
      }
      bounds.min = newMin;
      bounds.max = newMax;
      bounds.range = bounds.max - bounds.min;

      bounds.values = [];
      for (i = bounds.min; i <= bounds.max; i += bounds.step) {
        bounds.values.push(i);
      }

      return bounds;
    };

    Chartist.calculateLabelOffset = function (svg, data, labelClass, labelInterpolationFnc, offsetFnc) {
      var offset = 0;
      for (var i = 0; i < data.length; i++) {
        // If interpolation function returns falsy value we skipp this label
        var interpolated = labelInterpolationFnc(data[i], i);
        if (!interpolated && interpolated !== 0) {
          continue;
        }

        var label = svg.elem('text', {
          dx: 0,
          dy: 0
        }, labelClass).text('' + interpolated);

        // Check if this is the largest label and update offset
        offset = Math.max(offset, offsetFnc(label._node));
        // Remove label after offset Calculation
        label.remove();
      }

      return offset;
    };

    // Used to iterate over array, interpolate using a interpolation function and executing callback (used for rendering)
    Chartist.interpolateData = function (data, labelInterpolationFnc, callback) {
      for (var index = 0; index < data.length; index++) {
        // If interpolation function returns falsy value we skipp this label
        var interpolatedValue = labelInterpolationFnc(data[index], index);
        if (!interpolatedValue && interpolatedValue !== 0) {
          continue;
        }

        callback(data, index, interpolatedValue);
      }
    };

    Chartist.polarToCartesian = function (centerX, centerY, radius, angleInDegrees) {
      var angleInRadians = (angleInDegrees - 90) * Math.PI / 180.0;

      return {
        x: centerX + (radius * Math.cos(angleInRadians)),
        y: centerY + (radius * Math.sin(angleInRadians))
      };
    };

    // Initialize chart drawing rectangle (area where chart is drawn) x1,y1 = bottom left / x2,y2 = top right
    Chartist.createChartRect = function (svg, options, xAxisOffset, yAxisOffset) {
      return {
        x1: options.chartPadding + yAxisOffset,
        y1: (options.height || Chartist.getHeight(svg._node)) - options.chartPadding - xAxisOffset,
        x2: (options.width || Chartist.getWidth(svg._node)) - options.chartPadding,
        y2: options.chartPadding,
        width: function () {
          return this.x2 - this.x1;
        },
        height: function () {
          return this.y1 - this.y2;
        }
      };
    };

    Chartist.createXAxis = function (chartRect, data, grid, labels, options) {
      // Create X-Axis
      data.labels.forEach(function (value, index) {
        var interpolatedValue = options.axisX.labelInterpolationFnc(value, index),
          pos = chartRect.x1 + chartRect.width() / data.labels.length * index;

        // If interpolated value returns falsey (except 0) we don't draw the grid line
        if (!interpolatedValue && interpolatedValue !== 0) {
          return;
        }

        if (options.axisX.showGrid) {
          grid.elem('line', {
            x1: pos,
            y1: chartRect.y1,
            x2: pos,
            y2: chartRect.y2
          }, [options.classNames.grid, options.classNames.horizontal].join(' '));
        }

        if (options.axisX.showLabel) {
          // Use config offset for setting labels of
          var label = labels.elem('text', {
            dx: pos + 2
          }, [options.classNames.label, options.classNames.horizontal].join(' ')).text('' + interpolatedValue);

          // TODO: should use 'alignment-baseline': 'hanging' but not supported in firefox. Instead using calculated height to offset y pos
          label.attr({
            dy: chartRect.y1 + Chartist.getHeight(label._node) + options.axisX.offset
          });
        }
      });
    };

    Chartist.createYAxis = function (chartRect, bounds, grid, labels, offset, options) {
      // Create Y-Axis
      bounds.values.forEach(function (value, index) {
        var interpolatedValue = options.axisY.labelInterpolationFnc(value, index),
          pos = chartRect.y1 - chartRect.height() / bounds.values.length * index;

        // If interpolated value returns falsey (except 0) we don't draw the grid line
        if (!interpolatedValue && interpolatedValue !== 0) {
          return;
        }

        if (options.axisY.showGrid) {
          grid.elem('line', {
            x1: chartRect.x1,
            y1: pos,
            x2: chartRect.x2,
            y2: pos
          }, [options.classNames.grid, options.classNames.vertical].join(' '));
        }

        if (options.axisY.showLabel) {
          labels.elem('text', {
            dx: options.axisY.labelAlign === 'right' ? offset - options.axisY.offset + options.chartPadding : options.chartPadding,
            dy: pos - 2,
            'text-anchor': options.axisY.labelAlign === 'right' ? 'end' : 'start'
          }, [options.classNames.label, options.classNames.vertical].join(' ')).text('' + interpolatedValue);
        }
      });
    };

    Chartist.projectPoint = function (chartRect, bounds, data, index) {
      return {
        x: chartRect.x1 + chartRect.width() / data.length * index,
        y: chartRect.y1 - chartRect.height() * (data[index] - bounds.min) / (bounds.range + bounds.step)
      };
    };

    // Provides options handling functionality with callback for options changes triggered by responsive options and media query matches
    // TODO: With multiple media queries the handleMediaChange function is triggered too many times, only need one
    Chartist.optionsProvider = function (defaultOptions, options, responsiveOptions, optionsChangedCallbackFnc) {
      var baseOptions = Chartist.extend(Chartist.extend({}, defaultOptions), options),
        currentOptions,
        mediaQueryListeners = [],
        i;

      function applyOptions() {
        currentOptions = Chartist.extend({}, baseOptions);

        if (responsiveOptions) {
          for (i = 0; i < responsiveOptions.length; i++) {
            var mql = window.matchMedia(responsiveOptions[i][0]);
            if (mql.matches) {
              currentOptions = Chartist.extend(currentOptions, responsiveOptions[i][1]);
            }
          }
        }

        optionsChangedCallbackFnc(currentOptions);
        return currentOptions;
      }

      if (!window.matchMedia) {
        throw 'window.matchMedia not found! Make sure you\'re using a polyfill.';
      } else if (responsiveOptions) {

        for (i = 0; i < responsiveOptions.length; i++) {
          var mql = window.matchMedia(responsiveOptions[i][0]);
          mql.addListener(applyOptions);
          mediaQueryListeners.push(mql);
        }
      }

      return applyOptions();
    };

    // http://schepers.cc/getting-to-the-point
    Chartist.catmullRom2bezier = function (crp, z) {
      var d = [];
      for (var i = 0, iLen = crp.length; iLen - 2 * !z > i; i += 2) {
        var p = [
          {x: +crp[i - 2], y: +crp[i - 1]},
          {x: +crp[i], y: +crp[i + 1]},
          {x: +crp[i + 2], y: +crp[i + 3]},
          {x: +crp[i + 4], y: +crp[i + 5]}
        ];
        if (z) {
          if (!i) {
            p[0] = {x: +crp[iLen - 2], y: +crp[iLen - 1]};
          } else if (iLen - 4 === i) {
            p[3] = {x: +crp[0], y: +crp[1]};
          } else if (iLen - 2 === i) {
            p[2] = {x: +crp[0], y: +crp[1]};
            p[3] = {x: +crp[2], y: +crp[3]};
          }
        } else {
          if (iLen - 4 === i) {
            p[3] = p[2];
          } else if (!i) {
            p[0] = {x: +crp[i], y: +crp[i + 1]};
          }
        }
        d.push(
          [
            (-p[0].x + 6 * p[1].x + p[2].x) / 6,
            (-p[0].y + 6 * p[1].y + p[2].y) / 6,
            (p[1].x + 6 * p[2].x - p[3].x) / 6,
            (p[1].y + 6 * p[2].y - p[3].y) / 6,
            p[2].x,
            p[2].y
          ]
        );
      }

      return d;
    };

  }(window, document, Chartist));;/**
   * Chartist SVG module for simple SVG DOM abstraction
   *
   * @module Chartist.svg
   */
  /* global Chartist */
  (function(window, document, Chartist) {
    'use strict';

    Chartist.svg = function(name, attributes, className, parent) {

      var svgns = 'http://www.w3.org/2000/svg';

      function attr(node, attributes) {
        Object.keys(attributes).forEach(function(key) {
          node.setAttribute(key, attributes[key]);
        });

        return node;
      }

      function elem(svg, name, attributes, className, parentNode) {
        var node = document.createElementNS(svgns, name);
        node._ctSvgElement = svg;

        if(parentNode) {
          parentNode.appendChild(node);
        }

        if(attributes) {
          attr(node, attributes);
        }

        if(className) {
          addClass(node, className);
        }

        return node;
      }

      function text(node, t) {
        node.appendChild(document.createTextNode(t));
      }

      function empty(node) {
        while (node.firstChild) {
          node.removeChild(node.firstChild);
        }
      }

      function remove(node) {
        node.parentNode.removeChild(node);
      }

      function classes(node) {
        return node.getAttribute('class') ? node.getAttribute('class').trim().split(/\s+/) : [];
      }

      function addClass(node, names) {
        node.setAttribute('class',
          classes(node)
            .concat(names.trim().split(/\s+/))
            .filter(function(elem, pos, self) {
              return self.indexOf(elem) === pos;
            }).join(' ')
        );
      }

      function removeClass(node, names) {
        var removedClasses = names.trim().split(/\s+/);

        node.setAttribute('class', classes(node).filter(function(name) {
          return removedClasses.indexOf(name) === -1;
        }).join(' '));
      }

      function removeAllClasses(node) {
        node.className = '';
      }

      return {
        _node: elem(this, name, attributes, className, parent ? parent._node : undefined),
        _parent: parent,
        parent: function() {
          return this._parent;
        },
        attr: function(attributes) {
          attr(this._node, attributes);
          return this;
        },
        empty: function() {
          empty(this._node);
          return this;
        },
        remove: function() {
          remove(this._node);
          return this;
        },
        elem: function(name, attributes, className) {
          return Chartist.svg(name, attributes, className, this);
        },
        text: function(t) {
          text(this._node, t);
          return this;
        },
        addClass: function(names) {
          addClass(this._node, names);
          return this;
        },
        removeClass: function(names) {
          removeClass(this._node, names);
          return this;
        },
        removeAllClasses: function() {
          removeAllClasses(this._node);
          return this;
        },
        classes: function() {
          return classes(this._node);
        }
      };
    };

  }(window, document, Chartist));;/**
   * The Chartist line chart can be used to draw Line or Scatter charts. If used in the browser you can access the global `Chartist` namespace where you find the `Line` function as a main entry point.
   *
   * For examples on how to use the line chart please check the examples of the `Chartist.Line` method.
   *
   * @module Chartist.Line
   */
  /* global Chartist */
  (function(window, document, Chartist){
    'use strict';

    /**
     * This method creates a new line chart and returns an object handle to the internal closure. Currently you can use the returned object only for updating / redrawing the chart.
     *
     * @memberof Chartist.Line
     * @param {string|HTMLElement} query A selector query string or directly a DOM element
     * @param {object} data The data object that needs to consist of a labels and a series array
     * @param {object} [options] The options object with options that override the default options. Check the examples for a detailed list.
     * @param {array} [responsiveOptions] Specify an array of responsive option arrays which are a media query and options object pair => [[mediaQueryString, optionsObject],[more...]]
     * @return {object} An object with a version and an update method to manually redraw the chart
     * @function
     *
     * @example
     * // These are the default options of the line chart
     * var options = {
     *   // Options for X-Axis
     *   axisX: {
     *     // The offset of the labels to the chart area
     *     offset: 10,
     *     // If labels should be shown or not
     *     showLabel: true,
     *     // If the axis grid should be drawn or not
     *     showGrid: true,
     *     // Interpolation function that allows you to intercept the value from the axis label
     *     labelInterpolationFnc: function(value){return value;}
     *   },
     *   // Options for Y-Axis
     *   axisY: {
     *     // The offset of the labels to the chart area
     *     offset: 15,
     *     // If labels should be shown or not
     *     showLabel: true,
     *     // If the axis grid should be drawn or not
     *     showGrid: true,
     *     // For the Y-Axis you can set a label alignment property of right or left
     *     labelAlign: 'right',
     *     // Interpolation function that allows you to intercept the value from the axis label
     *     labelInterpolationFnc: function(value){return value;},
     *     // This value specifies the minimum height in pixel of the scale steps
     *     scaleMinSpace: 30
     *   },
     *   // Specify a fixed width for the chart as a string (i.e. '100px' or '50%')
     *   width: undefined,
     *   // Specify a fixed height for the chart as a string (i.e. '100px' or '50%')
     *   height: undefined,
     *   // If the line should be drawn or not
     *   showLine: true,
     *   // If dots should be drawn or not
     *   showPoint: true,
     *   // Specify if the lines should be smoothed (Catmull-Rom-Splines will be used)
     *   lineSmooth: true,
     *   // Overriding the natural low of the chart allows you to zoom in or limit the charts lowest displayed value
     *   low: undefined,
     *   // Overriding the natural high of the chart allows you to zoom in or limit the charts highest displayed value
     *   high: undefined,
     *   // Padding of the chart drawing area to the container element and labels
     *   chartPadding: 5,
     *   // Override the class names that get used to generate the SVG structure of the chart
     *   classNames: {
     *     chart: 'ct-chart-line',
     *     label: 'ct-label',
     *     series: 'ct-series',
     *     line: 'ct-line',
     *     point: 'ct-point',
     *     grid: 'ct-grid',
     *     vertical: 'ct-vertical',
     *     horizontal: 'ct-horizontal'
     *   }
     * };
     *
     * @example
     * // Create a simple line chart
     * var data = {
     *   // A labels array that can contain any sort of values
     *   labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
     *   // Our series array that contains series objects or in this case series data arrays
     *   series: [
     *     [5, 2, 4, 2, 0]
     *   ]
     * };
     *
     * // As options we currently only set a static size of 300x200 px
     * var options = {
     *   width: '300px',
     *   height: '200px'
     * };
     *
     * // In the global name space Chartist we call the Line function to initialize a line chart. As a first parameter we pass in a selector where we would like to get our chart created. Second parameter is the actual data object and as a third parameter we pass in our options
     * Chartist.Line('.ct-chart', data, options);
     *
     * @example
     * // Create a line chart with responsive options
     *
     * var data = {
     *   // A labels array that can contain any sort of values
     *   labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
     *   // Our series array that contains series objects or in this case series data arrays
     *   series: [
     *     [5, 2, 4, 2, 0]
     *   ]
     * };
     *
     * // In adition to the regular options we specify responsive option overrides that will override the default configutation based on the matching media queries.
     * var responsiveOptions = [
     *   ['screen and (min-width: 641px) and (max-width: 1024px)', {
     *     showPoint: false,
     *     axisX: {
     *       labelInterpolationFnc: function(value) {
     *         // Will return Mon, Tue, Wed etc. on medium screens
     *         return value.slice(0, 3);
     *       }
     *     }
     *   }],
     *   ['screen and (max-width: 640px)', {
     *     showLine: false,
     *     axisX: {
     *       labelInterpolationFnc: function(value) {
     *         // Will return M, T, W etc. on small screens
     *         return value[0];
     *       }
     *     }
     *   }]
     * ];
     *
     * Chartist.Line('.ct-chart', data, null, responsiveOptions);
     *
     */
    Chartist.Line = function (query, data, options, responsiveOptions) {

      var defaultOptions = {
          axisX: {
            offset: 10,
            showLabel: true,
            showGrid: true,
            labelInterpolationFnc: Chartist.noop
          },
          axisY: {
            offset: 15,
            showLabel: true,
            showGrid: true,
            labelAlign: 'right',
            labelInterpolationFnc: Chartist.noop,
            scaleMinSpace: 30
          },
          width: undefined,
          height: undefined,
          showLine: true,
          showPoint: true,
          lineSmooth: true,
          low: undefined,
          high: undefined,
          chartPadding: 5,
          classNames: {
            chart: 'ct-chart-line',
            label: 'ct-label',
            series: 'ct-series',
            line: 'ct-line',
            point: 'ct-point',
            grid: 'ct-grid',
            vertical: 'ct-vertical',
            horizontal: 'ct-horizontal'
          }
        },
        currentOptions,
        svg;

      function createChart(options) {
        var xAxisOffset,
          yAxisOffset,
          seriesGroups = [],
          bounds,
          normalizedData = Chartist.normalizeDataArray(Chartist.getDataArray(data), data.labels.length);

        // Create new svg object
        svg = Chartist.createSvg(query, options.width, options.height, options.classNames.chart);

        // initialize bounds
        bounds = Chartist.getBounds(svg, normalizedData, options);

        xAxisOffset = options.axisX.offset;
        if (options.axisX.showLabel) {
          xAxisOffset += Chartist.calculateLabelOffset(
            svg,
            data.labels,
            [options.classNames.label, options.classNames.horizontal].join(' '),
            options.axisX.labelInterpolationFnc,
            Chartist.getHeight
          );
        }

        yAxisOffset = options.axisY.offset;
        if (options.axisY.showLabel) {
          yAxisOffset += Chartist.calculateLabelOffset(
            svg,
            bounds.values,
            [options.classNames.label, options.classNames.horizontal].join(' '),
            options.axisY.labelInterpolationFnc,
            Chartist.getWidth
          );
        }

        var chartRect = Chartist.createChartRect(svg, options, xAxisOffset, yAxisOffset);
        // Start drawing
        var labels = svg.elem('g'),
          grid = svg.elem('g');

        Chartist.createXAxis(chartRect, data, grid, labels, options);
        Chartist.createYAxis(chartRect, bounds, grid, labels, yAxisOffset, options);

        // Draw the series
        // initialize series groups
        for (var i = 0; i < data.series.length; i++) {
          seriesGroups[i] = svg.elem('g');
          // Use series class from series data or if not set generate one
          seriesGroups[i].addClass([
            options.classNames.series,
            (data.series[i].className || options.classNames.series + '-' + Chartist.alphaNumerate(i))
          ].join(' '));

          var p = Chartist.projectPoint(chartRect, bounds, normalizedData[i], 0),
            pathCoordinates = [p.x, p.y],
            point;

          // First dot we need to add before loop
          if (options.showPoint) {
            // Small offset for Firefox to render squares correctly
            point = seriesGroups[i].elem('line', {
              x1: p.x,
              y1: p.y,
              x2: p.x + 0.01,
              y2: p.y
            }, options.classNames.point);
          }

          // First point is created, continue with rest
          for (var j = 1; j < normalizedData[i].length; j++) {
            p = Chartist.projectPoint(chartRect, bounds, normalizedData[i], j);
            pathCoordinates.push(p.x, p.y);

            //If we should show points we need to create them now to avoid secondary loop
            // Small offset for Firefox to render squares correctly
            if (options.showPoint) {
              point = seriesGroups[i].elem('line', {
                x1: p.x,
                y1: p.y,
                x2: p.x + 0.01,
                y2: p.y
              }, options.classNames.point);
            }
          }

          if (options.showLine) {
            var svgPathString = 'M' + pathCoordinates[0] + ',' + pathCoordinates[1] + ' ';

            // If smoothed path and path has more than two points then use catmull rom to bezier algorithm
            if (options.lineSmooth && pathCoordinates.length > 4) {

              var cr = Chartist.catmullRom2bezier(pathCoordinates);
              for(var k = 0; k < cr.length; k++) {
                svgPathString += 'C' + cr[k].join();
              }
            } else {
              for(var l = 3; l < pathCoordinates.length; l += 2) {
                svgPathString += 'L ' + pathCoordinates[l - 1] + ',' + pathCoordinates[l];
              }
            }

            seriesGroups[i].elem('path', {
              d: svgPathString
            }, options.classNames.line);
          }
        }
      }

      // Obtain current options based on matching media queries (if responsive options are given)
      // This will also register a listener that is re-creating the chart based on media changes
      currentOptions = Chartist.optionsProvider(defaultOptions, options, responsiveOptions, function (changedOptions) {
        currentOptions = changedOptions;
        createChart(currentOptions);
      });

      // TODO: Currently we need to re-draw the chart on window resize. This is usually very bad and will affect performance.
      // This is done because we can't work with relative coordinates when drawing the chart because SVG Path does not
      // work with relative positions yet. We need to check if we can do a viewBox hack to switch to percentage.
      // See http://mozilla.6506.n7.nabble.com/Specyfing-paths-with-percentages-unit-td247474.html
      // Update: can be done using the above method tested here: http://codepen.io/gionkunz/pen/KDvLj
      // The problem is with the label offsets that can't be converted into percentage and affecting the chart container
      window.addEventListener('resize', function () {
        createChart(currentOptions);
      });

      // Public members
      return {
        version: Chartist.version,
        update: function () {
          createChart(currentOptions);
        }
      };
    };

  }(window, document, Chartist));
  ;/**
   * The bar chart module of Chartist that can be used to draw unipolar or bipolar bar and grouped bar charts.
   *
   * @module Chartist.Bar
   */
  /* global Chartist */
  (function(window, document, Chartist){
    'use strict';

    /**
     * This method creates a new bar chart and returns an object handle with delegations to the internal closure of the bar chart. You can use the returned object to redraw the chart.
     *
     * @memberof Chartist.Bar
     * @param {string|HTMLElement} query A selector query string or directly a DOM element
     * @param {object} data The data object that needs to consist of a labels and a series array
     * @param {object} [options] The options object with options that override the default options. Check the examples for a detailed list.
     * @param {array} [responsiveOptions] Specify an array of responsive option arrays which are a media query and options object pair => [[mediaQueryString, optionsObject],[more...]]
     * @return {object} An object with a version and an update method to manually redraw the chart
     * @function
     *
     * @example
     * // These are the default options of the line chart
     * var options = {
     *   // Options for X-Axis
     *   axisX: {
     *     // The offset of the labels to the chart area
     *     offset: 10,
     *     // If labels should be shown or not
     *     showLabel: true,
     *     // If the axis grid should be drawn or not
     *     showGrid: true,
     *     // Interpolation function that allows you to intercept the value from the axis label
     *     labelInterpolationFnc: function(value){return value;}
     *   },
     *   // Options for Y-Axis
     *   axisY: {
     *     // The offset of the labels to the chart area
     *     offset: 15,
     *     // If labels should be shown or not
     *     showLabel: true,
     *     // If the axis grid should be drawn or not
     *     showGrid: true,
     *     // For the Y-Axis you can set a label alignment property of right or left
     *     labelAlign: 'right',
     *     // Interpolation function that allows you to intercept the value from the axis label
     *     labelInterpolationFnc: function(value){return value;},
     *     // This value specifies the minimum height in pixel of the scale steps
     *     scaleMinSpace: 30
     *   },
     *   // Specify a fixed width for the chart as a string (i.e. '100px' or '50%')
     *   width: undefined,
     *   // Specify a fixed height for the chart as a string (i.e. '100px' or '50%')
     *   height: undefined,
     *   // If the line should be drawn or not
     *   showLine: true,
     *   // If dots should be drawn or not
     *   showPoint: true,
     *   // Specify if the lines should be smoothed (Catmull-Rom-Splines will be used)
     *   lineSmooth: true,
     *   // Overriding the natural low of the chart allows you to zoom in or limit the charts lowest displayed value
     *   low: undefined,
     *   // Overriding the natural high of the chart allows you to zoom in or limit the charts highest displayed value
     *   high: undefined,
     *   // Padding of the chart drawing area to the container element and labels
     *   chartPadding: 5,
     *   // Specify the distance in pixel of bars in a group
     *   seriesBarDistance: 15,
     *   // Override the class names that get used to generate the SVG structure of the chart
     *   classNames: {
     *     chart: 'ct-chart-bar',
     *     label: 'ct-label',
     *     series: 'ct-series',
     *     bar: 'ct-bar',
     *     point: 'ct-point',
     *     grid: 'ct-grid',
     *     vertical: 'ct-vertical',
     *     horizontal: 'ct-horizontal'
     *   }
     * };
     *
     * @example
     * // Create a simple bar chart
     * var data = {
     *   labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
     *   series: [
     *     [5, 2, 4, 2, 0]
     *   ]
     * };
     *
     * // In the global name space Chartist we call the Bar function to initialize a bar chart. As a first parameter we pass in a selector where we would like to get our chart created and as a second parameter we pass our data object.
     * Chartist.Bar('.ct-chart', data);
     *
     * @example
     * // This example creates a bipolar grouped bar chart where the boundaries are limitted to -10 and 10
     * Chartist.Bar('.ct-chart', {
     *   labels: [1, 2, 3, 4, 5, 6, 7],
     *   series: [
     *     [1, 3, 2, -5, -3, 1, -6],
     *     [-5, -2, -4, -1, 2, -3, 1]
     *   ]
     * }, {
     *   seriesBarDistance: 12,
     *   low: -10,
     *   heigh: 10
     * });
     *
     */
    Chartist.Bar = function (query, data, options, responsiveOptions) {

      var defaultOptions = {
          axisX: {
            offset: 10,
            showLabel: true,
            showGrid: true,
            labelInterpolationFnc: Chartist.noop
          },
          axisY: {
            offset: 15,
            showLabel: true,
            showGrid: true,
            labelAlign: 'right',
            labelInterpolationFnc: Chartist.noop,
            scaleMinSpace: 40
          },
          width: undefined,
          height: undefined,
          high: undefined,
          low: undefined,
          chartPadding: 5,
          seriesBarDistance: 15,
          classNames: {
            chart: 'ct-chart-bar',
            label: 'ct-label',
            series: 'ct-series',
            bar: 'ct-bar',
            thin: 'ct-thin',
            thick: 'ct-thick',
            grid: 'ct-grid',
            vertical: 'ct-vertical',
            horizontal: 'ct-horizontal'
          }
        },
        currentOptions,
        svg;

      function createChart(options) {
        var xAxisOffset,
          yAxisOffset,
          seriesGroups = [],
          bounds,
          normalizedData = Chartist.normalizeDataArray(Chartist.getDataArray(data), data.labels.length);

        // Create new svg element
        svg = Chartist.createSvg(query, options.width, options.height, options.classNames.chart);

        // initialize bounds
        bounds = Chartist.getBounds(svg, normalizedData, options, 0);

        xAxisOffset = options.axisX.offset;
        if (options.axisX.showLabel) {
          xAxisOffset += Chartist.calculateLabelOffset(
            svg,
            data.labels,
            [options.classNames.label, options.classNames.horizontal].join(' '),
            options.axisX.labelInterpolationFnc,
            Chartist.getHeight
          );
        }

        yAxisOffset = options.axisY.offset;
        if (options.axisY.showLabel) {
          yAxisOffset += Chartist.calculateLabelOffset(
            svg,
            bounds.values,
            [options.classNames.label, options.classNames.horizontal].join(' '),
            options.axisY.labelInterpolationFnc,
            Chartist.getWidth
          );
        }

        var chartRect = Chartist.createChartRect(svg, options, xAxisOffset, yAxisOffset);
        // Start drawing
        var labels = svg.elem('g'),
          grid = svg.elem('g'),
        // Projected 0 point
          zeroPoint = Chartist.projectPoint(chartRect, bounds, [0], 0);

        Chartist.createXAxis(chartRect, data, grid, labels, options);
        Chartist.createYAxis(chartRect, bounds, grid, labels, yAxisOffset, options);

        // Draw the series
        // initialize series groups
        for (var i = 0; i < data.series.length; i++) {
          // Calculating bi-polar value of index for seriesOffset. For i = 0..4 biPol will be -1.5, -0.5, 0.5, 1.5 etc.
          var biPol = i - (data.series.length - 1) / 2,
          // Half of the period with between vertical grid lines used to position bars
            periodHalfWidth = chartRect.width() / normalizedData[i].length / 2;

          seriesGroups[i] = svg.elem('g');
          // Use series class from series data or if not set generate one
          seriesGroups[i].addClass([
            options.classNames.series,
            (data.series[i].className || options.classNames.series + '-' + Chartist.alphaNumerate(i))
          ].join(' '));

          for(var j = 0; j < normalizedData[i].length; j++) {
            var p = Chartist.projectPoint(chartRect, bounds, normalizedData[i], j),
              bar;

            // Offset to center bar between grid lines and using bi-polar offset for multiple series
            // TODO: Check if we should really be able to add classes to the series. Should be handles with SASS and semantic / specific selectors
            p.x += periodHalfWidth + (biPol * options.seriesBarDistance);

            bar = seriesGroups[i].elem('line', {
              x1: p.x,
              y1: zeroPoint.y,
              x2: p.x,
              y2: p.y
            }, options.classNames.bar + (data.series[i].barClasses ? ' ' + data.series[i].barClasses : ''));
          }
        }
      }

      // Obtain current options based on matching media queries (if responsive options are given)
      // This will also register a listener that is re-creating the chart based on media changes
      currentOptions = Chartist.optionsProvider(defaultOptions, options, responsiveOptions, function (changedOptions) {
        currentOptions = changedOptions;
        createChart(currentOptions);
      });

      // TODO: Currently we need to re-draw the chart on window resize. This is usually very bad and will affect performance.
      // This is done because we can't work with relative coordinates when drawing the chart because SVG Path does not
      // work with relative positions yet. We need to check if we can do a viewBox hack to switch to percentage.
      // See http://mozilla.6506.n7.nabble.com/Specyfing-paths-with-percentages-unit-td247474.html
      // Update: can be done using the above method tested here: http://codepen.io/gionkunz/pen/KDvLj
      // The problem is with the label offsets that can't be converted into percentage and affecting the chart container
      window.addEventListener('resize', function () {
        createChart(currentOptions);
      });

      // Public members
      return {
        version: Chartist.version,
        update: function () {
          createChart(currentOptions);
        }
      };
    };

  }(window, document, Chartist));
  ;/**
   * The pie chart module of Chartist that can be used to draw pie, donut or gauge charts
   *
   * @module Chartist.Pie
   */
  /* global Chartist */
  (function(window, document, Chartist) {
    'use strict';

    /**
     * This method creates a new pie chart and returns an object that can be used to redraw the chart.
     *
     * @memberof Chartist.Pie
     * @param {string|HTMLElement} query A selector query string or directly a DOM element
     * @param {object} data The data object in the pie chart needs to have a series property with a one dimensional data array. The values will be normalized against each other and don't necessarily need to be in percentage.
     * @param {object} [options] The options object with options that override the default options. Check the examples for a detailed list.
     * @param {array} [responsiveOptions] Specify an array of responsive option arrays which are a media query and options object pair => [[mediaQueryString, optionsObject],[more...]]
     * @return {object} An object with a version and an update method to manually redraw the chart
     * @function
     *
     * @example
     * // Default options of the pie chart
     * var defaultOptions = {
     *   // Specify a fixed width for the chart as a string (i.e. '100px' or '50%')
     *   width: undefined,
     *   // Specify a fixed height for the chart as a string (i.e. '100px' or '50%')
     *   height: undefined,
     *   // Padding of the chart drawing area to the container element and labels
     *   chartPadding: 5,
     *   // Override the class names that get used to generate the SVG structure of the chart
     *   classNames: {
     *     chart: 'ct-chart-pie',
     *     series: 'ct-series',
     *     slice: 'ct-slice',
     *     donut: 'ct-donut',
           label: 'ct-label'
     *   },
     *   // The start angle of the pie chart in degrees where 0 points north. A higher value offsets the start angle clockwise.
     *   startAngle: 0,
     *   // An optional total you can specify. By specifying a total value, the sum of the values in the series must be this total in order to draw a full pie. You can use this parameter to draw only parts of a pie or gauge charts.
     *   total: undefined,
     *   // If specified the donut CSS classes will be used and strokes will be drawn instead of pie slices.
     *   donut: false,
     *   // Specify the donut stroke width, currently done in javascript for convenience. May move to CSS styles in the future.
     *   donutWidth: 60,
     *   // If a label should be shown or not
     *   showLabel: true,
     *   // Label position offset from the standard position which is half distance of the radius. This value can be either positive or negative. Positive values will position the label away from the center.
     *   labelOffset: 0,
     *   // An interpolation function for the label value
     *   labelInterpolationFnc: function(value, index) {return value;},
     *   // Label direction can be 'neutral', 'explode' or 'implode'. The labels anchor will be positioned based on those settings as well as the fact if the labels are on the right or left side of the center of the chart. Usually explode is useful when labels are positioned far away from the center.
     *   labelDirection: 'neutral'
     * };
     *
     * @example
     * // Simple pie chart example with four series
     * Chartist.Pie('.ct-chart', {
     *   series: [10, 2, 4, 3]
     * });
     *
     * @example
     * // Drawing a donut chart
     * Chartist.Pie('.ct-chart', {
     *   series: [10, 2, 4, 3]
     * }, {
     *   donut: true
     * });
     *
     * @example
     * // Using donut, startAngle and total to draw a gauge chart
     * Chartist.Pie('.ct-chart', {
     *   series: [20, 10, 30, 40]
     * }, {
     *   donut: true,
     *   donutWidth: 20,
     *   startAngle: 270,
     *   total: 200
     * });
     *
     * @example
     * // Drawing a pie chart with padding and labels that are outside the pie
     * Chartist.Pie('.ct-chart', {
     *   series: [20, 10, 30, 40]
     * }, {
     *   chartPadding: 30,
     *   labelOffset: 50,
     *   labelDirection: 'explode'
     * });
     */
    Chartist.Pie = function (query, data, options, responsiveOptions) {

      var defaultOptions = {
          width: undefined,
          height: undefined,
          chartPadding: 5,
          classNames: {
            chart: 'ct-chart-pie',
            series: 'ct-series',
            slice: 'ct-slice',
            donut: 'ct-donut',
            label: 'ct-label'
          },
          startAngle: 0,
          total: undefined,
          donut: false,
          donutWidth: 60,
          showLabel: true,
          labelOffset: 0,
          labelInterpolationFnc: Chartist.noop,
          labelOverflow: false,
          labelDirection: 'neutral'
        },
        currentOptions,
        svg;

      function determineAnchorPosition(center, label, direction) {
        var toTheRight = label.x > center.x;

        if(toTheRight && direction === 'explode' ||
          !toTheRight && direction === 'implode') {
          return 'start';
        } else if(toTheRight && direction === 'implode' ||
          !toTheRight && direction === 'explode') {
          return 'end';
        } else {
          return 'middle';
        }
      }

      function createChart(options) {
        var seriesGroups = [],
          chartRect,
          radius,
          labelRadius,
          totalDataSum,
          startAngle = options.startAngle,
          dataArray = Chartist.getDataArray(data);

        // Create SVG.js draw
        svg = Chartist.createSvg(query, options.width, options.height, options.classNames.chart);
        // Calculate charting rect
        chartRect = Chartist.createChartRect(svg, options, 0, 0);
        // Get biggest circle radius possible within chartRect
        radius = Math.min(chartRect.width() / 2, chartRect.height() / 2);
        // Calculate total of all series to get reference value or use total reference from optional options
        totalDataSum = options.total || dataArray.reduce(function(previousValue, currentValue) {
          return previousValue + currentValue;
        }, 0);

        // If this is a donut chart we need to adjust our radius to enable strokes to be drawn inside
        // Unfortunately this is not possible with the current SVG Spec
        // See this proposal for more details: http://lists.w3.org/Archives/Public/www-svg/2003Oct/0000.html
        radius -= options.donut ? options.donutWidth / 2  : 0;

        // If a donut chart then the label position is at the radius, if regular pie chart it's half of the radius
        // see https://github.com/gionkunz/chartist-js/issues/21
        labelRadius = options.donut ? radius : radius / 2;
        // Add the offset to the labelRadius where a negative offset means closed to the center of the chart
        labelRadius += options.labelOffset;

        // Calculate end angle based on total sum and current data value and offset with padding
        var center = {
          x: chartRect.x1 + chartRect.width() / 2,
          y: chartRect.y2 + chartRect.height() / 2
        };

        // Draw the series
        // initialize series groups
        for (var i = 0; i < data.series.length; i++) {
          seriesGroups[i] = svg.elem('g');
          // Use series class from series data or if not set generate one
          seriesGroups[i].addClass([
            options.classNames.series,
            (data.series[i].className || options.classNames.series + '-' + Chartist.alphaNumerate(i))
          ].join(' '));

          var endAngle = startAngle + dataArray[i] / totalDataSum * 360;
          // If we need to draw the arc for all 360 degrees we need to add a hack where we close the circle
          // with Z and use 359.99 degrees
          if(endAngle - startAngle === 360) {
            endAngle -= 0.01;
          }

          var start = Chartist.polarToCartesian(center.x, center.y, radius, startAngle - (i === 0 ? 0 : 0.2)),
            end = Chartist.polarToCartesian(center.x, center.y, radius, endAngle),
            arcSweep = endAngle - startAngle <= 180 ? '0' : '1',
            d = [
              // Start at the end point from the cartesian coordinates
              'M', end.x, end.y,
              // Draw arc
              'A', radius, radius, 0, arcSweep, 0, start.x, start.y
            ];

          // If regular pie chart (no donut) we add a line to the center of the circle for completing the pie
          if(options.donut === false) {
            d.push('L', center.x, center.y);
          }

          // Create the SVG path
          // If this is a donut chart we add the donut class, otherwise just a regular slice
          var path = seriesGroups[i].elem('path', {
            d: d.join(' ')
          }, options.classNames.slice + (options.donut ? ' ' + options.classNames.donut : ''));

          // If this is a donut, we add the stroke-width as style attribute
          if(options.donut === true) {
            path.attr({
              'style': 'stroke-width: ' + (+options.donutWidth) + 'px'
            });
          }

          // If we need to show labels we need to add the label for this slice now
          if(options.showLabel) {
            // Position at the labelRadius distance from center and between start and end angle
            var labelPosition = Chartist.polarToCartesian(center.x, center.y, labelRadius, startAngle + (endAngle - startAngle) / 2),
              interpolatedValue = options.labelInterpolationFnc(data.labels ? data.labels[i] : dataArray[i], i);

            seriesGroups[i].elem('text', {
              dx: labelPosition.x,
              dy: labelPosition.y,
              'text-anchor': determineAnchorPosition(center, labelPosition, options.labelDirection),
              text: '' + interpolatedValue
            }, options.classNames.label).text('' + interpolatedValue);
          }

          // Set next startAngle to current endAngle. Use slight offset so there are no transparent hairline issues
          // (except for last slice)
          startAngle = endAngle;
        }
      }

      // Obtain current options based on matching media queries (if responsive options are given)
      // This will also register a listener that is re-creating the chart based on media changes
      currentOptions = Chartist.optionsProvider(defaultOptions, options, responsiveOptions, function (changedOptions) {
        currentOptions = changedOptions;
        createChart(currentOptions);
      });

      // TODO: Currently we need to re-draw the chart on window resize. This is usually very bad and will affect performance.
      // This is done because we can't work with relative coordinates when drawing the chart because SVG Path does not
      // work with relative positions yet. We need to check if we can do a viewBox hack to switch to percentage.
      // See http://mozilla.6506.n7.nabble.com/Specyfing-paths-with-percentages-unit-td247474.html
      // Update: can be done using the above method tested here: http://codepen.io/gionkunz/pen/KDvLj
      // The problem is with the label offsets that can't be converted into percentage and affecting the chart container
      window.addEventListener('resize', function () {
        createChart(currentOptions);
      });

      // Public members
      return {
        version: Chartist.version,
        update: function () {
          createChart(currentOptions);
        }
      };
    };

  }(window, document, Chartist));

  return Chartist;

}));
;/*!
	jQuery Colorbox v1.4.14 - 2013-04-16
	(c) 2013 Jack Moore - jacklmoore.com/colorbox
	license: http://www.opensource.org/licenses/mit-license.php
*/
(function ($, document, window) {
	var
	// Default settings object.
	// See http://jacklmoore.com/colorbox for details.
	defaults = {
		transition: "elastic",
		speed: 300,
		fadeOut: 300,
		width: false,
		initialWidth: "600",
		innerWidth: false,
		maxWidth: false,
		height: false,
		initialHeight: "450",
		innerHeight: false,
		maxHeight: false,
		scalePhotos: true,
		scrolling: true,
		inline: false,
		html: false,
		iframe: false,
		fastIframe: true,
		photo: false,
		href: false,
		title: false,
		rel: false,
		opacity: 0.9,
		preloading: true,
		className: false,
		
		// alternate image paths for high-res displays
		retinaImage: false,
		retinaUrl: false,
		retinaSuffix: '@2x.$1',

		// internationalization
		current: "image {current} of {total}",
		previous: "previous",
		next: "next",
		close: "close",
		xhrError: "This content failed to load.",
		imgError: "This image failed to load.",

		open: false,
		returnFocus: true,
		reposition: true,
		loop: true,
		slideshow: false,
		slideshowAuto: true,
		slideshowSpeed: 2500,
		slideshowStart: "start slideshow",
		slideshowStop: "stop slideshow",
		photoRegex: /\.(gif|png|jp(e|g|eg)|bmp|ico)((#|\?).*)?$/i,

		onOpen: false,
		onLoad: false,
		onComplete: false,
		onCleanup: false,
		onClosed: false,
		overlayClose: true,
		escKey: true,
		arrowKey: true,
		top: false,
		bottom: false,
		left: false,
		right: false,
		fixed: false,
		data: undefined
	},
	
	// Abstracting the HTML and event identifiers for easy rebranding
	colorbox = 'colorbox',
	prefix = 'cbox',
	boxElement = prefix + 'Element',
	
	// Events
	event_open = prefix + '_open',
	event_load = prefix + '_load',
	event_complete = prefix + '_complete',
	event_cleanup = prefix + '_cleanup',
	event_closed = prefix + '_closed',
	event_purge = prefix + '_purge',

	// Cached jQuery Object Variables
	$overlay,
	$box,
	$wrap,
	$content,
	$topBorder,
	$leftBorder,
	$rightBorder,
	$bottomBorder,
	$related,
	$window,
	$loaded,
	$loadingBay,
	$loadingOverlay,
	$title,
	$current,
	$slideshow,
	$next,
	$prev,
	$close,
	$groupControls,
	$events = $('<a/>'),
	
	// Variables for cached values or use across multiple functions
	settings,
	interfaceHeight,
	interfaceWidth,
	loadedHeight,
	loadedWidth,
	element,
	index,
	photo,
	open,
	active,
	closing,
	loadingTimer,
	publicMethod,
	div = "div",
	className,
	requests = 0,
	init;

	// ****************
	// HELPER FUNCTIONS
	// ****************
	
	// Convience function for creating new jQuery objects
	function $tag(tag, id, css) {
		var element = document.createElement(tag);

		if (id) {
			element.id = prefix + id;
		}

		if (css) {
			element.style.cssText = css;
		}

		return $(element);
	}
	
	// Get the window height using innerHeight when available to avoid an issue with iOS
	// http://bugs.jquery.com/ticket/6724
	function winheight() {
		return window.innerHeight ? window.innerHeight : $(window).height();
	}

	// Determine the next and previous members in a group.
	function getIndex(increment) {
		var
		max = $related.length,
		newIndex = (index + increment) % max;
		
		return (newIndex < 0) ? max + newIndex : newIndex;
	}

	// Convert '%' and 'px' values to integers
	function setSize(size, dimension) {
		return Math.round((/%/.test(size) ? ((dimension === 'x' ? $window.width() : winheight()) / 100) : 1) * parseInt(size, 10));
	}
	
	// Checks an href to see if it is a photo.
	// There is a force photo option (photo: true) for hrefs that cannot be matched by the regex.
	function isImage(settings, url) {
		return settings.photo || settings.photoRegex.test(url);
	}

	function retinaUrl(settings, url) {
		return settings.retinaUrl && window.devicePixelRatio > 1 ? url.replace(settings.photoRegex, settings.retinaSuffix) : url;
	}

	function trapFocus(e) {
		if ('contains' in $box[0] && !$box[0].contains(e.target)) {
			e.stopPropagation();
			$box.focus();
		}
	}

	// Assigns function results to their respective properties
	function makeSettings() {
		var i,
			data = $.data(element, colorbox);
		
		if (data == null) {
			settings = $.extend({}, defaults);
			if (console && console.log) {
				console.log('Error: cboxElement missing settings object');
			}
		} else {
			settings = $.extend({}, data);
		}
		
		for (i in settings) {
			if ($.isFunction(settings[i]) && i.slice(0, 2) !== 'on') { // checks to make sure the function isn't one of the callbacks, they will be handled at the appropriate time.
				settings[i] = settings[i].call(element);
			}
		}
		
		settings.rel = settings.rel || element.rel || $(element).data('rel') || 'nofollow';
		settings.href = settings.href || $(element).attr('href');
		settings.title = settings.title || element.title;
		
		if (typeof settings.href === "string") {
			settings.href = $.trim(settings.href);
		}
	}

	function trigger(event, callback) {
		// for external use
		$(document).trigger(event);

		// for internal use
		$events.trigger(event);

		if ($.isFunction(callback)) {
			callback.call(element);
		}
	}

	// Slideshow functionality
	function slideshow() {
		var
		timeOut,
		className = prefix + "Slideshow_",
		click = "click." + prefix,
		clear,
		set,
		start,
		stop;
		
		if (settings.slideshow && $related[1]) {
			clear = function () {
				clearTimeout(timeOut);
			};

			set = function () {
				if (settings.loop || $related[index + 1]) {
					timeOut = setTimeout(publicMethod.next, settings.slideshowSpeed);
				}
			};

			start = function () {
				$slideshow
					.html(settings.slideshowStop)
					.unbind(click)
					.one(click, stop);

				$events
					.bind(event_complete, set)
					.bind(event_load, clear)
					.bind(event_cleanup, stop);

				$box.removeClass(className + "off").addClass(className + "on");
			};
			
			stop = function () {
				clear();
				
				$events
					.unbind(event_complete, set)
					.unbind(event_load, clear)
					.unbind(event_cleanup, stop);
				
				$slideshow
					.html(settings.slideshowStart)
					.unbind(click)
					.one(click, function () {
						publicMethod.next();
						start();
					});

				$box.removeClass(className + "on").addClass(className + "off");
			};
			
			if (settings.slideshowAuto) {
				start();
			} else {
				stop();
			}
		} else {
			$box.removeClass(className + "off " + className + "on");
		}
	}

	function launch(target) {
		if (!closing) {
			
			element = target;
			
			makeSettings();
			
			$related = $(element);
			
			index = 0;
			
			if (settings.rel !== 'nofollow') {
				$related = $('.' + boxElement).filter(function () {
					var data = $.data(this, colorbox),
						relRelated;

					if (data) {
						relRelated =  $(this).data('rel') || data.rel || this.rel;
					}
					
					return (relRelated === settings.rel);
				});
				index = $related.index(element);
				
				// Check direct calls to Colorbox.
				if (index === -1) {
					$related = $related.add(element);
					index = $related.length - 1;
				}
			}
			
			$overlay.css({
				opacity: parseFloat(settings.opacity),
				cursor: settings.overlayClose ? "pointer" : "auto",
				visibility: 'visible'
			}).show();
			

			if (className) {
				$box.add($overlay).removeClass(className);
			}
			if (settings.className) {
				$box.add($overlay).addClass(settings.className);
			}
			className = settings.className;

			$close.html(settings.close).show();

			if (!open) {
				open = active = true; // Prevents the page-change action from queuing up if the visitor holds down the left or right keys.
				
				// Show colorbox so the sizes can be calculated in older versions of jQuery
				$box.css({visibility:'hidden', display:'block'});
				
				$loaded = $tag(div, 'LoadedContent', 'width:0; height:0; overflow:hidden').appendTo($content);

				// Cache values needed for size calculations
				interfaceHeight = $topBorder.height() + $bottomBorder.height() + $content.outerHeight(true) - $content.height();
				interfaceWidth = $leftBorder.width() + $rightBorder.width() + $content.outerWidth(true) - $content.width();
				loadedHeight = $loaded.outerHeight(true);
				loadedWidth = $loaded.outerWidth(true);
				
				
				// Opens inital empty Colorbox prior to content being loaded.
				settings.w = setSize(settings.initialWidth, 'x');
				settings.h = setSize(settings.initialHeight, 'y');
				publicMethod.position();

				slideshow();

				trigger(event_open, settings.onOpen);
				
				$groupControls.add($title).hide();

				$box.focus();
				
				// Confine focus to the modal
				// Uses event capturing that is not supported in IE8-
				if (document.addEventListener) {

					document.addEventListener('focus', trapFocus, true);
					
					$events.one(event_closed, function () {
						document.removeEventListener('focus', trapFocus, true);
					});
				}

				// Return focus on closing
				if (settings.returnFocus) {
					$events.one(event_closed, function () {
						$(element).focus();
					});
				}
			}
			
			load();
		}
	}

	// Colorbox's markup needs to be added to the DOM prior to being called
	// so that the browser will go ahead and load the CSS background images.
	function appendHTML() {
		if (!$box && document.body) {
			init = false;
			$window = $(window);
			$box = $tag(div).attr({
				id: colorbox,
				'class': $.support.opacity === false ? prefix + 'IE' : '', // class for optional IE8 & lower targeted CSS.
				role: 'dialog',
				tabindex: '-1'
			}).hide();
			$overlay = $tag(div, "Overlay").hide();
			$loadingOverlay = $tag(div, "LoadingOverlay").add($tag(div, "LoadingGraphic"));
			$wrap = $tag(div, "Wrapper");
			$content = $tag(div, "Content").append(
				$title = $tag(div, "Title"),
				$current = $tag(div, "Current"),
				$prev = $('<button type="button"/>').attr({id:prefix+'Previous'}),
				$next = $('<button type="button"/>').attr({id:prefix+'Next'}),
				$slideshow = $tag('button', "Slideshow"),
				$loadingOverlay,
				$close = $('<button type="button"/>').attr({id:prefix+'Close'})
			);
			
			$wrap.append( // The 3x3 Grid that makes up Colorbox
				$tag(div).append(
					$tag(div, "TopLeft"),
					$topBorder = $tag(div, "TopCenter"),
					$tag(div, "TopRight")
				),
				$tag(div, false, 'clear:left').append(
					$leftBorder = $tag(div, "MiddleLeft"),
					$content,
					$rightBorder = $tag(div, "MiddleRight")
				),
				$tag(div, false, 'clear:left').append(
					$tag(div, "BottomLeft"),
					$bottomBorder = $tag(div, "BottomCenter"),
					$tag(div, "BottomRight")
				)
			).find('div div').css({'float': 'left'});
			
			$loadingBay = $tag(div, false, 'position:absolute; width:9999px; visibility:hidden; display:none');
			
			$groupControls = $next.add($prev).add($current).add($slideshow);

			$(document.body).append($overlay, $box.append($wrap, $loadingBay));
		}
	}

	// Add Colorbox's event bindings
	function addBindings() {
		function clickHandler(e) {
			// ignore non-left-mouse-clicks and clicks modified with ctrl / command, shift, or alt.
			// See: http://jacklmoore.com/notes/click-events/
			if (!(e.which > 1 || e.shiftKey || e.altKey || e.metaKey || e.control)) {
				e.preventDefault();
				launch(this);
			}
		}

		if ($box) {
			if (!init) {
				init = true;

				// Anonymous functions here keep the public method from being cached, thereby allowing them to be redefined on the fly.
				$next.click(function () {
					publicMethod.next();
				});
				$prev.click(function () {
					publicMethod.prev();
				});
				$close.click(function () {
					publicMethod.close();
				});
				$overlay.click(function () {
					if (settings.overlayClose) {
						publicMethod.close();
					}
				});
				
				// Key Bindings
				$(document).bind('keydown.' + prefix, function (e) {
					var key = e.keyCode;
					if (open && settings.escKey && key === 27) {
						e.preventDefault();
						publicMethod.close();
					}
					if (open && settings.arrowKey && $related[1] && !e.altKey) {
						if (key === 37) {
							e.preventDefault();
							$prev.click();
						} else if (key === 39) {
							e.preventDefault();
							$next.click();
						}
					}
				});

				if ($.isFunction($.fn.on)) {
					// For jQuery 1.7+
					$(document).on('click.'+prefix, '.'+boxElement, clickHandler);
				} else {
					// For jQuery 1.3.x -> 1.6.x
					// This code is never reached in jQuery 1.9, so do not contact me about 'live' being removed.
					// This is not here for jQuery 1.9, it's here for legacy users.
					$('.'+boxElement).live('click.'+prefix, clickHandler);
				}
			}
			return true;
		}
		return false;
	}

	// Don't do anything if Colorbox already exists.
	if ($.colorbox) {
		return;
	}

	// Append the HTML when the DOM loads
	$(appendHTML);


	// ****************
	// PUBLIC FUNCTIONS
	// Usage format: $.colorbox.close();
	// Usage from within an iframe: parent.jQuery.colorbox.close();
	// ****************
	
	publicMethod = $.fn[colorbox] = $[colorbox] = function (options, callback) {
		var $this = this;
		
		options = options || {};
		
		appendHTML();

		if (addBindings()) {
			if ($.isFunction($this)) { // assume a call to $.colorbox
				$this = $('<a/>');
				options.open = true;
			} else if (!$this[0]) { // colorbox being applied to empty collection
				return $this;
			}
			
			if (callback) {
				options.onComplete = callback;
			}
			
			$this.each(function () {
				$.data(this, colorbox, $.extend({}, $.data(this, colorbox) || defaults, options));
			}).addClass(boxElement);
			
			if (($.isFunction(options.open) && options.open.call($this)) || options.open) {
				launch($this[0]);
			}
		}
		
		return $this;
	};

	publicMethod.position = function (speed, loadedCallback) {
		var
		css,
		top = 0,
		left = 0,
		offset = $box.offset(),
		scrollTop,
		scrollLeft;
		
		$window.unbind('resize.' + prefix);

		// remove the modal so that it doesn't influence the document width/height
		$box.css({top: -9e4, left: -9e4});

		scrollTop = $window.scrollTop();
		scrollLeft = $window.scrollLeft();

		if (settings.fixed) {
			offset.top -= scrollTop;
			offset.left -= scrollLeft;
			$box.css({position: 'fixed'});
		} else {
			top = scrollTop;
			left = scrollLeft;
			$box.css({position: 'absolute'});
		}

		// keeps the top and left positions within the browser's viewport.
		if (settings.right !== false) {
			left += Math.max($window.width() - settings.w - loadedWidth - interfaceWidth - setSize(settings.right, 'x'), 0);
		} else if (settings.left !== false) {
			left += setSize(settings.left, 'x');
		} else {
			left += Math.round(Math.max($window.width() - settings.w - loadedWidth - interfaceWidth, 0) / 2);
		}
		
		if (settings.bottom !== false) {
			top += Math.max(winheight() - settings.h - loadedHeight - interfaceHeight - setSize(settings.bottom, 'y'), 0);
		} else if (settings.top !== false) {
			top += setSize(settings.top, 'y');
		} else {
			top += Math.round(Math.max(winheight() - settings.h - loadedHeight - interfaceHeight, 0) / 2);
		}

		$box.css({top: offset.top, left: offset.left, visibility:'visible'});

		// setting the speed to 0 to reduce the delay between same-sized content.
		speed = ($box.width() === settings.w + loadedWidth && $box.height() === settings.h + loadedHeight) ? 0 : speed || 0;
		
		// this gives the wrapper plenty of breathing room so it's floated contents can move around smoothly,
		// but it has to be shrank down around the size of div#colorbox when it's done.  If not,
		// it can invoke an obscure IE bug when using iframes.
		$wrap[0].style.width = $wrap[0].style.height = "9999px";
		
		function modalDimensions(that) {
			$topBorder[0].style.width = $bottomBorder[0].style.width = $content[0].style.width = (parseInt(that.style.width,10) - interfaceWidth)+'px';
			$content[0].style.height = $leftBorder[0].style.height = $rightBorder[0].style.height = (parseInt(that.style.height,10) - interfaceHeight)+'px';
		}

		css = {width: settings.w + loadedWidth + interfaceWidth, height: settings.h + loadedHeight + interfaceHeight, top: top, left: left};

		if(speed===0){ // temporary workaround to side-step jQuery-UI 1.8 bug (http://bugs.jquery.com/ticket/12273)
			$box.css(css);
		}
		$box.dequeue().animate(css, {
			duration: speed,
			complete: function () {
				modalDimensions(this);
				
				active = false;
				
				// shrink the wrapper down to exactly the size of colorbox to avoid a bug in IE's iframe implementation.
				$wrap[0].style.width = (settings.w + loadedWidth + interfaceWidth) + "px";
				$wrap[0].style.height = (settings.h + loadedHeight + interfaceHeight) + "px";
				
				if (settings.reposition) {
					setTimeout(function () {  // small delay before binding onresize due to an IE8 bug.
						$window.bind('resize.' + prefix, publicMethod.position);
					}, 1);
				}

				if (loadedCallback) {
					loadedCallback();
				}
			},
			step: function () {
				modalDimensions(this);
			}
		});
	};

	publicMethod.resize = function (options) {
		if (open) {
			options = options || {};
			
			if (options.width) {
				settings.w = setSize(options.width, 'x') - loadedWidth - interfaceWidth;
			}
			if (options.innerWidth) {
				settings.w = setSize(options.innerWidth, 'x');
			}
			$loaded.css({width: settings.w});
			
			if (options.height) {
				settings.h = setSize(options.height, 'y') - loadedHeight - interfaceHeight;
			}
			if (options.innerHeight) {
				settings.h = setSize(options.innerHeight, 'y');
			}
			if (!options.innerHeight && !options.height) {
				$loaded.css({height: "auto"});
				settings.h = $loaded.height();
			}
			$loaded.css({height: settings.h});
			
			publicMethod.position(settings.transition === "none" ? 0 : settings.speed);
		}
	};

	publicMethod.prep = function (object) {
		if (!open) {
			return;
		}
		
		var callback, speed = settings.transition === "none" ? 0 : settings.speed;

		$loaded.empty().remove(); // Using empty first may prevent some IE7 issues.

		$loaded = $tag(div, 'LoadedContent').append(object);
		
		function getWidth() {
			settings.w = settings.w || $loaded.width();
			settings.w = settings.mw && settings.mw < settings.w ? settings.mw : settings.w;
			return settings.w;
		}
		function getHeight() {
			settings.h = settings.h || $loaded.height();
			settings.h = settings.mh && settings.mh < settings.h ? settings.mh : settings.h;
			return settings.h;
		}
		
		$loaded.hide()
		.appendTo($loadingBay.show())// content has to be appended to the DOM for accurate size calculations.
		.css({width: getWidth(), overflow: settings.scrolling ? 'auto' : 'hidden'})
		.css({height: getHeight()})// sets the height independently from the width in case the new width influences the value of height.
		.prependTo($content);
		
		$loadingBay.hide();
		
		// floating the IMG removes the bottom line-height and fixed a problem where IE miscalculates the width of the parent element as 100% of the document width.
		
		$(photo).css({'float': 'none'});

		callback = function () {
			var total = $related.length,
				iframe,
				frameBorder = 'frameBorder',
				allowTransparency = 'allowTransparency',
				complete;
			
			if (!open) {
				return;
			}
			
			function removeFilter() { // Needed for IE7 & IE8 in versions of jQuery prior to 1.7.2
				if ($.support.opacity === false) {
					$box[0].style.removeAttribute('filter');
				}
			}
			
			complete = function () {
				clearTimeout(loadingTimer);
				$loadingOverlay.hide();
				trigger(event_complete, settings.onComplete);
			};

			
			$title.html(settings.title).add($loaded).show();
			
			if (total > 1) { // handle grouping
				if (typeof settings.current === "string") {
					$current.html(settings.current.replace('{current}', index + 1).replace('{total}', total)).show();
				}
				
				$next[(settings.loop || index < total - 1) ? "show" : "hide"]().html(settings.next);
				$prev[(settings.loop || index) ? "show" : "hide"]().html(settings.previous);
				
				if (settings.slideshow) {
					$slideshow.show();
				}
				
				// Preloads images within a rel group
				if (settings.preloading) {
					$.each([getIndex(-1), getIndex(1)], function(){
						var src,
							img,
							i = $related[this],
							data = $.data(i, colorbox);

						if (data && data.href) {
							src = data.href;
							if ($.isFunction(src)) {
								src = src.call(i);
							}
						} else {
							src = $(i).attr('href');
						}

						if (src && isImage(data, src)) {
							src = retinaUrl(data, src);
							img = new Image();
							img.src = src;
						}
					});
				}
			} else {
				$groupControls.hide();
			}
			
			if (settings.iframe) {
				iframe = $tag('iframe')[0];
				
				if (frameBorder in iframe) {
					iframe[frameBorder] = 0;
				}
				
				if (allowTransparency in iframe) {
					iframe[allowTransparency] = "true";
				}

				if (!settings.scrolling) {
					iframe.scrolling = "no";
				}
				
				$(iframe)
					.attr({
						src: settings.href,
						name: (new Date()).getTime(), // give the iframe a unique name to prevent caching
						'class': prefix + 'Iframe',
						allowFullScreen : true, // allow HTML5 video to go fullscreen
						webkitAllowFullScreen : true,
						mozallowfullscreen : true
					})
					.one('load', complete)
					.appendTo($loaded);
				
				$events.one(event_purge, function () {
					iframe.src = "//about:blank";
				});

				if (settings.fastIframe) {
					$(iframe).trigger('load');
				}
			} else {
				complete();
			}
			
			if (settings.transition === 'fade') {
				$box.fadeTo(speed, 1, removeFilter);
			} else {
				removeFilter();
			}
		};
		
		if (settings.transition === 'fade') {
			$box.fadeTo(speed, 0, function () {
				publicMethod.position(0, callback);
			});
		} else {
			publicMethod.position(speed, callback);
		}
	};

	function load () {
		var href, setResize, prep = publicMethod.prep, $inline, request = ++requests;
		
		active = true;
		
		photo = false;
		
		element = $related[index];
		
		makeSettings();
		
		trigger(event_purge);
		
		trigger(event_load, settings.onLoad);
		
		settings.h = settings.height ?
				setSize(settings.height, 'y') - loadedHeight - interfaceHeight :
				settings.innerHeight && setSize(settings.innerHeight, 'y');
		
		settings.w = settings.width ?
				setSize(settings.width, 'x') - loadedWidth - interfaceWidth :
				settings.innerWidth && setSize(settings.innerWidth, 'x');
		
		// Sets the minimum dimensions for use in image scaling
		settings.mw = settings.w;
		settings.mh = settings.h;
		
		// Re-evaluate the minimum width and height based on maxWidth and maxHeight values.
		// If the width or height exceed the maxWidth or maxHeight, use the maximum values instead.
		if (settings.maxWidth) {
			settings.mw = setSize(settings.maxWidth, 'x') - loadedWidth - interfaceWidth;
			settings.mw = settings.w && settings.w < settings.mw ? settings.w : settings.mw;
		}
		if (settings.maxHeight) {
			settings.mh = setSize(settings.maxHeight, 'y') - loadedHeight - interfaceHeight;
			settings.mh = settings.h && settings.h < settings.mh ? settings.h : settings.mh;
		}
		
		href = settings.href;
		
		loadingTimer = setTimeout(function () {
			$loadingOverlay.show();
		}, 100);
		
		if (settings.inline) {
			// Inserts an empty placeholder where inline content is being pulled from.
			// An event is bound to put inline content back when Colorbox closes or loads new content.
			$inline = $tag(div).hide().insertBefore($(href)[0]);

			$events.one(event_purge, function () {
				$inline.replaceWith($loaded.children());
			});

			prep($(href));
		} else if (settings.iframe) {
			// IFrame element won't be added to the DOM until it is ready to be displayed,
			// to avoid problems with DOM-ready JS that might be trying to run in that iframe.
			prep(" ");
		} else if (settings.html) {
			prep(settings.html);
		} else if (isImage(settings, href)) {

			href = retinaUrl(settings, href);

			$(photo = new Image())
			.addClass(prefix + 'Photo')
			.bind('error',function () {
				settings.title = false;
				prep($tag(div, 'Error').html(settings.imgError));
			})
			.one('load', function () {
				var percent;

				if (request !== requests) {
					return;
				}

				photo.alt = $(element).attr('alt') || $(element).attr('data-alt') || '';

				if (settings.retinaImage && window.devicePixelRatio > 1) {
					photo.height = photo.height / window.devicePixelRatio;
					photo.width = photo.width / window.devicePixelRatio;
				}

				if (settings.scalePhotos) {
					setResize = function () {
						photo.height -= photo.height * percent;
						photo.width -= photo.width * percent;
					};
					if (settings.mw && photo.width > settings.mw) {
						percent = (photo.width - settings.mw) / photo.width;
						setResize();
					}
					if (settings.mh && photo.height > settings.mh) {
						percent = (photo.height - settings.mh) / photo.height;
						setResize();
					}
				}
				
				if (settings.h) {
					photo.style.marginTop = Math.max(settings.mh - photo.height, 0) / 2 + 'px';
				}
				
				if ($related[1] && (settings.loop || $related[index + 1])) {
					photo.style.cursor = 'pointer';
					photo.onclick = function () {
						publicMethod.next();
					};
				}

				photo.style.width = photo.width + 'px';
				photo.style.height = photo.height + 'px';

				setTimeout(function () { // A pause because Chrome will sometimes report a 0 by 0 size otherwise.
					prep(photo);
				}, 1);
			});
			
			setTimeout(function () { // A pause because Opera 10.6+ will sometimes not run the onload function otherwise.
				photo.src = href;
			}, 1);
		} else if (href) {
			$loadingBay.load(href, settings.data, function (data, status) {
				if (request === requests) {
					prep(status === 'error' ? $tag(div, 'Error').html(settings.xhrError) : $(this).contents());
				}
			});
		}
	}
		
	// Navigates to the next page/image in a set.
	publicMethod.next = function () {
		if (!active && $related[1] && (settings.loop || $related[index + 1])) {
			index = getIndex(1);
			launch($related[index]);
		}
	};
	
	publicMethod.prev = function () {
		if (!active && $related[1] && (settings.loop || index)) {
			index = getIndex(-1);
			launch($related[index]);
		}
	};

	// Note: to use this within an iframe use the following format: parent.jQuery.colorbox.close();
	publicMethod.close = function () {
		if (open && !closing) {
			
			closing = true;
			
			open = false;
			
			trigger(event_cleanup, settings.onCleanup);
			
			$window.unbind('.' + prefix);
			
			$overlay.fadeTo(settings.fadeOut || 0, 0);
			
			$box.stop().fadeTo(settings.fadeOut || 0, 0, function () {
			
				$box.add($overlay).css({'opacity': 1, cursor: 'auto'}).hide();
				
				trigger(event_purge);
				
				$loaded.empty().remove(); // Using empty first may prevent some IE7 issues.
				
				setTimeout(function () {
					closing = false;
					trigger(event_closed, settings.onClosed);
				}, 1);
			});
		}
	};

	// Removes changes Colorbox made to the document, but does not remove the plugin.
	publicMethod.remove = function () {
		if (!$box) { return; }

		$box.stop();
		$.colorbox.close();
		$box.stop().remove();
		$overlay.remove();
		closing = false;
		$box = null;
		$('.' + boxElement)
			.removeData(colorbox)
			.removeClass(boxElement);

		$(document).unbind('click.'+prefix);
	};

	// A method for fetching the current element Colorbox is referencing.
	// returns a jQuery object.
	publicMethod.element = function () {
		return $(element);
	};

	publicMethod.settings = defaults;

}(jQuery, document, window));



/**
 *	beancounterPopup
 *	@uses	colorbox
 *	@return	void
 */
function beancounterPopup(){

	if(typeof colorbox != 'function'){
		//return;
	}

	$('a.popup').colorbox({
		transition: 'elastic'
	});
	
	$('a.add_new_link').colorbox({
		transition: 'elastic', 
		iframe: true, 
		innerWidth: '695px', 
		innerHeight: '80%',
		iframe: true
	});
	
	
	// close links
	$('#Content').on('click', 'a.close-popup', function(e){
		$.fn.colorbox.close();
		e.preventDefault();
	});

}

beancounterPopup();;$(document).ready(function(){	
	initDatePicker();
	
});

function initDatePicker(){
	$('fieldset.date').append('<a href="#" class="date-pick button date" title="Choose date"></a>');	
	
	$('fieldset.date').each(function(){
		updateDate($(this));
	});
}

function updateDate(fieldset){
	
	var today = new Date(),
		startYear = $(fieldset).children('.year').children('option:nth-child(1)').val(),
		totalYearOptions = ($(fieldset).children('.year').children('option').length),
		endYear = $(fieldset).children('.year').children('option:nth-child('+(totalYearOptions)+')').val();

	// initialise the "Select date" link
	$(fieldset).children('.date-pick')
		.datePicker(
			// associate the link with a date picker
			{
				
				createButton:false,
				startDate:'01/01/' + startYear,
				endDate:'31/12/' + endYear
			}
		).bind(
			// when the link is clicked display the date picker
			'click',
			function()
			{
				updateSelects($(this).dpGetSelected()[0]);
				$(this).dpDisplay();
				return false;
			}
		).bind(
			// when a date is selected update the SELECTs
			'dateSelected',
			function(e, selectedDate, $td, state){
				updateSelects(selectedDate);
			}
		).bind(
			'dpClosed',
			function(e, selected){
				updateSelects(selected[0]);
			}
		);
		
	var updateSelects = function (selectedDate){
	
		selectedDate = new Date(selectedDate);
		var	d = selectedDate.getDate(),
			m = selectedDate.getMonth(),
			y = selectedDate.getFullYear();
		
		($(fieldset).children('.day')[0]).selectedIndex = d - 1;
		($(fieldset).children('.month')[0]).selectedIndex = m;
		($(fieldset).children('.year'))[0].selectedIndex = (y - startYear);
		
	}
	// listen for when the selects are changed and update the picker
	$(fieldset).children('.day, .month, .year')
		.bind(
			'change',
			function(){
				var d = new Date(
							$(fieldset).children('.year').val(),
							$(fieldset).children('.month').val()-1,
							$(fieldset).children('.day').val()
						);
				$(fieldset).children('.date-pick').dpSetSelected(d.asString());
			}
		);
	
	// default the position of the selects to today	
	
	
	// and update the datePicker to reflect it...
	$(fieldset).children('.day').trigger('change');

};/**
 *	showHideExtras JavaScript
 *	take field which are not required and hide them
 *	but allow user to click and show them
 *
 *	showHideExtras
 *	document ready
 *
 */
 
 /* showHideExtras */
 function showHideExtras(){
 
 	var link = '<p class="instructions"><a href="#" id="show_hide_extras_link">Add/edit extra details</a></p>'; //Create show/hide link after title input
	
	/* hide fields */
	$('div.field').hide();
	
	/* show required fields */
	$('label.required').parent('div.field').show(); 	
 	
 	$('#title').parent().append(link);
 	
 	/* assign clickability to above link */
 	$('#show_hide_extras_link').click(
		function(){
			/* show fields - update text */
			$('div.field').show();
			$(this).parent().remove();
		}	
 	)
 	
 }
 
 
 /* document ready */
 $(document).ready(function(){
 	showHideExtras();
 });;/**
 *	Bean Counter JavaScript
 *	Hide time
 *
 *	@library: jQuery
 *
 *	@author		philthompson.co.uk
 *	@copyright 	2008 philthompson.co.uk
 *	@edited 	24/04/2013
 */
 
 
/* Hide time elements */
$(document).ready(function(){
	hideTime();	
});
 
function hideTime(){
 	$('select.time').each(function(){
		// Grab id/name and make some hidden fields set to zero
		var fieldName = $(this).attr('name');
		$(this).parent().append('<input type="hidden" name="' + fieldName + '" value="00" />');
		$(this).remove();
	});
}
;/**
 *	Bean Counter JavaScript
 *	Projects
 *	
 *	@author		philthompson.co.uk
 *	@since		01/01/2008
 *	@version 	1.0
 *	@package	jQuery
 *
 */


/**
 *	generateInvoiceNumber
 *	create an invoice number via AJAX (see /ajax/invoice_number.php)
 */
function generateInvoiceNumber(){

	var $refNumber = $('#internal_reference_number');
	
	if($refNumber.length === 0){
		return;
	}
	
	// Add button
	$refNumber.addClass('int').after('<a href="#" class="refresh button" id="invoice_button">Generate</a>');
	
	// Get the invoice number with AJAX when user clicks the generate button
	$('#invoice_button').click(function(e){
		
		$(this).load('/ajax/invoice_number.php?mode=ajax', '', function(){
			$(this).hide();
			$refNumber.val($(this).html());
		});
		
		e.preventDefault();
	});
}


$(document).ready(function(){
	generateInvoiceNumber();	
});;/**
 *
 *	Bean Counter JavaScript
 *	Project dates
 *
 *	@library: jQuery
 *
 *	@copyright 2008 Phil Thompson
 *	@edited 24/04/2013 Phil Thompson
 */
 
 
/* Hide/show date element depending on the project stage elements */
$(document).ready(function(){
	initProjectDates(); 	
});
 
function initProjectDates(){

	project_dates();
 	
 	$('#project_stage').change(function(){
 		project_dates();
 	});
}
 
/**
 *	project_dates()
 *	hide date fields unless they are required
 */
function project_dates(){
 
 	var projectStage = $('#project_stage').val();
 	
 	$('fieldset.date').hide();

 	if(projectStage.length > 0){ 
 		
	 	$('#payment_required_day').parent().hide();
	 	
	 	if(projectStage > 1){	
	 		$('#payment_expected_day').parent().show();	 	
	 	}
	 	
	 	/* STAGE: Invoiced */
	 	if(projectStage === 3){
	 		$('#payment_required_day').parent().show();
	 	}
	 	
 	}
 	
}
;/**
 *	Bean Counter JavaScript
 *	VAT
 *	
 *	@author		philthompson.co.uk
 *	@since		05/10/2009
 *	@version 	1.0
 *	@package	jQuery
 *
 */


(function ($) {

    beancounterTax = {
    
    	config: {
    		
    	},

        // Run misc/generic functionality and call specific functions
        onReady: function () {
        
        	var self = this,
        		vatOptionHTML = '<div class="field"><input type="checkbox" name="ignore" class="checkbox" id="vat-checkbox" value="Y" /><label for="vat-checkbox" class="checklabel">Calculate VAT from price</label></div>',
				$tax = $('#vat'),
				$taxCheckbox = {},
				taxRate = $('#vat_rate').val(),
				$price = $('#price');
		
			// add tax checkbox
			if($tax.length === 0){
				return;
			}
			
			$tax.parent().before(vatOptionHTML);
			$taxCheckbox = $('#vat-checkbox');
			
			if($tax.val().length === 0){
				$tax.parent().hide();
			}
			
			$taxCheckbox.click(function(){
				if($taxCheckbox.is(':checked') === true){
					$tax.parent().show();
					$tax.val(calculateVAT($price.val(), taxRate));
				} else{
					$tax.val('');
					$tax.parent().hide();
				}
			});
			
			
			// Update the VAT (sales tax) values as and when the price field is edited
			$price.keyup(function(){
				
				if($taxCheckbox.is(':checked') === true){
					console.log('key up!' + $price.val());
					$tax.val(calculateVAT($price.val(), taxRate));
				}
			});
        	
        	
        },
        
        
        
        // Take Tax value (e.g. sales tax or VAT in the UK) (from hidden field) and create sales tax value from price field
        calculateTax : function(){
        	
        	var	rate = $("#vat_rate").val(),
				price = $("#price").val(),
				taxRate = (price * (rate / 100));
			
			
			$("#vat").val(taxRate);
        	
        }
        
	}
      
}(jQuery));


$(document).ready(function(){
	beancounterTax.onReady();
});


