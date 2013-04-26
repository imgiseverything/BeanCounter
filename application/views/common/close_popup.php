<?php 

/**
 *	Close popup link
 *	if we're in popup mode show a close link which
 *	close the popup
 */


if(in_array($objTemplate->getMode(), array('popup'))): 

?>
<div class="buttons clearfix">
	<a href="#" class="close-popup" onclick="refreshChoices('<?php echo $objScaffold->getTableName(); ?>');">Close popup</a>
</div>
<?php endif; ?>