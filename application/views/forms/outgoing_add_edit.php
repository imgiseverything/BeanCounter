<?php

	/**
	 *	Outgoing add/edit form
	 */	

// Make some minor search/replaces on the auto-generated form fields called in $objForm->getAllFieldElements() later on
$outgoings_form_search = array('<a href="/outgoing_', '<a href="/outgoing_categories', '<a href="/outgoing_payment', 'Add new outgoing_');	
$outgoings_form_replace = array('<a href="/outgoings/', '<a href="/outgoings/', '<a href="/', 'Add new '); 
	
// only show form if it hasn't been completed successfully: 
// to save repeated inserts/edits
if(form_success($user_feedback) !== true):
	$objForm = new Form($objScaffold->getFields(), $objScaffold->getFieldsDetails(), $objScaffold->getForeignKeys(), $properties);
?>
<form id="<?php echo $action; ?>_form" action="" method="post">
    <fieldset>
       <legend><?php echo ($action == 'add') ? 'Add a new ' . $objScaffold->getName() : 'Edit the ' . $objScaffold->getName() . ', <em>' . stripslashes($properties['title']) . '</em>'; ?></legend>
        <p class="instructions"><?php echo ($action == 'add') ? 'Add a new ' . $objScaffold->getName() : 'Edit the ' . $objScaffold->getName() . ', <em>' . stripslashes($properties['title']) . '</em>'; ?> by filling in the form details below. <br />
        <strong>Note:</strong> Items marked with a star are required fields.</p>
        <?php echo str_replace($outgoings_form_search, $outgoings_form_replace, $objForm->getAllFieldElements()); ?>
        <?php if($action == 'edit'): ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <?php endif; ?>
        <?php if($action != 'edit'): ?>
        <?php include('repeat.php'); ?>
        <?php endif; ?>
        <input type="hidden" name="vat_rate" id="vat_rate" value="<?php echo VAT; ?>" />
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