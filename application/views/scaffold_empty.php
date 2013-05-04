<?php

	/**
	 *	Scaffold no items to show
	 */

	// initialise ViewSnippet object - for automatic HTML creation from scaffold object data
	$objViewSnippet = new ViewSnippet();

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	$objTemplate->setDescription('This is a scaffold page for ' . SITE_NAME);
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables', 'colorbox'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'ajax_pagination', 'ajax_filter'));
	
	// Breadcrumb
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
		<div class="buttons">
			<?php if($objAuthorise->getLevel() == 'Superuser'): ?>
			<a href="<?php echo $objScaffold->getFolder(); ?>add/" class="button add">Add new <?php echo $objScaffold->getName(); ?></a>
			<?php endif; ?>
		</div>	
		<p>There are no <?php echo $objScaffold->getNamePlural(); ?>
		<?php if($objScaffold->getSearch()): ?>
		that match your search criteria.</p>
		<?php else: ?>
		on this page.</p>
		<?php endif; ?>
	</div>
<?php 
    if($objTemplate->getMode() == 'normal'):
    	include($objApplication->getViewFolder() . 'common/sidebar.php');
    endif; 
?>
<?php include($objTemplate->getFooterHTML()); ?>