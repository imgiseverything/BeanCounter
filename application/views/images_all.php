<?php

	/**
	 *	Images View
	 *	View all images
	 */
	 
	 
	// Page details
	$objTemplate->setTitle('Images');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables')); // must be an array
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
	$objTemplate->setBehaviour(array('jquery', 'beancounter')); // must be an array
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
                <p class="instructions">Images can be a maximum of <?php echo Upload::convertBytes($objImage->getMaxSize()); ?> in size and can only be <?php echo join(', or ', $objImage->extensions); ?></p>
                <label for="image">Image file:</label>
                <input type="file" name="image" id="image" />
                <input type="hidden" name="image_id" id="image_id" value="<?php echo ($id) ? $id : ($objImage->total + 1); ?>" />
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $objImage->getMaxSize(); ?>" />
                <button type="submit" class="positive">Upload</button>
            </fieldset>
        </form>
        <?php
			// images array exists - we have some images that the user has uploaded
			if(!empty($images)):
				
				// Pagination
				echo $objPagination->getPagination();
				
				// How many images do we have?
		?>
		<p><?php echo getShowingXofX($objImage->getPerPage(), $objImage->getCurrentPage(), sizeof($images), $objImage->getTotal()); ?> images</p>
		<ul id="images_list">
		<?php
				foreach($images as $image):
				
					$src = str_replace(SITE_PATH, '', $image['src']);
					$directory = str_replace(SITE_PATH, '', $objImage->getDirectory());
					$thumbnail_src = str_replace($directory, $directory . 'thumbnails/', $src);
		?>
					<li><a href="<?php echo str_replace(' ', '_', trim($image['name'])); ?>/"><img src="<?php echo $thumbnail_src; ?>" alt="<?php echo $image['name']; ?>" title="<?php echo $image['name']; ?>" /></a>
					Image name: <?php echo $image['name']; ?>
					Edited: <?php echo  date('d/m/Y \a\t H:i:s', filemtime($image['src'])); ?>
					</li>
		<?php endforeach; ?>
				
				</ul>
		<?php	
				// Pagination (again)
				echo $objPagination->getPagination();
				
			else:
		?>
		
		<p>You have not uploaded any images to your website yet</p>
		<?php
			endif;
		?>
	</div>
<?php include($objTemplate->getFooterHTML()); ?>