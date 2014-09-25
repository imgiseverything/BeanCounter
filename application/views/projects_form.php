<?php
	/**
	 *	Projects view
	 *  Add new project
	 */
	
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));

	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'colorbox', 'plugins/jquery.date', 'plugins/jquery.datepicker', 'datepicker', 'hide_time', 'projects_dates', 'vat', 'plugins/jquery.form', 'ajax_form_submit'));
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <?php include($form); ?>
	</div>
	<?php 
	// Sidebar
	if(form_success($user_feedback) !== true && $action != 'delete'): 
		//include(APPLICATION_PATH.'/views/common/projects_help_sidebar.php'); 
	endif; 
	?>
<?php include($objTemplate->getFooterHTML()); ?>