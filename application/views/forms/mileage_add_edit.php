<?php

	/**
	 *	Mileage add/edit form
	 */	
	
	
$ignored = array('outgoing_category', 'title', 'transaction_id', 'price', 'claimable_price', 'outgoing_supplier', 'outgoing_payment');
// only show form if it hasn't been completed successfully: 
// to save repeated inserts/edits
if(form_success($user_feedback) !== true):
	$objForm = new Form($objScaffold->getFields(), $objScaffold->getFieldsDetails(), $objScaffold->getForeignKeys(), $properties, $ignored);
?>
<form id="<?php echo $action; ?>_form" action="" method="post">
    <fieldset>
       <legend><?php echo ($action == 'add') ? 'Add new ' . $objScaffold->getName() : 'Edit this ' . $objScaffold->getName(); ?></legend>
        <p class="instructions"><?php echo ($action == 'add') ? 'Add new ' . $objScaffold->getName() : 'Edit this ' . $objScaffold->getName(); ?> by filling in the form details below. <br />
        <strong>Note:</strong> Items marked with a star are required fields.</p>
        <div class="field">
        	<label for="rate">Travel type:  <span title="Travel type is required" class="required">*</span></label>
        	<select name="rate" id="rate">
        		<option value="<?php echo CAR_RATE; ?>">Car (<?php echo (CAR_RATE * 100); ?>p per mile)</option>
        		<option value="<?php echo BIKE_RATE; ?>">Bicycle (<?php echo (BIKE_RATE * 100); ?>p per mile)</option>
        		<option value="<?php echo PASSENGER_RATE; ?>">Carrying a passenger in your car (<?php echo (PASSENGER_RATE * 100); ?>p per mile)</option>
        	</select>
        </div>
        <div class="field">
        	<label for="rate">Number of miles: <span title="Number of miles is required" class="required">*</span></label>
        	<input type="tel" class="int" id="miles" name="miles" value="<?php echo read($_POST, 'miles', 0); ?>" />
        </div>
        <?php echo str_replace(array('Title'), array('Number of miles'), $objForm->getAllFieldElements()); ?>
        <?php if($action == 'edit'): ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <?php endif; ?>
        <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
       <button type="submit" class="submit">Submit<?php if($action == 'edit'): echo ' changes'; endif;?></button>
    </fieldset>
</form>
<hr />
<?php include(APPLICATION_PATH . '/views/common/cancel_link.php'); ?>
<?php else:  // we have successfully submitted the form so give user some options ?>
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