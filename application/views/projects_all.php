<?php
	/**
	 *	Project view
	 *  View all projects and add/edit/delete them
	 */
	
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables', 'projects', 'colorbox', 'datepicker'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'ajax_pagination', 'colorbox', 'jquery.form', 'jquery.date', 'jquery.datepicker', 'datepicker', 'ajax_filter'));
	
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
						<th scope="col">Client</th>
						<th scope="col">Stage</th>
						<th scope="col"><?php echo CURRENCY; ?> (%)</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="row" colspan="4">Subtotal</th>
						<td><?php echo currency($objScaffold->getGrandTotal()); ?></td>
					</tr>
				</tfoot>
				<tbody>
			<?php
				// Create table rows by looping through obejct data
				for($i = 0; $i < $properties_size; $i++):
					// create easy to use variable names
					extract($properties[$i]);
					
					
					// project is complete (all paid up)
		
					$tr_class = strtolower(stripslashes($project_stage_title));
					
					// If invoice is late
					$invoice_days = (int)str_replace(' days', '', DateFormat::howManyDays($payment_required));
					$tr_class .= ($project_stage == 3 && $invoice_days > 0) ? ' negative' : '';
					
					
					// project is complete (all paid up)
					if($completed === true){
						$tr_class = 'completed';
					}
					
					
					$percentage = ( ( (float)$total / (float)$objScaffold->getGrandTotal()  ) * 100);
					$percentage = number_format($percentage, 2, '.', ',') . '%';
				
			?>
				<tr class="<?php echo $tr_class;?>">
						<td><span title="<?php echo DateFormat::howManyDays($date_added) . ' ago'; ?>"><?php echo DateFormat::getDate('ddmmyyyy', $date_added); ?></span></td>
						<?php if($objAuthorise->getLevel() == 'Superuser'): ?>
						<td>
							<a href="<?php echo $objScaffold->getFolder() . $id; ?>/"><?php echo stripslashes($title); ?></a> 
							<span class="secondary-info">#<?php echo Project::referenceNumber($id, $date_added); ?></span>
							<div class="group extra-options">
								<ul>
									<?php if($objAuthorise->getLevel() == 'Superuser'): ?>
									<li><a href="<?php echo $objScaffold->getFolder() . $id; ?>/">View</option>
									<li><a href="<?php echo $objScaffold->getFolder(); ?>download/<?php echo md5(SECRET_PHRASE . $id); ?>/">Print</a></li>
									<?php if(strtolower($project_stage_title) != 'completed'): ?>
									<li><a href="<?php echo $objScaffold->getFolder(); ?>invoice/<?php echo $id; ?>/">Invoice</a></li>
									<?php endif; ?>
									<?php if($completed !== true): ?>
									<li><a href="/payments/add/?project=<?php echo $id; ?>&amp;price=<?php echo $total; ?>&amp;title=<?php echo $title; ?>">Record payment</a></li>
									<?php endif; ?>
									<li><a href="<?php echo $objScaffold->getFolder(); ?>duplicate/<?php echo $id; ?>/">Duplicate</a></li>								
									<li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $id; ?>/">Edit</a></li>
									<li><a href="<?php echo $objScaffold->getFolder(); ?>delete/<?php echo $id; ?>/">Delete</a></li>
									<?php else: ?>
									<li><a href="<?php echo $objScaffold->getFolder(); ?>download/<?php echo $id; ?>/">Print</a></li>
									<?php endif; ?>
								</ul>
							</div>
						
						</td>
						<td><a href="<?php echo $objScaffold->getFolder() . '?client=' . $client?>"><?php echo stripslashes($client_title); ?></a></td>
						<?php else: ?>
						<td><?php echo stripslashes($title); ?></td>
						<td><?php echo stripslashes($client_title); ?></td>
						<?php endif; ?>
						<td><?php echo stripslashes($project_stage_title); ?>
						<?php echo (strtolower($project_stage_title) == 'invoiced' && $completed !== true) ? '<br /><strong>Due: ' . DateFormat::getDate('ddmmyyyy', $payment_required) . '</strong>' : ''; ?>
						</td>
						<td><?php echo currency($total + $total_vat); ?> <span class="secondary-info"><?php echo $percentage; ?></span></td>
					</tr>
					<?php endfor; ?>
				</tbody>
			</table>
			<?php echo $objPagination->getPagination(); ?>
		</div>
	</div>
     <?php 
    if($objTemplate->getMode() == 'normal'):
    	include(APPLICATION_PATH . '/views/common/projects_sidebar.php');
    endif; 
    ?>
<?php include($objTemplate->getFooterHTML()); ?>