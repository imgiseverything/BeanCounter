<?php

	/**
	 *	Discounts view
	 *  Add/edit/delete discounts
	 */
	 
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter',  'colorbox', 'plugins/jquery.form', 'ajax_form_submit'));
	
	$objTemplate->setExtraBehaviour();
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
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