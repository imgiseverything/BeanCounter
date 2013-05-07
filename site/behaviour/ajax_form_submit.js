/**
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

