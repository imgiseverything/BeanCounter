<?php
	/**
	 *	Suppliers view
	 *  View all suppliers
	 */
	

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables'));
	
	$objTemplate->setExtraStyle('
		ul#suppliers_list{
	
		}
	
			ul#suppliers_list li{
				border-bottom: 1px solid #DDD;
				list-style: none;
				margin-left: 0;
				padding: 10px 0;
			}
			
				ul#suppliers_list li a{
					display: block;
				}
	');
	
	// Interaction / Behaviour
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'ajax_pagination', 'plugins/jquery.form', 'ajax_filter'));
	
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
		<?php	
			// results exist
			if(!empty($properties)):
		?>
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
					<td colspan="2"><?php echo currency($objScaffold->getGrandTotal())?></td>
				</tr>
			</tfoot>
			<tbody>
			<?php
				// Create table ro3ws by looping through obejct data
				for($i = 0; $i < $properties_size; $i++):
					// create easy to use variable names
					extract($properties[$i]);
					
					// work out percentage of overall spend for this supplier
					$percentage = ( ( (float)$cost / (float)$objScaffold->getGrandTotal()  ) * 100);
					$percentage = number_format($percentage, 2, '.', ',') . '%';
					
				
			?>
				<tr>
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
					<td><?php echo currency($cost)?> <span class="secondary-info"><?php echo $percentage; ?></span></td>
				</tr>
			<?php
				endfor;
			?>
				</tbody>
			</table>
			<?php echo $objPagination->getPagination(); ?>
		</div>
		<?php	
			else:
				/* 
				No Results!!!
				
				Why?
				
				1. Are we on a page that isn't 1 e.g. has the user gone to a non-existent 
				page e.g. page 36 when there's only 35 pages of data?
				
			 	2. Or has a user searched for data that doesn't exist?
				
				Solution:
				Tell the user (in a understandable way) why they are seeing no results
				*/
				echo '<p>There are no ' . $objScaffold->getNamePlural();
				echo ($objScaffold->getSearch()) ? ' that match your search criteria</p>' : ' on this page</p><div class="buttons"><a href="' . $objScaffold->getFolder() . 'add/" class="button positive add">Add new ' . $objScaffold->getName() . '</a></div>';
				
			endif;

			?>
	</div>
<?php 
   if($objTemplate->getMode() == 'normal'):
    	include(APPLICATION_PATH . '/views/common/sidebar.php');
   endif;
?>
<?php include($objTemplate->getFooterHTML()); ?>