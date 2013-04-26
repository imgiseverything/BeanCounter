<?php
/**
 *	=========================================================================
 *	
 *	Excel Class
 *	-------------------------------------------------------------------------
 *	
 *	Create downloadable Excel file from supplied contents
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
 *	@since		19/12/2008
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
 *		setHeaders		
 *		setContents		
 *		setFileName		
 *		setCSV		
 *		setDownload		
 *		deleteOld
 *	
 *	=========================================================================
 *
 */
 
 	include(APPLICATION_PATH.'/class/PHPExcel/PHPExcel.php');
 	include(APPLICATION_PATH.'/class/PHPExcel/IOFactory.php');
 
	
	class Excel{
	
	
		// Variables
		public $filename;
		public $contents;
		public $contents_size;
		public $headers;
		public $download;
		public $download_folder;
		private $objPHPExcel;
		private $sheet;
		private $writer;
		private $alphabet = array();
		
		// Constructor
		public function __construct($contents, $filename, $headers = false){
		
			$this->download_folder = '/downloads/';
		
			$this->setAlphabet();	
		
		
			$this->contents = $contents;
			$this->headers = $headers;
			$this->filename = $filename;
			$this->contents_size = sizeof($contents);
			
			// PHPExcel objects/setup
			$this->objPHPExcel = new PHPExcel();			
			$this->sheet = $this->objPHPExcel->getActiveSheet();
			$this->writer = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
			$this->setFileName();
			$this->writer->save($this->filename);
			
			
			
			$this->getHeaders();
			$this->setContents();
			$this->setHeaders();
			
			$this->deleteOld($filename);
			$this->setCSV();
			$this->setDownload();
		}
		
		/**/
		private function setAlphabet(){
			$this->alphabet = array ("A","B","C","D","E","F","G","H","I","J","K","L","M",
"N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

			
		}
		
		// setContents
		function setContents(){
			
			$csv_contents = '';
			// contents exist
			if(!empty($this->contents)){
				$column_i = 2;
				// loop through all contents 
				foreach($this->contents as $row){
					$row_i = 0; //counter
					foreach($row as $item => $value){
						// array key exists in $this->headers array
						if(in_array($item, $this->headers)){
							// prettify item
							if(!is_array($value)){
								$cell = '"'.stripslashes(str_replace(array("\n","\r","\t",'"'),'',$value)).'"';
								// Add to Excel cell
								$this->sheet->setCellValue($this->alphabet[$row_i].$column_i, $cell);
							} // end if
							
						} // end if
						$row_i++;
					} // end foreach
					$column_i++;
				} // end foreach

			} // end if
		}
		
		// getHeaders
		function getHeaders(){
			
			$csv_headers = array();
			
			// no headers have been set
			if(empty($this->headers)){
				// but contents have so
				if(!empty($this->contents)){
					// loop through contents
					foreach($this->contents[0] as $row => $value){
						// and take array keys as CSV headers
							$csv_headers[] = $row;
					} // end foreach
					// join together as comma separated values with line break
					$this->headers = $csv_headers;
				} // end if
			} // end if
			
		}
		
		// setHeaders
		function setHeaders(){
			// clean up headers e.g. remove underscores etc
			for($i = 0; $i < sizeof($this->headers); $i++ ){
				$this->headers[$i] = str_replace('_',' ',ucwords($this->headers[$i]));
				// Add to Excel cell
				$this->sheet->setCellValue($this->alphabet[$i].'1', $this->headers[$i]);
			}
			
			// join together as comma separated values with line break
			//$this->headers = join(',',$this->headers)."\r\n";	
		}
		
		// setFileName
		function setFileName(){
			// Creates a filename for the CSV file.
			
			$alphanumeric = "abcdefghijklmnopqrstuvwxyz0123456789012345678901234567890123456789";
			
			$random_filename = $alphanumeric{rand(0, strlen($alphanumeric) - 1)};
			
			for($i = 0; $i < 6; $i++){
				$random_filename .= $alphanumeric{rand(0, strlen($alphanumeric) - 1)};
			} // end for
			
			$this->filename = $this->filename.'_'.$random_filename.'.xls';
		}
		
		// setCSV
		function setCSV(){
			// create the actual downloadable CSV file
			$file = @fopen(SITE_PATH.$this->download_folder.$this->filename, "w");
			// folder exists and is writable
			if($file){
				//$csv = 
				'header("Content-type: application/vnd.ms-excel");';
   				//$csv .= 'header("Content-disposition: csv" . date("Y-m-d") . ".xls");';
   				$csv_contents = $this->headers.$this->contents;
			
				// write contents to the file
				fwrite($file, $csv_contents);
				// close file
				fclose($file);
			} // end if
		}
		
		
		// setCSV
		function setDownload(){
			// download CSV file exists
			if(file_exists(SITE_PATH.$this->download_folder.$this->filename)){
				$this->download = '<a href="'.$this->download_folder.$this->filename.'" class="button download" title="right click and save">Download CSV - '.$this->contents_size.' records (Right-click and save)</a>';
			}
			// no download file yet
			else{
				$this->download = 'CSV has not been created.';
			} // end else
		}
		
		// deleteOld
		function deleteOld($filename){
			// loop through directory and remove all CSV files
			// that start with $filename
			
			// Open the folder
    		$directory_handle = @opendir(SITE_PATH.$this->download_folder) or die("Unable to open $path");
			
			// Loop through the files
    		while ($file = readdir($directory_handle)) {
				// file exi that starts the same way as the name prefix in the object construct eg '$filename'
				if(substr($file, 0, strlen($filename)+1) == $filename.'_'){
					// file definitely exists (a double-check that is probably not needed)
					if(file_exists(SITE_PATH.$this->download_folder.$file)){	
						// delete file
						@unlink(SITE_PATH.$this->download_folder.$file);
						//niceError('deleted: '.APPLICATION_PATH.$this->download_folder.$file);
					} // end if
				} // end if			
			} // end while
			
			// Close folder
    		closedir($directory_handle);
			
		}

	}
	
	
?>