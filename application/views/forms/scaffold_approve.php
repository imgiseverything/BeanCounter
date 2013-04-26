<?php

	/**
	 *	Approve form
	 */	

	// only show form if it hasn't been completed successfully
	if(form_success($user_feedback) !== true):
?>
<form id="delete_form" action="" method="post">
    <fieldset>
        <legend>Approve <?php echo stripslashes($properties['title']); ?>?</legend>
        <p class="instructions">Are you sure you want to approve the <?php echo $objScaffold->getName(); ?>, <?php echo stripslashes($properties['title']); ?>?</p>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="action" value="<?php echo $action; ?>" />
        <button type="submit">Yes, approve now</button>
    </fieldset>
</form>
<hr />
<p><a href="<?php echo $objScaffold->getFolder() . $id; ?>/">Cancel approval</a></p>
<?php else: // we have successfully deleted data: give user some options ?>
<hr />
<h2>Your options</h2>
<p><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></p>
<?php endif; ?>
