<?php
/*
 *	=========================================================================
 *
 *	DividendCompany Class
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
 *	@since		01/09/2017
 *
 *	edited by:  Phil Thompson
 *	@modified	01/09/2017
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


	class DividendCompany extends Scaffold{

		// Variables
		public $page_title;
		public $breadcrumb_title;

		// construct
		public function __construct($db, $filter = array(), $id = false){

			$this->_name = 'company';
			$this->_namePlural = 'companies';
			$this->_folder = '/dividends/companies/';
			$this->_sql['main_table'] = 'dividend_company';

			$this->_filter = $filter;


			parent::__construct($db, $this->_filter, $id);

		}




	}

?>