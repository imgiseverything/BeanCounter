<?php

	/**
	 *	Files Form View
	 *	Add/edit/deleet a new file/upload
	 *
	 *
	 */
	 
	 
	// Page details
	$objTemplate->setTitle($action. ' &lt; ' .$objFile->getNamePlural());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'plugins/jquery.form', 'ajax_form_submit')); // must be an array
	$objTemplate->setExtraBehaviour();
	
	// Menus
	$objMenu->setBreadcrumb($objFile->breadcrumb_title);
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
		<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucwords($objFile->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <?php include($objTemplate->getForm()); ?>
	</div>
	<div class="content-secondary">&nbsp;</div>
<?php include($objTemplate->getFooterHTML()); ?>