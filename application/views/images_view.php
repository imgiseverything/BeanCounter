<?php

	/**
	 *	Images View
	 *	View individual image and sizes
	 */

	// Page details
	$objTemplate->setTitle('Images');
	$objTemplate->setBodyClass('home');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	$objTemplate->setExtraStyle('
	
	ul#images_list{
		width: 100%;
	}
	
		ul#images_list li{
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
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter')); // must be an array
	$objTemplate->setExtraBehaviour();
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<h1>Images</h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <form id="image_upload" action="" method="post" enctype="multipart/form-data">
        	<fieldset>
            	<legend><?php echo ($id) ? 'Update' : 'Upload'; ?> image</legend>
                <p class="instructions">Images can be a maximum of <?php echo Upload::convertBytes($objImage->getMaxSize()); ?> in size and can only be <?php echo join(', or ',$objImage->extensions); ?></p>
                <label for="image">Image file:</label>
                <input type="file" name="image" id="image" />
                <input type="hidden" name="image_id" id="image_id" value="<?php echo ($id) ? $id : ($objImage->total+1); ?>" />
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $objImage->getMaxSize(); ?>" />
                <button type="submit" class="positive">Upload</button>
            </fieldset>
        </form>
        <p><a href="/image/">View all images</a></p>
        <h2>Original size</h2>
		<p><img src="<?php echo str_replace(SITE_PATH, '', $image); ?>" alt="" /></p>
		<h3>Copy and paste code:</h3>
		<p>&lt;img src="<?php echo str_replace(SITE_PATH, '', $image); ?>" alt="" /&gt;</p>
		<p>http://<?php echo $objApplication->getSiteUrl() . str_replace(SITE_PATH, '', $image); ?></p>
		<hr />
        <?php
				
			// now go through all the different images sizes 
			// and print them out
			foreach($objImage->dimensions as $dimension => $value):
			
				$src_dimension = str_replace($objImage->directory, $objImage->directory . $dimension . '/', $image);				
				// check this image size exists
				if(file_exists($src_dimension)):
					echo '<h2>' . str_replace('_', ' ', ucfirst($dimension)) . ' size</h2>';
					echo '<p><img src="' . str_replace(SITE_PATH, '', $src_dimension) . '" alt="" /></p>';
					echo '<h3>Copy and paste code:</h3>';
					echo '<p>&lt;img src="' . str_replace(SITE_PATH, '', $src_dimension) . '" alt="" /&gt;</p>';
					echo '<p>http://' . $objApplication->getSiteUrl() . str_replace(SITE_PATH, '', $src_dimension) . '</p>';
					echo '<hr />';
				endif; 
				
			endforeach;
			
		?>
	</div>
   <?php include(APPLICATION_PATH . '/views/common/sidebar_metadata.php'); ?>
<?php include($objTemplate->getFooterHTML()); ?>