<?php

/**
 *	Pagination bookings
 */

?>
<ul class="pagination">
	<li class="bookend"><a href="<?php echo $objScaffold->getFolder() . $previous_year; ?>/<?php echo $previous_month; ?>/">Previous month</a></li>
	<li class="selected"><a href="<?php echo $objScaffold->getFolder(); ?>"><?php echo $objCalendar->getMonthName() . ' ' . $objCalendar->getYear(); ?></a></li>
	<li class="bookend"><a href="<?php echo $objScaffold->getFolder() . $next_year; ?>/<?php echo $next_month; ?>/">Next month</a></li>
</ul>