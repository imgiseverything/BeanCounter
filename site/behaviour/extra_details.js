/**
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
	
	/* hide fields */
	$('div.field').hide();
	
	/* show required fields */
	$('label.required').parent('div.field').show();
 
 	/* Create show/hide link after title input */
 	var link = '<p class="instructions"><a href="#" id="show_hide_extras_link">Add/edit extra details</a></p>';
 	
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
 });