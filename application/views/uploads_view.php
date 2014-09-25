<?php

	/**
	 *	Files View
	 *	View individual file and sizes
	 *
	 *
	 */

	// Page details
	$objTemplate->setTitle($objFile->getNamePlural());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter')); // must be an array
	
	// Menus
	$objMenu->setBreadcrumb($objFile->getBreadcrumbTitle());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
		<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objFile->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <h2><?php echo $title; ?></h2>
        <div class="buttons clearfix">
			<a href="<?php echo $directory . $filename; ?>" class="download">Download</a>
		</div>
        <p><?php echo stripslashes($description); ?></p>
        <p>File type: <img src="/images/icons/<?php echo $objFile->upload->getIcon($upload_type_title); ?>" alt="" title="" /> <em><?php echo $upload_type_title; ?></em><br />
        File size: <?php echo $filesize; ?></p>
		<h2>Copy and paste code:</h2>
		<p>&lt;a href="<?php echo $directory . $filename?>" title="Download <?php echo $title; ?> (<?php echo $filesize; ?>)"&gt;<?php echo $title; ?>&lt;/a&gt;</p>
		<p>http://<?php echo $objApplication->getSiteUrl() . $directory . $filename; ?></p>
	</div>
    <?php include(APPLICATION_PATH . '/views/common/sidebar_metadata.php'); ?>
<?php include($objTemplate->getFooterHTML()); ?>