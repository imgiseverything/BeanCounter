<?php

	/**
	 *	System settings view
	 *  View all system settings e.g. site name, address details etc
	 */
	

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables'));
	
	$objTemplate->setExtraStyle('
		td.description,
		tr.even td.description{
			color: #999;
		}
	');
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1>Settings</h1>
    	<?php echo $objFeedback->getFeedback(); ?>
    	<div class="data">
			<table>
				<caption>
					<h1>Address <?php echo $objScaffold->getNamePlural(); ?> <a href="<?php echo $objScaffold->getFolder(); ?>edit/1/?type=address" title="Edit address <?php echo $objScaffold->getNamePlural(); ?>">Edit</a></h1>
				</caption>
				<thead>
					<tr>
						<th scope="col" width="25%">Title</th>
						<th scope="col" width="25%">Value</th>
						<th scope="col" width="50%">Description</th>
					</tr>
				</thead>
				<tbody>
				<?php
				// Loop through all properties show we can show basic details and links for each one
				for($i = 0; $i < min(10, $properties_size); $i++):
					extract($properties[$i]);
					if($title != 'currency_value'):
				?>
					<tr class="<?php echo assignOrderClass($i, $properties_size); ?>">
						<td><?php echo stripslashes($title); ?></td>
						<td><?php echo stripslashes($value); ?></td>
						<td class="description"><?php echo stripslashes($description); ?></td>
					</tr>
			   <?php 
					endif;
			   endfor; ?>	
				</tbody>
			</table>
		</div>
		<div class="data">
			<table>
				<caption>
					<h1>Financial <?php echo $objScaffold->getNamePlural(); ?> <a href="<?php echo $objScaffold->getFolder(); ?>edit/1/?type=financial" title="Edit financial <?php echo $objScaffold->getNamePlural(); ?>">Edit</a></h1>
				</caption>
				<thead>
					<tr>
						<th scope="col" width="25%">Title</th>
						<th scope="col" width="25%">Value</th>
						<th scope="col" width="50%">Description</th>
					</tr>
				</thead>
				<tbody>
				<?php
				// Loop through all properties show we can show basic details and links for each one
				for($i = 10; $i < min(20, $properties_size); $i++):
					extract($properties[$i]);
					if($title != 'currency_value'):
				?>
					<tr class="<?php echo assignOrderClass($i, $properties_size); ?>">
						<td><?php echo stripslashes($title); ?></td>
						<td><?php echo stripslashes($value); ?></td>
						<td class="description"><?php echo stripslashes($description); ?></td>
					</tr>
			   <?php 
					endif;
			   endfor; ?>	
				</tbody>
			</table>
		</div>
	</div>    
<?php include($objTemplate->getFooterHTML()); ?>