<?php

	/**
	 *	Tasks view
	 *  Add/edit/delete tasks
	 */
	 
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'colorbox'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter',  'colorbox',/*'tiny_mce/tiny_mce', 'tiny_mce/init.default',*/ 'jquery.form', 'ajax_form_submit', 'vat'));
	
	$objTemplate->setExtraBehaviour('');
	
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