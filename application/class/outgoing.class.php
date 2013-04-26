<?php
/*
 *	=========================================================================
 *	
 *	Outgoing Class
 *	-------------------------------------------------------------------------
 *	
 *	Add/edit/delete/view outgoings (exspenses from database)
 *	
 *	=========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 	2008-2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.01	
 *	@author		philthompson.co.uk
 *	@since		10/01/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	20/06/2010
 *	
 *	=========================================================================
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	variables
 *	
 *	construct
 *	
 *	methods
 *	
 *		customQueryFilters()
 *		getFirst
 *		setGrandTotal
 *		remittance
 *		get...
 *	
 *	==========================================================================
 *	
 */
	
	




	class Outgoing extends Scaffold{
	
		// Variables
		
		/**
		 *	@var float
		 */
		protected $_subTotal = 0;
		
		/**
		 *	@var float
		 */
		protected $_vatTotal = 0;
		
		/**
		 *	@var float
		 */
		protected $_grandTotal = 0;
		
		/**
		 *	@var int
		 */
		public $first_year;
		
		/**
		 *	@var int
		 */
		protected $_category;
		
		/**
		 *	@var int
		 */
		protected $_supplier;
		
	
	
		// construct
		public function __construct($db, $filter, $id = false){		
		
			$this->_name = 'outgoing';
			
			$this->_db = $db;
			$this->_filter = $filter;
			$this->_id = $id;
			
			if(!empty($_GET['id_hash'])){
				$this->_idHash = $_GET['id_hash'];
			}
			
			$this->_filter['order_by'] = read($filter, 'order_by', 'transaction_date'); // how shall we order the results (if more than one exists)?
			
			// filter data by supplier and/or category
			$this->_supplier = read($filter, 'supplier', '');
			$this->_category = read($filter, 'category', '');
			
			parent::__construct($db, $this->_filter, $id);
			
			// getFirst
			$this->getFirst();

			
			// generateTotal
			$this->setGrandTotal();
			
			if($this->_id){
				$this->setDocumentation();
			}
		
		
		}
		

		
		/**
		 *	customQueryFilters
		 */
		public function customQueryFilters(){
		
			$this->_queryFilter['custom'] = '';
			
			if($this->_supplier){
				$this->_queryFilter['custom'] .= " AND outgoing_supplier = '{$this->_supplier}' ";
			}
			if($this->_category){
				$this->_queryFilter['custom'] .= " AND outgoing_category = '{$this->_category}' ";
			}
			
		}
		
		/**
		 *	getFirst
		 *	grab the first ever outgoing and use that as the (glorious) first trading date
		 */
		public function getFirst(){
			// 
			$query = "SELECT transaction_date FROM `{$this->_sql['main_table']}` WHERE (transaction_date IS NOT NULL  AND transaction_date != '0000-00-00 00:00:00') ORDER BY transaction_date ASC LIMIT 1;";
					
			//niceError($query); // Debugging echo SQL
			
			// Cache data
			$objCache = new Cache($this->_name . '_first_date.cache', 1, 'account');
			if($objCache->getCacheExists() === true){
				$this->_firstYear = $objCache->getCache();
			}
			else{

				if($result = $this->_db->get_var($query)){
					$this->_firstYear = $result;
				}
				// no results, so first year is this year
				else{
					$this->_firstYear = date('Y-m-d H:i:s');
				}
				
				$objCache->createCache($this->_firstYear);
			}
			// End cache
			
			
		}
		
		
		/**
		 *	setById()
		 *	Load the $_properties with a specific item's 
		 *	info from a supplied id ($this->_id)
		 */
		protected function setById(){
			
			if(!empty($this->_idHash)){
				return $this->setByHash();
			} else{
				return parent::setById();
			}
			
			
		}
		
		
		/**
		 *	setByHash()
		 *	Load the $_properties with a specific item's 
		 *	info from a supplied hash md5(SECRET_PHRASE . int)
		 *	Note: we don't cache this query
		 *	@return	 boolean
		 */
		protected function setByHash(){

			$cache_used = 'No';	
			$objTimer = new Timer();
		
			$query = "SELECT {$this->_sql['select']} 
			FROM `{$this->_sql['main_table']}` t 
			{$this->_sql['joins']} 
			WHERE MD5(CONCAT('" . SECRET_PHRASE . "', t.id)) = '{$this->_idHash}' 
			{$this->_queryFilter['tense']} 
			{$this->_queryFilter['timeframe']} 
			{$this->_queryFilter['custom']}";
			
			$this->_properties = $this->_db->get_row($query, "ARRAY_A");
			
			
			// Debugging - echo SQL
			$query_speed = $objTimer->getSpeed(microtime());			
			niceError($query . '<br />Cache used: ' . $cache_used . '<br />Speed: ' . $query_speed); 
			
			// Run query
			if($this->_properties){ // worked
				$this->_id = $this->_properties['id'];
				return true;
			} else{ 
			
				// Query failed - database down or poorly formed query
				$this->_exists = false;
				return false;
			}

		}

		
		/**
		 *	setGrandTotal
		 */
		public function setGrandTotal(){
			
			// setup a default total of zero incase there are no tasks
			$total = 0;
			$vat = 0;
			
			// get total for one item
			if($this->_id){			
				$total = $this->_properties['price'];
				$vat = $this->_properties['vat'];
			} else{
				// get all item's totals
				$i = 0; //counter
				
				if(!empty($this->_properties)){
				
					// loop through all records
					foreach($this->_properties as $property){
						
						// project has tasks
						if(!empty($property['price'])){
								// add outgoing price onto total
								$total += $property['price'];
						}
						// put total value into relevant property array
						$this->_properties[$i]['total'] = $total;
						
						$i++; // increment counter
					}	
				}
			}
			
			// sub total
			$this->_subTotal = $total;
			$this->_vatTotal = $vat;
			
			$this->_grandTotal = ($vat + $total);
			
		}
		
		
		/**
		 *	remittance
		 *	send client (main contact) an email remittance advice
		 */
		public function remittance(){
			//
			
			$error = 0; // error counter, increment for every error

			// grab invoice file via download object
			$this->_application->setFilter('type', 'remittance');
			$objDownload = new Download($this->_application);
			$objDownload->setFileBody();
			
			// cache settings
			$cache_filename = 'download-' . $this->_id . '.html';
			$file_path = SITE_PATH . 'cache/' . $this->_name . '/';			
			$objCache = new Cache($cache_filename, 1, $this->_name);
			// end cache settings
			
			
			$cached_file = SITE_PATH . 'cache/' . $this->_name . '/download-' . md5(SECRET_PHRASE . $this->_id). '.html';
			
			// email subject
			$subject = read($_POST, 'subject', '');
			$message = read($_POST, 'message', '');

			//does all required data exist?
			$all_data_present  = ($subject && $message) ? true : false;
			
			
			// all (required) data is present
			if($all_data_present === true){
			
				// get client details from client object
				$objSupplier = new Supplier($this->_db, array(), $this->_properties['outgoing_supplier']);
			
			
				// set up email message body				
				$content = $objDownload->getFileHeader() . $objDownload->getFileBody() . $objDownload->getFileFooter();
				
				if(CACHE === true){
					$objCache->createCache($content, false, false);
				} else{
					$handle = fopen($cached_file, "w");
			
					if($handle){
						fwrite($handle, $content);
						fclose($handle);
					} 
				}
				
				$supplierProperties = $objSupplier->getProperties();
				
				
				$send_list = array($supplierProperties['email'] => $supplierProperties['main_contact']);

				// initialise invoice object
				$objInvoice = new InvoiceEmail($this->_application, $send_list, $cached_file, 'remittance');
				
				// attempt to send remittance advice note
				$user_feedback = $objInvoice->sendEmail($subject, $message);
				
			} else{ // data missing
				$error++;
				// tell user why
				$user_feedback['content'][] = 'Remittance advice not sent because:';
				
				// download file is missing
				if(!file_exists($cached_file)){
					$error++;
					$user_feedback['content'][] = 'Downloadable remittance advice file does not exist';
				}
				// no subject
				if(!$subject){
					$error++;
					$user_feedback['content'][] = 'You have not entered a subject';
				}
				// no message
				if(!$smessage){
					$error++;
					$user_feedback['content'][] = 'You have not entered a message';
				}
				// give user feedback;
				$user_feedback['type'] = 'error';
				
				
			}
			
			return $user_feedback;
			
		}

		
		
		/**
		 *	setDocumentation
		 */
		protected function setDocumentation(){
		
			if($this->_id){
				
				$filter = array();
				$filter['per_page'] = 10;
				$filter['outgoing'] = $this->_id;
				
				$objOutgoingDocumentation = new OutgoingDocumentation($this->_db, $filter);
				$this->_properties['outgoing_documentation'] = $objOutgoingDocumentation->getProperties();
				
			}
			
		}
		
		
		/**
		 *	add
		 *	@return array $user_feedback
		 */
		protected function add(){
			$user_feedback = parent::add();
			if($user_feedback['type'] == 'success'){
				$this->addRepeated();
			}
			return $user_feedback;
		}
		
		/**
		 *	addRepeated
		 *	@return array $user_feedback
		 */
		protected function addRepeated(){
		
			$repeated = read($_POST, 'repeated', 0);
			$repeated_number_of_times = read($_POST, 'repeated_number_of_times', 0);
			$repeated_frequency = read($_POST, 'repeated_frequency', null);
			
			// User has selected to repeat outgoing
			if($repeated == 'Y' && !empty($repeated_number_of_times) && !empty($repeated_frequency)){

				$transaction_date = strtotime($_POST['transaction_date_year'] . '-' . $_POST['transaction_date_month'] . '-' . $_POST['transaction_date_day'] . ' 00:00:00');
				
				// Loop through how many times the user has chosen to repeat and set a new transaction date for the future before adding the new record
				for($i = 1; $i < ($repeated_number_of_times + 1); $i++){
				
					switch($repeated_frequency){
						default:
							break;
							
						case 'weekly':
							$new_transaction_date = strtotime('+' . $i . ' weeks', $transaction_date);
							break;
							
						case 'fortnightly':
							$new_transaction_date = strtotime('+' . ($i * 2) . ' weeks', $transaction_date);
							break;
							
						case 'monthly':
							$new_transaction_date = strtotime('+' . $i . ' months', $transaction_date);
							break;
							
						case 'quarterly':
							$new_transaction_date = strtotime('+' . ($i * 3) . ' months', $transaction_date);
							break;
							
						case 'bi-annually':
							$new_transaction_date = strtotime('+' . ($i * 6) . ' months', $transaction_date);
							break;
							
						case 'yearly':
							$new_transaction_date = strtotime('+' . $i . ' years', $transaction_date);
							break;
						
					}
					
					// Set the new transaction date form values (CRUD class) will reassemble these $_POST values to a YYYY-MM-DD HH:MM:SS value
					$_POST['transaction_date_year'] = date('Y', $new_transaction_date);
					$_POST['transaction_date_month'] = date('m', $new_transaction_date);
					$_POST['transaction_date_day'] = date('d', $new_transaction_date);
					
					
					/*
					// Debugging
					echo date('jS F Y', $new_transaction_date);
					echo '<br>';
					*/
					
					$user_feedback = parent::add();
					
				}
			
			}
			
			// Reset the transaction date - in case of errors
			$_POST['transaction_date_year'] = date('Y', $transaction_date);
			$_POST['transaction_date_month'] = date('m', $transaction_date);
			$_POST['transaction_date_day'] = date('d', $transaction_date);
			

		}
		
		
		/**
		 *	getCategory()
		 */
		public function getCategory(){
			return $this->_category;
		}
		
		/**
		 *	getSupplier()
		 */
		public function getSupplier(){
			return $this->_supplier;
		}
		
		/**
		 *	getFirstYear()
		 */
		public function getFirstYear(){
			return $this->_firstYear;
		}
		
		/**
		 *	getSubtotal()
		 */
		public function getSubtotal(){
			return $this->_subTotal;
		}
		
		/**
		 *	getVATTotal()
		 */
		public function getVATTotal(){
			return $this->_vatTotal;
		}
		
		/**
		 *	getGrandTotal()
		 */
		public function getGrandTotal(){
			return $this->_grandTotal;
		}
		
		
		
	
	}

?>