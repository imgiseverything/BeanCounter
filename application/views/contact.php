<?php

	/**
	 *	Contact form view
	 * 	Allow users to send email to site owner
	 */
	
	// Page details
	$objTemplate->setTitle('Contact');
	$objTemplate->setDescription('Contact details for ' . SITE_NAME);
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('forms'));
	$objTemplate->setExtraStyle('
		textarea#message{
			height: 300px;
		}
	');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(array('vendor/jquery', 'beancounter'));
	$objTemplate->setExtraBehaviour();
	
	// Google analytics  (usability/error) tracking 
	$objTemplate->setStats(trackUserFeedback($user_feedback));
	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
	    <h1>Email <?php echo SITE_NAME; ?></h1>
		<?php echo $objFeedback->getFeedback(); ?>
        <?php if(form_success($user_feedback) !== true): /* don't show form if email successfully sent */ ?>
        <form id="contactForm" action="" method="post">
            <fieldset>
                <p class="instructions"><strong>From:</strong> <?php echo $from; ?></p>
                <input type="hidden" name="name" id="name" value="<?php echo $name; ?>" />
                <input type="hidden" name="email" id="email" value="<?php echo $email; ?>" />
                <label for="message" class="required">Message:</label>
                <textarea name="message" id="message" class="required"><?php echo $message; ?></textarea>
                <button type="submit" class="positive">Send</button>
            </fieldset>
        </form>
        <hr />
        <p><a href="/" class="cancel">Cancel</a></p>
        <?php else: // email successfully sent ?>
        <p>I'll be in touch shortly</p>
        <?php endif; ?>
	</div>
	<div class="content-secondary">
		<h2>Contact details</h2>
        <?php echo $objVcard->getVcard(); ?>
	</div>
<?php include($objTemplate->getFooterHTML()); ?>