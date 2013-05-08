<?php

	/**
	 *	Errors view e.g. 404, 500 etc
	 */

	// Page details
	$objTemplate->setTitle('Website error');
	$objTemplate->setDescription('This is an error page');
	$objTemplate->setBodyClass('error e' . read($_GET, 'error', '404'));
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('login')); // must be an array
	$objTemplate->setExtraStyle('ul#menu{width: 620px;} ul#menu li{ display: inline-block; }');
	
	// Behaviour / Interaction (Unobtrusive JavaScript files)
	$objTemplate->setBehaviour(); // must be an array
	$objTemplate->setExtraBehaviour();
	
	// Google Analytics (Usability/Error) Tracking
	$objTemplate->setStats('pageTracker._trackPageview("errors/' . read($_GET, 'error', '404') . $_SERVER['REQUEST_URI'] . '");');
	
	include($objTemplate->getHeaderHTML());
?>
	<div id="PrimaryContent" class="content-primary">
          	<h1>Website error</h1>
			<?php 
           	if(isset($_GET['error'])): // make sure an error variable is set
				switch($_GET['error']):
					default:
					case 404: // 404 Page Not Found
			?>
    		<p>The page you have requested, <em>http://<?php echo $objApplication->getSiteUrl(); ?><?php echo $_SERVER['REQUEST_URI']; ?></em>, is missing. </p>
			<h3>Why has this happened?</h3>
			<ul>
              <li>you may have mistyped the web address</li>
			  <li>a search engine may be listing an old web address</li>
			  <li>there may be an error on our part </li>
		  	</ul>
			<?php
						break;
						
					case 500: // 500 Internal Server Error
			?>
				<p>Our website has encountered an error and is not allowing you to view this page. This error has been reported and our technical team will be try to fix it as quickly as possible.</p>
			<?php		
						break;
						
					case 401: // 401 Unautorised access
					case 403:
			?>
				<p>Your are not authorised to view the page you have requested. </p>
			<?php					
						break;
						
				endswitch; // end switch
			?>
			<?php endif; // end if error variable exists  ?>
			<p><a href="/">Go to Dashboard</a></p>
	</div>
<?php include($objTemplate->getFooterHTML()); ?>