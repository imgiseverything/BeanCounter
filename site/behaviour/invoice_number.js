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
	
	// Add button
	$('#internal_reference_number').addClass('int').after('<a href="#" class="refresh button" id="invoice_button">Generate</a>');
	
	// Get the invoice number with AJAX when user clicks the generate button
	$('#invoice_button').click(function(){
		
		$(this).load('/ajax/invoice_number.php?mode=ajax', '', function(){
			$(this).hide();
			$('#internal_reference_number').val($(this).html());
		});
		
		return false;
	});
}


$(document).ready(function(){
	generateInvoiceNumber();	
});