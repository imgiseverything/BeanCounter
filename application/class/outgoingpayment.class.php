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
 *	@modified	06/05/2009
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
 *		
 *	=========================================================================
 *	
 */
	

	class OutgoingPayment extends Scaffold{
	
		// Variables
		public $page_title;
		public $breadcrumb_title;
	
		// construct
		public function __construct($db, $filter = array(), $id = false){		
		
			$this->_name = 'outgoing payment';
			$this->_namePlural = 'outgoing payments';
			$this->_folder = '/outgoings/payments/';
			$this->_sql['main_table'] = 'outgoing_payment';
			
			$this->_filter = $filter;
			
			
			parent::__construct($db, $this->_filter, $id);	
			
		}
		
		
		
	
	}

?>