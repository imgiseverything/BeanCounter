<?php

	/**
	 *	Scaffold view
	 *  View all items
	 */

	// initialise ViewSnippet object - for automatic HTML creation 
	// from scaffold object data
	$objViewSnippet = new ViewSnippet();

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	$objTemplate->setDescription($objScaffold->getPageDescription());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables', 'colorbox'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'ajax_add_new', 'ajax_pagination', 'sidebar', 'colorbox'));
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <div class="buttons clearfix">
        	<a href="<?php echo $objScaffold->getFolder(); ?>add/" class="button add">Add new <?php echo $objScaffold->getName(); ?></a>
        </div>
        <div class="data">
		<?php		
			// Pagination
			echo $objPagination->getPagination();
			
			// automatically show all results in object
			echo $objViewSnippet->autoViewAllTable($objScaffold);
				
			// Pagination (again)
			echo $objPagination->getPagination();

		?>
		</div>
	</div>
<?php 
    if($objTemplate->getMode() == 'normal'):
    	include(APPLICATION_PATH . '/views/common/sidebar.php');
    endif; 
?>
<?php include($objTemplate->getFooterHTML()); ?>