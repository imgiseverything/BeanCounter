<?php

/**
 *	Projects sidebar
 */


?>
<div class="filter-form">
	<form id="filterForm" method="get" action="<?php echo $objScaffold->getFolder(); ?>" class="hidden">
    	<fieldset>
        	<fieldset class="fieldset-row">
        		<div class="field">
            		<label for="search">Find <?php echo $objScaffold->getNamePlural(); ?> by keyword:</label>
                	<input type="search" name="search" id="search" value="<?php echo $objScaffold->getSearch(); ?>" /><br />
        		</div>
            	<div class="field">
                	<label for="show">Number of items to show:</label>
                	<input type="tel" name="show" id="show" value="<?php echo $objScaffold->getPerPage(); ?>" class="int" /> <span class="help">Enter anywhere from 1 to <?php echo $objScaffold->getTotal(); ?></span>
                </div>
                <div class="field">
               	 <label for="sort">Order by:</label>
                	<select name="sort" id="sort">
						<?php echo drawDropDown($sort_options, $objScaffold->getOrderBy()); ?>
                	</select>
                </div>
        	</fieldset>
        	<fieldset class="fieldset-row">
            	<div class="field">
                	<label>Show <?php echo $objScaffold->getNamePlural(); ?> by trading year</label>
                    <ul class="year-list">
                    	<?php 
							// loop through all dates 
							for($ii = date('Y'); $ii >= $objScaffold->getFirstYear(); $ii--):
						?>
                    	<li><a href="<?php echo $objScaffold->getFolder(); ?>?start_day=06&amp;start_month=04&amp;start_year=<?php echo $ii; ?>&amp;end_day=05&amp;end_month=04&amp;end_year=<?php echo ($ii + 1); ?>&amp;show=1000"><?php echo $ii; ?>-<?php echo ($ii + 1); ?></a></li>
                        <?php endfor; // end for ?>
                    </ul>
                </div>
            	<div class="field">
                    <label for="timeframe">Or pick a timeframe:</label>
                    <select name="timeframe" id="timeframe">
                    	<?php echo drawDropDown($timeframe_options, $objScaffold->getTimeframe()); ?>
                    </select><br />
                </div>
                <div class="field">
                	<label>Or choose your own timeframe</label>
                	<fieldset class="date">
                    	<legend>Start date</legend>
                        <?php echo FormDate::getDay('start', $objScaffold->getFirstYear());?>
						<?php echo FormDate::getMonth('start',read($_GET, 'start', $objScaffold->getFirstYear()), 'text', true); ?>
                        <?php echo FormDate::getYear('start',read($_GET, 'start', $objScaffold->getFirstYear()), 2,'past', NULL, true); ?>
                    </fieldset>
                    <fieldset class="date">
                    	<legend>End date</legend>
                    	<?php
                    		$timeframeCustom = $objScaffold->getTimeframeCustom();
                    	
                    	?>
                        <?php echo FormDate::getDay('end', $timeframeCustom['end']); ?>
						<?php echo FormDate::getMonth('end', read($_GET, 'end', $timeframeCustom['end']), 'text', true); ?>
                        <?php echo FormDate::getYear('end', read($_GET, 'end', $timeframeCustom['end']), 2, 'past', NULL, true); ?>
                    </fieldset>
                </div>
            </fieldset>                
        	<button type="submit">Filter <?php echo $objScaffold->getNamePlural(); ?></button>
        </fieldset>
    </form>
</div>