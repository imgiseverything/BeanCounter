<?php

	/**
	 *	Artists view
	 *  View all items
	 */

	// initialise ViewSnippet object - for automatic HTML creation from scaffold object data
	$objViewSnippet = new ViewSnippet();

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables', 'colorbox'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'jquery.columns', 'plugins/jquery.form', 'ajax_pagination', 'ajax_filter', 'colorbox'));
	
	// Breadcrumb
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <div class="buttons clearfix">
        	<a href="<?php echo $objScaffold->getFolder(); ?>delete/" class="button negative delete">Delete all <?php echo $objScaffold->getNamePlural(); ?></a>
        </div>
        <div class="data">
			<?php echo $objPagination->getPagination();?>
			<p id="Showing"><?php echo getShowingXofX($objScaffold->getPerPage(), $objScaffold->getCurrentPage(), $properties_size, min($objScaffold->getTotal(), sizeof($properties))) . ' ' . $objScaffold->getNamePlural(); ?></p>
			<table>
				<thead>
					<tr>
						<th scope="col"><a href="<?php echo $objScaffold->getFolder(); ?>?sort=<?php echo ($objApplication->getParameter('sort') == 'title_az') ? 'title_za' : 'title_az'; ?>">Error string</a></th>
						<th scope="col">Count</th>
						<th scope="col">File</th>
						<th scope="col">Level</th>
					</tr>
				</thead>
				<tbody>
				<?php
				for($i = 0; $i < $properties_size; $i++):
					extract($properties[$i]);
					$title = stripslashes($title);
				?>	
					<tr id="row-<?php echo $id; ?>">
						<td><?php echo $title; ?></td>
						<td><?php echo $total; ?></td>
						<td><span class="secondary-info">Line <?php echo $line; ?> on <?php echo $file; ?></span></td>
						<td><span class="secondary-info"><?php echo $error_level_title; ?></span></td>
					</tr>
				<?php endfor; ?>	
				</tbody>
			</table>
			<?php echo $objPagination->getPagination(); ?>
		</div>
	</div>
<?php include($objTemplate->getFooterHTML()); ?>