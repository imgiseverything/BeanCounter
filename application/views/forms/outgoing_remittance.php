<?php

	/**
	 *	Email remittance advice form
	 */	
	 
	// set up form values - 
	// @tod	put these in the controller (not the view)
	$supplier_name = explode(' ', trim($properties['outgoing_supplier_main_contact']));
	$contact_details = str_replace(", \n", ', ', strip_tags($objVcard->getVcard()));
 
 	$default_message = "Hello,\r\nPlease find attached to this email a HTML remittance advice (entitled, " . stripslashes($properties['title']) . ").\r\n\r\n";
 	
 	
 	$default_message .= 'View it here:' . "\r\n" . 'http://' . $objApplication->getSiteUrl() . $objScaffold->getFolder() . 'download/' . md5(SECRET_PHRASE . $objScaffold->getId()) . '/';
 	
 	$default_message .= "\r\n\r\nRegards,\r\n{$contact_details}"; 
 	
 	$subject = (empty($_POST['subject'])) ? 'Remittance advice: ' . stripslashes($properties['title']) : $_POST['subject'];
 	$message = (empty($_POST['message'])) ? strip_tags($default_message) : $_POST['message'];
 	
 	// reset behaviours - no wysiwyg
 	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'colorbox', 'plugins/jquery.form', 'ajax_form_submit'));

	// only show form if it hasn't been completed successfully
	if(form_success($user_feedback) !== true):
?>
<form id="invoice_form" action="" method="post">
    <fieldset>
        <legend>Send remittance advice for <?php echo stripslashes($properties['title']); ?>?</legend>
        <p class="instructions">Are you sure you want to send remittance advice for the <?php echo $objScaffold->getName(); ?>, <?php echo stripslashes($properties['title']); ?>? <br /><a href="/outgoings/download/<?php echo md5(SECRET_PHRASE . $objScaffold->getId()); ?>/" class="popup">Preview note</a></p>
        <p class="instructions"><strong>To:</strong> <?php echo $properties['outgoing_supplier_main_contact']; ?> &lt;<?php echo $properties['outgoing_supplier_email']; ?>&gt;</p>
        <label for="subject">Subject:</label>
        <input type="text" name="subject" id="subject" value="<?php echo $subject; ?>" /><br />
        <label for="message">Message:</label>
        <textarea name="message" id="message" rows="10" cols="10"><?php echo $message; ?></textarea><br />
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
        <button type="submit">Yes, send it now</button>
    </fieldset>
</form>
<hr />
<p><a href="<?php echo $objScaffold->getFolder() . $id; ?>/">No, don't send it now</a></p>
<iframe class="hidden">
<?php
 // include download file so it builds the cache file ready for emailing out
 include(APPLICATION_PATH . '/views/outgoings_download.php');
?>
</iframe>
<?php else: // we have successfully sent the form so give user some options ?>
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<hr />
<h2>Your options</h2>
<p><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></p>
<?php endif; ?>
