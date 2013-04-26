<?php

/**
 *	Mileage
 *	A quicker/easier way to add mileage as an outgoing.
 *
 *	@since		10/08/2011
 *	
 */


class Mileage extends Outgoing{


	/**
	 *	Constructor
	 */
	public function __construct($db, $filter, $id = false){
		parent::__construct($db, $filter, $id);

		
		$this->_name = $this->_namePlural = 'mileage';
		$this->_folder = '/outgoings/';
		$this->_sql['main_table'] =  'outgoing';
		
		
	}
	
	
	/**
	 *	add
	 *	Override the parent add method and set soem variables. Essentially we're using a cut doiwn form
	 */
	public function add(){
		
		
		$_POST['outgoing_category'] = 6;
		
		$_POST['transaction_id'] = 'n/a';
		$_POST['outgoing_payment'] = 6; // <- this is not good enough; it needs a more robust solution!
		$_POST['outgoing_supplier'] = 104; // <- this is not good enough; it needs a more robust solution!
		$_POST['price'] = $_POST['claimable_price'] = $_POST['claimable_price'] = ($_POST['miles'] * $_POST['rate']);
		$_POST['title'] = 'Mileage: ' . $_POST['miles'] . ' mile(s) at £' . $_POST['rate'] . ' per mile';
		
		
		switch($_POST['rate']){
			
			default:
			case CAR_RATE;
				$_POST['title'] = 'Car ' . $_POST['title'];
				break;
				
			case BIKE_RATE;
				$_POST['title'] = 'Bicycle ' . $_POST['title'];
				break;
				
			case PASSENGER_RATE;
				$_POST['title'] = 'Car Passenger ' . $_POST['title'];
				break;
		}
		
		$objCache = new Cache('outgoing', 1, 'outgoing');
		$objCache->delete('folder');
		
		return parent::add();

		
	}
	

}