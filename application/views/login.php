<?php

	/**
	 *	Log in View
	 */
	 
	$url_forwarder = ($objApplication->getParameter('url')) ? '?url=' . $objApplication->getParameter('url') : '';
	
	// Page details
	$objTemplate->setTitle('Log in');
	$objTemplate->setDescription();
	$objTemplate->setBodyClass('login');
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms', 'login')); // must be an array
	$objTemplate->setExtraStyle();
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('jquery')); // must be an array
	$objTemplate->setExtraBehaviour();
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	 <!-- // PRIMARY CONTENT DIV  // -->
	<div id="PrimaryContent">
		<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1>Log in</h1>
        <?php echo $objFeedback->getFeedback(); ?>
        <form id="loginForm" action="/login/" method="post">
                <fieldset>
                	<div class="field">
                		<label for="email">Email address</label>
                    	<input type="email" value="<?php echo $objAuthorise->getEmail(); ?>" name="email" id="email" required="true" aria-required="true" />
                	</div>
                	<div class="field">
                		<label for="password">Password</label>
                    	<input type="password" value="" name="password" id="password" class="password" required="true" aria-required="true" /> <span class="help"></span>
                    </div>
                	<div class="field">
                   	 <input type="checkbox" value="1" name="remember" id="remember" class="checkbox"<?php echo isChecked(1, $objAuthorise->getRemember());?> />
                   	 <label for="remember" class="checklabel">Remember me?</label>
                    </div>
                    <input type="hidden" name="forward" value="<?php echo $objApplication->getParameter('url'); ?>" />
                    <input type="hidden" name="action" value="login" />
                	<button name="submit" type="submit">Submit</button>
                </fieldset>
            </form>
            <p>I've got an account but <a href="/forgot/<?php echo $url_forwarder; ?>">I've forgotten my password</a><br /> <em>or</em> <br />I haven't got an account so, <a href="/register/<?php echo $url_forwarder; ?>"> I need to register</a>.</p>
	</div>
<?php include($objTemplate->getFooterHTML()); ?>