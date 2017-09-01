<?php

/**
 *	Pension Class
 *
 *	@package		Bean Counter
 *	@copyright 		2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@author			philthompson.co.uk
 *	@since			14/08/2010
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

class Pension extends Scaffold{

	/**
	 *	Constructor
	 *	@param object $db
	 *	@param array $filter - data options for SQL
	 *	@param (int|boolean) $id
	 */
	public function __construct($db, $filter, $id = false){

		// Object naming conventions
		$this->_name = 'pension';

		// Run parent's constructor
		parent::__construct($db, $filter, $id);

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

}

?>