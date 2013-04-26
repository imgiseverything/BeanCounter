<?php

	/**
	 *	Accounts (Profit/Loss) Controller
	 *  View profit/loss data for user-defined periods of time
	 */

	// Generic settings
	include(APPLICATION_PATH . "/inc/settings.inc.php");


	// If a basic user (client or supplier) is trying to access accounts, 
	// they aren't allowed to, so give an error message (404)
	if($objAuthorise->getLevel() == 'Basic'){
		$obj404 = new Error();
		$obj404->throw404($objTemplate, $objMenu, $objVcard, $objAuthorise);
	}
	
	
	
	// Initialise Object
	$objScaffold = new AccountOverview($db);

	
	// Tax year variables
	$tax_start_day = substr($objScaffold->getTaxYearStart(), 0, 2);
	$tax_start_month = substr($objScaffold->getTaxYearStart(), -2);
	$tax_start_year = substr($objScaffold->getCurrentTaxYear(), 0, 4);
	
	$tax_end_day = substr($objScaffold->getTaxYearEnd(), 0, 2);	 
	$tax_end_month = substr($objScaffold->getTaxYearEnd(), -2);
	$tax_end_year = substr($objScaffold->getCurrentTaxYear(), -4);
	
	
	// Fake URL variables so we only start by showing the current tax year data
	if(empty($_GET['start_day'])){
		$_GET['start_day'] = $tax_start_day;
	}
	
	if(empty($_GET['start_month'])){
		$_GET['start_month'] = $tax_start_month;
	}
	
	if(empty($_GET['start_year'])){
		$_GET['start_year'] = $tax_start_year;
	}
	
	if(empty($_GET['end_day'])){
		$_GET['end_day'] = $tax_end_day;
	}
	
	if(empty($_GET['end_month'])){
		$_GET['end_month'] = $tax_end_month;
	}
	
	if(empty($_GET['end_year'])){
		$_GET['end_year'] = $tax_end_year;
	}
	
	$objApplicationAccounts = new Application();
	$objApplicationAccounts->setFilters($objAuthorise);
	
	$objApplicationAccounts->setFilter('per_page', 1000);
	$type = $objApplicationAccounts->getParameter('type', 'all');
	$objApplicationAccounts->setFilter('type', $type);
	
	
	// Filter items by the transaction_date field when looking through timeframes
	$objApplicationAccounts->setFilter('date_order_field', 'transaction_date');

	$objScaffold->setData($objApplicationAccounts->getFilters());
	
	
	$properties = $objScaffold->getProperties();
	$properties_size = sizeof($properties);
	
	$months = $objScaffold->getMonths();
	
	$months_size = sizeof($months);

	
	// Pagination object e.g. Previous 1 2 3 4 5 Next
	$objPagination = new Pagination($objScaffold->getTotal(), $objScaffold->getPerPage(), $objScaffold->getCurrentPage()); 
	
	// Download file
	if($objApplication->getAction() == 'download'){
		$objCSV = new CSV($properties, 'accounts', array('payment_date', 'payee_name', 'category', 'title', 'price'));		
	}
	
	
	// Page title
	$accounts_title = 'Monthly profit/loss accounts';
	
	// Graph data
	$graph_data = '';
	foreach($months as $key => $value){
		 $graph_data .= $objScaffold->getMonthlySubtotal($key) . ',';
	}
	$graph_data = substr($graph_data , 0, -1);
	$axis_labels = join(',', array_keys($months));
	
	
	// Download 
	if(!empty($properties) && $action == 'download'){
		$objCSV = new CSV($properties, 'accounts', array('payment_date', 'payee_name', 'category', 'title', 'price'));
	}
	
	if($type != 'all'){	
		// View
		include(APPLICATION_PATH . "/views/accounts_details.php");
	} else{
		// View
		include(APPLICATION_PATH . "/views/accounts_all.php");
	}
	
?>