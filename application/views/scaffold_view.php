<?php

	/**
	 *	Scaffold view
	 *  View one item
	 */

	// initialise ViewSnippet object - for automatic HTML creation 
	// from scaffold object data
	$objViewSnippet = new ViewSnippet();
	
	
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter'));
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo $title; ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        
		<?php echo validateContent($description); ?>
		<?php
		// automatically show contents of individual object
		//echo $objViewSnippet->autoViewById($objScaffold);
		?>
	</div>
    <?php include(APPLICATION_PATH . '/views/common/sidebar_metadata.php'); ?>
<?php include($objTemplate->getFooterHTML()); ?>