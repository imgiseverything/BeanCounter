/**
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

	 	projectStage = parseInt(projectStage, 10)

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
