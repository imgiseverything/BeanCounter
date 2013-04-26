<?php 

/**
 *	Cancel (form) link
 *	Click to navigate away form the form.
 */

$edit_link = ($action == 'add') ? '' : $id . '/?' . createFeedbackURL('error', 'Your changes have been cancelled'); 

// create an inline onclick event to close the popup (if we're in popup mode)
// Now look here dissenter, we all know this is bad practice and we'll
// make this go away once we sort out how to get unobtrusive JavaScript
// to play nicer with the popup script (colorbox)
$close_popup = '';
if(in_array($objTemplate->getMode(), array('popup'))){
	$close_popup = ' class="close-popup" onclick="parent.$.fn.colorbox.close();return false;"';
}

?>
<p><a href="<?php echo $objScaffold->getFolder() . $edit_link; ?>" <?php echo $close_popup; ?>>Cancel</a></p>