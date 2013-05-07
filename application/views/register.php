<?php

	/**
	 *	Registration View
	 */
	 
	$url_forwarder = (!empty($_GET['url']))? '?url='. read($_GET, 'url', '') : '';
	
	// Page details
	$objTemplate->setTitle('Register');
	$objTemplate->setDescription();
	$objTemplate->setBodyClass('login register');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'login')); // must be an array
	$objTemplate->setExtraStyle();
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery')); // must be an array
	$objTemplate->setExtraBehaviour('');
	
	$required_label = '<span class="required"> *<span class="off-screen">Required</span></span>';
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
		<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1>Register</h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <?php
        // only show form if it hasn't been completed successfully: to save repeated inserts/edits
		if(form_success($user_feedback) !== true):
		?>
        <form id="registerForm" action="/register/" method="post">
			<fieldset>
				<p class="instructions">Fields marked with a star are required</p>
				<label for="firstname" class="required">First name<?php echo $required_label; ?></label>
				<input type="text" value="<?php echo $firstname; ?>" name="firstname" id="firstname" class="required" required="required" aria-required="true" /><br />
				<label for="surname" class="required">Surname<?php echo $required_label; ?></label>
				<input type="text" value="<?php echo $surname; ?>" name="surname" id="surname" class="required" required="required" aria-required="true" /><br />
				<label for="email" class="required">Email address<?php echo $required_label; ?></label>
				<input type="email" value="<?php echo $email; ?>" name="email" id="email" class="required" required="required" aria-required="true" /><br />
				<label for="password" class="required">Password<?php echo $required_label; ?></label>
				<input type="password" value="" name="password" id="password" class="required password" required="required" aria-required="true" /> <span class="help"></span><br />
				<label for="client" class="required" required="required" aria-required="true">Your company</label>
				<select name="client" id="client" class="required">
				<?php echo drawDropDown(getDropDownOptions('client', 'Choose'), $client); ?>
				</select><br />
				<input type="hidden" name="forward" value="<?php echo $objApplication->getParameter('url'); ?>" />
				<input type="hidden" name="action" value="register" />
				<input type="hidden" name="status" value="2" />
				<button name="submit" type="submit" class="positive secure">Submit registration request</button>
			</fieldset>
		</form>
		<p><a href="/login/<?php echo $url_forwarder; ?>">I'm already registered</a>.<br /> or <br />I'm already registered but <a href="/forgot/<?php echo $url_forwarder; ?>">I've forgotten my password</a>.</p>
		<?php else: // form was successfully posted so tell the new user what to do ?>
		<p><strong>Please note:</strong> Your registration request will have to be authorised by <?php echo SITE_NAME; ?> before you can login.</p>
		<p>An email has been sent to them informing them that you have requested to be registered.</p>
		<?php endif; ?>            
	</div>
<?php include($objTemplate->getFooterHTML()); ?>