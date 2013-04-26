<?php

	/**
	 *	Email invoice form
	 */	
	 
	
	// set up form values - 
	// @todo	put these in the controller (not the view)
	$client_name = explode(' ', trim($properties['client_main_contact']));
	$contact_details = str_replace(", \n", ', ', strip_tags($objVcard->getVcard()));
 
 	$default_message = "{$client_name[0]}\r\n\r\nPlease find attached to this email a HTML invoice entitled, `" . stripslashes($properties['title']) . "`, for work recently completed.\r\n\r\n";
 	
 	
 	$default_message .= 'View it here:' . "\r\n" . 'http://' . $objApplication->getSiteUrl() . $objScaffold->getFolder() . 'download/' . md5(SECRET_PHRASE . $objScaffold->getId()) . '/';
 	
 	$default_message .= "\r\n\r\nRegards,\r\n{$contact_details}"; 
 	
 	$subject = (empty($_POST['subject'])) ? 'Invoice: ' . stripslashes($properties['title']) : $_POST['subject'];
 	$message = (empty($_POST['message'])) ? $default_message : $_POST['message'];
 	
 	// reset behaviours - no wysiwyg
 	$objTemplate->setBehaviour(array('jquery', 'colorbox', 'jquery.date', 'jquery.datepicker', 'datepicker', 'hide_time', 'height', 'jquery.form', 'ajax_form_submit'));

	// only show form if it hasn't been completed successfully
	if(form_success($user_feedback) !== true):
		$objForm = new Form(array('payment_required'), $objScaffold->getFieldsDetails(), $objScaffold->getForeignKeys(), $properties);
?>
<form id="invoice_form" action="" method="post">
    <fieldset>
        <legend>Invoice <?php echo stripslashes($properties['title']); ?>?</legend>
        <p class="instructions">Are you sure you want to invoice the <?php echo $objScaffold->getName(); ?>, <?php echo stripslashes($properties['title']); ?>? <br /><a href="/projects/download/<?php echo md5(SECRET_PHRASE . $objScaffold->getId()); ?>/" class="popup">Preview invoice</a></p>
        <p class="instructions"><strong>To:</strong> <?php echo stripslashes($properties['client_main_contact']); ?> &lt;<?php echo $properties['client_email']; ?>&gt;</p>
         <div class="field">
        	<label for="cc">Cc:</label>
        	<input type="text" name="cc" id="cc" value="<?php echo $objApplication->getParameter('cc'); ?>" />
        	<span class="help">A comma separated list of other emails to send to</span>
        </div>
        <div class="field">
        	<label for="subject">Subject:</label>
        	<input type="text" name="subject" id="subject" value="<?php echo $subject; ?>" required="required" aria-required="true" />
        </div>
        <div class="field">
        	<label for="message">Message:</label>
        	<textarea name="message" id="message" rows="40" cols="10" required="required" aria-required="true"><?php echo trim($message); ?></textarea>
        </div>
        <?php echo $objForm->getAllFieldElements(); ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
        <button type="submit">Yes, send it now</button>
    </fieldset>
</form>
<hr />
<p><a href="<?php echo $objScaffold->getFolder() . $id; ?>/">Cancel</a></p>
<iframe class="hidden">
<?php
 // include download file so it builds the cache file ready for emailing out
 include(APPLICATION_PATH . '/views/projects_download.php');
?>
</iframe>
<?php else: //  we have successfully send the form so give user some options ?>
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<hr />
<h2>Your options</h2>
<p><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></p>
<?php endif; ?>