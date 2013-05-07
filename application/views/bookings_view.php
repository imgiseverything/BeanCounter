<?php
	/**
	 *  Bookings view
	 *  View individual booking
	 */	

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'projects', 'tables'));
	// On page CSS
	$objTemplate->setExtraStyle('');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery'));
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo stripslashes($title); ?> <em>(<?php echo $booking_type_title; ?>)</em></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <?php echo validateContent($description); ?>
        <p>Starts: <?php echo DateFormat::getDate('datetime', $date_started); ?><br />
        Ends: <?php echo DateFormat::getDate('datetime', $date_ended); ?></p>
        <p>For: <a href="/clients/<?php echo $client?>/"><?php echo $client_title; ?></a></p>
	</div>
    <?php include(APPLICATION_PATH . '/views/common/sidebar_metadata.php'); ?>
<?php include($objTemplate->getFooterHTML()); ?>