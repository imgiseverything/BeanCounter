/**
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
		$hiddenVatRate = $('#hidden_vat_rate');

	if($vatRate.length === 0 || $hiddenVatRate.length === 0){
		return;
	}
	
	if($hiddenVatRate.val().length === 0){
		return;
	}

	$('#charge_vat').click(function(e){
		e.preventDefualt();
		if($(this).is(':checked')){
			$vatRate.val($hiddenVatRate.val());
		} else{
			$vatRate.val('0');
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
});