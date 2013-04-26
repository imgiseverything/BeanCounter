<?php
/*
 *	=========================================================================
 *	
 *	OutgoingCategory Class
 *	-------------------------------------------------------------------------
 *	
 *	Add/edit/delete/view outgoing categories (exspenses from database)
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
 *	@modified	19/08/2009
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
 *		setOutgoings
 *		
 *	=========================================================================
 *	
 */
	

	class OutgoingCategory extends Scaffold{
	
		// Variables
		
		/**
		 *	@var float
		 */
		protected $_grandTotal = 0;
	
		/**
		 *	construct
		 *	@param object $db
		 *	@param array $filter
		 *	@param int|boolean $id
		 */
		public function __construct($db, $filter = array(), $id = false){		
		
			$this->_name = 'outgoings category';
			$this->_namePlural = 'outgoings categories';
			$this->_folder = '/outgoings/categories/';
			$this->_sql['main_table'] = 'outgoing_category';
			
			$this->_filter = $filter;
			
			
			parent::__construct($db, $this->_filter, $id);	
			
			
			// getOutgoings
			$this->setOutgoings();
		}
		
		
		/**
		 *	setOutgoings()
		 *	Find all the outgoing associated with this item(s)
		 */
		public function setOutgoings(){
			
			$grand_total = 0;
			$this->_grandTotal = 0;
			
			
			// get outgoings for one category
			if($this->_id){
				// Grab new outgoing object data for this supplier :: WARNING-> database intensive
				$this->_filter['per_page'] = 500;
				$this->_filter['category'] = $this->_properties['id'];
				$objOutgoing = new Outgoing($this->_db, $this->_filter);
				
				$outgoingProperties = $objOutgoing->getProperties();
				
				// outgoing object data exists
				if(!empty($outgoingProperties)){
					$this->_properties['total'] = 0;
					// loop through all this category's outgoings
					for($i = 0; $i < count($outgoingProperties); $i++){
					//echo "<pre>";print_r($objOutgoing->properties[$i]); echo "</pre>";
						if(!empty($outgoingProperties[$i]['price'])){
							$this->_properties['outgoings'] = $outgoingProperties[$i];
						
							$this->_properties['total'] += $outgoingProperties[$i]['price'];
						} //end if
					} //end for
					$grand_total += $this->_properties['total'];
				} // end if
						
				
			} else if(!empty($this->_properties)){
			
					// get for all categories
					// Loop through all items in object
					for($i = 0; $i < count($this->_properties); $i++){
						// Grab new outgoing object data for each category :: WARNING-> database intensive
						$this->_properties[$i]['total'] = 0;
						$filter['per_page'] = 500;
						$filter['category'] = read($this->_properties[$i],'id','');
						
						$objOutgoing = new Outgoing($this->_db, $filter);
						
						$outgoingProperties = $objOutgoing->getProperties();
						
						// outgoing object data exists
						if(!empty($outgoingProperties)){
							// loop through all this category's outgoings
							for($ii = 0; $ii < count($outgoingProperties); $ii++){
							//echo "<pre>";print_r($objOutgoing->properties[$ii]); echo "</pre>";
								if(!empty($outgoingProperties[$ii]['price'])){
									// add up number of projects
									$this->_properties[$i]['outgoings'] = $outgoingProperties[$ii];
									// add up cummulative order values
									$this->_properties[$i]['total'] += $outgoingProperties[$ii]['price'];
								} //end if
							} //end for
						} // end if
						$grand_total += $this->_properties[$i]['total'];
					} // end for
					$this->_grandTotal += $grand_total;
					
					// Get outgoing category percentage spend
					for($i = 0; $i < sizeof($this->_properties); $i++){
						$this->_properties[$i]['percentage'] = ($this->_grandTotal > 0 && !empty($this->_properties[$i]['total'])) ? ((float)$this->_properties[$i]['total']/(float)$this->_grandTotal*100) : '0';
					} // end for
					
			} // end else if
			
			
			
		}
		
		
		/**
		 *	getGrandTotal
		 *	return float $_grandTotal
		 */
		public function getGrandTotal(){
			return $this->_grandTotal;
		}
		
		
	
	}

?>