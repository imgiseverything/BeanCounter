<?php

	/**
	 *	Projects view
	 *  View one project
	 */
	 
	
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'colorbox'));
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
        <?php echo $objFeedback->getFeedback(); ?>
		<?php
			
			// Start output buffer - we'll use this content to create a 
			// cache and in turn a downloadable file
			ob_start(); // Turn on output buffering
		?>
    	<h1><span class="type"><?php echo (strtolower($project_stage_title) == 'proposal') ? 'Proposal' :  'Invoice'; ?></span> <?php echo stripslashes($title); ?> (ref: <?php echo Project::referenceNumber($id, $date_added); ?>)</h1>
		<!--NOT-IN-INVOICE--><p class="stage <?php echo $stage; ?>">Stage: <?php echo ucfirst($stage); ?></p><!--END-NOT-IN-INVOICE-->
		<?php if(!empty($clients_reference_number)): ?>
		<p><strong>Client reference number:</strong> <?php echo $clients_reference_number; ?></p>
		<?php endif; ?>
		<div id="ClientDetails">
			<h2>For the attention of:</h2>
			<?php echo stripslashes(nl2br($for_the_attention_of)); ?>
		</div>
		<?php if(!empty($description)): ?>
		<div id="ProjectDetails">
			<h2>Project details</h2>
			<div class="details">
				<?php echo $objScaffold->invoiceClean($description); ?>
			</div>
		</div>
		<?php endif; ?>
		<?php if(!empty($project_task) || !empty($project_discount)): ?>
		<div class="data">
			<table>
				<caption>Breakdown</caption>
				<thead>
					<tr>
						<th scope="col">Task</th>
						<th scope="col"><?php echo CURRENCY; ?></th>
					</tr>
				</thead>
				<tfoot>
					<?php if($objScaffold->getVATTotal()): ?>
					<tr>
						<th scope="row">Subtotal</th>
						<td><?php echo currency($objScaffold->getSubtotal()); ?></td>
					</tr>
					<tr>
						<th scope="row"><abbr title="Value Added Tax">VAT</abbr></th>
						<td><?php echo currency($objScaffold->getVATTotal()); ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<th scope="row">Total</th>
						<td><?php echo currency($objScaffold->getGrandTotal()); ?></td>
					</tr>
				</tfoot>
				<tbody>
					<?php 
					// Append itemised 'tasks' to the invoice
					if(!empty($project_task)):
						foreach($project_task as $task):  
					?>
					<tr>
						<td>
						<?php echo stripslashes($task['title']); ?><br />
						<?php echo $objScaffold->invoiceClean($task['description']); ?>
						<?php if($objAuthorise->getLevel() && $objAuthorise->getLevel() == 'Superuser'): ?>
						<!--NOT-IN-INVOICE--><div class="group extra-options">
						<ul>
							<li><a href="/tasks/duplicate/<?php echo $task['id']; ?>/">Duplicate</a></li>
							<li><a href="/tasks/edit/<?php echo $task['id']; ?>/">Edit</a></li>
							<li><a href="/tasks/delete/<?php echo $task['id']; ?>/">Remove</a></li>
						</ul>
						</div><!--END-NOT-IN-INVOICE-->
						<?php endif; ?>
						</td>
						<td><?php echo currency($task['price'] + read($task, 'vat', 0)); ?></td>
					</tr>
					<?php 
						endforeach; 
						endif;
					?>
					<?php 
					// Append discounts (if any) to the invoice
					if(!empty($project_discount)):
						foreach($project_discount as $discount):  
					?>
					<tr class="negative">
						<td>
							<?php echo stripslashes($discount['title']); ?><br />
							<?php echo $objScaffold->invoiceClean($discount['description']); ?>
							
							<?php if($objAuthorise->getLevel() && $objAuthorise->getLevel() == 'Superuser'): ?>
							<!--NOT-IN-INVOICE--><div class="group extra-options">
							<ul>
								<li><a href="/discounts/duplicate/<?php echo $discount['id']; ?>/">Duplicate</a></li>
								<li><a href="/discounts/edit/<?php echo $discount['id']; ?>/">Edit</a></li>
								<li><a href="/discounts/delete/<?php echo $discount['id']; ?>/">Remove</a></li>
							</ul>
							</div><!--END-NOT-IN-INVOICE-->
							<?php endif; ?>				
						</td>
						<td>-<?php echo currency(stripslashes($discount['price'])); ?></td>
					</tr>
					<?php 
						endforeach; 
						endif;
					?>
				</tbody>
			</table>
		</div>
		<div id="PaymentDetails">
			<h2>Payment</h2>
			<?php echo validateContent($appendix); ?>
			<?php if($project_stage > 1): // show payment details if not a quote ?>
			<h3>Bank account details</h3>
			<p><abbr title="Account number">A/C</abbr>: <?php echo BANK_AC; ?><br />
			<abbr title="Sort code">S/C</abbr>: <?php echo BANK_SC; ?></p>
			<?php if(IBAN): ?>
			<p>or</p>
			<p><abbr title="International Bank Account Number">IBAN</abbr>:<?php echo IBAN; ?></p>
			<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php else: ?>
		<p>This <?php echo strtolower($objScaffold->getName()); ?> has no associated tasks.</p>
		<?php endif; ?>
		<?php
			// Put HTML into buffer
			$page_content = ob_get_clean();
			// Clean out irrelevant data
			$page_content = str_replace(array("\t", "\r", "\n"), '', $page_content);
			$pattern = "/<!--NOT-IN-INVOICE-->[a-zA-Z0-9<>\"-_ ]+<!--END-NOT-IN-INVOICE-->/";
			$page_content_cached = preg_replace($pattern, '', $page_content);
			
			echo $page_content;
						
			// create a cached file for use as email invoice
			$objCache = new Cache($cache_filename, 1, $objScaffold->getName());
			$objCache->createCache($page_content_cached, false);
		?>	
	</div>
