<?php
/*
 *	=========================================================================
 *	
 *	OutgoingPayment Class
 *	-------------------------------------------------------------------------
 *	
 *	Add/edit/delete/view outgoing payments (types e.g. cheque, cash, card)
 *	
 *	=========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 	2008-2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.0	
 *	@author		philthompson.co.uk
 *	@since		06/03/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	15/11/2009
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
 *		setNamingConventions
 *		add()
 *		delete()
 *		duplicate()
 *		edit()
 *		deleteProjectPaymentCache()
 *		setFirstYear
 *		getFirstYear
 *		
 *	=========================================================================
 *	
 */
	

	class ProjectPayment extends Scaffold{
	
		// Variables
		
		/**
		 *	@var int
		 */
		protected $_firstYear;

	
		// construct
		public function __construct($db, $filter = array(), $id = false){		
		
			$this->_db = $db;
			$this->_id = $id;
			$this->_filter = $filter;

			parent::__construct($this->_db, $this->_filter, $this->_id);
			
			
			$this->setFirstYear();	
			
		}
		
		/**
		 *	setNamingConventions
		 */
		protected function setNamingConventions(){
			$this->_name = 'payment';
			$this->_sql['main_table'] = 'project_payment';
			
			parent::setNamingConventions();
		}
		
		
		/**
		 *	customQueryFilters
		 */
		public function customQueryFilters(){
			
			$this->_queryFilter['custom'] = '';
			
			// Custom timeframe filter
			if(!empty($this->_filter['transaction_date']) && $this->_filter['transaction_date'] === true && $this->_timeframeCustom && strlen($this->_timeframeCustom['start']) == 19 && strlen($this->_timeframeCustom['end']) == 19){	
			
				// convert from timestamp to datestamp
				//$this->_timeframeCustom['start'] = substr($this->_timeframeCustom['start'], 0, 10);
				//$this->_timeframeCustom['end'] = substr($this->_timeframeCustom['start'], 0, 10);
						
				$this->_queryFilter['timeframe'] = " AND `t`.`transaction_date` BETWEEN '{$this->_timeframeCustom['start']}' AND '{$this->_timeframeCustom['end']}' ";
			}
			
			
			$this->_sql['select'] .= ", `c`.`title` AS client_title";
			$this->_sql['joins'] .= " LEFT JOIN `client` `c` ON `c`.`id` = `t2`.`client`";
			
		}
		
		
		/**
		 *	add()
		 *	Use parent method but ensure project cache is
		 *	properly deleted
		 */
		protected function add(){

			$user_feedback = parent::add();		
				
			$this->deleteProjectPaymentCache($user_feedback);
			
			return $user_feedback;
		}
		
		/**
		 *	delete()
		 *	Use parent method but ensure project cache is
		 *	properly deleted
		 */
		protected function delete(){
		
			$user_feedback = parent::delete();		
				
			$this->deleteProjectPaymentCache($user_feedback);
			
			return $user_feedback;
		}
		
		/**
		 *	duplicate()
		 *	Duplciation is just adding except the form
		 *	view has data pre-filled. So just use the
		 *	add method :)
		 */
		protected function duplicate(){
			return $this->add();
		}
		
		/**
		 *	edit()
		 *	Use parent method but ensure project cache is
		 *	properly deleted
		 */
		protected function edit(){
		
			$user_feedback = parent::edit();		
				
			$this->deleteProjectPaymentCache($user_feedback);
			
			return $user_feedback;
		}	
		
		/**
		 *	deleteProjectPaymentCache()
		 *	@$user_feedback array
		 *	If the parent method has worked then we need
		 *	to delete the cache for projects
		 */
		private function deleteProjectPaymentCache($user_feedback){

			if(!empty($user_feedback['type']) && $user_feedback['type'] == 'success'){
				// create the cache file name
				// should be projects_id_tasks.cache
				$cacheFilename = 'project_' . $this->_properties['project'] . '_' . strtolower($this->_namePlural) . '.cache';
				$objCache = new Cache($cacheFilename, 1, 'project');
				$objCache->delete('file', $cacheFilename);
			}
			
		}	
		
		
		/**
		 *	setFirstYear
		 *	grab the first ever project and use that as the 
		 *	(glorious) first trading date
		 */
		public function setFirstYear(){

			$query = 	"SELECT `transaction_date`
						FROM `{$this->_sql['main_table']}` 
						WHERE (`transaction_date` IS NOT NULL 
						AND `transaction_date` != '0000-00-00 00:00:00') 
						ORDER BY `transaction_date` ASC 
						LIMIT 1;";
					
			niceError($query); // Debugging echo SQL
			
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
		 *	getFirstYear()
		 */
		public function getFirstYear(){
			return $this->_firstYear;
		}
		
		
		
	
	}

?>