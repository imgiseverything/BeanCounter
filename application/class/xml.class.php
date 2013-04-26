<?php
/**
 *	=========================================================================
 *	
 *	XML Class
 *	-------------------------------------------------------------------------
 *	
 *	Create an XML file from supplied contents
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
 *	@since		30/07/2008
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
 *		setXML		
 *		setDownload		
 *		deleteOld
 *	
 *	=========================================================================	
 *
 */
	
	class XML{
	
		// Variables
		public $filename;
		public $contents;
		public $contents_size;
		public $headers;
		public $download;
		public $download_folder;
		
		// Constructor
		public function __construct($contents, $filename, $headers = false){
		
			$this->download_folder = '/downloads/';
		
			$this->contents = $contents;
			$this->headers = $headers;
			$this->filename = $filename;
			$this->contents_size = sizeof($contents);
			$this->setContents();
			$this->setHeaders();
			$this->setFileName();
			$this->deleteOld($filename);
			$this->setXML();
			$this->setDownload();
		}
		
		// setContents
		public function setContents(){
			
			$xml_contents = "<{$this->filename}>\n\t";
			// contents exist
			if(!empty($this->contents)){
				// loop through all contents 
				foreach($this->contents as $row){
					$xml_contents .= "<item>\n\t\t";
					$i = 1; //counter
					foreach($row as $item => $value){
						// array key exists in $this->headers array
						if(in_array($item, $this->headers)){
							// prettify item
							if(!is_array($value)){
								// create the <tag></tag> name
								$tag = cleanString($item);
								$tag = str_replace('-','_',$item);
								// clean up the value e.g. <tag>value</tag>
								$value = stripslashes(str_replace(array("\n","\r","\t",'"'),'',$value));
								$xml_contents .= '<'.$tag.'>'.$value.'</'.$tag.'>'."\n\t";
							} // end if
							// add tab (unless this is the last item in the row)
							$xml_contents .= ($i < sizeof($row)) ? "\t" : '';
							$i++;
						} // end if
					} // end foreach
					
					$xml_contents .= "</item>\r\n\t"; /// add to other rows
					
				} // end foreach

			} // end if
			
			$xml_contents .= "</{$this->filename}>\r\n"; /// add to other rows
			
			$this->contents = $xml_contents;
		}
		
		// setHeaders
		function setHeaders(){
			// XML prolog
			$this->headers = '<?xml version="1.0" encoding="UTF-8" ?>'."\r\n";
			/*$this->headers .= '<?xml-stylesheet type="text/css" href="http://'.$this->_application->getSiteUrl().'/style/xml.css"?>'."\r\n";*/

		}
		
		// setFileName
		public function setFileName(){
			// Creates a filename for the XML file.
			
			$alphanumeric = "abcdefghijklmnopqrstuvwxyz0123456789012345678901234567890123456789";
			
			$random_filename = $alphanumeric{rand(0, strlen($alphanumeric) - 1)};
			
			for($i = 0; $i < 6; $i++){
				$random_filename .= $alphanumeric{rand(0, strlen($alphanumeric) - 1)};
			} // end for
			
			$this->filename = $this->filename.'_'.$random_filename.'.xml';
		}
		
		// setXML
		public function setXML(){
			// create the actual downloadable XML file
			$file = @fopen(SITE_PATH.$this->download_folder.$this->filename, "w");
			// folder exists and is writable
			if($file){
				//$xml = 'header("Content-type: text/xml");';
   				//$xml .= 'header("Content-disposition: xml" . date("Y-m-d") . ".xls");';
   				$xml_contents = $this->headers.$this->contents;
			
				// write contents to the file
				fwrite($file, $xml_contents);
				// close file
				fclose($file);
			} // end if
		}
		
		
		// setXML
		public function setDownload(){
			// download XML file exists
			if(file_exists(SITE_PATH.$this->download_folder.$this->filename)){
				$this->download = '<a href="'.$this->download_folder.$this->filename.'" class="button download" title="right click and save">Download XML - '.$this->contents_size.' records (Right-click and save)</a>';
			}
			// no download file yet
			else{
				$this->download = 'XML has not been created.';
			} // end else
		}
		
		// deleteOld
		public function deleteOld($filename){
			// loop through directory and remove all XML files
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