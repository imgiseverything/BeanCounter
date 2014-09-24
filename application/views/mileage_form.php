<?php

	/**
	 *	Scaffold view
	 *  Form add/edit/delete etc
	 */

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'datepicker', 'colorbox'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter',  'colorbox', 'plugins/jquery.date', 'plugins/jquery.datepicker', 'datepicker', 'plugins/jquery.form', 'hide_time', 'ajax_form_submit'));
	
	// Breadcrumb
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <?php include('forms/mileage_add_edit.php'); ?>
	</div>
    <?php include(APPLICATION_PATH . '/views/common/sidebar_forms.php'); ?>
<?php include($objTemplate->getFooterHTML()); ?>