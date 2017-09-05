<?php

/**
 *	Dividend Class
 *
 *	@package		Bean Counter
 *	@copyright 		2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@author			philthompson.co.uk
 *	@since			01/09/2017
 *	@lastmodified
 *	@version		1
 *
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	Variables
 *	Methods
 *		Constructor
 *
 */

class Dividend extends Scaffold{

	/**
	 *	@var float
	 */
	protected $_grandTotal = 0;

	/**
	 *	@var int
	 */
	protected $_firstYear = 0;

	/**
	 *	Constructor
	 *	@param object $db
	 *	@param array $filter - data options for SQL
	 *	@param (int|boolean) $id
	 */
	public function __construct($db, $filter, $id = false){

		// Object naming conventions
		$this->_name = 'dividend';

		// Run parent's constructor
		parent::__construct($db, $filter, $id);

		// generateTotal
		$this->setGrandTotal();

		$this->setFirstYear();

	}


	/**
	 *	add
	 *	@return array $user_feedback
	 */
	protected function add(){
		$user_feedback = parent::add();

		return $user_feedback;
	}

	/**
	 *	setGrandTotal
	 */
	public function setGrandTotal(){

		// setup a default total of zero incase there are no tasks
		$total = 0;
		$gift_aid = 0;

		// get total for one item
		if($this->_id){
			$total = $this->_properties['amount'];
		} else{
			// get all item's totals
			$i = 0; //counter

			if(!empty($this->_properties)){

				// loop through all records
				foreach($this->_properties as $property){

					// project has tasks
					if(!empty($property['amount'])){
							// add outgoing amount onto total
							$total += $property['amount'];
					}
					// put total value into relevant property array
					$this->_properties[$i]['total'] = $total;

					$i++; // increment counter
				}
			}
		}

		$this->_grandTotal = $total;

	}/**
	 *	setFirstYear
	 *	grab the first ever project and use that as the
	 *	(glorious) first trading date
	 */
	public function setFirstYear(){
		//
		$query = "SELECT `transaction_date` FROM `{$this->_sql['main_table']}` t WHERE 1 ORDER BY `transaction_date` ASC LIMIT 1;";

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
	 *	getFirstYear()
	 *	@return int
	 */
	public function getFirstYear(){
		return $this->_firstYear;
	}

	/**
	 *	getGrandTotal()
	 */
	public function getGrandTotal(){
		return $this->_grandTotal;
	}

}

?>
