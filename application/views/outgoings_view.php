<?php

	/**
	 *	Outgoings (expenses) view
	 *  View individual outgoing
	 */	

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('tables', 'colorbox'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'colorbox'));
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
    	<?php echo $objMenu->getBreadcrumb(); ?>
        <?php echo $objFeedback->getFeedback(); ?>
		<?php
			
			// Start output buffer - we'll use this content to create a cache and in turn a downloadable file
			ob_start(); // Turn on output buffering
		?>		
    	<h1><span class="type"><?php echo $outgoing_category_title; ?> (#<?php echo Project::referenceNumber($id, $transaction_date); ?>)</h1> 	
    	<p><?php echo DateFormat::getDate('date', $transaction_date); ?></p>
		 <div id="ClientDetails">
			<h2>For the attention of:</h2>
			<?php
			// Get Client Vcard
			echo $objVcardSupplier->getVcard();
			?>
		</div>
		<div class="data">
			<table>
				<caption>Details</caption>
				<thead>
					<tr>
						<th scope="col">Item</th>
						<th scope="col"><?php echo CURRENCY; ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="row">VAT (included):</th>
						<td><?php echo currency($vat); ?></td>
					</tr>
					<tr>
						<th scope="row">Total:</th>
						<td><?php echo currency($price); ?></td>
					</tr>
				</tfoot>
				<tbody>
					<tr>
						<td><?php echo stripslashes($title); ?><?php echo (!empty($transaction_id) && $transaction_id != ' ') ? ' (Reference: ' . $transaction_id . ')' : ''; ?><?php echo  ($description) ? '<br />' . $objTextile->TextileThis($description) : ''; ?></td>
						<td><?php echo currency($price); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
			$page_content = ob_get_clean();		
			echo $page_content;	
			
			// Cache content for emailing
			$objCache = new Cache($cache_filename, 1, $objScaffold->getName());
			$objCache->createCache($page_content, false);
		?>
		<?php if(!empty($outgoing_documentation)): ?>
		<h2>Documentation</h2>
		<table>
			<thead>
				<tr>
					<th scope="col">File name</th>
					<th scope="col">File type</th>
					<th scope="col">File size</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($outgoing_documentation as $document): ?>
			<tr>
				<td><a href="<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', $document['filename']); ?>" target="_blank" download><?php echo $document['title']; ?></a></td>
				<td><?php echo $document['mimetype']; ?></td>
				<td><?php echo Upload::convertBytes($document['filesize']); ?></td>
			</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
	</div>
   	<div id="SecondaryContent">
   		<h2>Relevant dates</h2>
		<p>Date added: <?php echo DateFormat::getDate('date', $date_added); ?> <em><?php echo DateFormat::howManyDays($date_added); ?> ago</em><br />
Date edited: <?php echo  (!empty($date_edited)) ? DateFormat::getDate('date', $properties['date_edited']) . ' <em>' . DateFormat::howManyDays($date_edited) . ' ago</em>' : 'N/A'; ?></p>
		<div id="Options">
			<h2>Options</h2>
			<ul>
				<li><a href="<?php echo $objScaffold->getFolder(); ?>remittance/<?php echo $objScaffold->getId(); ?>/" class="invoice">Send remittance advice email</a></li>
				<li><a href="<?php echo $objScaffold->getFolder(); ?>documentation/add/?outgoing=<?php echo $objScaffold->getId(); ?>" class="duplicate">Add a new piece of documentation</a></li>
				<li><a href="<?php echo $objScaffold->getFolder(); ?>duplicate/<?php echo $objScaffold->getId(); ?>/" class="duplicate">Duplicate this <?php echo $objScaffold->getName(); ?></a></li>
				<li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $objScaffold->getId(); ?>/" class="edit">Edit this <?php echo $objScaffold->getName(); ?></a></li>
				<li><a href="<?php echo $objScaffold->getFolder(); ?>delete/<?php echo $objScaffold->getId(); ?>/" class="negative">Delete this <?php echo $objScaffold->getName(); ?></a></li>
			</ul>
			<h3>More</h3>
			<ul>
				<li><a href="<?php echo $objScaffold->getFolder(); ?>?supplier=<?php echo $outgoing_supplier; ?>">View all <?php echo stripslashes($outgoing_supplier_title); ?> <?php echo $objScaffold->getNamePlural(); ?></a></li>
				<li><a href="<?php echo $objScaffold->getFolder(); ?>?category=<?php echo $outgoing_category; ?>">View all <?php echo $objScaffold->getNamePlural(); ?> in this category</a></li>
			</ul>
			
		</div>
   	</div>
<?php include($objTemplate->getFooterHTML()); ?>