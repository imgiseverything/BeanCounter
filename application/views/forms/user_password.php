<?php

	/**
	 *	User change password form
	 */	

	// View JavaScript
	$objTemplate->setBehaviour(array('jquery', 'jquery.form', 'ajax_submit_form')); // must be an array
	
	// only show form if it hasn't been completed successfully: to save repeated inserts/edits
	if(form_success($user_feedback) !== true):
		//$objForm = new Form($objScaffold->getFields(), $objScaffold->getFieldsDetails(), $objScaffold->getForeignKeys(), $properties);
?>
<form id="<?php echo $action; ?>Form" action="" method="post">
    <fieldset>
    <legend>Change <?php echo ($objScaffold->getId() == $objAuthorise->getId()) ? 'your' : $properties['title'].'\'s'; ?> password</legend>
    <p class="instructions">To change <?php echo ($objScaffold->getId() == $objAuthorise->getId()) ? '<em>your</em>' : 'this'; ?> password, first enter <?php echo ($objScaffold->getId() == $objAuthorise->getId()) ? '<em>your</em>' : 'the'; ?> current password. <br />Second, enter <?php echo ($objScaffold->getId() == $objAuthorise->getId()) ? '<em>your</em>' : 'a'; ?> new password.<br />Then finally, confirm that new password.</p>
    <div class="field">
    	<label for="old_password" class="required">Current password: <span class="required">* <span class="off-screen">Required</span></span></span></label>
   		<input type="password" value="" id="old_password" name="old_password" class="password required" required="true" autofill="false" /> <span class="help"></span>
    </div>
    <div class="field">
    	<label for="password" class="required">New password: <span class="required">* <span class="off-screen">Required</span></span></span></label>
    	<input type="password" value="" id="password" name="password" class="password required" required="true" autofill="false" /> <span class="help"></span>
    </div>
    <div class="field">
    	<label for="password2" class="required">Confirm your new password: <span class="required">* <span class="off-screen">Required</span></span></span></label>
   		 <input type="password" value="" id="password2" name="password2" class="password required" required="true" autofill="false" /> <span class="help"></span>
    </div>
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
    <button type="submit">Submit changes</button>
    </fieldset>
</form>
<hr />
<p><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $objScaffold->getId(); ?>/">Cancel</a></p>
<?php else: // we have successfully submitted the form so give user some options ?>
<?php if(in_array($objTemplate->getMode(), array('ajax', 'popup'))): ?>
<div class="buttons clearfix"><a href="#" class="close-popup" onclick="parent.$.fn.colorbox.close();return false;">Close popup</a></div>
<?php endif; ?>
<hr />
<h2>Your options</h2>
<ul class="options">
    <li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $objScaffold->getId(); ?>/">Edit your details</a></li>
</ul>
<?php endif; ?>