<?php

	/**
	 *	System settings view
	 *  View one system setting
	 */

	$objViewSnippet = new ViewSnippet();
	

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
		<?php
		// do properties exist?
		if(!empty($properties)):
			extract($properties);
			echo validateContent(ucfirst($objScaffold->getName()) . ': ' . stripslashes($title));
			// automatically show contents of individual object
			echo $objViewSnippet->autoViewById($objScaffold);
		endif;
		?>
		<hr />
		<div class="buttons">
			<a href="<?php echo $objScaffold->getFolder(); ?>" class="button">View all</a>
			<a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $objScaffold->getId(); ?>/" class="button edit">Edit</a>
		</div>
	</div>    
<?php include($objTemplate->getFooterHTML()); ?>