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
		<?php echo $objTemplate->getDescription(); ?>
		<?php echo $objTemplate->getStyle(); ?>
		<link rel="stylesheet" type="text/css" media="print" href="/style/print.css" />
		<link rel="home" title="Home" href="http://<?php echo $objApplication->getSiteUrl(); ?>/" />
		<link rel="Shortcut Icon" href="/favicon.ico" type="image/x-icon" />
		<?php echo $objTemplate->getRobots(); ?>
	</head>
<body id="<?php echo $objTemplate->getBodyId(); ?>" class="<?php echo $objTemplate->getBodyClass(); ?>">
<div id="BeanCounter" class="group">
	<div class="inner">
		<span class="logo">Bean Counter</span>
	</div>
</div>
<?php include($objApplication->getViewFolder() . $objTemplate->getWelcomeNote()); ?>
<div id="Container" class="group">
    <header id="Header" class="group">
    	<div class="inner">
        	<?php echo $objTemplate->getBranding(); ?>
        </div>
    </header>
<?php 
	// Menu
	include_once($objApplication->getViewFolder() . $objTemplate->getMenu()); 
?>
<div id="Content" class="group">
	<div class="inner">
