<?php
	
	/**
	 *	Backup view
	 */

	// Page details
	$objTemplate->setTitle('Backup');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array());
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'ajax_filter'));
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1>Back-up</h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <div class="buttons clearfix">
			<a href="/backup/download/" class="button">Download Back-up file</a>
		</div>
    </div>
<?php include($objTemplate->getFooterHTML()); ?>