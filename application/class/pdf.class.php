<?php
/**
 *
 *	=========================================================================
 *
 *	PDF Class
 *	-------------------------------------------------------------------------
 *	
 *	Format a single event into an iCal friendly text file 
 *	@usage
 *	$objPDF = new PDF($properties)
 *	$objPDF->display();
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
 *	@since		10/08/2009
 *	
 *	edited by:  Phil Thompson
 *	@modified	
 *	
 *	=========================================================================

 *	
 *	=========================================================================
 *	
 *	Table of contents:
 *	-------------------------------------------------------------------------
 *	
 *	Variables
 *	
 *	Constructor
 *	
 *	Methods
 *		setHeader
 *		setContent
 *		setFooter			
 *	
 *	=========================================================================
 *
 */
 

 	define('FPDF_FONTPATH', LIBRARY_PATH . '/fpdf16/font/'); 
 	
 	
 	require_once(LIBRARY_PATH . '/fpdf16/fpdf.php');
 	
 
 	class PDF extends FPDF{
 	
 	
 		/**
 		 *	@var
 		 */
 		 
 		/**
 		 *	Constructor
 		 */ 
 		public function __construct(){
 			parent::FPDF();
 		}
 		
			
		/**
 		 *	Footer
 		 */
		public function Footer(){
		    //Position at 1.5 cm from bottom
		    $this->SetY(-15);
		    //Arial italic 8
		    $this->SetFont('Arial', 'I', 8);
		    //Page number
		    $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
		}

 	
 	
 	}
 
 ?>