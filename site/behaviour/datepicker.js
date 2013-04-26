$(document).ready(function(){	
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

}