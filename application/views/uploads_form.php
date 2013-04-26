<?php

	/**
	 *	Files Form View
	 *	Add/edit/deleet a new file/upload
	 *
	 *
	 */
	 
	 
	// Page details
	$objTemplate->setTitle($action. ' &lt; ' .$objFile->getNamePlural());
	$objTemplate->setDescription();
	$objTemplate->setBodyClass('home');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables')); // must be an array
	$objTemplate->setExtraStyle('');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'jquery.form', 'ajax_form_submit')); // must be an array
	$objTemplate->setExtraBehaviour();
	
	// Menus
	$objMenu->setBreadcrumb($objFile->breadcrumb_title);
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
		<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucwords($objFile->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <?php include($objTemplate->getForm()); ?>
	</div>
	<div id="SecondaryContent">&nbsp;</div>
<?php include($objTemplate->getFooterHTML()); ?>