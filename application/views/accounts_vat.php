<?php
	
	/**
	 *	Accounts view
	 *	View VAT costs
	 */

	// Page details
	$objTemplate->setTitle('VAT Accounts');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	$objTemplate->setExtraStyle('
	
	#yearly_figures_options .selected{font-weight: bold;}
	#YearSelectForm, 
	#YearSelectForm fieldset{
		background: transparent;
		border: none;
		padding: 0;
		width: auto;
	}
	
	#YearSelectForm label{
		margin: 0;
	}
	
	#YearSelectForm select, 
	#YearSelectForm button{
		display: inline;
		float: none;
		margin: 0;
		width: auto;
	}
	
	#YearSelectForm button{
		padding: 2px;
	}
	');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'plugins/jquery.date', 'plugins/jquery.datepicker', 'datepicker', 'plugins/jquery.form', 'ajax_filter'));
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
		<?php echo $objMenu->getBreadcrumb(); ?>
		<h1><?php echo $accounts_title; ?></h1>
		<?php
		
		// results exist
		if(!empty($properties)):
		
			$i = 1; // counter
			// Tabular/Listings
		?>
			<div class="data">
				<table id="<?php echo strtolower($objScaffold->getNamePlural()); ?>_list">
					<tbody>
						<tr>
							<th scope="row" colspan="3">Turnover (including VAT)</th>
							<td><?php echo currency($objScaffold->getProjectsTotal() + $objScaffold->getVATDue()); ?></td>
						</tr>
						<tr>
							<th scope="row" colspan="3">Turnover (excluding VAT)</th>
							<td><?php echo currency($objScaffold->getProjectsTotal()); ?></td>
						</tr>
						<tr>
							<th scope="row" colspan="3"><abbr title="Value Added Tax">VAT</abbr> Received (<?php echo VAT; ?>%)</th>
							<td><?php echo currency($objScaffold->getVATDue()); ?></td>
						</tr> 
					</tbody>
				</table>
				</div>
		<?php
		else:
			/* 
			No Results!!!
			
			Why?
			1. 	Are we on a page that isn't 1 e.g. has the user gone to a non-existent page like 
				page 36 when there's only 35 pages of data?
			2. 	Or has a user searched for data that doesn't exist?
			
			Solution:
			Tell the user (in an understandable way) why they are seeing no results
			*/
			echo '<p>There are no ' . $objScaffold->getNamePlural();
			echo ($objScaffold->getSearch()) ? ' that match your search criteria' : ' on this page';
			echo '</p>';
			
		endif;
		?>
	</div>
		<?php if($objTemplate->getMode() == 'normal'): ?>
		<div class="filter-form">
			<form id="filterForm" method="get" action="/accounts/vat/" class="hidden">
						<fieldset>
							<fieldset class="fieldset-row">	
								 <div class="field">
										<fieldset class="date">
												<legend>VAT period start date</legend>
													<?php echo FormDate::getDay('start', read($_GET, 'start_day', $tax_start_day)); ?>
						<?php echo FormDate::getMonth('start', read($_GET, 'start_month', $tax_start_month),'text',true); ?>
													<?php echo FormDate::getYear('start', read($_GET, 'start_year', $tax_start_year), 2, 'past', NULL, true); ?>
											</fieldset>
								 	</div>
								<div class="field">
											<fieldset class="date">
												<legend>VAT period end date</legend>
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
<?php include($objTemplate->getFooterHTML()); ?>