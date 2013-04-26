<?php 

/**
 *	Sidebar Forms
 *	Generic sidebar content - used in most object_form.php views	
 */

// only show sidebar if we're in normal mode - if we're in AJAX mode it isn't required
if($objTemplate->getMode() == 'normal'):
?>
<div id="SecondaryContent">
	
</div>
<?php endif; ?>
