<?php

	/**
	 *	Outoging Documentation view
	 *  View all items
	 */

	// initialise ViewSnippet object - for automatic HTML creation 
	// from scaffold object data
	$objViewSnippet = new ViewSnippet();

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'ajax_pagination'));
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
		<a href="<?php echo $objScaffold->getFolder(); ?>add/" class="button-add"><span></span>Add new <?php echo $objScaffold->getName(); ?></a>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <div class="data">
			<?php echo $objPagination->getPagination(); ?>
			<p class="showing"><?php echo getShowingXofX($objScaffold->getPerPage(), $objScaffold->getCurrentPage(), $properties_size, $objScaffold->getTotal()) . ' ' . $objScaffold->getNamePlural(); ?></p>
			<table id="<?php echo strtolower($objScaffold->getNamePlural()); ?>_table">
			<thead>
				<tr>
					<th scope="col">Title</th>
					<th scope="col">Outgoing</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			// Loop through all properites show we can show basic details and links for each one
			for($i = 0; $i < $properties_size; $i++):
				
				if(!empty($properties[$i]['id'])):
					extract($properties[$i]);
			?>
				<tr>
					<td><a href="<?php echo $objScaffold->getFolder() . $id; ?>/"><?php echo stripslashes($title); ?></a>
					<span class="secondary-info"><?php echo upload::convertBytes($filesize); ?></span></td>
					<td><a href="/outgoings/<?php echo $outgoing_id; ?>/"><?php echo stripslashes($outgoing_title); ?><span class="secondary-info">#<?php echo Project::referenceNumber($outgoing_id, $outgoing_transaction_date); ?></span></td>
				</tr>
			<?php	
				endif; // end id
			endfor;  // end for loop
			?>
			</tbody>
			</table>
			<?php echo $objPagination->getPagination(); ?>
		</div>
	</div>
<?php 
    if($objTemplate->getMode() == 'normal'):
    	include(APPLICATION_PATH . '/views/common/sidebar.php');
    endif; 
?>
<?php include($objTemplate->getFooterHTML()); ?>