/**
 *	Bean Counter JavaScript
 *	VAT
 *	
 *	@author		philthompson.co.uk
 *	@since		05/10/2009
 *	@version 	1.0
 *	@package	jQuery
 *
 */
 
 
 		
$(document).ready(function(){

	initVat();

});


/**
 *	initVAT
 */
function initVat(){

	var vatOptionHTML = '<div class="field"><input type="checkbox" class="checkbox" id="vat-checkbox" value="Y" /><label for="vat-checkbox" class="checklabel">Add VAT on top?</label></div>';

	// add vat checkbox
	if($("#vat").length == 0){
		return;
	}
	
	$("#vat").parent().before(vatOptionHTML);
	
	if($("#vat").val().length == 0){
		$("#vat").parent().hide();
	}
	
	$("#vat-checkbox").click(function(){
		if($("#vat-checkbox").attr("checked") == true){
			$("#vat").parent().show();
			calculateVAT();
		} else{
			$("#vat").val();
			$("#vat").parent().hide();
		}
	});
	
	
	
	$("#price").keyup(function(){
		if($("#vat-checkbox").attr("checked") == true){
			calculateVAT();
		}
	});
		
}


/**
 *	calculateVAT
 */
function calculateVAT(){
	
	// take VAT rate (from database) and create VAT from price
	var	rate = $("#vat_rate").val(),
		price = $("#price").val(),
		vat_rate = (price * (rate / 100));
	
	
	$("#vat").val(vat_rate);
}

