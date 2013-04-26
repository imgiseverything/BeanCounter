<?php
	/**
	 *  Bookings view
	 *  View individual booking
	 */	

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'projects', 'tables', 'calendar'));
	// On page CSS
	$objTemplate->setExtraStyle('
	
	');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'ajax_pagination'));
	
	// Menus
	$objMenu->setBreadcrumb($page_title);
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo $page_title; ?></h1>
    	<p>Click a date to add a new booking starting on that day.</p>
        <?php echo $objFeedback->getFeedback(); ?>
        <div class="buttons clearfix">
        	<?php /*<a href="<?php echo $objScaffold->getFolder(); ?>add/" class="button add">Add new <?php echo $objScaffold->getName(); ?></a> */ ?>
        </div>
        <?php 
        	/**
        	 * Display bookings in a calendar format
        	 */	
       ?>
       <div class="data">
	       	<ul class="pagination">
	       		<li class="bookend"><a href="<?php echo $objScaffold->getFolder() . $previous_year; ?>/<?php echo $previous_month; ?>/"><?php echo $previous_month_name . ' ' . $previous_year; ?></a></li>
	       		<li class="selected"><a href="<?php echo $objScaffold->getFolder(); ?>"><?php echo $objCalendar->getMonthName() . ' ' . $objCalendar->getYear(); ?></a></li>
	       		<li class="bookend"><a href="<?php echo $objScaffold->getFolder() . $next_year; ?>/<?php echo $next_month; ?>/"><?php echo $next_month_name . ' ' . $next_year; ?></a></li>
	       	</ul>
	       	<p class="showing"><?php echo $objScaffold->getTotal() . ' ' . $objScaffold->getNamePlural(); ?></p>
	       	<table class="calendar">
	       		<thead>
	       			<tr>
		       			<th scope="col">Monday</th>
		       			<th scope="col">Tuesday</th>
		       			<th scope="col">Wednesday</th>
		       			<th scope="col">Thursday</th>
		       			<th scope="col">Friday</th>
		       			<th scope="col">Saturday</th>
		       			<th scope="col">Sunday</th>
	       			</tr>
	       		</thead>
	       		<tbody>
	       			<?php
	       			
	       			$date = $objScaffold->getPropertiesDate();       			
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
	       				<td id="date-<?php echo $i; ?>-<?php echo $ii; ?>" class="<?php echo $td_class?>"><?php if($empty !== true) : ?>
	       				<div class="day">
		       				<form method="post" action="<?php echo $objScaffold->getFolder(); ?>add/">
			       				<fieldset>
			       					<input type="hidden" name="date_started_day" value="<?php echo $actual_day; ?>" />
			       					<input type="hidden" name="date_started_month" value="<?php echo $objCalendar->getMonth(); ?>" />
			       					<input type="hidden" name="date_started_year" value="<?php echo $objCalendar->getYear(); ?>" />
			       					<input type="hidden" name="date_ended_day" value="<?php echo $actual_day?>" />
			       					<input type="hidden" name="date_ended_month" value="<?php echo $objCalendar->getMonth(); ?>" />
			       					<input type="hidden" name="date_ended_year" value="<?php echo $objCalendar->getYear(); ?>" />
			       					<button type="submit" title="Add new booking for <?php echo $actual_day . '/' . $objCalendar->getMonth() . '/'. $objCalendar->getYear(); ?>"><?php echo $actual_day; ?></button>
			       				</fieldset>
		       				</form>
	       				</div>
	       				<?php if(!empty($date[$actual_day])): ?>
	       				<ul class="bookings">
	       				<?php 
	       				$booking_i = 0;
	       				$bookings_size = sizeof($date[$actual_day]);
	       				
	       				foreach($date[$actual_day] as $booking): 
	       				
	       					// For personal projects show the booking type. Otherwise show the client name
	       					$title = ($booking['client_title'] == SITE_NAME) ? $booking['booking_type_title'] : $booking['client_title'];
							
	       				?>
	       					<li class="<?php echo assignOrderClass($booking_i, $bookings_size); ?> booking-<?php echo $booking['id']; ?> <?php echo $booking['class']; ?>"><a href="<?php echo $objScaffold->getFolder() .  $booking['id']; ?>/" title="<?php echo $booking['title']; ?>" style="height: <?php echo $objScaffold->getCSSHeight($booking, 15); ?>px;"><?php echo $title; ?></a></li>
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
		</div>
	</div>
<?php include($objTemplate->getFooterHTML()); ?>