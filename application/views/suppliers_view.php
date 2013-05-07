<?php

	/**
	 *	Suppliers view
	 *  View an individual supplier
	 */	

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());;
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms','tables'));
	
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
	$objTemplate->setBehaviour(array('jquery', 'ajax_pagination'));
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
		<?php
			// create easy to use variables
			extract($properties);			
		?>
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo stripslashes($title); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
		<p><?php echo stripslashes($description); ?></p>
		<?php echo $vcard; ?>

		<?php
		// Has the supplier got outgoings?
		if(!empty($outgoings)):
		?>
		<h2>Expenses</h2>
		<table>
		<thead>
			<tr>
				<th scope="col">Date</th>
				<th scope="col">Item</th>
				<th scope="col">Category</th>
				<th scope="col"><?php echo CURRENCY; ?> (%)</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="row" colspan="3">Subtotal</th>
				<td colspan="2"><?php echo currency($objScaffold->getGrandTotal()); ?></td>
			</tr>
		</tfoot>
		<?php
		
		
		
			// Show their outgoings
			for($i = 0; $i < sizeof($outgoings); $i++):
				extract($outgoings[$i]);
		?>
			<tr class="<?php echo assignOrderClass($i, sizeof($outgoings))?>">
				<td><?php echo DateFormat::getDate('ddmmyyyy', $transaction_date)?></td>
				<td>
					<?php echo stripslashes($title); ?>
					<div class="group extra-options">
						<ul>
							<li><a href="<?php echo $objScaffold->Outgoings->getFolder() . $id; ?>/">View</option>
							<li><a href="<?php echo $objScaffold->Outgoings->getFolder(); ?>duplicate/<?php echo $id; ?>/">Duplicate</a></li>								
							<li><a href="<?php echo $objScaffold->Outgoings->getFolder(); ?>edit/<?php echo $id; ?>/">Edit</a></li>
							<li><a href="<?php echo $objScaffold->Outgoings->getFolder(); ?>delete/<?php echo $id; ?>/">Delete</a></li>
						</ul>
					</div>	
				</td>
				<td><a href="/outgoings/?category=<?php echo $outgoing_category?>"><?php echo $outgoing_category_title?></a></td>
				<td><?php echo currency($price)?> <span class="secondary-info"><?php echo number_format(((float)$price/(float)$objScaffold->getGrandTotal())*100, 2, '.', '')?>%</span></td>

					</tr>
		<?php
			endfor;
		?>
		</tbody>
		</table>
		<?php 
			endif; // end if	
		 ?>
	</div>
    <?php include(APPLICATION_PATH.'/views/common/sidebar_metadata.php'); ?>
<?php include($objTemplate->getFooterHTML()); ?>