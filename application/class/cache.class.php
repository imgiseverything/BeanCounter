<?php
/**
 *	=========================================================================
 *	
 *	Cache Class
 *	-------------------------------------------------------------------------
 *	Check for and create/load static cache files so the system doesn't
 *	have to query the database for repettive data.
 *	More or less mimics memcache (apparently) because memcache isn't always
 * 	an option.
 *	
 *	=========================================================================
 *
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 	2008-2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.1
 *	@author		philthompson.co.uk
 *	@since		10/12/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	15/03/2011
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
 *		setCacheExists
 *		getCacheExists
 *		createCache
 *		getCache
 *		delete
 *		setIgnoredFiles
 *
 *	=========================================================================
 */

class Cache{

	// Variables
	
	/**
	 *	@var string
	 */
	private $_filename;
	
	/**
	 *	@var string
	 */
	private $_fullFilename;
	
	/**
	 *	@var int
	 *	time in hours
	 */
	private $_freshness;
	
	/**
	 *	@var boolean
	 */
	private $_cacheExists = false;
	
	/**
	 *	@var string
	 */
	private $_subfolder;
	
	/**
	 *	@var string
	 */
	private $_folder;
	
	/**
	 *	@var array
	 */
	private $_ignoredFiles = array();
	
	/**
	 *	Constructor
	 */
	public function __construct($filename, $freshness = 1, $subfolder = false){
		
		$this->_folder = SITE_PATH . '/cache/';			
		$this->_filename = $filename;			
		$this->_freshness = $freshness;
		
		$this->_subfolder = $subfolder;
		
		// Add on subfolder value
		if($this->_subfolder !== false){
			$this->_folder .= $this->_subfolder . '/';
		}
		
		$this->folderExists();
		
		$this->_fullFilename = $this->_folder . $this->_filename;				
		
		// Does the cache exist?
		$this->setCacheExists();
	}
	
	// Methods	
	
	
	/**
	 *	FolderExists()
	 *	check the folder exists, if it doesn't create it
	 */
	private function folderExists(){
		
		if(is_dir($this->_folder) === false){
			mkdir($this->_folder, 0755);
		}
	}
	
	
	/**
	 * 	setCacheExists()
	 *	check for cache, if it exists and is younger than the 
	 *	'freshness' counter then return true
	 */
	private function setCacheExists(){

		$this->_cacheExists = false;
		
		if(CACHE === false){
			return;
		}
		
		if(file_exists($this->_fullFilename) && filemtime($this->_fullFilename) > strtotime(DateFormat::removeHours(date('Y-m-d H:i:s'), $this->_freshness))){
			$this->_cacheExists = true;
		}
		
	}
	
	
	// getCacheExists
	public function getCacheExists(){
		return $this->_cacheExists;
	}
	
	
	/**
	 *			
	 *	createCache()
	 *				
	 *	Use a cached flat HTML snippet file instead of 
	 *	running queries all day long.
	 *
	 *	If the cache file doesn't exist,
	 *	create it then include it (if specified)
 	 *
 	 *	@param string content to be cached
 	 *	@param boolean include the file or not
 	 *	@param boolean encrypt (encode) the file
	 */	
	public function createCache($data, $include = false, $encrypt = true){
	
	
		if(CACHE === false){
			return;
		}
	
		if($encrypt === true){
			$contents = base64_encode(serialize($data));
		} else{
			$contents = $data;
		}
		
		$handle = @fopen($this->_fullFilename, "w");
		
		if($handle){
			fwrite($handle, $contents);
			fclose($handle);
		} 
		
		if($include === true){
			if(file_exists($this->_fullFilename)){
				/** Now include the new file  */
				include($this->_fullFilename);
			} else{
				// caching didn't work so just print the inputted data 
				echo $data;
			}
		}
					
	}
	
	/**
	 *	getCache()
	 */
	public function getCache(){
		if($this->_cacheExists === true){
			return unserialize(base64_decode(file_get_contents($this->_fullFilename)));
		} else{
			return false;
		}
	}		
	
	/**
	 *	deleteCache()
	 *	@param string $type
	 *	@param string $filename
	 */
	public function delete($type = 'folder', $filename = false){
	
		// Some files shouldn't be deleted
		$this->setIgnoredFiles();
		
		if($filename){				
			switch($type){
			
				default:
				case 'folder':
				
					// Delete all cache files that start with $filename_
					// Firstly, loop through the files and check
					// the file definitely exists 
					// and that it starts with the $foldername
					// variable then delete that file						
					$directory_handle = @opendir($this->_folder) or die("Unable to open {$this->_folder}");
					
					$foldername = $filename . '_';
				
		    		while ($file = readdir($directory_handle)) {
		    		
						if(file_exists($this->_folder . $file) && substr($file, 0, strlen($foldername)) == $foldername){	
						
							@unlink($this->_folder . $file);
							niceError('deleted: ' . $this->_folder . $file); // DEBUGGING
						}			
					}
					
					// Close cache folder
		    		closedir($directory_handle);

					break;
					
				case 'file':
					// delete specific cache file(s)
					if(is_array($filename)){
						// loop through all files
						foreach($filename as $file){
							// file exists so...
							if(file_exists($this->_folder . $file)){
								// delete it
								@unlink($this->_folder . $file);
							}
						}
					} else{
						// file exists so...
						if(file_exists($this->_folder . $filename)){
							// delete file
							@unlink($this->_folder . $filename);
						}
					}
					
					break;
				
			} // end switch				
		} else{
			// delete all cache items
			// Open the cache folder then 
			// Loop through the files check that the
			// file definitely exists and isn't on the 
			// ignore list then delete it
    		$directory_handle = @opendir($this->_folder) or die("Unable to open {$this->_folder}");

    		while ($file = readdir($directory_handle)) {
				if(file_exists($this->_folder . $file) && !in_array($file, $this->_ignoredFiles) && !is_dir($this->_folder . $file)){	
					@unlink($this->_folder . $file);
					niceError('deleted: ' . $this->_folder . $file);
				} else if(is_dir($this->_folder . $file)  && !in_array($file, $this->_ignoredFiles)){

					$subdirectory_handle = @opendir($this->_folder . $file) or die("Unable to open {$this->_folder}");
					while ($file_x = readdir($subdirectory_handle)) {
						if(!in_array($file_x, $this->_ignoredFiles)){
							@unlink($this->_folder . $file . '/' . $file_x);
							niceError('deleted: ' . $this->_folder . $file . '/' . $file_x);
						}
					}
					closedir($subdirectory_handle);
				}		
			}
			
			// Close cache folder
    		closedir($directory_handle);
		}
		
	}
	
	/**
	 *	setIgnoredFiles()
	 *	Create array of files that are not to be deleted
	 *	These are hidden system files like .svn or .htaccess
	 */
	private function setIgnoredFiles(){
		$this->_ignoredFiles = array(
			'.',
			'..',
			'.DS_Store',
			'.svn',
			'.htaccess'
		);
	}


}
?>