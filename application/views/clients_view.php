<?php
	/**
	 *  Clients view
	 *  View individual client
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
		<?php extract($properties); ?>
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo stripslashes($title); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
		<?php echo $vcard; ?>	
		<?php 
		if(!empty($projects)): 
			$i = 1; // counter
		?>
		<div class="data">
			<table>
				<caption>Projects</caption>
				<thead>
					<tr>
						<th scope="col">Project</th>
						<th scope="col">Stage</th>
						<th scope="col">Spend</th>
						<th scope="col">Date</th>
					</tr>
				</thead>
				<tbody>
			<?php	 
				// Loop through all properites show we can show 
				// basic details and links for each one
				foreach($projects as $property):
					if(!empty($property['title'])):
						echo '<tr class="' . strtolower(stripslashes($property['project_stage_title'])) . '">
						<td><a href="/projects/' . $property['id'] . '/">' . stripslashes($property['title']) . '</a></td>						<td>' . stripslashes($property['project_stage_title']) . '</td>
						<td>' . currency(stripslashes(read($property, 'total', 0))) . '</td>
						<td>' . DateFormat::getDate('date', $property['date_added']) . '</td>
						</tr>' . "\n";
					 endif;
					$i++; //increment counter
				endforeach;
			?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
	</div>
    <?php include(APPLICATION_PATH . '/views/common/sidebar_metadata.php'); ?>
<?php include($objTemplate->getFooterHTML()); ?>