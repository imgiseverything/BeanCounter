<?php

	/**
	 *	Timings view
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
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter'));
	
	// Breadcrumb
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo $title; ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
		<h2>Details</h2>
		<p>Project: <?php echo $project_title; ?></p>
		<p><em><?php echo $duration; ?> hours</em> on <?php echo $start_date; ?></p>
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
		
		<h2>Tags</h2>
		<?php if(!empty($timing_tag)): ?>
		<ul class="tags">
			<?php foreach($timing_tag as $tag): ?>
			<li><?php echo $tag['title']; ?></li>
			<?php endforeach;?>
		</ul>
		<?php else: ?>
		<p>There are not tags associated with this timing.</p>
		<?php endif; ?>
	</div>
    <?php include(APPLICATION_PATH . '/views/common/sidebar_metadata.php'); ?>
<?php include($objTemplate->getFooterHTML()); ?>