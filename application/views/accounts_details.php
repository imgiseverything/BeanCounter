<?php
	
	/**
	 *	Accounts view
	 *	View all accounts
	 */

	// Page details
	$objTemplate->setTitle(ucfirst($objScaffold->getNamePlural()));
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables', 'datepicker'));
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
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'ajax_pagination', 'jquery.date', 'jquery.datepicker', 'datepicker', 'jquery.form', 'ajax_filter'));
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<?php if($action != 'download'): ?>
    	<a href="<?php echo $objScaffold->getFolder(); ?>download/" class="button-add button-download"><span></span>Download these transactions</a>
    	<?php endif; ?>
    	<h1><?php echo $accounts_title; ?></h1>
		<?php
		
		// results exist
		if(!empty($properties)):
		
				$i = 1; // counter
				// Tabular/Listings
		?>
				<div class="buttons clearfix">
					
				</div>
				<div class="data">
					<?php echo $objPagination->getPagination(); ?>
					<p class="showing">Showing <?php echo $objScaffold->getTotal(); ?> transactions.</p>
	                <table id="<?php echo strtolower($objScaffold->getNamePlural()); ?>_list">
	                    <thead>
	                        <tr>
	                            <th scope="col">Date</th>
	                            <th scope="col">Client/Supplier</th>
	                            <th scope="col">Item</th>
	                            <th scope="col">Value</th>
	                        </tr>
	                    </thead>
	                    <tfoot>
	                        <tr>
	                            <th scope="row" colspan="3">Turnover</th>
	                            <td><?php echo currency($objScaffold->getProjectsTotal()); ?></td>
	                        </tr>
	                        <tr>
	                            <th scope="row" colspan="3">Costs</th>
	                            <td><span class="minus">-</span><?php echo currency($objScaffold->getOutgoingsTotal()); ?></td>
	                        </tr>        
	                        <tr>
	                            <th scope="row" colspan="3"><abbr title="Value Added Tax">VAT</abbr> Received from invoices (<?php echo VAT; ?>%)</th>
	                            <td><span class="minus">-</span><?php echo currency($objScaffold->getVATDue()); ?></td>
	                        </tr>
	                        <tr>
	                            <th scope="row" colspan="3"><abbr title="Value Added Tax">VAT</abbr> Paid on goods (<?php echo VAT; ?>%)</th>
	                            <td><?php echo currency($objScaffold->getVATOwed()); ?></td>
	                        </tr>
	                        <tr class="<?php echo ((float)$objScaffold->getProfit() > 0) ? 'positive' : 'negative'; ?>">
	                            <th scope="row" colspan="3">Gross profit</th>
	                            <td><?php echo currency($objScaffold->getSubtotal()); ?></td>
	                        </tr>   
	                       <?php /*<tr>
	                            <th scope="row" colspan="3">Estimated Tax (<?php echo INCOME_TAX; ?>%)</th>
	                            <td>-<?php echo currency($objScaffold->getIncomeTax()); ?></td>
	                        </tr>
	                        <tr>
	                            <th scope="row" colspan="3">Estimated <abbr title="National Insurance">NI</abbr> (<?php echo NI_TAX; ?>%)</th>
	                            <td>-<?php echo currency($objScaffold->getNI()); ?></td>
	                        </tr>*/ ?>
	                        <?php /*<tr class="<?php echo ((float)$objScaffold->getProfit() > 0) ? 'positive' : 'negative'; ?>">
	                            <th scope="row" colspan="3">Net profit</th>
	                            <td><?php echo currency($objScaffold->getProfit()); ?></td>
	                        </tr>*/?>
	                    </tfoot>
	                    <tbody>
	                    <?php
	                    // Loop through all properites show we can show basic details and links for each one
	                    foreach($properties as $property):
	                       // if(!empty($property['title'])){
	                            $minus = ($property['type'] == 'negative') ? '<span class="minus">-</span>' : '';
	                            $link = ($property['type'] == 'negative') ? '/outgoings/' : '/projects/';
	                            $cost = (!empty($property['price'])) ? $property['price'] : read($property, 'total', 0);
	                            
	                            echo '<tr class="' . $property['type'] . '">
	                            <td>' . DateFormat::getDate('ddmmyyyy', $property['transaction_date']) . '</td>
	                            <td>' . $property['payee_name'] . '</td>
	                            <td><a href="' . $link . $property['id'] . '/">' . stripslashes($property['title']) . '</a></td>
	                            <td>' . $minus . currency($cost) . '</td>
	                            </tr>' . "\n";
	                       // }
	                        $i++; // increment counter
	                    endforeach;
	                    ?>
	                    </tbody>
	                </table>
				<?php echo $objPagination->getPagination(); ?>
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
    <?php 
    if($objTemplate->getMode() == 'normal'):
    ?>
    <div class="filter-form">
    	<form id="filterForm" method="get" action="<?php echo $objScaffold->getFolder(); ?>" class="hidden">
            <fieldset>
            	<fieldset class="fieldset-row">
            		<div class="field">
                		<label for="show">Show:</label>
                		<input type="tel" name="show" id="show" value="<?php echo $objScaffold->getPerPage(); ?>" class="int" /> 
                		<span class="help">Enter anywhere from 1 to <?php echo $objScaffold->getTotal(); ?></span>
                	</div>
                </fieldset>
                <fieldset class="fieldset-row">  
                	
            		<div class="field">
            			<label>Show <?php echo $objScaffold->getNamePlural(); ?> by trading year</label>
            			<?php include(APPLICATION_PATH . '/views/common/tax_year_links.php'); ?>
            		</div>       
                	<div class="field">
                		<label for="timeframe">Or choose a timeframe:</label>
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
<?php include($objTemplate->getFooterHTML()); ?>