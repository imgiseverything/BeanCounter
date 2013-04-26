<?php

/**
 *	Projects help sidebar
 */

?>
<div id="SecondaryContent">
 	<h2>Help</h2>
 	<p>Forms can be confusing so this handy-guide should explain what the forms to your left mean.</p>
 	<h3>Questions</h3>
 	<dl>
 		<dt>Where do I add the price for the invoice/quote?</dt>
 		<dd><?php echo $objApplication->getApplicationName(); ?> allows for itemised quotes. Once you've added the project you can then add tasks on the next screen. <?php echo $objApplication->getApplicationName(); ?> will then add up the values for each task to get the full invoice price.</dd>
 		<dt>What do the stars mean?</dt>
 		<dd>The stars mean, the form field is required meaning you have to enter information in that field.</dd>
 		<dt>Will this automatically send this quote/invoice to my client?</dt>
 		<dd>No it won't.</dd>
 	</dl>
 	<?php if($action == 'add' || $action == 'edit') : ?>
 	<h3>Form items explained</h3>
 	<dl>
 		<dt>Client</dt>
 		<dd>Select who this invoice/quote is for? If the client isn't present click the add button <img src="/images/icons/add.png" alt="" /> next to the drop down and add a new client in the popup.</dd>
 		<dd>When that's done, close the popup and click the refresh button <img src="/images/icons/refresh.png" alt="" /> to update the drop down menu.</dd>
 		<dt>Title</dt>
 		<dd>Try to give your invoice/quote a meaningful name</dd>
 		<dt>Internal reference number</dt>
 		<dd>A unique ID for your records. Click the refresh button <img src="/images/icons/refresh.png" alt="" /> to generate one for you.</dd>
 		<dt>External reference number</dt>
 		<dd>The unique ID that your client refers to this invoice as.</dd>
 		<dt>Description</dt>
 		<dd>The information you want to display before your itemised costings. Use this to thoroughly detail the work agreed.</dd>
 		<dt>Appendix</dt>
 		<dd>The information you want to display after your itemised costings.</dd>
 		<dt>Requires deposit?</dt>
 		<dd>Do you require 50% up front for this work?</dd>
 		<dt>Project stage</dt>
 		<dd><strong>Proposal:</strong> Is the work just a quote?</dd>
 		<dd><strong>Started:</strong> Have you started the work?</dd>
 		<dd><strong>Invoiced:</strong> Are you awaiting payment for it?</dd>
 		<dd><strong>Completed:</strong> Or is it all paid up and finished?</dd>
 		<dt>Payment required</dt>
 		<dd>What date is/was the invoice due to be paid?</dd>
 		<dt>Transaction date</dt>
 		<dd>What date is/was the invoice actually paid?</dd>
 	</dl>
 	<?php endif; ?>
 </div>