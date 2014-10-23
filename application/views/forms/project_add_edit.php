<?php

/**
 *	Project add/edit form
 */
 
// Quick fudge to ensure a 0 is displayed in the vat rate field in add mode
if($action != 'edit' && empty($_POST['vat_rate'])){
	$_POST['vat_rate'] = 0;
}

//$ignored = array('appendix');

// only show form if it hasn't been completed successfully: to save repeated inserts/edits
if(form_success($user_feedback) !== true):

	$objForm = new Form($objScaffold->getFields(), $objScaffold->getFieldsDetails(), $objScaffold->getForeignKeys(), $properties, $ignored);
?>
<form id="<?php echo $action; ?>_form" action="" method="post">
    <fieldset>
       <legend><?php echo ($action == 'add') ? 'Add a new ' . $objScaffold->getName() : ucwords($action) . ' the ' . $objScaffold->getName() . ', <em>' . stripslashes($properties['title']) . '</em>'; ?></legend>
        <p class="instructions"><?php echo  ($action == 'add') ? 'Add a new '.$objScaffold->getName() : 'Edit the ' . $objScaffold->getName() . ', <em>' . stripslashes($properties['title']) . '</em>'; ?> by filling in the form details below.<br /> 
        <strong>Note:</strong> Items marked with a star are required fields.</p>
        <?php echo str_replace(array('<option value="1">' . SITE_NAME . '</option>', '>Title', '>Description'), array('', '>' . ucwords($objScaffold->getName()) . ' title', '>Further details about this ' . $objScaffold->getName() . ' e.g. projected timelines and scope of works'), $objForm->getAllFieldElements()); ?>
        <?php 
        /*
        Tasks
        */
        
        $objTask = new Task($db, $objApplication->getFilters(), read($properties, 'id', ''));
        $task_properties = read($properties, 'tasks', array());   
        	
		$ignored = array('project');
		
		$vat_rate_set = VAT;
		if(empty($vat_rate_set)):
			$ignored[] = 'vat';
		endif;

        $objTaskForm = new Form($objTask->getFields(), $objTask->getFieldsDetails(), $objTask->getForeignKeys(), $task_properties, $ignored);
        
        $i = 1;
        ?>
        <fieldset id="ManageTasks">
        	<legend>Line items</legend>
        	<p class="instructions">Add/edit itemised tasks to this <?php echo $objScaffold->getName(); ?>.</p>
        	<?php 
        	if(!empty($properties['project_task'])): 
        		foreach($properties['project_task'] as $task): 
        	?>
        	<fieldset class="edit-task">
        		<legend><?php echo $task['title']; ?> : <?php echo currency($task['price']); ?></legend>
        		<input type="hidden" name="taskid" value="<?php echo $i; ?>" />
        		<?php if($action != 'duplicate'): ?>
        		<input type="hidden" name="task[<?php echo $i; ?>][id]" value="<?php echo $task['id']; ?>" />
        		<?php endif; ?>
        	<?php
        	
        		$post = $_POST; // store and fake $_POST to make Form class work and show posted values when form errors
        		if(!empty($_POST['task'][$i])){
        			$_POST = $_POST['task'][$i];
        		}
        	
        	 	$objTaskForm = new Form($objTask->getFields(), $objTask->getFieldsDetails(), $objTask->getForeignKeys(), $task, $ignored);
        	 	$field_elements = $objTaskForm->getAllFieldElements();
        	 	$field_elements = str_replace(array('id="', 'for="'), array('id="task' . $i . '_', 'for="task' . $i . '_'), $field_elements);
        	 	
        	 	$field_elements = preg_replace('/name="([a-z_]+)"/', 'name="task[' . $i . '][\\1]"', $field_elements);
        	 	
        	 	echo $field_elements;
        	 	
        	 	
        	 	$_POST = $post; // reset $_POST;
        	?> 
        	<div class="field">
        		<input type="checkbox" name="task[<?php echo $i; ?>][delete]" id="task<?php echo $i; ?>delete" value="<?php echo $task['id']; ?>" />
        		<label for="task<?php echo $i; ?>delete">Delete this line item?</label>
        	</div>
        	<?php $i++; ?>
        	</fieldset>
        	<?php endforeach; endif; ?>
        	<?php for($ii = 1; $ii < 5; $ii++): ?>
        	<fieldset class="add-new-task">
        		<legend>Add a new line item</legend>
        		<input type="hidden" name="taskid" value="<?php echo $i; ?>" />
        	<?php
        	
        		$post = $_POST; // store and fake $_POST to make Form class work and show posted values when form errors
        		if(!empty($_POST['task'][$i])){
        			$_POST = $_POST['task'][$i];
        		}
        		
        	 	$objTaskForm = new Form($objTask->getFields(), $objTask->getFieldsDetails(), $objTask->getForeignKeys(), array(), $ignored);
        	 	$field_elements = $objTaskForm->getAllFieldElements();
        	 	$field_elements = str_replace(array('id="', 'for="'), array('id="task' . $i . '_', 'for="task' . $i . '_'), $field_elements);
        	 	
        	 	$field_elements = preg_replace('/name="([a-z_]+)"/', 'name="task[' . $i . '][\\1]"', $field_elements);
        	 	echo $field_elements;
        	 	
        	 	//print_r($_POST['task']);
        	 	
        	 	$_POST = $post; // reset $_POST;
        	 	
        	 	$i++;
        	?>    	
        	</fieldset>
            <?php endfor; ?> 
        </fieldset>
        <?php if($action == 'edit'): ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <?php endif; ?>
        <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
        <input type="hidden" name="hidden_vat_rate" id="hidden_vat_rate" value="<?php echo VAT; ?>" />
        <input type="hidden" name="hidden_vat_flat_rate_percentage" id="hidden_vat_flat_rate_percentage" value="<?php echo VAT_FLAT_RATE_PERCENTAGE; ?>" />
       <button type="submit" class="submit">Submit<?php if($action == 'edit'): echo ' changes'; endif;?></button>
    </fieldset>
</form>
<hr />
<?php include(APPLICATION_PATH . '/views/common/cancel_link.php'); ?>
<?php else: ?>
<hr />
<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
<hr />
<h2>Your options</h2>
<ul class="options">
	<li><a href="<?php echo $objScaffold->getFolder(); ?><?php echo $objScaffold->getId(); ?>/">View &#8216;<?php echo stripslashes($properties['title']); ?>&#8217;</a></li>
    <li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $objScaffold->getId(); ?>/">Edit &#8216;<?php echo stripslashes($properties['title']); ?>&#8217;</a></li>
    <li><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></li>
    <li><a href="<?php echo $objScaffold->getFolder(); ?>add/">Add another <?php echo $objScaffold->getName(); ?></a></li>
</ul>
<?php endif; ?>