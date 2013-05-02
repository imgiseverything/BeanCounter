/**
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
});