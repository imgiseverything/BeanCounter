<?php

	/**
	 *	Outgoings Documentation view
	 *  View one item
	 */


	
	
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	$objTemplate->setDescription($objScaffold->getPageDescription());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter'));
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?>: <?php echo $title; ?><br />
    	<span>For outgoing: <a href="/outgoings/<?php echo $outgoing; ?>/"><?php echo $outgoing_title; ?> (#<?php echo $outgoing; ?>)</a></span></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <div class="buttons clearfix">
        	<a href="<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', $filename); ?>" class="download" download>Download <?php echo Upload::convertBytes($filesize); ?> (<?php echo $mimetype; ?>)</a>
        </div>
	</div>
    <?php include(APPLICATION_PATH . '/views/common/sidebar_metadata.php'); ?>
<?php include($objTemplate->getFooterHTML()); ?>