<?php

	/**
	 *	Cache cleared View
	 */
	
	// Page details
	$objTemplate->setTitle('Cache cleared');

	
	// HTML header
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
		<?php echo $objMenu->getBreadcrumb(); ?>
    	<h1>Cache cleared</h1>
    	<p>The website's cache has been cleared. Meaning any changes made to the website will now be in effect.</p>
        <p><a href="<?php echo read($_SERVER, 'HTTP_REFERER', '/'); ?>">Go back to where you came from</a></p>
        <h2>What is a cache?</h2>
        <p>Most websites run on a database. Whenever someone loads a webpage the website's code checks the database for the relevant information. The cache stores that information so we don't have to keep going back and forth to the database. This speeds up the website for users, and stops our server from having a heart attack.</p>
        <p>But, having a cache sometimes means adding something new to a website doesn't show up straightaway which can be frustrating. Clearing the cache like you just have done should fix issues like that.</p>
        <h2>Help my new content isn't showing up on the website</h2>
        <p>If you clear the cache but your new content still isn't showing - double check that you put the correct dates in the forms where applicable. Some items have a &quot;date published&quot; option; if you select a date in the future for this value the content won't show on the website until that date.</p>
        <p><a href="<?php echo read($_SERVER, 'HTTP_REFERER', '/'); ?>">Go back to where you came from</a></p>
    </div>
<?php include($objTemplate->getFooterHTML()); ?>