<?php

	/**
	 *	Projects view
	 *  Download project
	 */
	
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	
	// when is the invoice due?
	$due_date = (!empty($properties['payment_required'])) ? strtotime($properties['payment_required']) : strtotime('+15 days', strtotime($properties['invoice_date']));
	
?>
<!DOCTYPE html>
<html dir="ltr" lang="en-GB">
<head>
<meta charset="UTF-8" />
<meta name="robots" content="noindex,nofollow" />
<title><?php echo SITE_NAME . ' | #' . Project::referenceNumber($properties['id'], $properties['date_added']); ?></title>
<style type="text/css" media="all">
<?php echo reduceFileSize($objDownload->getStyle()); ?>
</style>
<link rel="stylesheet" media="print" href="http://<?php echo $objApplication->getSiteUrl(); ?>/style/print.css" />
</head>
<body class="download invoice">
<div class="download-container">
	<div id="Download">
		<p><a href="http://<?php echo $objApplication->getSiteUrl(); ?><?php echo $objScaffold->getFolder(); ?>pdf/<?php echo md5(SECRET_PHRASE . $objScaffold->getId()); ?>/">Download this document as a PDF (beta)</a></p>
	</div>
<div id="Container">
	<div id="Header">
		<div class="date">
		<?php
		 // If the invoice is fully paid up: state when the invoice was paid
		 // if money is owed: state the date that the invoice was (first) sent
		 // otherwise, show today's date
		
		 if($properties['completed'] === true):
		 	$last_payment = end($properties['project_payment']);
			echo '<strong>PAID:</strong> ' . DateFormat::getDate('date', $last_payment['transaction_date']);
		 elseif($properties['project_stage'] == 3):
		 	echo DateFormat::getDate('date', $properties['invoice_date']);
		 else:
		 	echo DateFormat::getDate('date', date('Y-m-d'));
		 endif;
		?>
		</div>
		
		<?php echo $objTemplate->getBranding(); ?>
		<?php echo (!empty($sender_address)) ? '<div class="vcard">' . $sender_address . '</div>' : $objVcard->getVcard(); ?>
	</div>
	<div id="PrimaryContent">
	<?php
		// create easy to use variables
		extract($properties);
		// Start output buffer - we'll use this content to create a cache and in turn a downloadable file
		ob_start(); // Turn on output buffering
		
	?>
	<h1><span class="type"><?php echo (strtolower($project_stage_title) == 'proposal') ? 'Proposal' :  'Invoice'; ?></span> <?php echo stripslashes($title); ?> (ref: <?php echo Project::referenceNumber($id, $date_added); ?>)</h1>
	<?php if(!empty($clients_reference_number)): ?>
	<p><strong>Client reference number:</strong> <?php echo $clients_reference_number; ?></p>
	<?php endif; ?>
	<?php if($properties['project_stage'] == 3): ?>
	<p class="due">Due for payment on&nbsp;<?php echo DateFormat::getDate('date', date('Y-m-d', $due_date)); ?></p>
	<?php endif; ?>
	<div id="ClientDetails">
		<h2 class="fao">For the attention of:</h2>
		<?php echo (!empty($for_the_attention_of)) ? '<div class="vcard">' . stripslashes($for_the_attention_of) . '</div>' : $objVcardClient->getVcard(); ?>
	</div>
	<?php if(!empty($description)): ?>
	<div id="Appendix">
		<h2>Project details</h2>
		<div class="details">
		<?php echo validateContent($objScaffold->invoiceClean($description)); ?>
		</div>
	</div>
	<?php endif; ?>
	<?php if(!empty($project_task)): ?>
	<table>
		<caption>Breakdown</caption>
		<thead>
			<tr>
				<th scope="col">Task</th>
				<th scope="col"><?php echo CURRENCY; ?></th>
		</thead>
		<tfoot>
			<?php if($requires_deposit == 'Y'): // is a deposit required? ?>
			<tr>
				<th scope="row">Deposit</th>
				<td colspan="2"><?php echo currency($objScaffold->getGrandTotal()/2); ?></td>
			</tr>
			<tr>
				<th scope="row">Final payment</th>
				<td colspan="2"><?php echo currency($objScaffold->getGrandTotal() / 2); ?></td>
			</tr>
			<?php endif; // end if deposit required ?>
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
				<td colspan="2"><?php echo currency($objScaffold->getGrandTotal()); ?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php if(!empty($project_task)): foreach($project_task as $task): ?>
			<tr>
				<td><?php echo stripslashes($task['title']); ?><br />
				<?php echo $objScaffold->invoiceClean($task['description']); ?>
				</td>
				<td><?php echo currency($task['price']); ?></td>
			</tr>
			<?php endforeach; endif; ?>
			<?php if(!empty($project_discount)): foreach($project_discount as $discount): ?>
			<tr>
				<td><?php echo stripslashes($discount['title']); ?><br />
				<?php echo $objScaffold->invoiceClean($discount['description']); ?>
				</td>
				<td>-<?php echo currency($discount['price']); ?></td>
			</tr>
			<?php endforeach; endif; ?>
		</tbody>
	</table>
	<?php if(($project_stage <= 3 || $project_stage == 5) && $completed !== true): ?>
	<div id="PaymentDetails">
		<h2>Payment</h2>
		<?php echo validateContent($appendix); ?>		
		<?php if($project_stage > 1): // show payment details if not a quote ?>
		<h3>Bank transfer details</h3>
		<p><abbr title="Account number">A/C</abbr>:&nbsp;<strong><?php echo BANK_AC; ?></strong><br />
		<abbr title="Sort code">S/C</abbr>:&nbsp;<strong><?php echo BANK_SC; ?></strong></p>
		<?php if(IBAN): ?>
		<p>or</p>
		<p><abbr title="International Bank Account Number">IBAN</abbr>:&nbsp;<strong><?php echo IBAN; ?></strong></p>
		<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	<?php else: ?>
	<p><em><?php echo stripslashes(ucwords($title)); ?></em> has no associated tasks.</p>
	<?php endif; ?>
</div>
<?php
	// Put HTML into buffer
	$page_content = ob_get_clean();	
	$page_content = reduceFileSize($page_content);	
	echo $page_content;	
	
	$cached_file = SITE_PATH . 'cache/' . $objScaffold->getName() . '/download-' . md5(SECRET_PHRASE . $objScaffold->getId()) . '.html';
	
	
	if(CACHE === true){
		// Cache content for email invoicing
		$objCache = new Cache($cache_filename, 1, $objScaffold->getName());
		$objCache->createCache($page_content, false, false);
	} else{
		$handle = fopen($cached_file, "w");

		if($handle){
			fwrite($handle, $page_content);
			fclose($handle);
		} 
	}
	
	
	// Footer
	include($objApplication->getViewFolder() . 'layout/footer_download.php'); 
?>