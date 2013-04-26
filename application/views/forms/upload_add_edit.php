<?php

	/**
	 *	File Upload + add/edit form
	 */	

// only show form if it hasn't been completed successfully: to save repeated inserts/edits
if(form_success($user_feedback) !== true):
	$objForm = new Form($fields, $objFile->getFieldsDetails(), $objFile->getForeignKeys(), $objFile->getProperties());
?>
<form id="file_upload" action="" method="post" enctype="multipart/form-data">
	<fieldset>
    	<legend><?php echo ($action == 'add') ? 'Add a new ' . $objFile->getName() : ucwords($action) . ' the ' . $objFile->getName() . ', <em>' . stripslashes($properties['title']) . '</em>'; ?></legend>
    	<p class="instructions"><?php echo ($action == 'add') ? 'Add a new ' . $objFile->getName() : 'Edit the ' . $objFile->getName() . ', <em>' . stripslashes($properties['title']) . '</em>'; ?> by filling in the form details below.<br /> <strong>Note:</strong> Items marked with a star are required fields.</p>                
    	<p class="instructions">files can be a maximum of <?php echo Upload::convertBytes($objFile->upload->getMaxSize()); ?> in size and can only be <?php echo join(', or ',$objFile->upload->extensions); ?></p>
        <label for="file" class="required">File:</label>
        <input type="file" name="file" id="file" />
        <input type="hidden" name="file_id" id="file_id" value="<?php echo ($id) ? $id : ($objFile->upload->total + 1); ?>" />
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $objFile->upload->getMaxSize(); ?>" />
        <?php echo $objForm->getAllFieldElements(); ?>
		<?php if($action == 'edit'): ?>
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<?php endif; ?>
		<input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
        <button type="submit" class="positive">Upload</button>
    </fieldset>
</form>
<hr />
<?php include(APPLICATION_PATH . '/views/common/cancel_link.php'); ?>
<?php else: ?>
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<h2>Options</h2>
<ul>
	<li><a href="/<?php echo $objFile->getFolder();?>/<?php echo $objFile->getId();?>">View <?php echo $objFile->getName();?></a></li>
	<li><a href="/<?php echo $objFile->getFolder();?>/">View all <?php echo $objFile->getNamePlural();?></a></li>
</ul>
<?php endif; ?>