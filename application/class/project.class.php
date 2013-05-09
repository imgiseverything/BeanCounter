<?php
/**
 *	=========================================================================
 *	
 *	Project Class	
 *	-------------------------------------------------------------------------
 *	
 *	Add/Edit/Delete/View Items from database
 *	
 *	=========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 		2008-2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version		1.2	
 *	@author			philthompson.co.uk
 *	@since			10/01/2008
 *	
 *	@lastmodified	09/05/2013
 *
 */
	

	class Project extends Scaffold{
	
		// Variables
	
		/**
		 *	@var int
		 */
		public $client;
		
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
		protected $_category;
		
		/**
		 *	@var int
		 */
		protected $_projectStage;
		
		/**
		 *	@var string
		 */
		protected $_transactionDate;
		
		/**
		 *	@var int
		 */
		protected $_firstYear;
		
		/**
		 *	@var array
		 */
		protected $_outstandingInvoices = array();
		
		/**
		 *	@var float
		 */
		protected $_outstandingBalance = 0;
		
		
		/**
		 *	@var string
		 *	A hash of the id (int) and the secredt phase constant
		 *	which is set in the URL so people can view invoices without
		 *	logging in and without being able to guess URLs
		 */
		protected $_idHash;
	
		/**
		 *	Constructor
		 *	@param object $db
		 *	@param array $filter - data options for SQL
		 *	@param (int|boolean) $id
		 */
		public function __construct($db, $filter, $id = false){
		
			$this->_db = $db;
			$this->_filter = $filter;
			$this->_id = $id;
			if(!empty($_GET['id_hash'])){
				$this->_idHash = $_GET['id_hash'];
			}

		
			// Object naming conventions
			$this->_name = (!isset($this->_name)) ? 'project' : $this->_name;
			
			// client filter
			$this->_client = read($filter, 'client', '');
			
			// stage filter
			$this->_projectStage = (!$id) ? read($filter, 'project_stage', array(2, 3, 4, 5)) : '';


			// Run parent's constructor
			parent::__construct($db, $filter, $id);
			
			// setFirstYear
			$this->setFirstYear();
			
			// set project's tasks
			$this->setTasks();
			
			// set project's discounts
			$this->setDiscounts();
			
			// get total (prices) for items
			$this->setGrandTotal();
			
			// set project's payments
			$this->setPayments();
			
			// set project's balance (paid+oustanding values)
			$this->setBalance();
		
		
			// If this is a single proposal so change the naming convention a little to ensure links go to the right places
			if($this->_id && $this->_properties['project_stage'] == 1){
				// Object naming conventions
				$this->_name = 'proposal';
				$this->_folder = '/proposals/';
				$this->_namePlural = 'proposals';
			}
			
			
		
		}
		
		
		/**
		 *	Project specific methods
		 *	
		 *	customQueryFilters()
		 *	getTasks()
		 *	setGrandTotal()
		 *	getClientTitle()
		 *	clientFilter()
		 *	invoice()
		 *	quote()
		 *	duplicate()
		 *	
		 *
		 */
		
		/**
		 *	customQueryFilters
		 *	Extend the CRUD class generic SQL query to get all or one record
		 */
		public function customQueryFilters(){
			
			$this->_queryFilter['custom'] = '';
			$this->_queryFilter['client'] = '';
			
			// filter projects by client
			if($this->_client){
				$this->_queryFilter['client'] .= " AND `t`.`client` = '{$this->_db->escape($this->_filter['client'])}' ";
				$this->_queryFilter['custom'] .= $this->_queryFilter['client'];
			}
			
			// filter by project_stage e.g. completed/invoiced/etc
			if($this->_projectStage){
				if(is_array($this->_projectStage)){
					$this->_queryFilter['custom'] .= " AND `t`.`project_stage` IN(" . join(',', $this->_projectStage) . ") ";
				} else{
					$this->_queryFilter['custom'] .= " AND `t`.`project_stage` = '{$this->_projectStage}' ";
				}
			}

		}
		
		/**
		 *	setFirstYear
		 *	Select the first ever project and use that as the 
		 *	(glorious) first trading date
		 */
		public function setFirstYear(){
			 
			$query = "SELECT `transaction_date` FROM `project_payment` t WHERE 1 {$this->_queryFilter['client']} ORDER BY `transaction_date` ASC LIMIT 1;";
					
			niceError($query); // Debugging echo SQL
			
			$objCache = new Cache($this->_name . '_first_date.cache', 1, 'account');
			if($objCache->getCacheExists() === true){
				$this->_firstYear = $objCache->getCache();
			} else{

				if($result = $this->_db->get_var($query)){
					$this->_firstYear = $result;
				} else{
					// no results, so first year is this year
					$this->_firstYear = date('Y-m-d H:i:s');
				}
				
				$objCache->createCache($this->_firstYear);
			}
			// End cache
		}
		
		
		/**
		 *	setTasks
		 *	Go and get all the tasks related to one or all projects. 
		 *	Tasks to Projects is a one to many relationship.
		 *	Tasks dictate the cost value of the entire project
		 */	
		public function setTasks(){
			
			// get total for one item
			if($this->_id != ''){
			
				// project properties exist
				if(!empty($this->_properties)){
					// Query
					$query = "SELECT * FROM `project_task` WHERE `project` = '{$this->_id}' AND `status` = '1';";
					
					niceError($query); // Debugging - echo SQL
					
					// Cache
					$objCache = new Cache($this->_name . '_' . $this->_id . '_tasks.cache', 1, $this->_name);
					if($objCache->getCacheExists() === true){
						$this->_properties['project_task'] = $objCache->getCache();
					} else{
						//Run query and put result into properties array
						$this->_properties['project_task'] = $this->_db->get_results($query, "ARRAY_A");
						$objCache->createCache($this->_properties['project_task']);
					}
					// End cache
					
					//Run query and put result into properties array
					$this->_properties['project_task'] = $this->_db->get_results($query, "ARRAY_A");
				}
	
			} else{
				// get all item's tasks
				$i = 0; //counter
				
				if(!empty($this->_properties)){
					// loop through all records
					foreach($this->_properties as $property){
						
						// Query
						$query = "SELECT * FROM `project_task` WHERE `project` = '{$property['id']}' AND `status` = '1';";
						
						niceError($query); // Debugging - echo SQL

						// Cache
						$objCache = new Cache($this->_name . '_' . $property['id'] . '_tasks.cache', 1, $this->_name);
						if($objCache->getCacheExists() === true){
							$this->_properties[$i]['project_task'] = $objCache->getCache();
						} else{
							//Run query and put result into properties array
							$this->_properties[$i]['project_task'] = $this->_db->get_results($query, "ARRAY_A");
							$objCache->createCache($this->_properties[$i]['project_task']);
						}
						// End cache
						
						$i++; // increment counter
					}	
				}
			}
		}
		
		/**
		 *	setDiscounts
		 *	Go and get all the discounts related to one or all projects. 
		 *	Discounts to Projects is a one to many relationship
		 */	
		public function setDiscounts(){
			
			// get total for one item
			if($this->_id != ''){
			
				// project properties exist
				if(!empty($this->_properties)){
					// Query
					$query = "SELECT * FROM `project_discount` WHERE `project` = '{$this->_id}' AND `status` = '1';";
					
					niceError($query); // Debugging - echo SQL
					
					// Cache
					$objCache = new Cache($this->_name . '_' . $this->_id . '_discounts.cache', 1, $this->_name);
					if($objCache->getCacheExists() === true){
						$this->_properties['project_discount'] = $objCache->getCache();
					} else{
						//Run query and put result into properties array
						$this->_properties['project_discount'] = $this->_db->get_results($query, "ARRAY_A");
						$objCache->createCache($this->_properties['project_discount']);
					}
					// End cache
				}
	
			} else{
				// get all item's discounts
				$i = 0; //counter
				
				if(!empty($this->_properties)){
					// loop through all records
					foreach($this->_properties as $property){
						
						// Query
						$query = "SELECT * FROM project_discount WHERE project = '{$property['id']}' AND status = '1';";
						
						niceError($query); // Debugging - echo SQL

						// Cache
						$objCache = new Cache($this->_name . '_' . $property['id'] . '_discounts.cache', 1, $this->_name);
						if($objCache->getCacheExists() === true){
							$this->_properties[$i]['project_discount'] = $objCache->getCache();
						} else{
							//Run query and put result into properties array
							$this->_properties[$i]['project_discount'] = $this->_db->get_results($query, "ARRAY_A");
							$objCache->createCache($this->_properties[$i]['project_discount']);
						}
						// End cache
						
						$i++; // increment counter
					}	
				}
			}
		}
		
		
		/**
		 *	setPayments
		 * 	Get all the payments for a project so we can later on see how much is paid and owed
		 *	Payments (`project_payment`) to Projects is a one to many relationship
		 */	
		public function setPayments(){
			
			// get total for one item
			if($this->_id != ''){
			
				// project properties exist
				if(!empty($this->_properties)){
					// Query
					$query = "SELECT * FROM `project_payment` WHERE `project` = '{$this->_id}' AND `status` = '1';";
					
					//niceError($query); // Debugging - echo SQL
					
					// Cache
					$objCache = new Cache($this->_name . '_' . $this->_id . '_payments.cache', 1, $this->_name);
					if($objCache->getCacheExists() === true){
						$this->_properties['project_payment'] = $objCache->getCache();
					} else{
						//Run query and put result into properties array
						$this->_properties['project_payment'] = $this->_db->get_results($query, "ARRAY_A");
						$objCache->createCache($this->_properties['project_payment']);
					}
					// End cache
				}
	
			} else{
				// get all item's payments
				$i = 0; //counter
				
				if(!empty($this->_properties)){
					// loop through all records
					foreach($this->_properties as $property){
						
						// Query
						$query = "SELECT * FROM `project_payment` WHERE `project` = '{$property['id']}' AND `status` = '1';";
						
						//niceError($query); // Debugging - echo SQL

						// Cache
						$objCache = new Cache($this->_name . '_' . $property['id'] . '_payments.cache', 1, $this->_name);
						if($objCache->getCacheExists() === true){
							$this->_properties[$i]['project_payment'] = $objCache->getCache();
						} else{
							//Run query and put result into properties array
							$this->_properties[$i]['project_payment'] = $this->_db->get_results($query, "ARRAY_A");
							$objCache->createCache($this->_properties[$i]['project_payment']);
						}
						// End cache
						
						$i++; // increment counter
					}	
				}
			}
		}

		
		/**
		 *	setGrandTotal
		 *	Set prices of projects with itemised tasks, discounts and VAT (sales tax)
		 *	all added up. Also create a grand total for all projects when relevant
		 */
		public function setGrandTotal(){
			
			// set-up a default total of zero incase there are no tasks
			$this->_subTotal = 0;
			$this->_vatTotal = 0;
			$this->_grandTotal = 0;
			
			// get total for one item
			if($this->_id != ''){
				// project has tasks so add them up
				if(!empty($this->_properties['project_task'])){
					// loop through all tasks
					foreach($this->_properties['project_task'] as $task){
						// add task price onto total
						$this->_subTotal += (float)$task['price'];
					}
					
					
				}
				
				// then remove discounts if any exist
				if(!empty($this->_properties['project_discount'])){
					// loop through all discounts & remove discount from subtotal
					foreach($this->_properties['project_discount'] as $discount){ 
						$this->_subTotal -= (float)$discount['price'];
					}
				}
				

				// work out VAT
				$this->_vatTotal += ($this->_subTotal * ((float)$this->_properties['vat_rate'] / 100));
				$this->_properties['total_vat'] = $this->_vatTotal;
				
			} else{
				// get all item's totals
				//$i = 0; //counter
				
				if(!empty($this->_properties)){
					// loop through all records
					for($i = 0; $i < sizeof($this->_properties); $i++){
						$this->_properties[$i]['total'] = 0;
						$this->_properties[$i]['total_vat'] = 0;
						// create easy to use variables
						extract($this->_properties[$i]);
						// project has tasks
						if(!empty($project_task)){
							// loop through all tasks
							foreach($project_task as $task){
								// add task price onto total
								$this->_properties[$i]['total'] += (float)$task['price'];
							} // end foreach
							
						}
						
						// then remove discounts if any exist
						if(!empty($project_discount)){
							// loop through all discounts & remove the value from subtotal
							foreach($project_discount as $discount){
								$this->_properties[$i]['total'] -= (float)$discount['price'];
							}
						}
						
						$this->_properties[$i]['total_vat'] = ($this->_properties[$i]['total'] * ($this->_properties[$i]['vat_rate'] / 100));
						
						// add project total onto grand total
						$this->_subTotal += $this->_properties[$i]['total'];
						$this->_vatTotal += $this->_properties[$i]['total_vat'];
						$this->_properties[$i]['grand_total'] = ($this->_properties[$i]['total'] + $this->_properties[$i]['total_vat']);
						
						
					}	 // end for
				}
			}
			
			$this->_grandTotal = ($this->_subTotal + $this->_vatTotal);
			
		}
		
		/**
		 *	setBalance
		 *	Calculate how many payments have been made to this project and work out
		 *	how much money is outstanding
		 *	@return	void
		 */
		protected function setBalance(){
			
			if($this->_id){
						
				$this->_properties['paid'] = 0;
				$this->_properties['outstanding'] = $this->_grandTotal;
				$this->_properties['completed'] = false;
				
				if(!empty($this->_properties['project_payment'])){
					foreach($this->_properties['project_payment'] as $payment){
						$this->_properties['paid'] += $payment['price'];
						$this->_properties['outstanding'] -= $payment['price'];
					}
					
					// mark as complete if it's all paid up
					if($this->_properties['paid'] == $this->_grandTotal){
						$this->_properties['completed'] = true;
						$this->_properties['project_stage_title'] = 'Completed';
					}
					
				}
				
				
			} else if(!empty($this->_properties)){
				
				for($i = 0; $i < $this->_propertiesSize; $i++){
				
					$grand_total_formatted = (float)number_format($this->_properties[$i]['grand_total'], 2, '.', '');
					$this->_properties[$i]['paid'] = 0.00;
					$this->_properties[$i]['outstanding'] = $grand_total_formatted;
					$this->_properties[$i]['completed'] = false;
					
					if(!empty($this->_properties[$i]['project_payment'])){
						foreach($this->_properties[$i]['project_payment'] as $payment){
							$price = (float)number_format($payment['price'], 2, '.', '');
							$this->_properties[$i]['paid'] += $price;
							$this->_properties[$i]['outstanding'] = ($this->_properties[$i]['outstanding'] - $price);	
						}
		
						
						// mark as complete if it's all paid up
						if((float)$this->_properties[$i]['paid'] >= $grand_total_formatted){
							$this->_properties[$i]['completed'] = true;
							$this->_properties[$i]['project_stage_title'] = 'Completed';
						} 
						
					}
					
					// Outstanding invoices
					if(($this->_properties[$i]['project_stage'] == 3 && $this->_properties[$i]['completed'] !== true)){
						$this->_outstandingInvoices[] = $this->_properties[$i];
					}
					
					
				}	
				
				
				// set an outstanding balance
				if(!empty($this->_outstandingInvoices)){
					foreach($this->_outstandingInvoices as $outstanding){
						$this->_outstandingBalance += $outstanding['outstanding'];
					}
				}
				
				
			}
			
		}
		
		
		
		/**
		 *	invoice
		 *	send client (main contact) an email invoice
		 *	@param string ('inovice') $type
		 *	@return array $user_feedback
		 */
		public function invoice($type = 'invoice'){
		
			$error = 0;
			
			// Cache settings (for the HTML version of the invoice)
			$cache_filename = 'download-' . $this->_id . '.html';
			$file_path = SITE_PATH . 'cache/' . $this->_name . '/';			
			$objCache = new Cache($cache_filename, 1, $this->_name);
			// end cache settings
			
			// grab invoice file via download object
			$this->_application->setFilter('type', 'invoice');
			$objDownload =  new Download($this->_application);
			$objDownload->setFileBody();
			
			$cached_file = SITE_PATH . 'cache/' . $this->_name . '/download-' . md5(SECRET_PHRASE . $this->_id) . '.html';
			
			
			
			// Start building email headers/variables
			$subject = read($_POST, 'subject', '');
			$message = read($_POST, 'message', '');
			
			//does all required data exist?
			$all_data_present  = false;
			if(
				$subject 
				&& $message 
				&& strtolower($this->_properties['project_stage']) != 'completed'
			){
				$all_data_present = true;
			}
			
			
			// all (required) data is present
			if($all_data_present === true){
			
				// get client details from client object
				$objClient = new Client($this->_db, array(), $this->_properties['client']);		
				$clientProperties = $objClient->getProperties();	
			Ï
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
				
				
				$send_list = array($clientProperties['email'] => $clientProperties['main_contact']);
				
				$cc_list = (!empty($_POST['cc'])) ? explode(',', $_POST['cc']) : array(); 
				
				if(!empty($cc_list)){
					
					// TODO loop through and validate email address in CC list
					$send_list = array_merge($send_list, $cc_list);
				}
				
				// initialise invoice object
				$objInvoice = new InvoiceEmail($this->_application, $send_list, $cached_file, $type);
				
				// attempt to send invoice
				$user_feedback = $objInvoice->sendEmail($subject, $message);
				
				// update project so the status has been changed to 'invoiced'
				if($user_feedback['type'] == 'success'){
					
					// if we've just invoiced someone - mark the project as 'invoiced'
					if($type == 'invoice'  && $this->_properties['project_stage'] < 3){
						// put object values into $_POST array so we can trick the method into thinking the form has been posted
						// Put existing object properties into $_POST array to trick method
						foreach($this->_properties as $property => $value){
							// don't include payment required: we need to set this ourselves
							if($property != 'payment_required'){
								$_POST[$property] = $value;
							}
						}
						// 'invoiced' - see project_status table
						if($_POST['project_stage'] < 3){
							$_POST['project_stage'] = 3;
						}
						
						// if the invoice date for this project has not been set - set it so we know when the invoice was sent to the client
						$_POST['invoice_date'] = date('Y-m-d');
						$_POST['invoice_date_day'] = date('d');
						$_POST['invoice_date_month'] = date('m');
						$_POST['invoice_date_year'] = date('Y');

						
						// Update the project
						$this->edit();
					}
					
				}
				
			} else{
			
				// data missing
				$error++;
				// tell user why
				$user_feedback['content'][] = ucwords($type) . ' not sent because:';
				
				// Error: download file is missing
				if($objCache->getCacheExists() !== true){
					$error++;
					$user_feedback['content'][] = 'Downloadable ' . $type . ' file does not exist';
				}
				// Error: no email subject
				if(!$subject){
					$error++;
					$user_feedback['content'][] = 'You have not entered a subject for this email';
				}
				// Error: no message/email body
				if(!$message){
					$error++;
					$user_feedback['content'][] = 'You have not entered a message for this email';
				}
				// project has already been completed
				if(strtolower($this->_properties['completed']) == true){
					$error++;
					$user_feedback['content'][] = 'Project is marked as <em>complete</em>. It is therefore, assumed that it has already been invoiced and paid for.';
				}
				// give user feedback;
				$user_feedback['type'] = 'error';
						
			}
			
			return $user_feedback;
			
		}
		
		/**
		 *	quote
		 *	@return array
		 */
		public function quote(){
			return $this->invoice('quote');
		}
		
		
		/**
		 *	add
		 *	Use CRUD's add method but extend it to add a project's related tasks when a project is added
		 *	@return array $user_feedback
		 */
		protected function add(){
			$user_feedback = parent::add();
			$this->addTasks();
			return $user_feedback;
		}
		
		
		/**
		 *	delete
		 *	Use CRUD's delete method but extend it to delete a project's related tasks when a project is deleted
		 *	@return array $user_feedback
		 */
		protected function delete(){
			$user_feedback = parent::delete();
			$this->deleteTasks();
			return $user_feedback;
		}
		
		
		/**
		 *	edit
		 *	Use CRUD's edit method but extend it to update a project's related tasks when a project is updated
		 *	@return array $user_feedback
		 */
		protected function edit(){
			$user_feedback = parent::edit();
			$this->editTasks();
			return $user_feedback;
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
		 *	pdf
		 *	Create a PDF usig a third party library
		 */
		public function pdf(){
		
			global $objVcard, $objVcardClient, $last_payment, $objApplication, $objTextile;
		
		
			$this->_properties;

			$pdf = new PDF('P', 'mm', 'A4');
			$pdf->setAuthor(SITE_NAME, true);		
			$pdf->setAuthor($objApplication->getApplicationName(), true);
			$pdf->AddPage();
			$pdf->setTitle($this->_properties['title']);	
			$pdf->SetFont('Arial', 'B', 20);
			
			
			 if($this->_properties['completed'] === true){
			 	$last_payment = end($this->_properties['project_payment']);
				$date = 'PAID: ' . DateFormat::getDate('date', $last_payment['transaction_date']);
			 } else if($this->_properties['project_stage'] == 3){
			 	$date = DateFormat::getDate('date', $this->_properties['invoice_date']);
			 } else{
			 	$date = DateFormat::getDate('date', date('Y-m-d'));
			 }
			
			
			// Company name       Date
			$pdf->Cell(150, 10, SITE_NAME);
			
			$pdf->SetFont('Arial', '', 12);
			$pdf->Cell(50, 10, $date);
			$pdf->Ln();
			
			// Address
			$address = (!empty($this->_properties['sender_address'])) ? trim($this->_properties['sender_address'])  : trim(strip_tags($objVcard->getVcard()));
			
			
			$pdf->write(6, str_replace("\n\n", "\n", $address));
			$pdf->Ln();
			$pdf->Ln();
			
			// Invoice/proposal
			$project_type = (strtolower($this->_properties['project_stage_title']) == 'proposal') ? 'Proposal' :  'Invoice';
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(160, 10, $project_type);
			$pdf->Cell(40, 10, 'ref:' . self::referenceNumber($this->_properties['id'], $this->_properties['date_added'])); 
			$pdf->Ln();
			// Title
			$pdf->SetFont('Arial', 'B', 16);
			$pdf->Cell(40, 10, stripslashes($this->_properties['title']));
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont('Arial', '', 12);
			
			
			if(!empty($this->_properties['clients_reference_number'])){
				$pdf->Cell(40, 10, 'Client reference number:' . $this->_properties['clients_reference_number']);
				$pdf->Ln();
				$pdf->Ln();
			}
			
			if($this->_properties['project_stage'] == 3){
				$pdf->SetTextColor(222, 0, 0);
				$pdf->SetFont('Arial', 'B', 14);
				$pdf->Cell(40, 10, 'Due for payment on ' .  DateFormat::getDate('date', date('Y-m-d', strtotime('+15 days', strtotime($this->_properties['invoice_date'])))));
				$pdf->Ln();
				$pdf->Ln();
				
				$pdf->SetTextColor(0, 0, 0);
				
			}
			
			
			
			
			// FAO
			$fao = (!empty($this->_properties['for_the_attention_of'])) ? $this->_properties['for_the_attention_of']  : strip_tags($objVcardClient->getVcard());
			
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(40, 10, 'For the attention of:');
			$pdf->Ln();
			$pdf->SetFont('Arial', '', 12);
			$pdf->write(6, $fao);
			$pdf->Ln();
			$pdf->Ln();
			$pdf->Ln();
			
			// Project details
			if(!empty($this->_properties['description'])){
				
				$pdf->SetFont('Arial', 'B', 14);
				$pdf->Cell(40, 10, 'Project details');
				$pdf->Ln();
				
				$pdf->SetFont('Arial', '', 12);
				$pdf->write(6, str_replace(array('&#8216;', '&#8217;'), "'", html_entity_decode(strip_tags($objTextile->TextileThis($this->_properties['description'])), ENT_QUOTES)));
				$pdf->Ln();
				$pdf->Ln();
				$pdf->Ln();
			
			}
			
			
			// Price/list table
			$pdf->SetFillColor(102);
			
			if(!empty($this->_properties['project_task'])){
			
			
				// header
				$pdf->SetFont('Arial', 'B', 12);
				$pdf->Cell(140, 10, 'Item');
				$pdf->Cell(40, 10, html_entity_decode(CURRENCY, ENT_NOQUOTES, 'ISO-8859-1'));
				$pdf->Ln(10);
				$pdf->Cell(180, 1, '', 0, 1, 'L', true);
				$pdf->SetFont('Arial', '', 11);
				
				$pdf->SetFillColor(215);
				
				// body
				foreach($this->_properties['project_task'] as $task){
					
					$title = html_entity_decode(strip_tags(stripslashes($task['title'])));
					$description = str_replace(array('&#8216;', '&#8217;'), "'", html_entity_decode(strip_tags(stripslashes(str_replace('<li>', '<li>> ', $objTextile->TextileThis($task['description'])))), ENT_QUOTES));
					
					$pdf->SetFont('Arial', 'B', 11);
					$pdf->Cell(140, 10, $title, 0);
					$pdf->Cell(40, 10, html_entity_decode(currency($task['price']), ENT_NOQUOTES, 'ISO-8859-1'), 0);
					$pdf->Ln();
					
					
					$pdf->SetFont('Arial', '', 11);
					$pdf->Cell(180, 10, $pdf->Multicell(140, 10, $description), 0);
					$pdf->Ln(10);
					$pdf->Cell(180, 1, '', 0, 1, 'L', true);
				}
				
				$pdf->SetFont('Arial', 'B', 12);

				
				// VAT
				if($this->getVATTotal()){
					$pdf->Cell(140, 10, 'Subtotal', 1);
					$pdf->Cell(40, 10, html_entity_decode(currency($this->getSubtotal()), ENT_NOQUOTES, 'ISO-8859-1'));
					$pdf->Ln(10);
					$pdf->Cell(180, 1, '', 0, 1, 'L', true);
					
					$pdf->Cell(140, 10, 'VAT');
					$pdf->Cell(40, 10, html_entity_decode(currency($this->getVATTotal()), ENT_NOQUOTES, 'ISO-8859-1'));
					$pdf->Ln(10);
					$pdf->Cell(180, 1, '', 0, 1, 'L', true);
				}
				
				
				
				$pdf->SetFillColor(102);
				$pdf->Cell(140, 10, 'Total');
				$pdf->Cell(40, 10, html_entity_decode(currency($this->getGrandTotal()), ENT_NOQUOTES, 'ISO-8859-1'));
				$pdf->Ln(10);
				$pdf->Cell(180, 1, '', 0, 1, 'L', true);
				$pdf->Ln();
				$pdf->Ln();
				$pdf->Ln();
				$pdf->SetFont('Arial', '', 12);
				
				
				
			
			}
			
			
			
			
			
			
			
			// Payment details
			
			if($this->_properties['project_stage'] <= 3 && $this->_properties['completed'] !== true){
				
				$pdf->SetFont('Arial', 'B', 14);
				$pdf->Cell(40, 10, 'Payment');
				$pdf->Ln();
				
				$pdf->SetFont('Arial', '', 12);
				$pdf->write(6, $this->_properties['appendix']);	
				$pdf->Ln();
				$pdf->Ln();
				
				if($this->_properties['project_stage'] > 1){ // show payment details if not a quote
					$pdf->SetFont('Arial', 'B', 14);
					$pdf->Cell(40, 10, 'Bank transfer details');
					$pdf->Ln();
					$pdf->SetFont('Arial', '', 12);
					$pdf->Cell(40, 10, 'A/C ' . BANK_AC);
					$pdf->Ln();
					$pdf->Cell(40, 10, 'S/C ' . BANK_SC);
					$pdf->Ln();
					if(IBAN){
						$pdf->Cell(40, 10, 'IBAN ' . IBAN);
						$pdf->Ln();
					}
				}
		}
			
			// Powered by
			$pdf->Ln();
			$pdf->Cell(200, 10, 'Powered by ' . $objApplication->getApplicationName()  . ' for ' . SITE_NAME);
			
			
			
			$pdf->Output(cleanString(SITE_NAME) . '-' . $this->_name . '-' . $this->_id . '.pdf', 'I');

			
			
		}
		
		/**
		 *	addTasks
		 *	Try to add all the tasks when a project is added
		 *	@return array $user_feedback
		 */
		protected function addTasks(){
		
			$user_feedback = array();			
			
			$POST_storage = $_POST;
			
			if(!empty($_POST['task'])){
				foreach($_POST['task'] as $task){
				
					// Only add if the task has been checked to be deleted
					if(empty($task['delete'])){
						$objTask = new Task($this->_db, array(), false);
						$_POST = $task;
						$_POST['action'] = 'add';
						$_POST['project'] = $this->_id;

						$user_feedback = $objTask->add();
					}				
				}
			}
			
			$_POST = $POST_storage;
			
			return $user_feedback;
		}
		
		/**
		 *	deleteTasks
		 *	
		 *	@return array $user_feedback
		 */
		protected function deleteTasks(){
			$user_feedback = array();
			return $user_feedback;
		}
		
		/**
		 *	editTasks
		 *	@return array $user_feedback
		 */
		protected function editTasks(){
		
			$user_feedback = array();

			$POST_storage = $_POST;
			
			foreach($_POST['task'] as $task){
			
				if(!empty($task['id'])){
					$objTask = new Task($this->_db, array(), $task['id']);
									
					if(!empty($task['delete'])){
						$user_feedback = $objTask->delete();
					} else{
						$_POST = $task;
						$_POST['action'] = 'edit';
						$_POST['project'] = $this->_id;
						$user_feedback = $objTask->edit();	
					}
				} else{
					$objTask = new Task($this->_db, array(), false);
					$_POST = $task;
					$_POST['action'] = 'add';
					$_POST['project'] = $this->_id;
					$user_feedback = $objTask->add();
				}
			
			}
						
			$_POST = $POST_storage;
			
			return $user_feedback;
		}

		
		/**
		 *	completed
		 *	Mark a project as 'completed'
		 *	@return array
		 */
		public function completed(){
		
			// Put existing object properties into $_POST array to trick method
			foreach($this->_properties as $property => $value){
				// don't include transaction_date: we need to set this ourselves
				if($property != 'transaction_date'){
					$_POST[$property] = $value;
				}
			}
			// Set stage to be 4 AKA completed
			$_POST['project_stage'] = 4;
			return $this->edit();
			
		}
		
		
		/**
		 *	invoiceClean
		 *	Clean up some formatting errors (line breaks) on invoices
		 *	@param string $string
		 *	@return string
		 */
		public function invoiceClean($string){
			
			$objTextile = new Textile();
		
			$string = $objTextile->TextileThis($string);
			$string =  stripslashes(str_replace(array('>rn<', 'rnrn', '&amp;pound;'), array(">\r\n<", "\r\n\r\n", '&pound;'), $string));
			
			return $string;
		}
		
		
		/**
		 *	referenceNumber
		 *	Create a unique reference number for project/expenses which combines
		 *	the year and a padded id so project id 83 in 2009 would be
		 *	0900083. 
		 *	@param	int	unique id (unique int usually)
		 *	@param	string	timestamp (YYYY-MM-DD HH:MM:SS)
		 *	@return string	example 1000001
		 */
		public static function referenceNumber($id, $date = NULL){
		
			if(empty($date)){
				$date = date('Y-m-d H:i:s');
			}
		
			$year = date('y', strtotime($date));
			$padded_id = str_pad($id, 4, '0', STR_PAD_LEFT);
			
			return $year . $padded_id;
		}
		
		/**
		 *	getClient()
		 *	@return string
		 */
		public function getClient(){
			return $this->_client;
		}
		
		/**
		 *	getProjectStage()
		 *	@return int (1|2|3)
		 */
		public function getProjectStage(){
			return $this->_projectStage;
		}
		
		/**
		 *	getTransactionDate()
		 *	@return string (timestamp)
		 */
		public function getTransactionDate(){
			return $this->_transactionDate;
		}
		
		/**
		 *	getFirstYear()
		 *	@return
		 */
		public function getFirstYear(){
			return $this->_firstYear;
		}
		
		/**
		 *	getSubtotal()
		 *	@return float
		 */
		public function getSubtotal(){
			return $this->_subTotal;
		}
		
		/**
		 *	getVATTotal()
		 *	@return float
		 */
		public function getVATTotal(){
			return $this->_vatTotal;
		}
		
		/**
		 *	getGrandTotal()
		 *	@return float
		 */
		public function getGrandTotal(){
			return $this->_grandTotal;
		}
		
		/**
		 *	getOutstandingInvoices()
		 *	@return array
		 */
		public function getOutstandingInvoices(){
			return $this->_outstandingInvoices;
		}
		
		/**
		 *	getOutstandingBalance()
		 *	@return array
		 */
		public function getOutstandingBalance(){
			return $this->_outstandingBalance;
		}
		
		
		
	
	}

?>