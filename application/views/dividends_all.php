<?php

	/**
	 *	Dividends - View all items
	 */

	// initialise ViewSnippet object - for automatic HTML creation
	// from scaffold object data
	$objViewSnippet = new ViewSnippet();

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());

	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));

	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'ajax_pagination', 'ajax_filter', 'colorbox'));

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
					<th scope="col">Title</th>
					<th scope="col">Company</th>
					<th scope="col">Amount</th>
				</tr>
			</thead>
			<tbody>
			<?php
			// Loop through all properites show we can show basic details and links for each one
			for($i = 0; $i < $properties_size; $i++):
				extract($properties[$i]);
				$title = stripslashes($title);
				if(!empty($id)):
					$tense_class = (strtotime($transaction_date) > strtotime('now')) ? 'future inactive' : '';
			?>
				<tr>
					<td><?php echo DateFormat::getDate('ddmmyyyy', $transaction_date); ?></td>
					<td>
						<a href="<?php echo $objScaffold->getFolder(); ?><?php echo $id; ?>/"><?php echo $title; ?></a>
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
					<td><?php echo $dividend_company_title; ?></td>
					<td><?php echo currency($amount); ?></td>
				</tr>
			<?php endif; endfor; ?>
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