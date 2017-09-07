<?php

	/**
	 *	Delete form
	 */

$year = date('Y', strtotime($properties['date_started']));
$month = date('m', strtotime($properties['date_started']));
$redirect_url = $objScaffold->getFolder() .  $year . '/' . $month . '/';

	// only show form if it hasn't been completed successfully
	if(form_success($user_feedback) !== true):
?>
<form id="delete_form" action="" method="post">
    <fieldset>
        <legend><?php echo ucfirst($action); ?> <?php echo stripslashes($properties['title']); ?>?</legend>
        <p class="instructions">Are you sure you want to <?php echo $action; ?> the <?php echo $objScaffold->getName(); ?>, <?php echo stripslashes($properties['title']); ?> (starting: <?php echo date('jS M Y', strtotime($properties['date_started'])); ?>)?</p>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="action" value="<?php echo $action; ?>" />
        <input type="hidden" name="redirect" value="<?php echo $redirect_url; ?>/" />
        <button type="submit">Yes, do it now</button>
    </fieldset>
</form>
<hr />
<?php if($objTemplate->getMode() == 'popup'): ?>
<p><a href="<?php echo $objScaffold->getFolder() . $id; ?>/" class="close-popup">Cancel</a></p>
<?php else: ?>
<p><a href="<?php echo $objScaffold->getFolder() . $id; ?>/">Cancel</a></p>
<?php endif; ?>
<?php else:  // we have successfully deleted data: give user some options ?>
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<hr />
<h2>Your options</h2>
<p><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></p>
<?php endif; ?>
