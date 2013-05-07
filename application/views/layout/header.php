<?php
/**
 * Header template file
 * Basic HTML that is used on the top of every page 
 * where it is in normal (full screen) mode
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php echo $objTemplate->getTitle(); ?></title>
		<script>document.documentElement.className = document.documentElement.className.replace(/\bno-js\b/,'js');</script>
		<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
		<meta name="viewport" content="width=device-width">
		<?php echo $objTemplate->getStyle(); ?>
		<link rel="stylesheet" media="print" href="/style/print.css" />
		<link rel="home" title="Home" href="http://<?php echo $objApplication->getSiteUrl(); ?>/" />
		<?php echo $objTemplate->getRobots(); ?>
	</head>
<body id="<?php echo $objTemplate->getBodyId(); ?>" class="<?php echo $objTemplate->getBodyClass(); ?>">
<div class="group product-branding">
	<div class="inner">
		<a href="<?php echo $objApplication->getApplicationUrl(); ?>" class="logo"><?php echo $objApplication->getApplicationName(); ?></a>
	</div>
</div>
<?php include($objApplication->getViewFolder() . $objTemplate->getWelcomeNote()); ?>
<div class="site-container">
    <header class="group site-header">
    	<div class="inner">
        	<?php echo $objTemplate->getBranding(); ?>
        </div>
    </header>
<?php 
	// Menu
	include_once($objApplication->getViewFolder() . $objTemplate->getMenu()); 
?>
<div class="group site-content">
	<div class="inner">
