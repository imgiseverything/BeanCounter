<?php

	/**
	 *	Outgoings download view
	 */
	
	// Page details
	$objTemplate->setTitle($objScaffold->getPageTitle());
	
	// Style / Appearance (CSS)
	$objTemplate->setStyle(array('tables', 'projects', 'receipt'));

	
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="robots" content="noindex,nofollow" />
	<title><?php echo SITE_NAME . ' | Remittance Advice | #' . $id; ?></title>
	<style type="text/css" media="all">
	<?php echo reduceFileSize($objDownload->getStyle()); ?>
	</style>
	<link rel="stylesheet" type="text/css" media="print" href="http://<?php echo $objApplication->getSiteUrl(); ?>/style/print.css" />
</head>
<body class="download invoice">
<div class="download-container">
<div class="site-container">
	<div class="group site-header">
		<div class="date"><?php echo DateFormat::getDate('date', $properties['transaction_date']);?></div>
	<?php echo $objTemplate->getBranding(); ?>
	<?php echo $objVcard->getVcard(); ?>
	</div>
	<div id="PrimaryContent">
	<?php
			// create easy to use variables
			extract($properties);

			// Start output buffer - we'll use this content to create a cache and in turn a downloadable file
			ob_start(); // Turn on output buffering
	?>

		<h1>Remittance advice</h1>
		 <div id="ClientDetails">
		 	<h2>For the attention of:</h2>
			<?php
			// Get Client Vcard
			$objSupplier = new Supplier($db, array(), $outgoing_supplier);
			$objVcardSupplier = new Vcard($objSupplier->getProperties());
			echo $objVcardSupplier->getVcard();
			?>
		</div>
		<p><strong>Payment made by <?php echo ($outgoing_payment_title); ?> on <?php echo DateFormat::getDate('date', $transaction_date); ?></strong> </p>
		<p><strong>Your ref:</strong> <?php echo $transaction_id?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Our ref:</strong> <?php echo $id; ?></p>
		<table>
			<thead>
				<tr>
					<th scope="col">Item</th>
					<th scope="col"><?php echo CURRENCY; ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="row">VAT (included):</th>
					<td><?php echo currency($vat); ?></td>
				</tr>
				<tr>
					<th scope="row">Total:</th>
					<td><?php echo currency($price); ?></td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td><?php echo stripslashes($title); ?><?php echo ($description) ? '<br />' . stripslashes(str_replace(array('>rn<', 'rnrn'), array(">\r\n<", "\r\n\r\n"), $description)) : ''; ?></td>
					<td><?php echo currency($price); ?></td>
				</tr>
			</tbody>
		</table>
</div>
<?php
	$page_content = ob_get_clean();	
	$page_content = reduceFileSize($page_content);	
	echo $page_content;	
	
	
	$cached_file = SITE_PATH . 'cache/' . $objScaffold->getName() . '/download-' . md5(SECRET_PHRASE . $objScaffold->getId()) . '.html';
	
	
	if(CACHE === true){
		// Cache content for email invoicing
		$objCache = new Cache($cache_filename, 1, $objScaffold->getName());
		$objCache->createCache($page_content, false, false);
	} else{
		$handle = fopen($cached_file, "w");

		if($handle){
			fwrite($handle, $page_content);
			fclose($handle);
		} 
	}
	

	// Footer
	include($objApplication->getViewFolder() . 'layout/footer_download.php'); 
?>