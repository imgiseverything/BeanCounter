<?php

	/**
	 *	Outgoing categories view
	 *  View individual outgoing categories
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
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?> : <?php echo $title; ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
		<?php echo validateContent($description); ?>
		<p><a href="/outgoings/?category=<?php echo $id; ?>">View all outgoings in this category</a></p>
	</div>
	<?php include(APPLICATION_PATH . '/views/common/sidebar_metadata.php'); ?>
	<?php include($objTemplate->getFooterHTML()); ?>