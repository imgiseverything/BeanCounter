<?php

	/**
	 *	Leads view
	 *  View all items
	 */

	// initialise ViewSnippet object - for automatic HTML creation 
	// from scaffold object data
	$objViewSnippet = new ViewSnippet();

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	$objTemplate->setDescription($objScaffold->getPageDescription());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables', 'colorbox'));
	
	
	$objTemplate->setExtraStyle('
	tr.bangon td{font-weight: bold;;}
	tr.likely td{background-color: #EDFFE2;}
	tr.medium td{background-color: #f6cf71;}
	tr.unlikely td{background-color: #FFE9E8;}
	');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'ajax_pagination', 'highcharts'));
	

	// Graph data
	$objTemplate->setExtraBehaviour("
	var chart1; 
	$(document).ready(function() {
      chart1 = new Highcharts.Chart({
         chart: {
            renderTo: 'VisualData',
            defaultSeriesType: 'line'
         },
         colors: ['#666666'],
	
         title: {
            text: 'Leads from " . read($simple_months, 0, '') . " - " . end($simple_months) . "'
         },
         xAxis: {
            categories: ['" . join("','", $simple_months) . "']
         },
         yAxis: {
            title: {
               text: 'Leads'
            }
         },
         series: [{
            name: 'Leads',
            data: [" . join(',', $leads) . "]
         }]
      });
   });
   ");

	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <div class="buttons clearfix">
        	<a href="<?php echo $objScaffold->getFolder(); ?>add/" class="button add">Add new <?php echo $objScaffold->getName(); ?></a>
        </div>
        <div id="VisualData" style="clear:both;">
			&nbsp;
		</div>
        <div class="data">
		<?php echo $objPagination->getPagination(); ?>
		<p class="showing"><?php echo getShowingXofX($objScaffold->getPerPage(), $objScaffold->getCurrentPage(), $properties_size, $objScaffold->getTotal()) . ' ' . $objScaffold->getNamePlural(); ?></p>
			<table id="<?php echo strtolower($objScaffold->getNamePlural()); ?>_table">
			<thead>
				<tr>
					<th scope="col">Date</th>
					<th scope="col">Title</th>
					<th scope="col">Client</th>
					<th scope="col">Potential value</th>
					<th scope="col">Likelihood</th>
					<th scope="col">Source</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			// Loop through all properites show we can show basic details and links for each one
			for($i = 0; $i < $properties_size; $i++):
				extract($properties[$i]);
				$title = stripslashes($title);
				
				
				$colour_class = null;
				
				if($likelihood == 100){
					$colour_class = 'likely bangon';
				} else if($likelihood >= 75){
					$colour_class = 'likely';
				} else if($likelihood == 0){
					$colour_class = 'unlikely';
				} else if($likelihood > 25 && $likelihood < 75){
					$colour_class = 'medium';
				}
				
				
				
				
				if(!empty($id)):
			?>
				<tr class="<?php echo assignOrderClass($i, $properties_size); ?> <?php echo $colour_class; ?>">
					<td><?php echo DateFormat::getDate('ddmmyyyy', $first_contact_date); ?></td>
					<td>
						<a href="<?php echo $objScaffold->getFolder(); ?><?php echo $id; ?>/"><?php echo $title; ?></a>
						<div class="group extra-options">
							<ul>
								<li><a href="<?php echo $objScaffold->getFolder() . $id; ?>/">View</option>
								<?php if($objAuthorise->getLevel() == 'Superuser'): ?>
								<li><a href="<?php echo $objScaffold->getFolder(); ?>duplicate/<?php echo $id; ?>/">Duplicate</a></li>								
								<li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $id; ?>/">Edit</a></li>
								<li><a href="<?php echo $objScaffold->getFolder(); ?>delete/<?php echo $id; ?>/">Delete</a></li>
								<?php endif; ?>
							</ul>
						</div>	
					</td>
					<td><a href="/clients/<?php echo $client; ?>"><?php echo $client_title; ?></a></td>
					<td><?php echo currency($job_value); ?></td>
					<td><?php echo $likelihood; ?>%</td>
					<td><a href="<?php echo $objScaffold->getFolder(); ?>?lead_type=<?php echo $lead_type; ?>"><?php echo $lead_type_title; ?></a></td>
				</tr>
			<?php endif; endfor; ?>
			</tbody>
			</table>
		<?php echo $objPagination->getPagination(); ?>
		</div>
	</div>
<?php 
    if($objTemplate->getMode() == 'normal'):
    	include(APPLICATION_PATH . '/views/common/sidebar.php');
    endif; 
?>
<?php include($objTemplate->getFooterHTML()); ?>