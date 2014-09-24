<?php

	/**
	 *	Outgoings (expenses) view
	 *  View all outgoings
	 */
	

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables', 'colorbox', 'datepicker'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter',  'ajax_pagination', 'colorbox', 'plugins/jquery.date', 'plugins/jquery.datepicker', 'datepicker', 'plugins/jquery.form', 'ajax_filter'));
	
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
					<th scope="col">Date</th>
					<th scope="col">Item</th>
					<th scope="col">Category</th>
					<th scope="col">Supplier</th>
					<th scope="col"><?php echo CURRENCY; ?> (%)</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="row" colspan="4">Subtotal</th>
					<td colspan="2"><?php echo currency($objScaffold->getGrandTotal()); ?></td>
				</tr>
			</tfoot>
			<tbody>
			<?php 
			// Loop through all properites show we can show basic details and links for each one
			for($i = 0; $i < $properties_size; $i++):
				extract($properties[$i]);
				$title = stripslashes($title);
				if(!empty($id)):
					$tense_class = (strtotime($transaction_date) > strtotime('now')) ? 'future inactive' : '';
			?>
				<tr class="<?php echo $tense_class; ?>">
					<td><?php echo DateFormat::getDate('ddmmyyyy', $transaction_date); ?></td>
					<td>
						<a href="<?php echo $objScaffold->getFolder(); ?><?php echo $id; ?>/"><?php echo $title; ?></a>
						<span class="secondary-info">#<?php echo Project::referenceNumber($id, $transaction_date); ?></span>
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
					<td><a href="<?php echo $objScaffold->getFolder(); ?>?category=<?php echo $outgoing_category?>" title="View all <?php echo $objScaffold->getNamePlural(); ?> in <?php echo $outgoing_category_title?>"><?php echo $outgoing_category_title?></a></td>
					<td><a href="<?php echo $objScaffold->getFolder(); ?>?supplier=<?php echo $outgoing_supplier?>" title="View all <?php echo $objScaffold->getNamePlural(); ?> from <?php echo $outgoing_supplier_title?>"><?php echo $outgoing_supplier_title?></a></td>
					<td><?php echo currency($price); ?> <span class="secondary-info"><?php echo number_format(((float)$price/(float)$objScaffold->getGrandTotal())*100, 2, '.', ''); ?>%</span></td>
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
    // Sidebar
    if($objTemplate->getMode() == 'normal'):
    	include(APPLICATION_PATH . '/views/common/outgoings_sidebar.php');
    endif;
    ?>
<?php include($objTemplate->getFooterHTML()); ?>