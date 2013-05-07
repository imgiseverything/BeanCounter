<?php
	/**
	 *  Clients view
	 *  View all or individual clients and add/edit/delete them
	 */	

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'projects', 'tables'));
	// On page CSS
	$objTemplate->setExtraStyle('');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'ajax_pagination', 'jquery.form', 'ajax_filter'));
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
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
						<th scope="col">Name</th>
						<th scope="col"><?php echo CURRENCY; ?> (%)</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="row">Subtotal</th>
						<td><?php echo currency($objScaffold->getGrandTotal())?></td>
					</tr>
				</tfoot>		
				<tbody>
			<?php
				// Create table ro3ws by looping through obejct data
				for($i= 0; $i < $properties_size; $i++):
					// create easy to use variable names
					extract($properties[$i]);
					
					// work out percentage of overall spend for this client
					$percentage = ( ( (float)$spend / (float)$objScaffold->getGrandTotal()  ) * 100);
					$percentage = number_format($percentage, 2, '.', ',') . '%';

			?>
				<tr class="<?php echo assignOrderClass($i, $properties_size); ?>">
					<td>
						<a href="<?php echo $objScaffold->getFolder() . $id; ?>/"><?php echo stripslashes($title); ?></a>
						<div class="group extra-options">
							<ul>
								<li><a href="<?php echo $objScaffold->getFolder() . $id; ?>/">View</option>
								<?php if($objAuthorise->getLevel() == 'Superuser'): ?>
								<li><a href="<?php echo $objScaffold->getFolder(); ?>duplicate/<?php echo $id; ?>/">Duplicate</a></li>								
								<li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $id; ?>/">Edit</a></li>
								<li><a href="<?php echo $objScaffold->getFolder(); ?>delete/<?php echo $id; ?>/">Delete</a></li>
								<?php endif; ?>
							</ul>
						</div>	
					</td>
					<td><?php echo currency($spend); ?><span class="secondary-info"><?php echo $percentage; ?></span></td>
				</tr>
			<?php endfor; ?>
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