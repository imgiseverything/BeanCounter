<?php

/**
 *	Home invoices
 *	Table of invoices/quotes used on the dashboard
 */


?>
<?php if($invoice_size > 0): ?>
<div class="data">
	<table class="<?php echo strtolower($invoice->getNamePlural()); ?>_table">
		<caption><?php echo $caption; ?></caption>
		<thead>
			<tr>
				<?php if($type == 'invoice'): ?>
				<th scope="col">Due date</th>
				<?php else: ?>
				<th scope="col" title="Expected due date">Due date *</th>
				<?php endif; ?>
				<th scope="col" title="Reference number">#</th>
				<?php if($objAuthorise->getLevel() == 'Superuser'): ?>
				<th scope="col">Client</th>
				<?php endif; ?>
				<th scope="col" class="value"><?php echo CURRENCY; ?></th>
			</tr>
		</thead>
		<?php if($type != 'proposal' && $invoice_size > 0): ?>
		<tfoot>
			<tr>
				<?php if($type == 'invoice'): ?>
				<td>&nbsp;</td>
				<th scope="row" colspan="2">Subtotal</th>
				<?php else: ?>
				<td colspan="2"><small>* Expected due date</small></td>
				<th scope="row">Subtotal</th>
				<?php endif; ?>
				<td><?php echo currency($total); ?></td>
			</tr>
		</tfoot>
		<?php endif; ?>
		<tbody>
		<?php
		
		// Create table rows by looping through obejct data
		for($i = 0; $i < $invoice_size; $i++):
			// create easy to use variable names
			extract($invoices[$i]);
			// If invoice is late
			
			$invoice_days = 0;
		
			$invoice_days = (int)str_replace(' days', '', DateFormat::howManyDays($payment_required));
			$invoice_class = ($project_stage == 3 && $invoice_days > 0) ? ' late' : '';	
			
		?>
		<tr class="<?php echo assignOrderClass($i, $invoice_size); ?><?php echo $invoice_class; ?>">
				<?php if($type == 'invoice'): ?>
				<td><?php echo DateFormat::getDate('ddmmyyyy', $payment_required); ?><?php if($invoice_days > 0 && (!isset($proposal) || $proposal !== true)): ?><span class="secondary-info"><?php echo $invoice_days; ?> days late</span><?php endif;?></td>
				<?php else: ?>
				<td><?php echo DateFormat::getDate('ddmmyyyy', $payment_expected); ?></td>
				<?php endif; ?>
				<?php if($objAuthorise->getLevel() == 'Superuser'): ?>
				<td>
					<a href="<?php echo $invoice->getFolder() . $id?>/" title="<?php echo stripslashes($title); ?>">#<?php echo Project::referenceNumber($id, $date_added); ?></a>
					<div class="group extra-options">
						<ul>
						<?php if($objAuthorise->getLevel() == 'Superuser'): ?>
						<li><a href="<?php echo $invoice->getFolder() . $id; ?>/">Details</a></li>
						<?php if($project_stage == 2): ?>
						<li><a href="<?php echo $invoice->getFolder() . 'invoice/' . $id; ?>/">Invoice</a></li>
						<?php endif; ?>
						<li><a href="/payments/add/?project=<?php echo $id; ?>&amp;price=<?php echo $total; ?>&amp;title=<?php echo $title; ?>">Record payment</a></li>
						<?php else: ?>
						<li><a href="/projects/download/<?php echo $id; ?>/">Print</a></li>
						<?php endif; ?>
						</ul>
					</div>
				</td>
				<td><?php echo stripslashes($client_title); ?></td>
				<td><?php echo currency($total + $total_vat); ?></td>
				<?php else: ?>
				<td><?php echo stripslashes($title); ?></td>
				<td><?php echo currency($total); ?></td>
				<?php endif; ?>
			</tr>
		<?php
		endfor;
		?>
		</tbody>
	</table>
</div>
<?php endif; ?>