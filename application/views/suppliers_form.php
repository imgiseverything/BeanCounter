<?php
	/**
	 *	Suppliers view
	 *  View all or individual suppliers and add/edit/delete them
	 */
	

	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'tables'));
	
	$objTemplate->setExtraStyle('
		ul#suppliers_list{
	
		}
	
			ul#suppliers_list li{
				border-bottom: 1px solid #DDD;
				list-style: none;
				margin-left: 0;
				padding: 10px 0;
			}
			
				ul#suppliers_list li a{
					display: block;
				}
	');
	
	// Interaction / Behaviour
	$objTemplate->setBehaviour(array('jquery', 'beancounter',  'extra_details', 'jquery.form', 'ajax_form_submit'));
	
	// Menus
	$objMenu->setBreadcrumb($objScaffold->getBreadcrumb());
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1><?php echo ucfirst($objScaffold->getNamePlural()); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <?php include($objTemplate->getForm()); ?>
	</div>
    <?php include(APPLICATION_PATH . '/views/common/sidebar_forms.php'); ?>    
<?php include($objTemplate->getFooterHTML()); ?>