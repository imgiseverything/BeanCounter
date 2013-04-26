<?php
/**
 *	=========================================================================
 *	
 *	CSV Class
 *	-------------------------------------------------------------------------
 *	
 *	Create downloadable CSV file from supplied contents
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
 *	@since		13/12/2007
 *	
 *	edited by:  Phil Thompson
 *	@modified	18/03/2009
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
 *		setDownloadContents		
 *		setDownload		
 *		deleteOld
 *		getDownload
 *	
 *	=========================================================================	
 *
 */
	
	class CSV{
	
	
		// Variables
		
		/**
		 *	@var string
		 */
		protected $_filename;
		
		/**
		 *	@var string
		 */
		protected $_fileExtension;
		
		/**
		 *	@var array
		 */
		protected $_contents;
		
		/**
		 *	@var string
		 */
		protected $_contentsSize;
		
		/**
		 *	@var string
		 */
		protected $_headers;
		
		/**
		 *	@var string
		 */
		protected $_download;
		
		/**
		 *	@var string
		 */
		protected $_downloadFolder;
		
		/**
		 *	@var array
		 */
		protected $_ignoredFields = array();
		
		/**
		 *	Constructor
		 *	@param array $contents
		 *	@param string $filename		 
		 *	@param array $headers	
		 *	@param array $ignored	 
		 */
		public function __construct($contents, $filename, $headers = false, $ignored = array()){
		
		
			$this->setFolder();	
			$this->setFileExtension();	
			$this->_contents = $contents;
			$this->_headers = $headers;
			$this->_ignoredFields = $ignored;
			$this->_filename = $filename;
			$this->_contentsSize = sizeof($this->_contents);
			$this->getHeaders();
			$this->setContents();
			$this->setHeaders();
			$this->setFileName();
			//$this->deleteOld($filename);
			$this->forceDownload();
			//$this->setDownload();
		}
		
		/**
		 *	setFolder()
		 */
		protected function setFolder(){
			$this->_downloadFolder = APPLICATION_PATH . '/downloads/';
		}
		
		/**
		 *	setFileExtension()
		 */
		protected function setFileExtension(){
			$this->_fileExtension = '.csv';
		}
		
		/**
		 *	setContents
		 */
		public function setContents(){
			
			$contents = '';
			// contents exist
			if(!empty($this->_contents)){
				// loop through all contents 
				foreach($this->_contents as $row){
					$this_row = array();
					$i = 1; //counter
					foreach($row as $item => $value){
						// array key exists in $this->_headers array
						if(in_array($item, $this->_headers)){
							// prettify item
							if(!is_array($value)){
								$this_row[] = '"' . stripslashes(str_replace(array("\n", "\r", "\t", '"'), '', $value)) . '"';
							} // end if
							// add comma (unless this is the last item in the row)
							//$this_row .= ($i < sizeof($row)) ? ',' : '';
							$i++;
						} // end if
					} // end foreach
					//$this_row .= ";\r\n"; // add line break
					
					$contents .= join(',', $this_row) . "\r\n"; /// add to other rows
					
				} // end foreach

			} // end if
			
			$this->_contents = $contents;
		}
		
		/**
		 *	getHeaders
		 */
		public function getHeaders(){
			
			$headers = array();
			
			
			
			// no headers have been set
			if(empty($this->_headers)){
			
				// but contents have so
				if(!empty($this->_contents)){
					// loop through contents
					foreach($this->_contents[0] as $row => $value){
						// and take array keys as CSV headers
						$headers[] = $row;
					} // end foreach
					// join together as comma separated values with line break
					$this->_headers = $headers;
				} // end if
			}
			
			// Remove unwanted fields
			$this->_headers = array_diff($this->_headers, $this->_ignoredFields);
			$this->_headers = array_values($this->_headers);
			
			
			
		}
		
		/**
		 *	setHeaders
		 */
		public function setHeaders(){
		
		
			$headers_size = sizeof($this->_headers);
		
			// clean up headers e.g. remove underscores etc
			for($i = 0; $i < $headers_size; $i++ ){
				$this->_headers[$i] = str_replace('_', ' ', ucwords($this->_headers[$i]));
				
			}
			
			// join together as comma separated values with line break
			$this->_headers = join(',', $this->_headers) . "\r\n";	
		}
		
		/**
		 *	setFilename
		 */
		public function setFileName(){
			// Creates a filename for the CSV file.
			
			$alphanumeric = "abcdefghijklmnopqrstuvwxyz0123456789012345678901234567890123456789";
			
			$random_filename = $alphanumeric{rand(0, strlen($alphanumeric) - 1)};
			
			for($i = 0; $i < 6; $i++){
				$random_filename .= $alphanumeric{rand(0, strlen($alphanumeric) - 1)};
			} // end for
			
			$this->_filename = $this->_filename . '_' . $random_filename . $this->_fileExtension;
		}
		
		/**
		 *	forceDownload
		 *	display backup file and force it to download
		 */
		public function forceDownload(){
			// 
			//$file = @fopen($this->_downloadFolder . $this->_filename, "w");
			// folder exists and is writable
			/*if($file){
				//$csv = 'header("Content-type: application/vnd.ms-excel");';
   				//$csv .= 'header("Content-disposition: csv" . date("Y-m-d") . ".xls");';
   				$contents = $this->_headers . $this->_contents;
			
				// write contents to the file
				//fwrite($file, $contents);
				// close file
				//fclose($file);
			} // end if
			*/
			
			$contents = $this->_headers . $this->_contents;
			if(!empty($contents)){
				//header ("Expires: Mon, 26 Jul 2009 05:00:00 GMT");
				header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
				header ("Cache-Control: no-cache, must-revalidate");
				header ("Pragma: no-cache");
				header ("Content-type: plain/text");
				header ("Content-Disposition: attachment; filename=\"" . $this->_filename . "\"" );
				header ("Content-Description: PHP/INTERBASE Generated Data" );
				exit($contents);
			}
		}
		
		
		/**
		 *	setDownload
		 */
		public function setDownload(){
			// download CSV file exists
			if(file_exists($this->_downloadFolder . $this->_filename)){
				$this->_download = '<a href="' . str_replace(APPLICATION_PATH, '', $this->_downloadFolder) . $this->_filename. '" class="button download" title="right click and save">Download ' . strtoupper($this->_fileExtension) . ' - ' . $this->_contentsSize . ' records (Right-click and save)</a>';
			} else{
				// no download file yet
				$this->_download = strtoupper($this->_fileExtension) . ' has not been created.';
			} // end else
		}
		
		/**
		 *	deleteOld
		 *	@param string $filename
		 */
		public function deleteOld($filename){
			// loop through directory and remove all CSV files
			// that start with $filename
			
			// Open the folder
    		$directory_handle = @opendir($this->_downloadFolder);// or die("Unable to open {$this->_downloadFolder}");
			
			// Loop through the files
    		while ($file = readdir($directory_handle)) {
				// file exi that starts the same way as the name prefix in the object construct eg '$filename'
				if(substr($file, 0, strlen($filename) + 1) == $filename . '_'){
					// file definitely exists (a double-check that is probably not needed)
					if(file_exists($this->_downloadFolder . $file)){	
						// delete file
						@unlink($this->_downloadFolder . $file);
						niceError('deleted: ' . $this->_downloadFolder . $file);
					} // end if
				} // end if			
			} // end while
			
			// Close folder
    		closedir($directory_handle);
			
		}
		
		/**
		 *	getDownload()
		 */
		public function getDownload(){
			return $this->_download;
		}

	}
	
	
?>