<?php

/**
 *	Donation Class
 *
 *	@package		Bean Counter
 *	@copyright 		2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@author			philthompson.co.uk
 *	@since			14/08/2010
 *	@lastmodified	05/09/2017
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

class Donation extends Scaffold{

	/**
	 *	@var float
	 */
	protected $_subTotal = 0;

	/**
	 *	@var float
	 */
	protected $_giftAidTotal = 0;

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
		$this->_name = 'donation';

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
	 *	setGrandTotal
	 */
	public function setGrandTotal(){

		// setup a default total of zero incase there are no tasks
		$total = 0;
		$gift_aid = 0;

		// get total for one item
		if($this->_id){
			$total = $this->_properties['amount'];
			$gift_aid = $this->_properties['gift_aid'];
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
							$gift_aid += $property['gift_aid'];
					}
					// put total value into relevant property array
					$this->_properties[$i]['total'] = $property['amount'];
					$this->_properties[$i]['gift_aid'] = $property['gift_aid'];

					$i++; // increment counter
				}
			}
		}

		// sub total
		$this->_subTotal = $total;
		$this->_giftAidTotal = $gift_aid;

		$this->_grandTotal = ($gift_aid + $total);

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
	 *	getSubTotal()
	 */
	public function getSubTotal(){
		return $this->_subTotal;
	}

	/**
	 *	getGiftAidTotal()
	 */
	public function getGiftAidTotal(){
		return $this->_giftAidTotal;
	}

	/**
	 *	getGrandTotal()
	 */
	public function getGrandTotal(){
		return $this->_grandTotal;
	}


}

?>
