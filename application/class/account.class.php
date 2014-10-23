<?php
/**
 *	===================================================================================
 *	
 *	Account Class
 *	-----------------------------------------------------------------------------------
 *	
 *	View profit/loss on sales and outgoings
 *	
 *	===================================================================================
 *
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 	2008-2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.0
 *	@author		philthompson.co.uk
 *	@since		27/02/2008
 *	
 *	@modified	29/05/2010
 *	
 *	===================================================================================
 *	
 *	Table of Contents
 *	-----------------------------------------------------------------------------------
 *
 *	variables
 *	
 *	methods
 *		constructor
 *		getOutgoings
 *		getProjectPayments
 *		consolidate
 *		setTotal
 *		setIncomeTax
 *		setNI
 *		setProfit
 *		setFirstTradingDate
 *		getVATOwed
 *		getVATDue
 *	
 *	===================================================================================
 *	
 */
	

	class Account{
	
		// Variables
		
		/**
		 *	@var array
		 */
		protected $_properties = array();
		
		/**
		 *	@var int
		 */
		protected $_total = 0;
		
		/**
		 *	@var array
		 */
		protected $_filter = array();
		
		/**
		 *	@var int
		 */
		protected $_subtotal = 0;
		
		/**
		 *	@var string
		 *	DDMM of the tax year e.g. 0604 would be 6th April
		 *	Used to calculate which records to show first
		 */
		protected $_taxYearStart = '0604';
		
		/**
		 *	@var string
		 *	DDMM of the tax year e.g. 0504 would be 5th April
		 *	Used to calculate which records to show first
		 */
		protected $_taxYearEnd = '0504';
		
		/**
		 *	@var string
		 *	YYYY/YYYY of current tax year
		 */
		protected $_currentTaxYear = '20092010';
		
		/**
		 *	@var float
		 */
		protected $_incomeTaxRate = 0;
		
		/**
		 *	@var float
		 */
		protected $_incomeTax = 0;
		
		/**
		 *	@var float
		 */
		protected $_NIRate = 0;
		
		/**
		 *	@var float
		 */
		protected $_NI = 0;
		
		/**
		 *	@var float
		 */
		protected $_VATRate = 0;
		
		/**
		 *	@var float
		 */
		protected $_VATOwed = 0;
		
		/**
		 *	@var float
		 */
		protected $_VATDue = 0;
		
		/**
		 *	@var float
		 */
		protected $_profit = 0;
		
		/**
		 *	@var string
		 */
		protected $_firstTradingDate;
		
		/**
		 *	@var int
		 */
		protected $_firstTradingYear;
		
		/**
		 *	@var array
		 */
		private $_firstDate = array();	
		
		/**
		 *	@var array
		 */
		protected $_projects = array();	
		
		/**
		 *	@var array
		 */
		protected $_outgoings = array();	
		
		/**
		 *	@var float
		 */
		protected $_projectsTotal = 0;
		
		/**
		 *	@var float
		 */
		protected $_outgoingsTotal = 0;
		
		/**
		 *	@var int
		 */
		protected $_totalOutgoings = 0;
		
		/**
		 *	@var int
		 */
		protected $_totalProjects = 0;
		
		/**
		 *	@var int
		 */
		protected $_currentPage = 1;
		
		/**
		 *	@var int
		 */
		protected $_perPage = 0;
		
		/**
		 *	@var string
		 */
		protected $_orderBy = 0;
		
		/**
		 *	@var string
		 */
		protected $_search;
		
		/**
		 *	@var string
		 *	past or future
		 */
		protected $_tense;
		
		/**
		 *	@var int
		 */
		protected $_status;
		
		/**
		 *	@var int
		 *	number of days
		 */
		protected $_timeframe;
		
		/**
		 *	@var array
		 */
		protected $_timeframeCustom;
		
		/**
		 *	@var object
		 *	Local database connection object
		 *	@see	ezsql.class.php
		 */
		protected $_db;
		
		/**
		 *	@var string
		 */
		protected $_name;
		
		/**
		 *	@var string
		 */
		protected $_namePlural;
		
		/**
		 *	@var string
		 */
		protected $_folder;
		
		/**
		 *	@var object
		 */
		protected $_dateFormat;
	
	
		/**
		 *	constructor
		 *	@param object $objApplication
		 *	@param object $db
		 */
		public function __construct($db){	
			
			// setup local database object
			$this->_db = $db;
			
			$this->_dateFormat = new DateFormat();
			
			$this->_taxYearStart = TAX_YEAR_START;
			$this->setCurrentTaxYear();
		
			// Object naming conventions
			$this->_name = 'account';
			$this->_namePlural = 'accounts';
			$this->_folder = '/' . $this->_namePlural . '/details/';	
			
			$this->_incomeTaxRate = INCOME_TAX; // in percent
			$this->_NIRate = NI_TAX; // in percent
			$this->_VATRate = VAT; // in percent

		}
		
		/**
		 *	setData
		 */
		public function setData($filters){
		
			$this->_filter = $filters;
		
			// Object Population Filters
			$this->_currentPage = read($this->_filter, 'current_page', 1); // what page of results are we on (e.g. usually 1 unless there are lots of results
			$this->_perPage = read($this->_filter, 'per_page', 20); //  how many results do we show per page?
			$this->_orderBy = read($this->_filter, 'order_by', 'date'); // how shall we order the results (if more than one exists)?
			$this->_status = read($this->_filter, 'status', 1); // are we showing all results are just those of a specific status e.g. active or inactive
			$this->_tense = read($this->_filter, 'tense', 'past'); // are we showing all results or just ones that have been explicitly published
			$this->_search = read($this->_filter, 'search', ''); // is someone searching the object for specific keywords?
			$this->_timeframe = read($this->_filter, 'timeframe', ''); // show data from a set timeframe e.g. last 7 days
			$this->_timeframeCustom = read($this->_filter, 'timeframe_custom', ''); // show data from a user defined timeframe e.g. from one date to another
			$this->_type = read($this->_filter, 'type', 'all');
		
			// get project payments
			$this->getProjectPayments();
			// get outgoings
			$this->getOutgoings();
				
			
			// consolidate outgoings and projects
			$this->consolidate();			
				
			$this->setTotal();
			
			
			
			// get subtotal profit- loss
			$this->_subtotal = ($this->_projectsTotal + $this->_outgoingsTotal);
			//$this->_subtotal = ($this->_subtotal - (float)$this->_VATDue);
			
			// subtract taxes
						
			// Tax
			$this->setIncomeTax();
			// NI
			$this->setNI();
			
			// VAT
			//$this->setVAT();
			
			// get total
			$this->setProfit();
			
			
			// get the first trading date
			$this->setFirstTradingDate();
		}
		
		
		/**
		 *	getOutgoings
		 */
		public function getOutgoings(){
		
			$this->_totalOutgoings = 0;
			
			if($this->_type != 'incomings'){
			
				$this->_filter['order_by'] = 'transaction_date';
				// initialise outgoing object
				$objOutgoing = new Outgoing($this->_db, $this->_filter);
			
				$this->_outgoings = $objOutgoing->getProperties();
				
				// what are the total number of outgoings
				$this->_totalOutgoings = $objOutgoing->getTotal();
				
				$this->_firstDate[] = strtotime($objOutgoing->getFirstYear());
				
				$this->_VATOwed = $objOutgoing->getVATTotal();
			}
		}
		
		
		/**
		 *	getProjectPayments
		 */
		public function getProjectPayments(){
		
			$this->_totalProjects = 0;
			$this->_projects = array();
		
			if($this->_type != 'outgoings'){	
				// project object filters
				$project_filter = $this->_filter;
				$project_filter['order_by'] = 'transaction_date';
				$project_filter['transaction_date'] = true;
				
				// initialise a ProjectPayment object
				$objProject = new ProjectPayment($this->_db, $project_filter);
				
				$payments = $objProject->getProperties();
				$i = 0;
				if(!empty($payments)){
					foreach($payments as $payment){
						$this->_projects[$i] = $payment;
						
						// work out how much of the payment received was VAT
						if(isset($this->_projects[$i]['project_vat_rate']) && $this->_projects[$i]['project_vat_rate'] > 0){

							// Flat rate has been chosen and flat rate has been set
							if(isset($this->_projects[$i]['project_vat_flat_rate_percentage']) && $this->_projects[$i]['project_vat_flat_rate_percentage'] > 0){
								$this->_projects[$i]['vat'] += ($this->_projects[$i]['price']*($this->_projects[$i]['project_vat_flat_rate_percentage']/100));
							} else{
								// Flat rate has not been chosen
								$this->_projects[$i]['vat'] = calculateVAT($payment['price'], $this->_projects[$i]['project_vat_rate']);
								$this->_VATDue += $this->_projects[$i]['vat'];
							}
							
							$this->_VATDue += $this->_projects[$i]['vat'];
				
							
						}
						
						$this->_projects[$i]['grand_total'] = $payment['price'];
						$i++;
	
					}
				}
				

				$this->_firstDate[] = strtotime($objProject->getFirstYear());
				
				// what are the total number of project payments
				$this->_totalProjects = $i;
								
			}
			
		}
		
		
		/**
		 *	consolidate
		 */
		public function consolidate(){
		
			/*
				we need
				timestamp (for ordering)
			 	transaction_date
				id
				type
				title
				price
			*/
			
			// get Totals
			$this->_projectsTotal = 0;
			$this->_outgoingsTotal = 0;
			
			// format projects
			
			$i = 0; // set counter
			if(!empty($this->_projects)){
				foreach($this->_projects as $property){					
					
					// remove incompleted projects (items with no tranasction date have not ben paid for)
					if(empty($this->_projects[$i]['transaction_date'])){
						//unset($this->_projects[$i]);
					} else{
						
						
					
						// item has been paid for, so set a timestamp - which we'll use to order the accounts by date
						$this->_properties[$i]['timestamp'] = strtotime($this->_projects[$i]['transaction_date']);
						$this->_properties[$i]['transaction_date'] = $this->_projects[$i]['transaction_date'];
						$this->_properties[$i]['unique_id'] = $i;
						$this->_properties[$i]['id'] = $this->_projects[$i]['project'];
						$this->_properties[$i]['payment_date'] = DateFormat::getDate('ddmmyyyy', $this->_projects[$i]['transaction_date']);
						$this->_properties[$i]['payee_name'] = $this->_projects[$i]['client_title'];
						$this->_properties[$i]['category'] = 'invoice';
						$this->_properties[$i]['title'] = 'Invoice #' . Project::referenceNumber($this->_projects[$i]['id'], $this->_projects[$i]['date_added']);
						$this->_properties[$i]['type'] = 'positive';
						
						
						$vat_rate = $this->_projects[$i]['project_vat_rate'];
						$vat_flat_rate_percentage = $this->_projects[$i]['project_vat_flat_rate_percentage'];
						$this->_properties[$i]['vat_rate'] = $vat_rate;
						$this->_properties[$i]['vat_flat_rate_percentage'] = $vat_flat_rate_percentage;
						
						
						/*
							Set Project total = if on VAT Flat Rate scheme the price paid won't be the same as the paid show in accounts as Flat rate means you pay n% of the total:
							- so on a £100 invoice you charge 20 VAT and receive £120 from the client
							- but you pay HRMC only 13.5% of that grand total which would be
							- 120 - (120 * 0.135) which is 16.20 
							- and you get 103.8 from the 120 total
							
							Wow an extra £3.80 per £100
						
						*/
						if(
							isset($vat_rate) && $vat_rate > 0
							&& 
							isset($vat_flat_rate_percentage) && $vat_flat_rate_percentage > 0
						){
							echo $vat_rate . ':' . $this->_projects[$i]['grand_total'] .';';
							$this->_properties[$i]['price'] = ($this->_projects[$i]['grand_total'] - ($this->_projects[$i]['grand_total'] * ($this->_properties[$i]['vat_flat_rate_percentage']/100)));
								
						} else{
							$this->_properties[$i]['price'] = $this->_projects[$i]['grand_total'];
						}
						
						$this->_projectsTotal += $this->_properties[$i]['price'];
						
						
					}
					$i++; // increment counter
				}
			}
			
			// format outgoings
			$ii = $i; // set counter
			$i = 0;
			if(!empty($this->_outgoings)){
				foreach($this->_outgoings as $property){
		
					// item has been paid for, so set a tiemstamp - which we'll use to order the accounts by date
					if(!empty($this->_outgoings[$i]['transaction_date'])){
						$this->_properties[$ii]['timestamp'] = strtotime($this->_outgoings[$i]['transaction_date']);
						$this->_properties[$ii]['transaction_date'] = $this->_outgoings[$i]['transaction_date'];
						$this->_properties[$ii]['unique_id'] = $ii;
						$this->_properties[$ii]['id'] = $this->_outgoings[$i]['id'];
						$this->_properties[$ii]['payment_date'] = DateFormat::getDate('ddmmyyyy', $this->_outgoings[$i]['transaction_date']);
						$this->_properties[$ii]['payee_name'] = $this->_outgoings[$i]['outgoing_supplier_title'];
						$this->_properties[$ii]['category'] = $this->_outgoings[$i]['outgoing_category_title'];
						$this->_properties[$ii]['title'] = $this->_outgoings[$i]['title'];
						$this->_properties[$ii]['price'] = '-'.$this->_outgoings[$i]['price'];
						$this->_properties[$ii]['type'] = 'negative';
						$this->_outgoingsTotal += $this->_properties[$ii]['price'];
						$this->_VATOwed += $property['vat'];
					}
					$i++;
					$ii++; // increment counter
				}
			}
			
			rsort($this->_properties);
		}
		
		
		/**
		 *	setTotal
		 */
		public function setTotal(){
			$this->_total = ($this->_totalOutgoings + $this->_totalProjects);
		}
		
		
		/**
		 *	setIncomeTax
		 */
		public function setIncomeTax(){
			$this->_incomeTax = ($this->_subtotal * ($this->_incomeTaxRate / 100));
		}
		
		
		/**
		 *	setNI
		 */
		public function setNI(){
			$this->_NI = ($this->_subtotal * ($this->_NIRate / 100));
		}
		

		
		/**
		 *	setProfit
		 */
		public function setProfit(){
			$this->_profit = ($this->_subtotal - (float)$this->_incomeTax - (float)$this->_NI);
		}
		
		
		/** 
		 *	setFirstTradingYear() (date technically)
		 * 	grab the first ever transaction (incoming or outgoing) 
		 * 	and use that as the (glorious) first trading date
		 */
		public function setFirstTradingDate(){
			
			// If a first date has been set for projects and/or outgoings work out which was first
			if(!empty($this->_firstDate)){
				sort($this->_firstDate);
				$this->_firstTradingDate = date('Y-m-d H:i:s', $this->_firstDate[0]);
			} else{ // We mustn't have any trading dates so just get today's date
				$this->_firstTradingDate = date('Y-m-d H:i:s');
			}
			
			$this->_firstTradingYear = date('Y', strtotime($this->_firstTradingDate));
			
		}
		
		
		/** 
		 *	setCurrentTaxYear
		 *	Work out which tax year we're in.
		 */
		public function setCurrentTaxYear(){
			
			
			$start_day = substr($this->_taxYearStart, 0, 2);
			$start_month = substr($this->_taxYearStart, -2);
			
			/*
			if the start day is the first of the month
			then the end mon th must be the preceidng month
			*/
			if($start_day != '01'){
				$end_month = $start_month;
			} else{
				$end_month = str_pad(($start_month - 1), 2, 0, STR_PAD_LEFT);
			}
			
			
			if($end_month == $start_month){
				$end_day = str_pad(($start_day - 1), 2, 0, STR_PAD_LEFT);
			} else{
				switch($end_month){
					
					// Jan/Mar/May/Jul/Aug/Oct/Dec have 31 days
					default:
					case '01':
					case '03':
					case '05':
					case '07':
					case '08':
					case '10':
					case '12':
						$end_day = '31';
						break;
						
					// Feb has 28/29 days	
					case '02':
						$end_day = '29';
						break;
					
					// Apr/Jun/Sep/Nov have 30 days
					case '04':
					case '06':
					case '09':
					case '11':
						$end_day = '30';
						break;
				
					
				}
			}
			
			//echo 'Starts on ' . $start_day . '/' . $start_month . '<br />';
			//echo 'Ends on ' . $end_day . '/' . $end_month . '<br />';
			
			$this->_taxYearEnd = $end_day . $end_month;
			
			$current_year = date('Y');
			
			if(date('m') > (int)$start_month || (date('m') == (int)$start_month && date('d') >= (int)$start_day)){
				//echo 'Therefore the current tax year is ' . date('Y') . '/' . (date('Y') + 1);
				$this->_currentTaxYear = date('Y') . (date('Y') + 1);
			} else{
				//echo 'Therefore the current tax year is ' . (date('Y') - 1) . '/' . date('Y');
				$this->_currentTaxYear = (date('Y') - 1) . date('Y');
			}
			
			
		}
		
		
		
		/**
		 *	getName
		 */
		public function getName(){
			return $this->_name;
		}
		
		/**
		 *	getNamePlural
		 */
		public function getNamePlural(){
			return $this->_namePlural;
		}
		
		/**
		 *	getFolder
		 */
		public function getFolder(){
			return $this->_folder;
		}
		
		/**
		 *	getProperties
		 */
		public function getProperties(){
			return $this->_properties;
		}
		
		/**
		 *	getTotal
		 */
		public function getTotal(){
			return $this->_total;
		}
		
		/**
		 *	getCurrentPage()
		 */
		public function getCurrentPage(){
			return $this->_currentPage;
		}
		
		/**
		 *	getPerPage()
		 */
		public function getPerPage(){
			return $this->_perPage;
		}
		
		/**
		 *	getOrderBy()
		 */
		public function getOrderBy(){
			return $this->_orderBy;
		}
		
		/**
		 *	getStatus()
		 */
		public function getStatus(){
			return $this->_status;
		}
		
		/**
		 *	getSearch()
		 */
		public function getSearch(){
			return $this->_search;
		}
		
		/**
		 *	getTimeframe()
		 */
		public function getTimeframe(){
			return $this->_timeframe;
		}
		
		/**
		 *	getTimeframeCustom()
		 */
		public function getTimeframeCustom(){
			return $this->_timeframeCustom;
		}
		
		
		
		/**
		 *	getSubtotal()
		 */
		public function getSubtotal(){
			return $this->_subtotal;
		}
		
		/**
		 *	getProjectsTotal()
		 */
		public function getProjectsTotal(){
			return $this->_projectsTotal;
		}
		
		/**
		 *	getOutgoingsTotal()
		 */
		public function getOutgoingsTotal(){
			return $this->_outgoingsTotal;
		}
		
		/**
		 *	getIncomeTax()
		 */
		public function getIncomeTax(){
			return $this->_incomeTax;
		}
		
		/**
		 *	getNI()
		 */
		public function getNI(){
			return $this->_NI;
		}
		
		/**
		 *	getVATOwed()
		 */
		public function getVATOwed(){
			return $this->_VATOwed;
		}
		
		/**
		 *	getVATDue()
		 */
		public function getVATDue(){
			return $this->_VATDue;
		}
		
		/**
		 *	getProfit()
		 */
		public function getProfit(){
			return $this->_profit;
		}
		
		/**
		 *	getFirstTradingDate()
		 */
		public function getFirstTradingDate(){
			return $this->_firstTradingDate;
		}
		
		/**
		 *	getFirstTradingYear()
		 */
		public function getFirstTradingYear(){
			return $this->_firstTradingYear;
		}
		
		/**
		 *	getTaxYearStart()
		 */
		public function getTaxYearStart(){
			return $this->_taxYearStart;
		}
		
		/**
		 *	getTaxYearEnd()
		 */
		public function getTaxYearEnd(){
			return $this->_taxYearEnd;
		}
		
		/**
		 *	_currentTaxYear()
		 */
		public function getCurrentTaxYear(){
			return $this->_currentTaxYear;
		}
		
		
		
		
	
	}

?>