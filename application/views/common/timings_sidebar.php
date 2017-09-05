<?php

/**
 *	Timings sidebar
 */


?>
<div class="filter-form">
	<form id="filterForm" method="get" action="<?php echo $objScaffold->getFolder(); ?>" class="hidden">
    	<fieldset>
	    	<fieldset class="fieldset-row">
    			<?php /*<div class="field">
	    			<label for="timing_tag">Choose a tag:</label>
	                <select name="tag" id="timing_tag">
	                	<?php echo drawDropDown(getDropDownOptions('timing_tag', 'Choose'), $objScaffold->getTag()); ?>
	                </select>
	            </div> */ ?>
    			<div class="field">
	    			<label for="client">Choose a client:</label>
	                <select name="client" id="client">
	                	<?php echo drawDropDown(getDropDownOptions('client', 'Choose'), $objScaffold->getClient()); ?>
	                </select>
	            </div><?php /*
    			<div class="field">
	    			<label for="project">Choose a project:</label>
	                <select name="project" id="project">
	                	<?php echo drawDropDown(getDropDownOptions('project', 'Choose'), $objScaffold->getProject()); ?>
	                </select>
	            </div> */ ?>
    		</fieldset>
        	<fieldset class="fieldset-row">
            	<div class="field">
                    <label for="timeframe">Pick a timeframe:</label>
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
