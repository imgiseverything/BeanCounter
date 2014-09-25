<?php
/**
 * Header (popup mode) template file
 * Basic HTML that is used on top of every page (on popup mode)
 */
?>
<!doctype html>
<!--[if lt IE 7 ]><html lang=en-gb class="no-js ie ie6"><![endif]-->
<!--[if IE 7 ]><html lang=en-gb class="no-js ie ie7"><![endif]-->
<!--[if IE 8 ]><html lang=en-gb class="no-js ie ie8"><![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang=en-gb class=no-js><!--<![endif]-->
<head>
<meta charset=utf-8>
<meta http-equiv=X-UA-Compatible content="IE=Edge;chrome=1">
<title><?php echo $objTemplate->getTitle(); ?></title>
<script>document.documentElement.className = document.documentElement.className.replace(/\bno-js\b/,'js');</script>
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<meta name="viewport" content="initial-scale=1.0,minimal-ui">
<?php echo $objTemplate->getStyle(); ?>
<?php echo $objTemplate->getRobots(); ?>
</head>
<body id="<?php echo $objTemplate->getBodyId(); ?>" class="<?php echo $objTemplate->getBodyClass(); ?>">
<div id="Container" class="site-container">
	<div id="Content"class="site-content">