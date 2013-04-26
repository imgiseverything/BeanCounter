/**
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
