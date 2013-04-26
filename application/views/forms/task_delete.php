<?php

	/**
	 *	Delete task form
	 */
	 
	$objProject = new Project($db, array(), $properties['project']);
	$project_details = $objProject->getProperties(); 

	// only show form if it hasn't been completed successfully
	if(form_success($user_feedback) !== true):
?>
<form id="delete_form" action="" method="post">
    <fieldset>
        <legend>Delete <?php echo stripslashes($properties['title']); ?>?</legend>
        <p class="instructions">Are you sure you want to delete the <?php echo $objScaffold->getName(); ?>, <?php echo stripslashes($properties['title']); ?> which is a part of the <?php echo strtolower($objProject->getName()); ?> called <?php echo $project_details['title']; ?>?</p>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="action" value="<?php echo $action; ?>" />
        <input type="hidden" name="redirect" value="/projects/<?php echo $properties['project']; ?>/" />
        <button type="submit">Yes, do it now</button>
    </fieldset>
</form>
<hr />
<p><a href="<?php echo $objProject->getFolder(); ?><?php echo $properties['project_id']; ?>/">Cancel</a></p>
<?php else:  // we have successfully deleted data: give user some options ?>
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<hr />
<h2>Your options</h2>
<ul class="options">
	<li><a href="<?php echo $objProject->getFolder(); ?><?php echo $properties['project_id']; ?>/">View <?php echo stripslashes($properties['project_title']); ?></a></li>
   	<li><a href="<?php echo $objScaffold->getFolder(); ?>add/?project=<?php echo $properties['project_id']; ?>">Add another <?php echo $objScaffold->getName(); ?> to this project</a></li>
</ul>
<?php endif; ?>
