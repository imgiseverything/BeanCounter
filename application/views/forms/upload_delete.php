<?php

	/**
	 *	Delete upload (file) form
	 */

	// only show form if it hasn't been completed successfully
	if(form_success($user_feedback) !== true):
?>
<form id="delete_form" action="" method="post">
    <fieldset>
        <legend>Delete <?php echo stripslashes($properties['title']); ?>?</legend>
        <p class="instructions">Are you sure you want to delete the <?php echo $objFile->getName(); ?>, <?php echo stripslashes($properties['title']); ?>?</p>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="action" value="<?php echo $action; ?>" />
        <button type="submit">Yes, delete it now</button>
    </fieldset>
</form>
<hr />
<p><a href="/<?php echo $objFile->getFolder(); ?>/<?php echo $properties['id']; ?>/">No, don't delete it now</a></p>
<?php else: // we have successfully deleted data: give user some options ?>
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<hr />
<h2>Your options</h2>
<ul class="options">
	<li><a href="/<?php echo $objFile->getFolder(); ?>/">View all <?php echo $objFile->getNamePlural(); ?></a></li>
</ul>
<?php endif; ?>
