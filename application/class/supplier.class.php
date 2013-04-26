<?php
/**
 *	=========================================================================
 *	
 *	Supplier Class
 *	-------------------------------------------------------------------------
 *	
 *	Add/edit/delete/view outgoings (suppliers from database)
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
 *	@since		25/02/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	12/04/2009
 *	
 *  =========================================================================
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
 *		getOutgoings		
 *		getTotalCosts		
 *		setVcard		
 *		setPageTitle		
 *		setBreadcrumbTitle
 *	
 *	=========================================================================
 *
 */
	

	class Supplier extends Scaffold{
	
		// variables
		
		/**
		 *	@var int
		 */
		protected $_grandTotal = 0;
		
		
		/**
		 *	@var object
		 */
		public $Outgoings;
	
		/**
		 *	construct
		 *	@param object $db
		 *	@param arary $filter
		 *  @param (int|boolean) $id (FALSE)
		 */
		public function __construct($db, $filter = array(), $id = false){		
			
			// Object naming conventions
			$this->_name = 'supplier';		
		
			// SQL - Database related namings
			$this->_sql['main_table'] =  'outgoing_supplier';
			
			parent::__construct($db, $filter, $id);
			
			// get each suppliers list of outgoings
			$this->getOutgoings();
			
			// work how much each supplier has cost
			$this->setGrandTotal();
			
			//print_r($this->_properties);
			
			$this->setVcard();
		}	
		
		
		/**
		 *	deleteCache()
		 *	remove all cached files from the cache
		 *	public so other classes using this one can use
		 *	it's power.
		 */
		public function deleteCache(){
		
			parent::deleteCache();
			
			// Now delete the outgoing_supplier cache too.
			$objCache = new Cache($this->_name, 1, 'outgoing_supplier');
			$objCache->delete('folder');
		}
		
		/**
		 *	getOutgoings
		 */
		public function getOutgoings(){
		
			
			// get total for one item
			if($this->_id != ''){
			
				// supplier properties exist
				if(!empty($this->_properties)){

					$outgoing_filter['supplier'] = $this->_id;
					$outgoing_filter['per_page'] = 10000;
					$this->Outgoings = new Outgoing($this->_db, $outgoing_filter, '');
					
					//Run query and put result into properties array
					$this->_properties['outgoings'] = $this->Outgoings->getProperties();
					
				}
	
			} else{
			
				// get all supplier's outgoings
			
			
				$i = 0; //counter
				
				// loop through all records
				if(!empty($this->_properties)){
					foreach($this->_properties as $property){
						
						
						$outgoing_filter['supplier'] = $property['id'];
						$outgoing_filter['per_page'] = 10000;
						$this->Outgoings = new Outgoing($this->_db, $outgoing_filter, '');
						
						
						
						//Run query and put result into properties array
						$this->_properties[$i]['outgoings'] = $this->Outgoings->getProperties();
						
						$i++; // increment counter
					}	
				}
			}
		}	
		
		/**
		 *	setGrandTotal
		 */
		public function setGrandTotal(){
		
		
			// get total for one item
			if($this->_id != ''){
			
				// supplier's outgoings exist
				if(!empty($this->_properties)){
					
					// start total cost with 0
					$this->_properties['cost'] = 0;
					
					// client has outgoings
					if(!empty($this->_properties['outgoings'])){
						// loop through all outgoings
						foreach($this->_properties['outgoings'] as $outgoing){
							if(!empty($outgoing['total'])){
								// add project total on client total spend
								$this->_properties['cost'] += $outgoing['total'];
							} // end if
						} // end foreach
						$this->_grandTotal += $outgoing['total'];
					} // end if
					
				} // end if
	
			} else{
				
				// get all supplier's outgoings
			
			
			
				$i = 0; //counter
				
				// loop through all records
				if(!empty($this->_properties)){
					foreach($this->_properties as $property){
					
						// start total spend with 0
						$this->_properties[$i]['cost'] = 0;
						
						// client has outgoings
						if(!empty($property['outgoings'])){
							// loop through all outgoings
							foreach($property['outgoings'] as $outgoing){
							
								if(!empty($outgoing['price'])){
									// add project total on client total spend
									$this->_properties[$i]['cost'] += $outgoing['price'];
								} // end if
							} // end foreach
							$this->_grandTotal += $this->_properties[$i]['cost'];
							
						} // end if
						
						$i++; // increment counter
					
					} // end foreach
				}	
	
			} // end else
		}
		
		/**
		 *	setVcard
		 */
		public function setVcard(){
			if($this->_id){
				$objVcardClient = new Vcard($this->_properties);
				$this->_properties['vcard'] = (!empty($this->_properties['address1'])) ? $objVcardClient->getVcard() : '';
			}
		}
		
		
		public function getGrandTotal(){
			return $this->_grandTotal;
		}
	
	}

?>