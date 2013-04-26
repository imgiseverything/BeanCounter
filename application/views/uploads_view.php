<?php

	/**
	 *	Files View
	 *	View individual file and sizes
	 *
	 *
	 */

	// Page details
	$objTemplate->setTitle($objFile->getNamePlural());
	$objTemplate->setDescription();
	$objTemplate->setBodyClass('home');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables')); // must be an array
	$objTemplate->setExtraStyle('
	
	ul#files_list{
		width: 100%;
	}
	
		ul#files_list li{
			border: 1px solid #DDD;
			float: left;
			list-style: none;
			margin: 0 10px 10px;
			padding: 10px;
			text-align: center;
			width: 130px;
		}
	');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter')); // must be an array
	$objTemplate->setExtraBehaviour();
	
	// Menus
	$objMenu->setBreadcrumb($objFile->getBreadcrumbTitle());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
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