<?php

	/**
	 *	Email quote form
	 */	
	 
	 
	// set up form values - 
	// @tod	put these in the controller (not the view)
	$client_name = explode(' ', trim($properties['client_main_contact']));
	$contact_details = str_replace(", \n", ', ', strip_tags($objVcard->getVcard()));
 
 	$default_message = "{$client_name[0]}\r\n\r\nPlease find attached to this email the proposal that you recently requested. \r\n\r\n";
 	
 	
 	$default_message .= 'View it here:' . "\r\n" . 'http://' . $objApplication->getSiteUrl() . $objScaffold->getFolder() . 'download/' . md5(SECRET_PHRASE . $objScaffold->getId()) . '/';
 	
 	$default_message .= "\r\n\r\nRegards,\r\n{$contact_details}"; 
 	
 	$subject = (empty($_POST['subject'])) ? 'Quotation: ' . stripslashes($properties['title']) : $_POST['subject'];
 	$message = (empty($_POST['message'])) ? $default_message : $_POST['message'];
 	
 	// reset behaviours - no wysiwyg
 	$objTemplate->setBehaviour(array('vendor/jquery', 'colorbox', 'plugins/jquery.date', 'plugins/jquery.datepicker', 'datepicker', 'hide_time', 'height', 'plugins/jquery.form', 'ajax_form_submit'));


	// only show form if it hasn't been completed successfully
	if(form_success($user_feedback) !== true):
?>
<form id="invoice_form" action="" method="post">
    <fieldset>
        <legend>Email <?php echo $objScaffold->getName(); ?> for <?php echo stripslashes($properties['title']); ?></legend>
        <p class="instructions">Are you sure you want to send a quote for the <?php echo $objScaffold->getName(); ?>, <?php echo stripslashes($properties['title']); ?>? <br /><a href="<?php echo $objScaffold->getFolder(); ?>download/<?php echo md5(SECRET_PHRASE . $objScaffold->getId()); ?>/" class="popup">Preview <?php echo $objScaffold->getName(); ?></a></p>
        <p class="instructions"><strong>To:</strong> <?php echo $properties['client_main_contact'];?> &lt;<?php echo $properties['client_email']; ?>&gt;</p>
        <label for="subject">Subject:</label>
        <input type="text" name="subject" id="subject" value="<?php echo $subject; ?>" required="required" aria-required="true" /><br />
        <label for="message">Message:</label>
        <textarea name="message" id="message" rows="20" cols="10" required="required" aria-required="true"><?php echo trim($message); ?></textarea><br />
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
        <button type="submit">Yes, send it now</button>
    </fieldset>
</form>
<hr />
<p><a href="<?php echo $objScaffold->getFolder() . $id; ?>/">No, don't send it now</a></p>
<!--
<?php
 // include download file so it builds the cache file ready for emailing out
 include(APPLICATION_PATH . '/views/projects_download.php');
?>
-->
<?php else: // we have successfully send the form so give user some options ?>
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<hr />
<h2>Your options</h2>
<p><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></p>
<?php endif; ?>