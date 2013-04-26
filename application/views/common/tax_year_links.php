<?php

/**
 *	Bean Counter
 *	Views
 *	Tax year links
 */
 
?>
<ul id="YearlyFiguresOptions" class="grid-list">
<?php  

	// loop through all tax (trading) years
	for($ii = $tax_end_year; $ii > $objScaffold->getFirstTradingYear(); $ii--):
		$class = (read($_GET, 'end_year', '') == $ii) ? ' class="selected"' : '';
?>
	<li<?php echo $class; ?>><a href="<?php echo $objScaffold->getFolder(); ?>
	?start_day=<?php echo $tax_start_day; ?>
	&amp;start_month=<?php echo $tax_start_month; ?>
	&amp;start_year=<?php echo ($ii - 1); ?>
	&amp;end_day=<?php echo $tax_end_day; ?>
	&amp;end_month=<?php echo $tax_end_month; ?>
	&amp;end_year=<?php echo $ii; ?>
	&amp;show=1000"><?php echo ($ii - 1); ?>-<?php echo $ii; ?></a></li>
<?php 
    endfor;
?>
</ul>