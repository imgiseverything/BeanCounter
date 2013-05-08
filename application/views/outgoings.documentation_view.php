<?php

	/**
	 *	Outgoings Documentation view
	 *  View one piece of outgoings documentation e.g. a PDF receipt for an expense
	 */


	
	
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	$objTemplate->setDescription($objScaffold->getPageDescription());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle();
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter'));
	
	// Breadcrumb
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<a href="<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', $filename); ?>" class="button-add button-download" download><span></span>Download <?php echo Upload::convertBytes($filesize); ?> (<?php echo $mimetype; ?>)</a>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?>: <?php echo $title; ?></h1>
    	<p><span>For the outgoing: <a href="/outgoings/<?php echo $outgoing; ?>/"><?php echo $outgoing_title; ?> (#<?php echo $outgoing; ?>)</a></span></h1>
        <?php echo $objFeedback->getFeedback(); ?>
	</div>
    <?php include(APPLICATION_PATH . '/views/common/sidebar_metadata.php'); ?>
<?php include($objTemplate->getFooterHTML()); ?>