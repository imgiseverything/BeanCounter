<?php

	/**
	 *	User add/edit form
	 */	
		
	// if user isn't a super user remove the client field from the form builder
	// and add a hidden form field with that client's id pre-filled in
	// because only superusers can add new clients/users for any client
	$form_fields = ($objAuthorise->getLevel() == 'Superuser' &&  $objAuthorise->getId() != $objScaffold->getId()) ? array('firstname', 'surname', 'email', 'password', 'status', 'client', 'access_level') : array('firstname', 'surname', 'email', 'password', 'status');

	
	
	// only show form if it hasn't been completed successfully: to save repeated inserts/edits
	if(form_success($user_feedback) !== true):
		$_POST['status'] = (empty($_POST)) ? 1 : read($_POST, 'status', 1);
		$objForm = new Form($form_fields, $objScaffold->getFieldsDetails(), $objScaffold->getForeignKeys(), $properties);
		
?>
<form id="<?php echo $action; ?>_form" action="" method="post">
    <fieldset>
       <legend><?php echo ($action == 'add') ? 'Add a new ' . $objScaffold->getName() : 'Edit the ' . $objScaffold->getName() . ', <em>'.stripslashes($properties['title']) . '</em>'; ?></legend>
        <p class="instructions"><?php echo ($action == 'add') ? 'Add a new ' . $objScaffold->getName() : 'Edit the ' . $objScaffold->getName() . ', <em>' . stripslashes($properties['title']) . '</em>'; ?> by filling in the form details below. <br /><strong>Note:</strong> Items marked with a star are required fields.</p>
        <?php echo $objForm->getAllFieldElements(); ?>
        <?php if($action == 'edit'): ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="date_last_login_day" value="<?php echo date('d', strtotime($properties['date_last_login'])); ?>" />
        <input type="hidden" name="date_last_login_month" value="<?php echo date('m', strtotime($properties['date_last_login'])); ?>" />
        <input type="hidden" name="date_last_login_year" value="<?php echo date('Y', strtotime($properties['date_last_login'])); ?>" />
        <input type="hidden" name="date_last_login_hour" value="<?php echo date('H', strtotime($properties['date_last_login'])); ?>" />
        <input type="hidden" name="date_last_login_minute" value="<?php echo date('i', strtotime($properties['date_last_login'])); ?>" />
        <?php endif; ?>
        <?php if($objAuthorise->getLevel() != 'Superuser' || ($objAuthorise->getLevel() == 'Superuser' && $objAuthorise->getId() == $objScaffold->getId()) ):?>
        <input type="hidden" name="client" value="<?php echo $objAuthorise->getClient(); ?>" />
        <input type="hidden" name="access_level" value="<?php echo read($properties, 'access_level', 2); ?>" />
        <?php endif; ?>
        <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
       <button type="submit" class="submit">Submit<?php if($action == 'edit'): echo ' changes'; endif;?></button>
    </fieldset>
</form>
<hr />
<p><a href="/">Cancel</a></p>
<?php else:  // we have successfully submitted the form so give user some options ?>
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<hr />
<h2>Your options</h2>
<ul class="options">
    <li><a href="/">View dashboard</a></li>
</ul>
<?php endif; ?>