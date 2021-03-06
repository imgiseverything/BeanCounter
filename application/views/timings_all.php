<?php

	/**
	 *	TIme tracking view
	 *  View all timings
	 */

	// initialise ViewSnippet object - for automatic HTML creation 
	// from scaffold object data
	$objViewSnippet = new ViewSnippet();

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));

	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'ajax_pagination', 'colorbox', 'plugins/jquery.form', 'plugins/jquery.date', 'plugins/jquery.datepicker', 'datepicker', 'ajax_filter'));
	
	// Breadcrumb
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
		<a href="<?php echo $objScaffold->getFolder(); ?>add/" class="button-add"><span></span>Add new <?php echo $objScaffold->getName(); ?></a>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <?php if($objScaffold->getTotalProjectHours() != false): ?>
        <p style="">Total hours for this project: <?php echo $objScaffold->getTotalProjectHours(); ?> hours</p>
        <?php endif; ?>
        <div class="data">
		<?php		
			// Pagination
			echo $objPagination->getPagination();
		?>
		<table id="<?php echo strtolower($objScaffold->getNamePlural()); ?>_table">
				<thead>
					<tr>
						<th scope="col">Date</th>
						<th scope="col">Description</th>
						<th scope="col">Project / Client</th>
						<th scope="col">Tags</th>
						<th scope="col">Duration</th>
					</tr>
				</thead>
				<tfoot>
					<th scope="row" colspan="4">Total</th>
					<td><?php echo $objScaffold->getTotalHours(); ?> hours</td>
				</tfoot>
				<tbody>
			<?php
				// Create table rows by looping through obejct data
				for($i= 0; $i < $properties_size; $i++):
					// create easy to use variable names
					extract($properties[$i]);
			?>
				<tr>
						<td><span title="<?php echo DateFormat::howManyDays($date_added) . ' ago'; ?>"><?php echo DateFormat::getDate('ddmmyyyy', $start_date); ?></span></td>
						<td>
							<?php echo $title; ?>
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
						<td><a href="<?php echo $objScaffold->getFolder(); ?>?project=<?php echo $project; ?>"><?php echo stripslashes($project_title); ?></a> / <a href="<?php echo $objScaffold->getFolder(); ?>?client=<?php echo $project_client; ?>" title="View all the times for <?php echo $client_title; ?>"><?php echo stripslashes($client_title); ?></a></td>
						<td><?php if(!empty($timing_tag)): ?>
							<ul class="tags">
							<?php foreach($timing_tag as $tag): ?>
								<li><?php echo trim($tag['title']); ?></li>
							<?php endforeach;?>
							</ul>
							<?php endif; ?>
						</td>
						<td><?php echo ($duration === 1) ? '1 hour' : $duration . ' hours'; ?></td>
					</tr>
					<?php endfor; ?>
				</tbody>
			</table>
		<?php
				
			// Pagination (again)
			echo $objPagination->getPagination();

		?>
		</div>
	</div>
<?php 
    if($objTemplate->getMode() == 'normal'):
    	include(APPLICATION_PATH . '/views/common/timings_sidebar.php');
    endif; 
?>
<?php include($objTemplate->getFooterHTML()); ?>