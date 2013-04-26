<?php

	/**
	 *	Delete Error form
	 */	

	// only show form if it hasn't been completed successfully
	if(form_success($user_feedback) !== true):
?>
<form id="<?php echo $action; ?>-form" action="" method="post">
    <fieldset>
        <h2 class="legend"><?php echo ucfirst($action); ?> all <?php echo $objScaffold->getNamePlural(); ?>?</h2>
        <p class="instructions">Are you sure you want to <?php echo $action; ?> all <?php echo $objScaffold->getNamePlural(); ?>?</p>
        <input type="hidden" name="action" value="<?php echo $action; ?>" />
        <button type="submit">Yes, <?php echo $action; ?> them now</button>
    </fieldset>
</form>
<hr />
<p><a href="<?php echo $objScaffold->getFolder() . $id; ?>/" onclick="parent.$.fn.colorbox.close();return false;">No, don&#8217;t <?php echo $action; ?> them now</a></p>
<?php else:  // we have successfully deleted data: give user some options ?>
	<?php if($objApplication->getParameter('mode') == 'popup'): ?>
	<div class="buttons clearfix"><a href="#" onclick="$('#row-<?php echo $id; ?>', top.document).remove(); self.parent.tb_remove();">Close popup</a></div>
	<?php else: ?>
	<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
	<hr />
	<h2>Your options</h2>
	<p><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></p>
	<?php endif; ?>

<?php endif; ?>
