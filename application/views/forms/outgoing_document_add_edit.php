<?php

	/**
	 *	Outgoings/documentation add/edit form
	 */	
	 
	$ignoredFields = array('filename', 'filesize', 'mimetype');
	// only show form if it hasn't been completed successfully: 
	// to save repeated inserts/edits
	if(form_success($user_feedback) !== true):
		$objForm = new Form($objScaffold->getFields(), $objScaffold->getFieldsDetails(), $objScaffold->getForeignKeys(), $objScaffold->getProperties(), $ignoredFields);
?>
<form id="<?php echo $action; ?>_form" action="" method="post" enctype="multipart/form-data">
    <fieldset>
       <legend><?php echo ($action == 'add') ? 'Add a new ' . $objScaffold->getName() : ucwords($action) . ' the ' . $objScaffold->getName() . ', <em>' . stripslashes($properties['title']) . '</em>'; ?></legend>
        <p class="instructions"><?php echo ($action == 'add') ? 'Add a new ' . $objScaffold->getName() : 'Edit the ' . $objScaffold->getName() . ', <em>' . stripslashes($properties['title']) . '</em>'; ?> by filling in the form details below.<br /> 
        <strong>Note:</strong> Items marked with a star are required fields.</p>                
    	<p class="instructions">Documents can be a maximum of <?php echo Upload::convertBytes($objScaffold->getMaxSize()); ?> in size and can only be <?php echo join(', or ', $objScaffold->getExtensions()); ?></p>
    	<div class="field">
        	<label for="file" class="required">Document: <span class="required" title="Required">*</span></label>
        	<input type="file" name="file" id="file" aria-required="true" />
        </div>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $objScaffold->getMaxSize(); ?>" />
        <?php echo $objForm->getAllFieldElements(); ?>
        <?php if($action == 'edit'): ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <?php endif; ?>
        <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
       <button type="submit" class="submit">Submit<?php if($action == 'edit'): echo ' changes'; endif;?></button>
    </fieldset>
</form>
<hr />
<?php include(APPLICATION_PATH . '/views/common/cancel_link.php'); ?>
<?php else: // we have successfully added/edited data: give user some options ?>
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<hr />
<h2>Your options</h2>
<ul class="options">
    <li><a href="<?php echo $objScaffold->getFolder(); ?><?php echo $objScaffold->getId(); ?>/">View &#8216;<?php echo stripslashes($properties['title']); ?>&#8217;</a></li>
    <li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $objScaffold->getId(); ?>/">Edit &#8216;<?php echo stripslashes($properties['title']); ?>&#8217;</a></li>
    <li><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></li>
    <li><a href="<?php echo $objScaffold->getFolder(); ?>add/">Add another <?php echo $objScaffold->getName(); ?></a></li>
</ul>
<?php endif; ?>