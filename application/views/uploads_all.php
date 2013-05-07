<?php

	/**
	 *	files View
	 *	View all files
	 *
	 *
	 */
	 
	 
	// Page details
	$objTemplate->setTitle(ucwords($objFile->getNamePlural()));
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables', 'colorbox')); // must be an array
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'colorbox')); // must be an array
	$objTemplate->setExtraBehaviour();
	
	// Menus
	$objMenu->setBreadcrumb($objFile->breadcrumb_title);
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	 <!-- // PRIMARY CONTENT DIV  // -->
	<div id="PrimaryContent" class="content-primary">
		<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucwords($objFile->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <div class="buttons clearfix">
        	<a href="/<?php echo $objFile->getFolder()?>/add/" class="button add">Add a new <?php echo $objFile->getName(); ?></a>
        </div>
        <?php
			// files array exists - we have some files that the user has uploaded
			if(!empty($properties)):	
				// Pagination
				echo $objPagination->getPagination();

				echo validateContent(getShowingXofX($objFile->getPerPage(), $objFile->getCurrentPage(), sizeof($properties), $objFile->getTotal()) . ' ' . $objFile->getNamePlural());
?>
				<table id="files_list">
				<thead>
					<tr>
						<th scope="col">Type</th>
						<th scope="col">File</th>
						<th scope="col">Size</th>
					</tr>
				</thead>
				<tbody>
			<?php
				$i = 0;
				foreach($properties as $file):
					$directory = $objFile->upload->getDirectory();
					$filesize = Upload::convertBytes(filesize($directory . $file['filename']));
			?>
				<tr>
					<td><img src="/images/icons/<?php echo $objFile->upload->getIcon($file['upload_type_title']); ?>" alt="<?php echo $file['upload_type_title']; ?>" title="<?php echo $file['upload_type_title']; ?>" /></td>
					<td>
						<a href="<?php echo $objFile->getFolder() . $file['id']; ?>/"><?php echo stripslashes($file['title'])?></a>
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
					<td><?php echo $filesize; ?></td>
				</tr>
			<?php 
					$i++;  
				endforeach; 
			?>
				</tbody>
			</table>
		<?php	// Pagination (again)
				echo $objPagination->getPagination();
			else:
				echo validateContent('You have not uploaded any files to your website yet');
			endif;
		?>
	</div>
<?php 
    if($objTemplate->getMode() == 'normal'):
    	include(APPLICATION_PATH . '/views/common/sidebar.php');
    endif; 
?>
<?php include($objTemplate->getFooterHTML()); ?>