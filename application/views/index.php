<?php

	/**
	 *	Dashboard/homepage/control panel view
	 */
	
	// Page details
	$objTemplate->setTitle('Dashboard');
	$objTemplate->setBodyClass('home');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('tables', 'calendar', 'home')); // must be an array
	$objTemplate->setExtraStyle();
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter')); // must be an array
	$objTemplate->setExtraBehaviour();
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
	<?php
        // Show last invoiced projects
        if($objAuthorise->getLevel() == 'Superuser'):
        
        	if(isset($objInvoicedProjects) && $objInvoicedProjects->getOutstandingInvoices()):
	        	
	        	$type = 'invoice';
	        	$invoice = $objInvoicedProjects;
	        	$invoices = $invoice->getOutstandingInvoices();
	        	$invoice_size = sizeof($invoices);
	        	$total = $invoice->getOutstandingBalance();
	        	
	        	$name = (sizeof($invoices) == 1) ? $invoice->getName() : $invoice->getNamePlural();
	        	
	        	$caption =  'You currently have ' . sizeof($invoices) . ' ' . $name . ' awaiting payment.';
	        	include(APPLICATION_PATH . '/views/common/home_invoices.php');
        	
        	endif;
        	
        	if(isset($objStartedProjects) && $objStartedProjects->getProperties()):
	        	
	        	$type = 'ongoing';
	        	$invoice = $objStartedProjects;
	        	$invoices = $invoice->getProperties();
	        	$invoice_size = sizeof($invoices);
	        	$total = $invoice->getGrandTotal();
	        	$name = (sizeof($invoices) == 1) ? $invoice->getName() : $invoice->getNamePlural();
	        	$caption = 'You are currently working on ' . sizeof($invoices) . ' ' . $name . '.';
	        	include(APPLICATION_PATH . '/views/common/home_invoices.php');
        	
        	endif;
        	
        	
        	
        	if(isset($objGreenLitProjects) && $objGreenLitProjects->getProperties()):
	        	
	        	$type = 'ongoing';
	        	$invoice = $objGreenLitProjects;
	        	$invoices = $invoice->getProperties();
	        	$invoice_size = sizeof($invoices);
	        	$total = $invoice->getGrandTotal();
	        	$name = (sizeof($invoices) == 1) ? $invoice->getName() : $invoice->getNamePlural();
	        	$caption = 'You have ' . sizeof($invoices) . ' ' . $name . ' due to start.';
	        	include(APPLICATION_PATH . '/views/common/home_invoices.php');
        	
        	endif;
        	
        	
        	
        	
        	if($objStartedProjects->getProperties() === null && $objInvoicedProjects->getOutstandingInvoices() == null && $objGreenLitProjects->getProperties() === null):
        	?>
        	<h2>Welcome to Bean Counter</h2>
        	<p>Manage your online work. Create and send quotes (proposals). File and send invoices (projects).</p>
        	<p><a href="/projects/add/" class="button add">Add a new project</a></p>
        	<?php
        	endif;
        	
        endif;
        ?>    
	</div>
	<div id="SecondaryContent">
<?php if($objAuthorise->getLevel() == 'Superuser'): ?>
		<table class="calendar calendar-mini">
			<caption>This month&#8217;s <a href="<?php echo $objBooking->getFolder(); ?>" title="View in more detail">bookings</a></caption>
	       		<thead>
	       			<tr>
		       			<th scope="col">M</th>
		       			<th scope="col">T</th>
		       			<th scope="col">W</th>
		       			<th scope="col">T</th>
		       			<th scope="col">F</th>
		       			<th scope="col">S</th>
		       			<th scope="col">S</th>
	       			</tr>
	       		</thead>
	       		<tbody>
	       			<?php
	       			
	       			$date = $objBooking->getPropertiesDate();  
 			
	       			$weeks = $objCalendar->getWeeks();
	       			
	       			
	       			$days_i = 1;
	       			for($i = 0; $i < $weeks; $i++):
	       			?>
	       			<tr class="<?php echo assignOrderClass($i, $weeks); ?>">
	       				<?php 
	       				
	       				for($ii = 1; $ii < 8; $ii++):
	       				
	       				// Actual day
	       				$actual_day = $days_i-($objCalendar->getStartWeekDay() - 1);
	       				
	       				// is date empty?
	       				$empty = (($days_i < $objCalendar->getStartWeekDay() || $actual_day > $objCalendar->getDays())) ? true : false;
	       				$td_class = ($empty === true) ? 'empty' : 'date';
	       				       				
	       				?>
	       				<td id="date-<?php echo $i . '-' . $ii; ?>" class="<?php echo $td_class?>"><?php if($empty !== true) : ?>
	       				<div class="day">
		       				<form method="post" action="<?php echo $objBooking->getFolder(); ?>add/">
			       				<fieldset>
			       					<input type="hidden" name="date_started_day" value="<?php echo $actual_day; ?>" />
			       					<input type="hidden" name="date_started_month" value="<?php echo $objCalendar->getMonth(); ?>" />
			       					<input type="hidden" name="date_started_year" value="<?php echo $objCalendar->getYear(); ?>" />
			       					<input type="hidden" name="date_ended_day" value="<?php echo $actual_day?>" />
			       					<input type="hidden" name="date_ended_month" value="<?php echo $objCalendar->getMonth(); ?>" />
			       					<input type="hidden" name="date_ended_year" value="<?php echo $objCalendar->getYear(); ?>" />
			       					<button type="submit" title="Add new booking for this day"><?php echo $actual_day; ?></button>
			       				</fieldset>
		       				</form>
	       				</div>
	       				<?php if(!empty($date[$actual_day])): ?>
	       				<ul class="bookings">
	       				<?php 
	       				$booking_i = 0;
	       				$bookings_size = sizeof($date[$actual_day]);
	       				
	       				
	       				foreach($date[$actual_day] as $booking): 
	       					$height = $objBooking->getCSSHeight($booking, 5);
	       					
	       				?>
	       					<li class="<?php echo assignOrderClass($booking_i, $bookings_size); ?> booking-<?php echo $booking['id']; ?> <?php echo $booking['class']; ?>"><a href="<?php echo $objBooking->getFolder() . 'edit/' . $booking['id']; ?>/" title="<?php echo $booking['client_title']; ?> (<?php echo $booking['booking_type_title']; ?>)" style="height: <?php echo $height; ?>px;"><span class="off-screen"><?php echo $booking['client_title']; ?></span></a></li>
	       				<?php 
	       				$booking_i++;
	       				endforeach; 
	       				?>
	       				</ul>
	       				<?php endif;?>
	       				<?php else: echo '&nbsp;'; endif; ?></td>
	       				<?php 
	       				$days_i++;
	       				endfor; ?>
	       			</tr>
	       			<?php
	       				//$days_i += 7;
	       			endfor;
	       			?>
	       		</tbody>
	       	</table>

<?php endif; ?>	
	</div>
<?php include($objTemplate->getFooterHTML()); ?>