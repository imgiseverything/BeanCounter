<?php
	
	/**
	 *	Backup view
	 */

	// Page details
	$objTemplate->setTitle('Download backup');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array());
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'ajax_pagination', 'ajax_filter'));
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1>Download backup file</h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <div class="buttons clearfix">
			<a href="/cache/<?php echo $objBackup->getFilename(); ?>" class="button">Download</a>
		</div>
    </div>
<?php include($objTemplate->getFooterHTML()); ?>