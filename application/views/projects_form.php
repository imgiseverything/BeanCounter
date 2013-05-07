<?php
	/**
	 *	Projects view
	 *  Add new project
	 */
	
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	//$objTemplate->setDescription('This is a scaffold page for '.SITE_NAME);
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables', 'projects', 'colorbox', 'datepicker'));
	$objTemplate->setExtraStyle('');

	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery', 'beancounter', 'colorbox', 'jquery.date', 'jquery.datepicker', 'datepicker', 'hide_time', 'projects_dates', 'vat', 'jquery.form', 'ajax_form_submit'));
	
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