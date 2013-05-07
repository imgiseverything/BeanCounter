<?php

	/**
	 *	Month by month accounts (Profit/Loss) View
	 *  View profit/loss data for user-defined periods of time
	 */

	// Page details
	$objTemplate->setTitle(ucfirst($objScaffold->getNamePlural()));
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables'));
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
	
	');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'ajax_pagination', 'ajax_filter', 'highcharts')); // must be an array
	$profit = $loss = $chart_months = array();
	foreach($months as $key => $value){
   		$profit[] = ceil($objScaffold->getMonthlyProfit($key));
   		$loss[] = ceil(str_replace('-', '', $objScaffold->getMonthlyLoss($key)));
   		$chart_months[] = date('M Y', strtotime(trim($key . '-01 00:00:00')));
	}

	$objTemplate->setExtraBehaviour("
	var chart1; 
	$(document).ready(function() {
      chart1 = new Highcharts.Chart({
         chart: {
            renderTo: 'VisualData',
            defaultSeriesType: 'line'
         },
         colors: ['#66CC33', '#FF3333'],
	
         title: {
            text: '" . end($chart_months) . " - " . read($chart_months, 0, '') . "'
         },
         xAxis: {
            categories: ['" . join("','", array_reverse($chart_months)) . "']
         },
         yAxis: {
            title: {
               text: 'Money'
            }
         },
         series: [{
            name: 'Revenue',
            data: [" . join(',', array_reverse($profit)) . "]
         }, {
            name: 'Expenses',
            data: [" . join(',', array_reverse($loss)) . "]
         }]
      });
   });
   ");
   
   
	$objTemplate->setSubmenu('accounts');
	
	
	
	$objMenu->setBreadcrumb('Monthly figures');
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<?php if($action != 'download'): ?>
    	<a href="<?php echo $objScaffold->getFolder(); ?>download/" class="button-add download"><span></span>Download these transactions</a>
    	<?php endif; ?>
    	<h1><?php echo $accounts_title; ?></h1>
			<div id="VisualData" style="clear:both;">
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
<?php include($objTemplate->getFooterHTML()); ?>
