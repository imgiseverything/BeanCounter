<?php

	/**
	 *	System settings view
	 *  View one system setting
	 */

	$objViewSnippet = new ViewSnippet();
	

	// Page details
	$objTemplate->setTitle('Edit settings');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter', 'plugins/jquery.form', 'ajax_form_submit'));
	
	// Menus
	$objMenu->setBreadcrumb('Edit settings');
	
	$objForm = new Form();
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
    	<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1>Edit your <?php echo $settings_type . ' ' . $objScaffold->getNamePlural(); ?></h1>
        <?php echo $objFeedback->getFeedback(); ?>
<?php 

	// only show form if it hasn't been completed successfully: 
	// to save repeated inserts/edits
	if(form_success($user_feedback) !== true):
?>
		<form id="<?php echo $action; ?>_form" action="" method="post"<?php echo (isset($_GET['type']) && $_GET['type'] == 'appearance') ? ' enctype="multipart/form-data"' : ''; ?>>
		    <fieldset>
		        <p class="instructions">Edit these <?php echo $settings_type . ' ' . $objScaffold->getNamePlural(); ?> by filling in the form details below.<br />
		        <strong>Note:</strong> Items marked with a star are required fields.</p>
<?php
		
				// Loop through and show fields
	        	
				for($i = 0; $i < $properties_size; $i++):
				
					extract($properties[$i]);
					
					$class = array();
					$element_name = str_replace(array(' ', '/'), '_', strtolower($title));
					$class[] = (in_array($title, $arrInts)) ? 'int' : '';
					$class[] = (in_array($title, $arrRequired)) ? 'required' : '';
					
					$element_class = (!empty($class)) ? ' class="' . trim(join(' ', $class)) . '"' : '';
					
					$label_required = '';
					$attribute_required = '';
					
					if(in_array($title, $arrRequired)){
						$label_required = ' <span class="required">* <span class="off-screen">Required</span></span>';
						$attribute_required = ' required="required" aria=required="true"';
					}
					
					if(in_array($title, $activeFields)){
						
						echo '<div class="field">' . "\n";
					
						switch($title){
							default:
								echo '<label for="' . $element_name . '"' . $element_class . '>' . $title . $label_required . '</label>' . "\n";
								echo '<input type="text" name="' . $element_name . '" id="' . $element_name . '" value="' . stripslashes($value) . '"' . $element_class . $attribute_required . ' /> <p class="help">' . $description . '</p>' . "\n";
								break;
								
							
							case 'Invoice appendix':
								echo '<label for="' . $element_name . '"' . $element_class . '>' . $title . $label_required . '</label>' . "\n";
								echo '<textarea name="' . $element_name . '" id="' . $element_name . '"' . $element_class . $attribute_required . '>' . stripslashes($value) . '</textarea> <p class="help">' . $description . '</p>' . "\n";
								break;
								
							case 'currency_value':
								break;
								
							case 'Main currency';
								$currency_options = drawDropDown(getDropDownOptions('currency', false, 'title'), $value);
							
								echo '<label for="' . $element_name . '"' . $element_class . '>' . $title . '</label>' . "\n";
								echo '<select name="main_currency" id="main_currency">' . $currency_options . '</select>' . "\n";
								break;
								
							case 'Start of financial year':
								$day = substr($value, 0, 2);
								$month = substr($value, 2, 2);
								echo '<fieldset class="date">' . "\n";
								echo '<legend>' . $title . '</legend>' . "\n";
								echo '<p>' . $description . '</p>';
								echo FormDate::getDay($element_name, read($_POST, $element_name . '_day', $day));
								echo FormDate::getMonth($element_name, read($_POST, $element_name . '_month', $month));
								echo '<input type="hidden" name="' . $element_name . '" id="' . $element_name . '" value="' . stripslashes($value) . '">';
								echo '</fieldset>';
								break;
							
						}
						
						echo '</div>' . "\n";
					
					} else{
						// these fields are hidden
						echo '<input type="hidden" name="' . $element_name . '" value="' . stripslashes($value) . '" />' . "\n";
					}
					
				endfor;
?>
				
		       <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
		       <button type="submit" class="submit">Submit<?php if($action == 'edit'): echo ' changes'; endif;?></button>
		    </fieldset>
		</form>
		<hr />
		<p><a href="/settings/">Cancel</a></p>
		<?php else:  // we have successfully submitted the form so give user some options ?>
		<?php include(APPLICATION_PATH . '/views/common/close_popup.php'); ?>
		<hr />
		<h2>Your options</h2>
		<ul>
		    <li><a href="<?php echo $objScaffold->getFolder(); ?>">Review <?php echo $objScaffold->getNamePlural(); ?></a></li>
		    <li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $objScaffold->getId(); ?>/?type=<?php echo $settings_type; ?>">Edit <?php echo $settings_type . ' ' . stripslashes($objScaffold->getNamePlural()); ?> again</a></li>
		</ul>
		<?php endif; ?>
        
	</div>
<?php include($objTemplate->getFooterHTML()); ?>