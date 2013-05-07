<?php
	/**
	 *	Proposals view
	 *  View all proposals and add/edit/delete them
	 */
	
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables', 'projects', 'datepicker'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'ajax_pagination', 'jquery.form', 'jquery.date', 'jquery.datepicker', 'datepicker', 'ajax_filter'));
	
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
						<th scope="col"><?php echo CURRENCY; ?></th>
					</tr>
				</thead>
				<tbody>
			<?php
				// Create table rows by looping through obejct data
				for($i= 0; $i < $properties_size; $i++):
					// create easy to use variable names
					extract($properties[$i]);
					
				
			?>
				<tr>
						<td><span title="<?php echo DateFormat::howManyDays($date_added) . ' ago'; ?>"><?php echo DateFormat::getDate('ddmmyyyy', $date_added); ?></span></td>
						<td>
							<a href="<?php echo $objScaffold->getFolder() . $id; ?>/"><?php echo stripslashes($title); ?></a>
							<div class="group extra-options">
								<ul>
									<?php if($objAuthorise->getLevel() == 'Superuser'): ?>
									<li><a href="<?php echo $objScaffold->getFolder() . $id; ?>/">View</option>
									<li><a href="<?php echo $objScaffold->getFolder(); ?>download/<?php echo md5(SECRET_PHRASE . $id); ?>/">Print</a></li>
									<li><a href="<?php echo $objScaffold->getFolder(); ?>quote/<?php echo $id; ?>/">Send quote</a></li>
									<li><a href="<?php echo $objScaffold->getFolder(); ?>duplicate/<?php echo $id; ?>/">Duplicate</a></li>								
									<li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $id; ?>/">Edit</a></li>
									<li><a href="<?php echo $objScaffold->getFolder(); ?>delete/<?php echo $id; ?>/">Delete</a></li>
									<?php else: ?>
									<li><a href="<?php echo $objScaffold->getFolder(); ?>download/<?php echo $id; ?>/">Print</a></li>
									<?php endif; ?>
								</ul>
							</div>						
						</td>
						<td><?php echo stripslashes($client_title); ?></td>
						<td><?php echo currency($total); ?></td>
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