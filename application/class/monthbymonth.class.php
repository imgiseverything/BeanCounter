<?php
/**
 *	=========================================================================
 *	
 *	MonthByMonth Class
 *	-------------------------------------------------------------------------
 *	
 *	View profit/loss on sales and outgoings
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
 *	@since		09/07/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	12/04/2009
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
 *		getFirstTradingYear
 *		
 *	
 *	==========================================================================
 *	
 */
	

	class MonthByMonth extends Account{
	
		// Variables
	
		// construct
		public function __construct($db, $filter){	
			
			$this->_filter = $filter;
			
			parent::__construct($this->filter);

		}
		
				
		// getFirstTradingYear (date technically)
		public function getFirstTradingYear(){
			return true;			
		}
		
	
	}

?>