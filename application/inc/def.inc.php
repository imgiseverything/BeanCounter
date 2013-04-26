<?php

	/**
	 *	Definitions file
	 *	This file defines certain key characteristics of this website
	 */

	/**
	 *	Use object settings as defined variables for easy use across the site 
	 *	e.g. 
	 *	<h1 id="Logo"><?php echo SITE_NAME?; ></h1>
	 *	as opposed to 
	 *	<h1 id="Logo"><?php echo read($objSite->config, 'Website name', ''); ?></h1>
	 *	
	 */
	
	// Site name details
	define('SITE_NAME', read($objSite->config, 'Business name', $objApplication->getApplicationName()));
	
	// EMAIL DETAILS
	define('EMAIL_ADDRESS', read($objSite->config, 'Email address', ''));
	
	// Currency
	define('CURRENCY_TITLE', read($objSite->config, 'Main currency', ''));
	define('CURRENCY', read($objSite->config, 'currency_value', ''));
	
	// Payment
	define('BANK_AC', read($objSite->config, 'Bank account number', ''));
	define('BANK_SC', read($objSite->config, 'Bank sort code', ''));
	define('IBAN', read($objSite->config, 'IBAN', ''));
	
	// TAX
	define('INCOME_TAX', read($objSite->config, 'Income tax rate', ''));
	define('NI_TAX', read($objSite->config, 'National insurance', ''));
	define('VAT', read($objSite->config, 'VAT rate', ''));	
	
	define('TAX_YEAR_START', read($objSite->config, 'Start of financial year', '0604'));
	
	// Defualt tetx to be shown after prices in invoice/proposal
	define('APPENDIX', read($objSite->config, 'Invoice appendix', ''));
		
	// Mileage claimable rates per mile - TODO ensure this is editable
	define('CAR_RATE', '0.45');
	define('BIKE_RATE', '0.20');
	define('PASSENGER_RATE', '0.05');

?>