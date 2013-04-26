<?php

	/**
	 *	Project completed form
	 *	When a project is 'completed' it means it's finished and paid for.
	 */	

	// only show form if it hasn't been completed successfully
	if(form_success($user_feedback) !== true):
		$objForm = new Form(array('transaction_date'), $objScaffold->getFieldsDetails(), $objScaffold->getForeignKeys(), $properties);

?>
<form id="complete_form" action="" method="post">
    <fieldset>
        <legend>Mark <?php echo stripslashes($properties['title']); ?> as completed?</legend>
        <p class="instructions">Are you sure you want to mark this <?php echo $objScaffold->getName(); ?>, <?php echo stripslashes($properties['title']); ?> as completed?</p>
        <?php echo $objForm->getAllFieldElements(); ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="action" value="<?php echo $action; ?>" />
        <button type="submit">Yes, mark it as completed now</button>
    </fieldset>
</form>
<hr />
<p><a href="<?php echo $objScaffold->getFolder() . $id; ?>/">Cancel</a></p>
<?php else:  // we have successfully completed data: give user some options ?>
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<hr />
<h2>Your options</h2>
<p><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></p>
<?php endif; ?>
