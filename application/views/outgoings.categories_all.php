<?php

	/**
	 *	Outgoing categories view
	 *  View all outgoing categories
	 */
	 
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
					<th scope="col"><?php echo CURRENCY; ?></th>
					<th scope="col">%</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="row" colspan="1">Subtotal</th>
					<td colspan="2"><?php echo currency($objScaffold->getGrandTotal()); ?></td>
				</tr>
			</tfoot>
			<tbody>
			<?php 
			// Loop through all properites show we can show basic details and links for each one
			for($i = 0; $i < $properties_size; $i++):
				
				if(!empty($properties[$i]['id'])):
					extract($properties[$i]);
			?>
				<tr>
					<td><?php echo stripslashes($title); ?></td>
					<td><?php echo currency($total); ?></td>
					<td><?php echo round($percentage, 2); ?>%</td>
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