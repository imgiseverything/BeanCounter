<?php

	/**
	 *	Settings add/edit form
	 */
	 	
	// only show form if it hasn't been completed successfully: 
	// to save repeated inserts/edits
	if(form_success($user_feedback) !== true):
?>
<form id="<?php echo $action; ?>_form" action="" method="post">
    <fieldset>
       <legend>Edit these <?php echo $objScaffold->getNamePlural(); ?></legend>
        <p class="instructions">Edit these <?php echo $objScaffold->getNamePlural(); ?> by filling in the form details below.<br />
        <strong>Note:</strong> Items marked with a star are required fields.</p>
<?php

			// Field swhich areb to be displayed (all other fields will be hidden)
			$activeFields = array();
        
        	// Fields which require the int class because they are usually small numbers
        	$arrInts = array(
        		'VAT rate', 
        		'National insurance', 
        		'Income tax rate', 
        		'Postal code', 
        		'Bank account number', 
        		'Bank sort code',
        		'Start of financial year'
        	);
        	
        	// Fields which need the required class because they are mandatory fields
        	$arrRequired = array(
        		'Website name', 
        		'Address line 1', 
        		'City/Town', 
        		'Postal code', 
        		'Country', 
        		'Main telephone number', 
        		'Email address', 
        		'Main currency', 
        		'Start of financial year'
        	);
        	
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
				
				
				switch($title):
					default:
						echo '<label for="' . $element_name . '"' . $element_class . '>' . $title . $label_required . '</label>' . "\n";
						echo '<input type="text" name="' . $element_name . '" id="' . $element_name . '" value="' . stripslashes($value) . '"' . $element_class . $attribute_required . ' /> <p class="help">' . $description . '</p>'."\n";
						break;
						
					case 'currency_value':
						break;
						
					case 'Main currency';
						echo '<label for="' . $element_name . '"' . $element_class . '>' . $title . '</label>' . "\n";
?>
						<select name="main_currency" id="main_currency">
							<?php echo drawDropDown(getDropDownOptions('currency', false, 'title'), $value); ?>
						</select>
<?php
						break;
					
				endswitch;
				
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
<ul class="options">
    <li><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></li>
    <li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $objScaffold->getId(); ?>/">Edit <?php echo stripslashes($objScaffold->getNamePlural()); ?></a></li>
</ul>
<?php endif; ?>