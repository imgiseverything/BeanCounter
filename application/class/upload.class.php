<?php
/**
 *	=========================================================================
 *	
 *	Upload Class	
 *	-------------------------------------------------------------------------
 *	
 *	Add/Edit/Delete/View uploads
 *
 *	@copyright Phil Thompson
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
 *	@since		22/12/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	21/11/2009
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
 *		setMaxSize
 *		getMaxSize		
 *		setExtensions
 *		getExtensions	
 *		upload		
 *		update				
 *		getUploadError		
 *		setExtension
 *		getExtensions	
 *		setAll		
 *		convertBytes
 *	
 *	=========================================================================
 *	
 *
 */
	
	

	ini_set("memory_limit", "80M");


	class Upload{
	
		// Variables
		
		/**
		 *	@var int
		 */
		protected $_fileSize;
		
		/**
		 *	@var int
		 */
		protected $_maxSize;
		
		/**
		 *	@var string
		 */
		protected $_filename;
		
		/**
		 *	@var string
		 */
		protected $_mimeType;
		
		/**
		 *	@var array
		 */
		public $files = array();
		
		/**
		 *	@var array
		 */
		protected $_files = array();
		
		/**
		 *	@var string
		 */
		protected $_directory;
		
		/**
		 *	@var array
		 */
		private $_sql;
		
		/**
		 *	@var array
		 */
		protected $_images = array();
		
		/**
		 *	@var array
		 */
		protected $_extensions = array();
		
		/**
		 *	@var int
		 */
		protected $_total;
		
		/**
		 *	@var object
		 */
		protected $_db;
		
		/**
		 *	@var int
		 */
		protected $_id;
		
		/**
		 *	@var int
		 */
		protected $_currentPage;
		
		/**
		 *	@var int
		 */
		protected $_perPage;
		
		/**
		 *	@var string
		 */
		protected $_orderBy;
		
		/**
		 *	@var string
		 */
		protected $_search;
		
		/**
		 *	@var string
		 *	past or future
		 */
		protected $_tense;
		
		/**
		 *	@var int
		 */
		protected $_status;
		
		
		/**
		 *	@var string
		 */
		protected $_fileNamePrefix;
		
		/**
		 *	@var int
		 *	number of days
		 */
		protected $_timeframe;
		
		/**
		 *	@var array
		 */
		protected $_timeframeCustom;
	
		/**
		 *	Constructor
		 *	@param object $db
		 *	@param array $filter - data options for SQL
		 *	@param (id|bool) $id
		 */
		public function __construct($db, $filter = array(), $id = false){
			
			// Local database object
			$this->_db = $db;
			
			$this->_id = $id;
		
			$this->_sql['main_table'] = 'uploads';
			
			// set directory (where files are stored/created)
			$this->setDirectory();
			
			$this->setExtensions();
			
			$this->setMaxSize(2);

			
			
			// Object Population Filters
			$this->_currentPage = read($filter, 'current_page', 1); // what page of results are we on (e.g. usually 1 unless there are lots of results
			$this->_perPage = read($filter, 'per_page', 20); //  how many results do we show per page?
			$this->_orderBy = read($filter, 'order_by', 'date'); // how shall we order the results (if more than one exists)?
			$this->_search = read($filter, 'search', ''); // is someone searching the object for specific keywords?
			$this->_timeframe = read($filter, 'timeframe', '');
			$this->_timeframeCustom = read($filter, 'timeframe_custom', '');
			
			
			$this->_fileNamePrefix = read($filter, 'file_name_prefix', '');
			
			
			
			// get all uploads and put into an array
			$this->setAll();
			$this->setById();
		
		}
		
		
		/**
		 *	setDirectory
		 */
		protected function setDirectory(){
			$this->_directory = SITE_PATH . 'uploads/'; // where the files will go
			$this->checkDirectory();
		}
		
		
		/**
		 *	getCleanDirectory()
		 *	@return string
		 */
		public function getCleanDirectory(){	
			$clean_directory = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->_directory);
			return $clean_directory;
		}
		
		/**
		 *	checkDirectory()
		 *	Make directory if it doesn't exist
		 */
		protected function checkDirectory(){
	
			if(!is_dir($this->_directory)){
				mkdir($this->_directory, 0755);
			}
			
		}
		
		/**
		 *	setExtensions()
		 */
		protected function setExtensions(){
			$this->_extensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'gif', 'png', 'zip', 'tar.gz');
		}


		/**
		 *	upload
		 */
		public function upload(){

			// Error counter. 
			// Increment everytime there is an error.
			$error = 0;
			
			// what fields are needed/might be submitted?
			$fields = array('title');
			
			// turn field name into easy to use variable names: e.g. $email
			extract(cleanFields($fields));	
			
			// loop through all potential file uploads - original and views A, B, and C
			foreach($_FILES as $upload => $value){
			
				// upload exists
				if(!empty($_FILES[$upload]['name'])){
					//echo $upload.'<br>';
					$file = $_FILES[$upload];

					$this->_fileSize = $file['size'];
					$this->_mimeType = $file['type'];
					
					$too_big = ($this->_fileSize > $this->_maxSize) ? true : false;
			
					// are required variables present?
					$all_data_present = ($file && $title && $too_big === false) ? true : false;
					
					// yes they are
					if($all_data_present === true){
					
						$this->file_id = cleanString($title);
						
						// has file upload been attempted?		
						if(is_uploaded_file($file['tmp_name'])){
							//$user_feedback['content'][] = 'file has uploaded';
							if(!empty($file['tmp_name'])){								
								$this->setExtension($file['type'], $_FILES[$upload]['name']);
							} else{
								return false;
							}
						} else{
							$error++;
							$user_feedback['content'][] = $this->getUploadError($file['error']);
						}
						
						
						// if the file type isn't supported we have to tell the user so
						$file_not_supported = (!in_array($this->extension, $this->_extensions)) ? true : false;
						
						
						// is the file big enough?
						
						// work out file filename (ID)
						$alt_view_id = str_replace('file', '', $upload);
						$alt_view_id = str_replace('_', '', $alt_view_id);
						$this->_filename = $this->_directory . $this->niceFileName($_FILES[$upload]['name']) . '.' . $this->extension;
						
						// If there is a file with this name already append a -1 on the end
						// TODO - what if there's multiples - do we really want filename-1-1-1.pdf ? Needs a more elegant solution
						if(file_exists($this->_filename)){
							$this->_filename = str_replace('.' . $this->extension, '-1.' . $this->extension, $this->_filename);
						}	
									
						
						if($file_not_supported === false){
							// put original file version into new location
							if (!copy($file['tmp_name'], $this->_filename)){
								  // if an error occurs the file could not
								  // be written, read or possibly does not exist
								  $user_feedback['content'][] = "There was an error uploading the file" . $this->_filename;
								  $error++;
							} else{
								$user_feedback['content'][] = '<a href="' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->_filename) . '" download class="button-add butotn-download"><span></span>Download new file</a>';
								// check dimensions
								$user_feedback['content'][] = 'File has uploaded and copied to the correct location';
								
							}
						} else{ // file type is not supported
							$error++;
							$user_feedback['content'][] = 'The type of file you have tried to upload is not supported.';
							$user_feedback['content'][] = 'We can only accept files that end in ' . join(', or ', $this->_extensions);
						}
						
					
					} else{
						// data is missing
						
						$error++;
						$user_feedback['content'][] = 'The file was not uploaded due to the following problems:';
						// No file
						if(!is_uploaded_file($file['tmp_name'])){
							$error++;
							$user_feedback['content'][] = 'The actual document was missing';
						}
						
						// No product ID
						if(!$title){
							$error++;
							$user_feedback['content'][] = 'The document\'s title was missing';
						}
						
						// file too big
						if($file_is_too_big === true){
							$error++;
							$user_feedback['content'][] = 'The document you tried to upload was too big. The maximum size of a file we can take is ' . self::convertBytes($this->max_size) . ' but your document is ' . self::convertBytes($file['size']);
						}
					}
					
				}
			}
			
			
			$this->setAll();
			
			// redirect user & give feedback
			$user_feedback['type'] = ($error > 0) ? 'error' : 'success';
			
			return $user_feedback;
			
		}
		
		/**
		 *	update
		 *	@param string $filename
		 */
		public function update($filename){
		
			$error = 0;
			
			$this->_filename = $filename;
			$new_file_details = getfilesize($this->_filename);
			
			$arrFile = explode('/', $this->_filename);
			$file_name = array_pop($arrFile);
			
			// copy file to new location
			copy($this->_filename, $this->_directory . $file_name);
			
			
		}	
				
		
		/**
		 *	getUploadError
		 *	return english language error based on the file upload error code
		 *	@param int $code
		 *	@return string $error
		 */
		public function getUploadError($code) {
			
			switch ($code) {
			
				case UPLOAD_ERR_OK:
					$error = false;
					break;
					
				case UPLOAD_ERR_INI_SIZE:
					//$error = "The uploaded file exceeds the upload_max_filesize directive (".ini_get("upload_max_filesize").") in php.ini.";
					$error = "The uploaded file was too big.";
					break;
					
				case UPLOAD_ERR_FORM_SIZE:
					//$error = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
					$error = "The uploaded file was too big.";
					break;
					
				case UPLOAD_ERR_PARTIAL:
					$error = "The uploaded file was only partially uploaded.";
					break;
					
				case UPLOAD_ERR_NO_FILE:
					$error = "No file was uploaded.";
					break;
					
				case UPLOAD_ERR_NO_TMP_DIR:
					$error = "There isn't a temporary folder on your web server. Please contact your server administrator for assistance.";
					break;
					
				case UPLOAD_ERR_CANT_WRITE:
					$error = "There is a problem with permissions on your web server. This file could not be copied to the correct folder because of it. Please contact your server administrator for assistance.";
					break;
					
				default:
					$error = "Unknown file error.";
					break;
					
			}
			
			return $error;
									
		}
		
		
		/**
		 *	setExtension()
		 *	work out what the files extension is, if it is already 
		 *	supplied by the filename e.g. filename.doc then use that
		 *	otherwise try to guess it. Bloody OS X not adding extensions
		 *	or users!  
		 *	SECURITY: Could a user upload an exe or js as a .doc and 
		 *	have it work? That should be blocked... but how?
		 *	@param string $mimetype
		 *	@param (boolean|string ) $filename
		 */
		public function setExtension($mimetype, $filename = false){
		
			if($filename != false && strpos($filename, '.') !== false){
				$dots_array = explode('.', $filename);
				$this->extension = end($dots_array);
			} else{
			
				// return file extension based on mimetype
				switch($mimetype){
					default:
						$this->extension = 'xxx';
						break;
						
					case 'image/jpg':
					case 'image/jpeg':
						$this->extension = 'jpg';
						break;
						
					case 'image/png':
						$this->extension = 'png';
						break;
						
					case 'image/gif':
						$this->extension = 'gif';
						break;
						
					case 'image/tiff':
						$this->extension = 'tif';
						break;
						
					case 'image/x-icon':
						$this->extension = 'ico';
						break;
						
					case 'image/bmp':
						$this->extension = 'bmp';
						break;
					case 'image/psd':
						$this->extension = 'psd';
						break;
					
					case 'application/octet-stream':
						$this->extension = 'docx';
						break;
						
					case 'text/vnd.ms-word':
						$this->extension = 'doc';
						break;
						
					case 'application/x-zip':
						$this->extension = 'zip';
						break;
					
					case 'application/vnd.ms-excel':
						$this->extension = 'xls';
						break;
				}
			}
			
		}
		
		
		
		/**
		 *	setAll()
		 *	go to the specified images directory and put all 
		 *	the images into an array...
		 *	...but what if we have lots of images?
		 */
		public function setAll(){
		
			// open specified directory
			$open_directory = opendir($this->_directory);
			
			$i = 0; // counter

			
			$this->_files = array();
			while (false !== ($file = readdir($open_directory))) {
			  	// if not a subdirectory and if filename contains the string '.jpg/.png/.gif' 
				if(!is_dir($file)) {
					
					// image file names will be like image_name_title.jpg so 
					// let's make them a bit easier to read e.g. 'image name title'
					$replace_old = array('_', '-', '%20', '.jpg', '.gif', '.png');
					$friendly_file_name = str_replace($replace_old, ' ', $file);
					// add images to the array
					$this->_files[$i] = array('filename' => $file, 'name' => trim($friendly_file_name), 'href' => $this->_directory . $file);
					$i++;
			  	}
		   	} 
		   	
		   	closedir($open_directory);

			$this->_total = sizeof($this->_files);
			
			$this->filter();
		}
		
		/**
		 *	setById()
		 *	@param int $id
		 */
		public function setById($id = false){
		
			$id = ($id) ? $id : $this->_id;
		
			if($id){
				foreach($this->_files as $file){
					if($file['name'] == $id){
						$this->_filename = $file['filename'];
						break;
					}
				}
			}
		}
		
		/**
		 *	filter()
		 */
		public function filter(){
		
			// files exists in directory
			if($this->_files){
				// counter  - array to number to start with 
				$i = ($this->_perPage * ($this->_currentPage - 1));
				// max number of files to show
				$limit = ($this->_perPage * $this->_currentPage);

				// loop through and put set numbers of files into new $_images array
				for($i; $i < $limit; $i++){
					if(!empty($this->_files[$i])){
						$this->_images[$i] = $this->_files[$i];
					}
				}
				
			}
			
		}
		
		
		/**
		 *	convertBytes
		 *	give  auser friendly filesize from a the file's byte size
		 *	in the following format 2.34Mb
		 *	@param string $bytes
		 *	@return string 
		 */
		public static function convertBytes($bytes){
		
			$size = ($bytes / 1024);
			
			if($size < 1024){ // Kilobytes
				$size = number_format($size, 2);
				$size .= ' Kb';
			} else{

				if($size / 1024 < 1024){ // Megabytes
					$size = number_format($size / 1024, 2);
					$size .= ' Mb';
				} else if ($size / 1024 / 1024 < 1024){ // Gigabytes
					$size = number_format($size / 1024 / 1024, 2);
					$size .= ' Gb';
				}
				
			} 
			
			return $size;
		} 
		
		/**
		 *	niceFileName
		 *	remove the file extension
		 *	then clean up the filename to make it file system friendly
		 *	e.g. no spaces, or weird characters
		 *	@param string $filename
		 *	@return string 
		 */
		protected function niceFileName($filename){
			
			// if dots exist in the filename then an extension
			// is presumed to be present so let's remove it
			if(strpos($filename, '.') !== false){
				$dots_array = explode('.', $filename);			
				$extension = end($dots_array);			
				$filename = str_replace('.' . $extension, '', $filename);
			}
			
			
			cleanString($filename);
			
			if(!empty($this->_fileNamePrefix)){
				$filename = $this->_fileNamePrefix . '-' . $filename;
			}
			
			return $filename;
		}
		
		
		/**
		 *	getIcon
		 *	@param string $type
		 *	@return string 
		 */
		public function getIcon($type){
			
			switch(strtolower($type)){
				
				default:
					$icon = 'report';
					break;
				
				case 'image':
					$icon = 'picture';
					break;
					
				case 'document':
					$icon = 'page_white_word';
					break;
					
				case 'spreadsheet':
					$icon = 'page_white_excel';
					break;
					
				case 'presentation':
					$icon = 'page_white_powerpoint';
					break;
					
				case 'zip':
					$icon = 'page_white_zip';
					break;
					
				case 'audio':
					$icon = 'sound';
					break;
					
				case 'video':
					$icon = 'film';
					break;
					
			}
			
			return $icon . '.png';
		}
		
		/**
		 *	getExtensions()
		 */
		public function getExtensions(){
			return $this->_extensions;
		}
		
		/**
		 *	getDirectory()
		 */
		public function getDirectory(){
			return $this->_directory;
		}
		
		
		
		/**
		 *	getFilename()
		 */
		public function getFilename(){
			return $this->_filename;
		}
		
		/**
		 *	getMaxSize()
		 */
		public function getMaxSize(){
			return $this->_maxSize; 
		}
		
		/**
		 *	getFiles()
		 */
		public function getFiles(){
			return $this->_files; 
		}
		
		/**
		 *	setMaxSize()
		 *	maximum size the image can be in bytes
		 */
		public function setMaxSize($maxSize = 2){
			$this->_maxSize = ($maxSize * 1024 * 1024); 
		}
		
		
		/**
		 *	getTotal()
		 */
		public function getTotal(){
			return $this->_total;
		}
		
		/**
		 *	getCurrentPage()
		 */
		public function getCurrentPage(){
			return $this->_currentPage;
		}
		
		/**
		 *	getPerPage()
		 */
		public function getPerPage(){
			return $this->_perPage;
		}
		
		/**
		 *	getOrderBy()
		 */
		public function getOrderBy(){
			return $this->_orderBy;
		}
		
		/**
		 *	getStatus()
		 */
		public function getStatus(){
			return $this->_status;
		}
		
		/**
		 *	getSearch()
		 */
		public function getSearch(){
			return $this->_search;
		}
		
		/**
		 *	getTimeframe()
		 */
		public function getTimeframe(){
			return $this->_timeframe;
		}
		
		/**
		 *	getTimeframeCustom()
		 */
		public function getTimeframeCustom(){
			return $this->_timeframeCustom;
		}
		
		
		/**
		 *	getFileSize()
		 */
		public function getFileSize(){
			return $this->_fileSize;
		}
		
		/**
		 *	getMimeType()
		 */
		public function getMimeType(){
			return $this->_mimeType;
		}
		
		
	}
	
?>