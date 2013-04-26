<?php

	/**
	 *	Scaffold view
	 *  Form add/edit/delete etc
	 */

	// Page details
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	$objTemplate->setDescription($objScaffold->getPageDescription());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'datepicker', 'colorbox'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'jquery.date', 'jquery.datepicker', 'datepicker', 'jquery.form', 'ajax_form_submit', 'colorbox'));
	
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