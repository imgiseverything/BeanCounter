<fieldset>
        	<legend>Does this <?php echo $objScaffold->getName(); ?> repeat in the future?</legend>
	        <div class="field">
	        	<input type="hidden" name="repeated" value="N" />
	        	<input type="checkbox" name="repeated" id="repeated" value="Y"<?php echo isChecked('Y', read($_POST, 'repeated', 'N')); ?> />
	        	<label for="repeated">Yes it repeats</label>
	        </div>
	        <div class="field">
	        	<label for="repeated_frequency">How often does it repeat?</label>
	        	<select name="repeated_frequency" id="repeated_frequency">
		        	<option value="">Please select</option>
		        	<option value="weekly"<?php echo isSelected('weekly', read($_POST, 'repeated_frequency')); ?>>Weekly</option>
		        	<option value="fortnightly"<?php echo isSelected('fortnightly', read($_POST, 'repeated_frequency')); ?>>Fortnightly</option>
		        	<option value="monthly"<?php echo isSelected('monthly', read($_POST, 'repeated_frequency')); ?>>Monthly</option>
		        	<option value="bi-annually"<?php echo isSelected('bi-annually', read($_POST, 'repeated_frequency')); ?>>Bi-annually</option>
		        	<option value="yearly"<?php echo isSelected('yearly', read($_POST, 'repeated_frequency')); ?>>Yearly</option>
	        	</select>
	        </div>
	        <div class="field">
	        	<label for="repeated_number_of_times">How many times does it repeat?</label>
	        	<input type="number" name="repeated_number_of_times" id="repeated_number_of_times" value="<?php echo read($_POST, 'repeated_number_of_times', '0'); ?>" class="int" />
	        </div>
        </fieldset>