<?php
/**
 *	=========================================================================
 *	
 *	File Manager Class
 *	-------------------------------------------------------------------------
 *	
 *	Look after files or uploads in a database
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
 *	@since		23/12/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	21/11/2009
 *	
 *  =========================================================================
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	Variables
 *	
 *	Constructor
 *	
 *	Methods
 *	
 *		
 *	
 *	=========================================================================
 */

	class FileManager extends Scaffold{
	
		// Variables
		
		
		/**
		 *	@var object
		 */
		private $_file;

		
		
		/**
		 *	Constructor
		 *	@param object $db
		 *	@param array $filter - data options for SQL
		 *	@param (id|bool) $id
		 */
		public function __construct($db, $filter = array(), $id = false){
			
			$this->_file = new File($db, $filter, $this->_id); 
			
			parent::__construct($db, $filter, $id);
			
			// Set a filename
			if($this->_id){
				$this->_filename = $this->_properties['filename'];
			}
		}
		
		/**
		 *	setNamingConventions()
		 *	This whole system's automation is *very* dependant
		 *	upon the naming of key variables $_name, $_namePlural
		 *	and $_folder - it used to slim down the amount of code needed.
		 *	Object naming conventions
		 *  unless a child object have themselves set some defaults
		 */
		protected function setNamingConventions(){
			$this->_name = (empty($this->_name)) ? 'upload' : $this->_name;
			$this->_namePlural = (empty($this->_namePlural)) ? 'uploads' : $this->_namePlural;
			$this->_folder = (empty($this->_folder)) ? '/uploads/' : $this->_folder;
			
			parent::setNamingConventions();
		}

		/**
		 *	upload()
		 *	use the file object to physically upload the file
		 */
		public function upload(){
			$this->_file->upload();
		}
		
		
		/**
		 *	add()
		 *	Add a new file to the database
		 *	First upload the file then use the parent object's add
		 *	method to add a record in the database
		 */
		protected function add(){

			// File added
			$upload_feedback = $this->_file->upload();
			
			// If file uploads ok then add the file into the database
			// but if that fails we need to roll back the whole shebang
			// Actually: is this a nuisance?
			if(!empty($upload_feedback['type']) && $upload_feedback['type'] == 'success'){
			
				$this->_filename = str_replace($this->_file->getDirectory(), '', $this->_file->getFilename());
				$_POST['filename'] = $this->_file->getFilename();
				$_POST['mimetype'] = $this->_file->getMimeType();
				$_POST['filesize'] = $this->_file->getFileSize();
				
				$user_feedback = parent::add();
				
				// Delete file from system
				if(empty($user_feedback['type']) && $user_feedback['type'] != 'success'){
					@unlink($this->_file->getDirectory() . $this->_filename);
				}
			} else{
				$user_feedback = $upload_feedback;
			}
			
			return $user_feedback;
		}
		
		/**
		 *	delete()
		 *	Delete file from the database & the file system
		 */
		protected function delete(){
	
			$user_feedback = parent::trash();
			// THe database delete worked so now delete the file from the system	
			if(!empty($user_feedback['type']) && $user_feedback['type'] == 'success'){
				@unlink($this->_file->getDirectory() . $this->_filename);
			}
			
			return $user_feedback;
		}
		
		/**
		 *	edit()
		 *	Edit a file the database
		 *	Check if a file needs updating then use the parent's edit
		 *	method to manipulate the record in the database
		 */
		protected function edit(){
		
			if(!empty($_FILES['file'])){
				// File added
				$upload_feedback = $this->_file->upload();
				
				// If file uploads ok then add the file into the database
				// but if that fails we need to roll back the whol shebang
				// Actually: is this a nuisance?
				if(!empty($upload_feedback['type']) && $upload_feedback['type'] == 'success'){
					
					$this->_filename = str_replace($this->_file->getDirectory(), '', $this->_file->filename);
					$_POST['filename'] = $this->_filename;
				
					$user_feedback = parent::edit();
				
					if(empty($user_feedback['type']) && $user_feedback['type'] != 'success'){
						@unlink($this->_file->getDirectory() . $this->_filename);
					}
				}

			} else{
				$user_feedback = parent::__construct();
			}
		
		
			return $user_feedback;
		}
		
		
		/**
		 *	getDownloadFolder
		 *	@return string
		 */
		public function getDownloadFolder(){
			return $this->_file->getDirectory();
		}
		
		/**
		 *	convertBytes
		 *	@param int $bytes
		 *	@return string
		 */
		public function convertBytes($bytes){
			return $this->_file->convertBytes($bytes);
		}
		
		
		/**
		 *	getMaxSize
		 *	@return int
		 */
		public function getMaxSize(){
			return $this->_file->getMaxSize();
		}
		
		/**
		 *	getExtensions
		 *	@return array
		 */
		public function getExtensions(){
			return $this->_file->getExtensions();
		}
		
		
		/**
		 *	getCleanDirectory
		 *	@return string
		 */
		public function getCleanDirectory(){
			return $this->_file->getCleanDirectory();
		}
		
	
	
	}
?>