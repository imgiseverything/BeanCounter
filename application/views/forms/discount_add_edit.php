<?php

/**
 *	Discount add/edit form
 */	
 
 
// Get details about the project via a Project object

if($action == 'add'){
	$project_id = $objApplication->getParameter('project');
} else{
	$project_id = $properties['project'];
}


$objProject = new Project($db, array(), $project_id);
$project_details = $objProject->getProperties();


// only show form if it hasn't been completed successfully: to save repeated inserts/edits
if(form_success($user_feedback) !== true):
	$objForm = new Form($objScaffold->getFields(), $objScaffold->getFieldsDetails(), $objScaffold->getForeignKeys(), $properties, array('project'));
?>
<form id="<?php echo $action; ?>_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <fieldset>
       <legend><?php echo ($action == 'add') ? 'Add a new ' . $objScaffold->getName() . ' to ' . $project_details['title'] : 'Edit the ' . $objScaffold->getName() . ', <em>' . stripslashes($properties['title']) . '</em>'; ?></legend>
        <p class="instructions"><?php echo ($action == 'add') ? 'Add a new ' . $objScaffold->getName() . ' to the ' . strtolower($objProject->getName()) . ' called ' . $project_details['title'] : 'Edit the ' . $objScaffold->getName() . ', <em>' . stripslashes($properties['title']) . '</em>'; ?> by filling in the form details below.</p> 
        <p><strong>Note:</strong> Items marked with a star are required fields.</p>
        <?php echo $objForm->inputHidden('project', $objForm->getValue('project')); ?>
        <div class="field">
        	<label><?php echo ucwords($objProject->getName()); ?></label>
        	<div class="input"><?php echo $project_details['title']; ?> for <?php echo $project_details['client_title']; ?></div>
        </div>
        <?php echo $objForm->getAllFieldElements(); ?>
        <?php if($action == 'edit'): ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <?php endif; ?>
        <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
        <input type="hidden" name="redirect" value="/projects/<?php echo $objProject->getId(); ?>/" />
        <input type="hidden" name="popup" id="popupfield" value="false" />
       <button type="submit" class="submit">Submit<?php if($action == 'edit'): echo ' changes'; endif;?></button>
    </fieldset>
</form>
<hr />
<p><a href="<?php echo $objProject->getFolder(); ?><?php echo $project_id; ?>/">Cancel</a></p>
<?php else:  // we have successfully submitted the form so give user some options ?>
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<hr />
<h2>Your options</h2>
<ul class="options">
    <li><a href="<?php echo $objProject->getFolder() . $project_id; ?>/">View &#8216;<?php echo stripslashes($properties['project_title']); ?>&#8217;</a></li>
    <li><a href="<?php echo $objScaffold->getFolder(); ?>add/?project=<?php echo $project_id; ?>">Add another <?php echo $objScaffold->getName(); ?> to this project</a></li>
</ul>
<?php endif; ?>