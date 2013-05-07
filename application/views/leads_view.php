<?php

	/**
	 *	Leads view
	 *  View one item
	 */

	// initialise ViewSnippet object - for automatic HTML creation 
	// from scaffold object data
	$objViewSnippet = new ViewSnippet();
	
	
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	$objTemplate->setDescription($objScaffold->getPageDescription());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle();
	
	$objTemplate->setExtraStyle('
	
		.block{
			background: #eee;
			border: 1px solid #fff;
			float: left;
			margin: 0 0 40px;
			text-align: center;
			width: 33%;
		}
		
		.block em{
			display: block;
			font-size: 36px;
			padding: 40px 0 20px;
		}
	
	');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter'));
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo $title; ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <div class="block">
        	<em><?php echo DateFormat::getDate('ddmmyyyy', $first_contact_date); ?></em>
        	<p>First contact from client</p>
        </div>
        <div class="block">
        	<em><?php echo currency($job_value); ?></em>
        	<p>Potential project value</p>
        </div>
        <div class="block">
			<em><?php echo $likelihood; ?>%</em>
			<p>Likelihood this will go ahead</p>
		</div>
		<h2>Client details</h2>
		<?php
			// Get Client Vcard
			echo $objVcardClient->getVcard();
		?>
		<?php if(!empty($description)): ?>
		<div>
			<h2>Further Details</h2>
			<div class="details">
				<?php echo $description; ?>
			</div>
		</div>
		<?php endif; ?>
		
	</div>
    <?php include(APPLICATION_PATH . '/views/common/sidebar_metadata.php'); ?>
<?php include($objTemplate->getFooterHTML()); ?>