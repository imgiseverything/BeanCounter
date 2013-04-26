<?php
/**
 *	=========================================================================
 *	
 *	Client Class
 *	
 *	-------------------------------------------------------------------------
 *	
 *	Add/Edit/Delete/View Items from database
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
 *	@since		21/01/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	12/04/2009
 *
 *
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	variables
 *	
 *	construct
 *	
 *		
 *	// Client specific methods
 *		getProjects
 *		setVcard
 *		setTotalSpend
 *		
 *	=========================================================================
 *
 */
	
class Client extends Scaffold{

	// variables
	
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
	 *	Constructor
	 *	@param object database object
	 *	@param array 
	 *	@param int id of table row
	 */
	public function __construct($db, $filter = array(), $id = false){
		
		global $objAuthorise;
	
		// Object naming conventions
		$this->_name = 'client';
		
		// Only allow id if user is a superuser or that client to stop nosy clients seeing your other clients.
		$id = ($objAuthorise->getLevel() == 'Superuser' || $objAuthorise->dontAuthorise === true) ? $id : $objAuthorise->getClient();
		
		
		parent::__construct($db, $filter, $id);
		
		// set projects
		$this->setProjects();
		
		// set Vcard
		$this->setVcard();
		
		// work out how much client has spent
		$this->setTotalSpend();
		
	
	}

	
	
	/**
	 *
	 *	Client speficic code
	 *	
	 *	customQueryFilters
	 *	getProjects
	 *	setVcard
	 *  setTotalSpend
	 *  setPageTitle
	 *  setBreadcrumb
	 *
	 */
	
	/**
	 *	customQueryFilters
	 */
	protected function customQueryFilters(){
	
		$this->_queryFilter['custom'] = '';
		// don't show the site owner's client in the main listings
		if(!$this->_id){
			$this->_queryFilter['custom'] .= " AND t.id != '1' ";
			
			
			// Only show clients who have projects
			if($this->_filter['include_zero_spend'] !== false){
				
			} else{
				//$this->_sql['joins'] .= "LEFT JOIN project p ON p.client = t.id";
				//$this->_queryFilter['custom'] .= " AND p.id IS NOT NULL GROUP BY t.id";
			}
			
			
		}
		
		
		
		
		
	}
	
	/**
	 *	setProjects
	 */		
	protected function setProjects(){
		
		// get total for one item
		if($this->_id != ''){
		
			// project properties exist
			if(!empty($this->_properties)){
				// Query
				//$query = "SELECT * FROM project WHERE client = '{$this->_id}';";
				$project_filter['client'] = $this->_id;
				$objProject = new Project($this->_db, $project_filter, '');
				//niceError($query); // Debugging - echo SQL
				
				//Run query and put result into properties array
				$this->_properties['projects'] = $objProject->getProperties();
				//$this->_properties['projects'] = $db->get_results($query, "ARRAY_A");
				
			}

		} else{
		
			// get all clients' projects
			
			if(!empty($this->_properties)){
				// loop through all records
				for($i = 0; $i < sizeof($this->_properties); $i++){
					
					// Query
					$project_filter['client'] = $this->_properties[$i]['id'];

					$objProject_{$i} = new Project($this->_db, $project_filter, '');
					
					// Run query and put result into properties array
					$this->_properties[$i]['projects'] = $objProject_{$i}->getProperties();
				}	
			}
		}
		

	}
	
	/**
	 *	setVcard
	 */
	public function setVcard(){
		if($this->_id){
			$objVcardClient = new Vcard($this->_properties);
			$this->_properties['vcard'] = $objVcardClient->getVcard();
		}
	}
	
	/**
	 * setTotalSpend()
	 */
	public function setTotalSpend(){
		// get total for one item
		if($this->_id != ''){
		
			// project properties exist
			if(!empty($this->_properties)){
				
				// start total spend with 0
				$this->_properties['spend'] = 0;
				
				// client has projects
				if(!empty($this->_properties['projects'])){
					// loop through all projects
					foreach($this->_properties['projects'] as $project){
						// add project total on client total spend
						$this->_properties['spend'] += $project['total'];
						
					}
				}
				
				$this->_grandTotal = $this->_properties['spend'];
				
			}

		} else if(!empty($this->_properties)){
				
			// loop through all records
			for($i = 0; $i < sizeof($this->_properties); $i++){
			
				// start total spend with 0
				$this->_properties[$i]['spend'] = 0;
				
				// client has projects
				if(!empty($this->_properties[$i]['projects'])){
					// loop through all projects
					foreach($this->_properties[$i]['projects'] as $project){
						// add project total on client total spend
						$this->_properties[$i]['spend'] += $project['total'];
						$this->_grandTotal += $project['total']; 
						
					}
				}
			
			}
		}

		
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
	

}

?>