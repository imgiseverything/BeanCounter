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


