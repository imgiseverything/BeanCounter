<?php
	
	/**
	 *	Forgotten Password View
	 */
	 
	$url_forwarder = ($objApplication->getParameter('url')) ? '?url=' . $objApplication->getParameter('url') : '';
	
	// Page details
	$objTemplate->setTitle('Forgot password');
	$objTemplate->setBodyClass('login forgot');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('main.min', 'login'));
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(); // must be an array
	$objTemplate->setExtraBehaviour();
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
		<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1>Forgot your password?</h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <?php 
        // don't show form is password reset was successful
        if(form_success($user_feedback) !== true): 
        ?>
        <form id="forgotForm" action="/forgot/" method="post">
            <fieldset>
	            <p class="instructions">In order to maintain security, passwords are encrypted. This means we'll have to email you a new <strong>randomly generated</strong> password if you have forgotten yours. You can change this new password the next time you log in.</p>
	            <div class="field">
	           		<label for="email">Email address</label>
	            	<input type="email" value="<?php echo $objAuthorise->getEmail(); ?>" name="email" id="email" required="required" aria-required="true" />
	            </div>
	            <input type="hidden" name="forward" value="<?php echo $objApplication->getParameter('url'); ?>" />
	            <input type="hidden" name="action" value="forgot" />
	            <button name="submit" type="submit">Send me a new password</button>
            </fieldset>
        </form>
        <p><a href="/login/<?php echo $url_forwarder; ?>">Back to log in screen</a></p>
        <?php else: // password reset was successful ?>
        <p><a href="/login/<?php echo $url_forwarder; ?>">Log in here</a></p>
        <?php endif; ?>
	</div>
<?php include($objTemplate->getFooterHTML()); ?>