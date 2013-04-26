<?php

	/**
	 *	Outgoings (expenses) view
	 *  Add new outgoing
	 */

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables', 'colorbox', 'datepicker'));
	
	$objTemplate->setExtraStyle('
		div#PrimaryContent form fieldset textarea#description{
			height: 10em;
		}
	');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter',  'colorbox', /*'tiny_mce/tiny_mce', 'tiny_mce/init.default',*/ 'jquery.date', 'jquery.datepicker', 'datepicker', 'jquery.form', 'hide_time', 'ajax_form_submit'));
	
	$objTemplate->setExtraBehaviour('
	
	$(document).ready(function(){
		copyPriceValues();
	});
	
	function copyPriceValues(){
	
		$("#claimable_price").blur(function(){
			$(this).addClass("touched");
		});
	
		$("#price").keyup(function(){
			if(!$("#claimable_price").hasClass("touched")){
			var value = $(this).val();
			$("#claimable_price").val(value);
			}
		});
	}

	
	');
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <?php include($objTemplate->getForm()); ?>
	</div>
	<?php include(APPLICATION_PATH . '/views/common/sidebar_forms.php'); ?>    
<?php include($objTemplate->getFooterHTML()); ?>