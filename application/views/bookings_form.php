<?php
	/**
	 *  Bookings view
	 *  View individual booking
	 */	

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'projects', 'tables', 'colorbox', 'datepicker'));
	// On page CSS
	$objTemplate->setExtraStyle('');
	
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'colorbox', /*'tiny_mce/tiny_mce', 'tiny_mce/init.default',*/ 'jquery.date', 'jquery.datepicker', 'datepicker', 'jquery.form', 'ajax_form_submit'));
	
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
<?php include($objTemplate->getFooterHTML()); ?>