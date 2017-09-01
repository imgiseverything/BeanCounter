<?php

/**
 *	Sidebar
 *	Generic sidebar content - used in most object_all.php views
 *	Contains a form to filter the data in the main content block
 */

if($objTemplate->getMode() == 'normal'):
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
									<input type="tel" name="show" id="show" value="<?php echo min($objScaffold->getTotal(), $objScaffold->getPerPage()); ?>" class="int" /> <span class="help">Enter anywhere from 1 to <?php echo $objScaffold->getTotal(); ?></span>
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
								<label for="timeframe">Choose a timeframe:</label>
								<select name="timeframe" id="timeframe">
									<?php echo drawDropDown($timeframe_options, $objScaffold->getTimeframe()); ?>
								</select>
							</div>
								 <div class="field">
									<label>Or choose a custom timeframe</label>
									<fieldset class="date">
											<legend>Start date</legend>
												<?php echo FormDate::getDay('start', read($_GET, 'start_day', $tax_start_day)); ?>
					<?php echo FormDate::getMonth('start', read($_GET, 'start_month', $tax_start_month),'text',true); ?>
												<?php echo FormDate::getYear('start', read($_GET, 'start_year', $tax_start_year), 2, 'past', NULL, true); ?>
										</fieldset>
										<fieldset class="date">
											<legend>End date</legend>
											<?php
												// timeframe
												$timeframeCustom = $objScaffold->getTimeframeCustom();
											?>
												<?php echo FormDate::getDay('end', read($_GET, 'end_day', read($timeframeCustom, 'end', $tax_end_day))); ?>
					<?php echo FormDate::getMonth('end', read($_GET, 'end_month', read($timeframeCustom, 'end', $tax_end_month)), 'text', true); ?>
												<?php echo FormDate::getYear('end', read($_GET, 'end_year', read($timeframeCustom, 'end', $tax_end_year)), 2, 'future', NULL, true); ?>
										</fieldset>
								</div>
						</fieldset>
			<button type="submit">Filter <?php echo $objScaffold->getNamePlural(); ?></button>
		</fieldset>
	 </form>
</div>
<?php endif; ?>