<?php if($objTemplate->getMode() != 'ajax'): ?>
<div class="content-secondary">
	<?php if($project_stage > 1): ?>
	<div id="legend">
		<h2>Money</h2>
		<p><strong>Owed:</strong> <?php echo currency($outstanding); ?><br />
		<strong>Paid:</strong> <?php echo currency($paid); ?></p>
	</div>
	<?php endif; ?>
	<h2>Relevant dates</h2>
	<p>Date added: <?php echo DateFormat::getDate('date', $date_added); ?> <em><?php echo DateFormat::howManyDays($date_added); ?> ago</em><br />
Date edited: <?php echo (!empty($date_edited) && $date_edited != '0000-00-00 00:00:00') ? DateFormat::getDate('date', $date_edited) . ' <em>' . DateFormat::howManyDays($date_edited) . ' ago</em>' : 'N/A'; ?></p>
	<div id="Options">
		<h2>Options</h2>
		<ul>
	<?php if($project_stage > 1 && $completed !== true && $objAuthorise->getLevel() && $objAuthorise->getLevel() == 'Superuser'): /* Only projects at least stage 2('Started') can be invoiced */ ?>
			<li><a href="<?php echo $objScaffold->getFolder(); ?>invoice/<?php echo $objScaffold->getId(); ?>/" class="invoice">Send invoice</a>
			<?php elseif($project_stage == '1' && $objAuthorise->getLevel() && $objAuthorise->getLevel() == 'Superuser'): /* Only projects at least stage 1 can have quotes sent */ ?>
			<li><a href="<?php echo $objScaffold->getFolder(); ?>quote/<?php echo $objScaffold->getId(); ?>/" class="invoice">Send quote</a></li>
			<?php endif; // end if ?>
			<li><a href="<?php echo $objScaffold->getFolder(); ?>download/<?php echo md5(SECRET_PHRASE . $objScaffold->getId()); ?>/" class="print">View printable version</a></li>
			<?php if($objAuthorise->getLevel() && $objAuthorise->getLevel() == 'Superuser'): /* Only superusers can edit/delete projects */ ?>
			<?php if($completed !== true): ?>
			<li><a href="/tasks/add/?project=<?php echo $objScaffold->getId(); ?>" class="discount">Add a task</a></li>
			<li><a href="/discounts/add/?project=<?php echo $objScaffold->getId(); ?>" class="discount">Add a discount</a></li>
			<?php endif; ?>
			<?php if($completed !== true && $project_stage > 1): ?>
			<li><a href="/payments/add/?project=<?php echo $id; ?>&amp;price=<?php echo $outstanding; ?>&amp;title=<?php echo urlencode($title); ?>">Record a client payment</a></li>
			<?php endif; ?>
			<li><a href="<?php echo $objScaffold->getFolder(); ?>duplicate/<?php echo $objScaffold->getId(); ?>/" class="duplicate">Duplicate this <?php echo $objScaffold->getName(); ?></a></li>
			<li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $objScaffold->getId(); ?>/" class="edit">Edit this <?php echo $objScaffold->getName(); ?></a></li>
			<li><a href="<?php echo $objScaffold->getFolder(); ?>delete/<?php echo $objScaffold->getId(); ?>/" class="negative">Delete this <?php echo $objScaffold->getName(); ?></a></li>
			<?php endif; // end if ?>
			<li><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></li>
		</ul>
	</div>
</div>
<?php endif; ?>
<?php include($objTemplate->getFooterHTML()); ?>