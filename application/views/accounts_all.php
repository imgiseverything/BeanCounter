<?php

	/**
	 *	Month by month accounts (Profit/Loss) View
	 *  View profit/loss data for user-defined periods of time
	 */

	// Page details
	$objTemplate->setTitle(ucfirst($objScaffold->getNamePlural()));
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	
	$objTemplate->setExtraStyle('
	ul#yearly_figures_options li.selected{font-weight: bold;}
	th.positive, td.positive{
		
	}
	th.negative, td.negative{
		
	}
	
	thead th, 
	tbody th,
	td{
		text-align: right;
	}
	
	div#graphs{
		text-align: center;
	}
	
	.ct-chart{
		height: 600px;
		margin-top: 60px;
	}
	
	@media screen and (max-width: 767px){
		.ct-chart{
			height: 300px;
			margin-top: 30px;
		}
	}
	
	.ct-point, .ct-line{
		opacity: 0.25;
	}
	
	.ct-chart .ct-series.ct-series-a .ct-point,
	.ct-chart .ct-series.ct-series-a .ct-line{ stroke: #0082ff; opacity: 1; }
	
	.ct-chart .ct-series.ct-series-b .ct-point,
	.ct-chart .ct-series.ct-series-b .ct-line{ stroke: #66cc33;  }
	.ct-chart .ct-series.ct-series-c .ct-point,
	.ct-chart .ct-series.ct-series-c .ct-line{ stroke: #ff3333; }
	
	.ct-chart .ct-series.ct-series-d .ct-point,
	.ct-chart .ct-series.ct-series-d .ct-line{ stroke: #666; }
	
	');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'ajax_filter', 'chartist')); // must be an array
	$profit = $loss = $chart_months = $average = array();
	foreach($months as $key => $value){
   		$profit[] = ceil($objScaffold->getMonthlyProfit($key));
   		$loss[] = ceil(str_replace('-', '', $objScaffold->getMonthlyLoss($key)));
   		$subtotals[] = ceil($objScaffold->getMonthlySubtotal($key));
   		$chart_months[] = date('M Y', strtotime(trim($key . '-01 00:00:00')));
   		$average[] = ceil($objScaffold->getProjectsTotal() / $months_size);
	}

   $objTemplate->setExtraBehaviour("
	var data = {
		labels: ['" . join("','", array_reverse($chart_months)) . "'],
		series: [
			[" . join(',', array_reverse($subtotals)) . "],
			[" . join(',', array_reverse($profit)) . "],
			[" . join(',', array_reverse($loss)) . "],
			[" . join(',', $average) . "]
		]
	};
	
	var options = {};
	var responsiveOptions = {};
	
	Chartist.Line('.ct-chart', data, options, responsiveOptions);  
   ");
   
   
	$objTemplate->setSubmenu('accounts');
	
	
	
	$objMenu->setBreadcrumb('Monthly figures');
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<?php if($action != 'download'): ?>
    	<a href="<?php echo $objScaffold->getFolder(); ?>download/" class="button-add button-download" download><span></span>Download these transactions</a>
    	<?php endif; ?>
    	<h1><?php echo $accounts_title; ?></h1>
			<div id="VisualData" class="ct-chart" style="clear:both;">
				&nbsp;
			</div>
			<div class="data">
                <table id="<?php echo strtolower($objScaffold->getNamePlural()); ?>_table">
                    <thead>
                        <tr>
                            <th scope="col">Month</th>
                            <th scope="col">Revenue</th>
                            <th scope="col">Expenses</th>
                            <th scope="col">Profit/Loss</th>
                        </tr>
                    </thead>
                    <tfoot>
                    <?php if($months_size > 0): ?>
                    	<tr>
							<th scope="row">Averages</th>
							<th scope="col" class="positive"><?php echo currency($objScaffold->getProjectsTotal() / $months_size); ?></th>
							<th scope="col" class="negative"><span class="minus">-</span><?php echo currency($objScaffold->getOutgoingsTotal() / $months_size); ?></th>
							<th scope="col"><?php echo currency($objScaffold->getSubtotal() / $months_size); ?></th>
                        </tr>
                     <?php endif; ?>
                    	<tr>
							<th scope="row">Totals</th>
							<th scope="col" class="positive"><?php echo currency($objScaffold->getProjectsTotal()); ?></th>
							<th scope="col" class="negative"><span class="minus">-</span><?php echo currency($objScaffold->getOutgoingsTotal()); ?></th>
							<th scope="col"><?php echo currency($objScaffold->getSubtotal()); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
						<?php
						
						/**
						 *	Loop through all the months since we started trading
						 *	and show the incomings/outgoings and profit/loss column to boot
						 *
						 */
						foreach($months as $key => $value):
							$subtotal_class = (strpos($objScaffold->getMonthlySubtotal($key), '-') === false) ? 'positive' : 'negative';
							$nice_name = date('M Y', strtotime(trim($key . '-01 00:00:00')));
						?>
							<tr>
								<th scope="row"><?php echo $nice_name; ?></th>
								<td class="positive"><?php echo currency($objScaffold->getMonthlyProfit($key)); ?></td>
								<td class="negative"><span class="minus">-</span><?php echo currency($objScaffold->getMonthlyLoss($key)); ?></td>
								<td class="<?php echo $subtotal_class; ?>"><?php echo ($objScaffold->getMonthlySubtotal($key) < 0) ? '<span class="minus">-</span>' : ''; echo currency($objScaffold->getMonthlySubtotal($key)); ?></td>
							</tr>
						<?php
						endforeach;
						?>
                    </tbody>
                </table>
            </div>
	</div>
<?php if($objTemplate->getMode() == 'normal'): ?>
    <div class="filter-form">
    	<form id="filterForm" method="get" action="<?php echo $objScaffold->getFolder(); ?>" class="hidden">
            <fieldset>
                <fieldset class="fieldset-row">  
                	
            		<div class="field">
            			<label>Show <?php echo $objScaffold->getNamePlural(); ?> by trading year</label>
            			<?php include(APPLICATION_PATH . '/views/common/tax_year_links.php'); ?>
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
<?php include($objTemplate->getFooterHTML()); ?>
