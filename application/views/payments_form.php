<?php

	/**
	 *	Scaffold view
	 *  Form add/edit/delete etc
	 */

	// Page details
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'plugins/jquery.date', 'plugins/jquery.datepicker', 'datepicker', 'plugins/jquery.form', 'ajax_form_submit', 'colorbox'));
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <?php include($objTemplate->getForm()); ?>
	</div>
    <?php include(APPLICATION_PATH . '/views/common/sidebar_forms.php'); ?>    
<?php include($objTemplate->getFooterHTML()); ?>