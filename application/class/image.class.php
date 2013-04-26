<?php
/**
 *	=========================================================================
 *	
 *	Image Class	
 *	-------------------------------------------------------------------------
 *	
 *	Add/Edit/Delete/View Images
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
 *	@since		23/01/2008
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
 *		setDimensions	
 *		upload		
 *		update		
 *		resize		
 *		makeJpg		
 *		makePng		
 *		makeGif		
 *		createHeight				
 *		setAll		
 *	
 *	=========================================================================
 *
 */
	
	

	ini_set("memory_limit","80M");


	class Image extends Upload{
	
		// Variables
		
		/**
		 *	@var int
		 */
		public $width;
		
		/**
		 *	@var int
		 */
		public $height;
		
		/**
		 *	@var string
		 */
		public $name;

		
		/**
		 *	@var array
		 */
		public $_dimensions = array();
		
		
		/**
		 *	@var array
		 */
		public $images = array();
		
		
		/**
		 *	@var array
		 */
		public $images_array = array();
		
		
		/**
		 *	@var int;
		 */
		protected $_resizeQuality;
	
		
		
		/**
		 *	construct
		 */
		public function __construct($db, $filter, $id){
		
			parent::__construct($db, $filter, $id);
		
			$this->_sql['main_table'] = 'images';
			
			// set image dimensions
			$this->setDimensions();
			
			$this->_maxSize = (2 * 1024 * 1024); // maximum size the image can be in bytes
			
			$this->_resizeQuality = 90; // % for image quality resizing
			
		
		}
		
		/**
		 *	setDirectory()
		 *	folder (directory) where the images will go
		 */
		protected function setDirectory(){
			$this->_directory = SITE_PATH . '/uploads/images/';
			$this->checkDirectory();
		}
		
		/**
		 *	setExtensions()
		 */
		protected function setExtensions(){
			$this->_extensions = array('jpg', 'gif', 'png');
		}
		
		/**
		 *	setDimensions()
		 */
		public function setDimensions(){
			$this->_dimensions['small']['width'] = '100';
			$this->_dimensions['medium']['width'] = '250';
			$this->_dimensions['large']['width'] = '400';
		}
		
		/**
		 *	upload()
		 */
		public function upload(){
			
			// Set error counter to 0. 
			// Increment everytime theres is an error.
			$error = 0; 

			
			// loop through all potential image uploads - 
			// original and views A, B, and C
			foreach($_FILES as $upload => $value){
			
				// uplaod exists
				if(!empty($_FILES[$upload]['name'])){
					//echo $upload.'<br>';
					$file = $_FILES[$upload];
					
					$image_is_too_big = ($file['size'] > $this->_maxSize) ? true : false;
			
					// are required variables present?
					$all_data_present = ($file && $image_is_too_big === false) ? true : false;
					
					// yes they are
					if($all_data_present === true){
						
						// has image upload been attempted?		
						if(is_uploaded_file($file['tmp_name'])){
						
							//$user_feedback['content'][] = 'Image has uploaded';
							if(!empty($file['tmp_name'])){
	
								if($new_image_details = @getimagesize($file['tmp_name'])){
									$this->width = $new_image_details[0];
									$this->height = $new_image_details[1];
									$this->channels = read($new_image_details,'channels','');
									$this->bits =  read($new_image_details,'bits','');
									$this->mime = $new_image_details['mime'];
									// work out the image extension/type
									$this->setExtension($this->mime);
								} else{
									$this->extension = 'xxx';
								}
								
							} else{
								return false;
							}
							
						} else{
							$error++;
							$user_feedback['content'][] = $this->getUploadError($file['error']);
						}
						
						
						// if the image type isn't supported we have to tell the user so
						$image_not_supported = (!in_array($this->extension, $this->_extensions)) ? true : false;
						
						
						// is the image big enough?
						
						// work out image filename (ID)
						$alt_view_id = str_replace('image', '', $upload);
						$alt_view_id = str_replace('_', '', $alt_view_id);
						$this->_filename = $this->_directory . $alt_view_id . '.' . $this->extension;
						
						
						
						if($image_not_supported === false){
							// put original image version into new location
							if (!copy($file['tmp_name'], $this->_filename)){
								  // if an error occurs the file could not
								  // be written, read or possibly does not exist
								  $user_feedback['content'][] = "There was an error uploading the image";
								  $error++;
							} else{
								$user_feedback['content'][] = '<a href="' . str_replace(SITE_PATH, '', $this->_filename) . '">View new image</a>';
								// check dimensions
								$user_feedback['content'][] = 'Image has uploaded and copied to the correct location';
								$user_feedback['file'] = $this->_filename;
								
								// resize images in all different dimensions
								foreach($this->_dimensions as $dimension => $value){	
									// resize has worked
									if($resized = $this->resize($this->_filename, $dimension)){
										$user_feedback['content'][] = str_replace('_', ' ',ucfirst($dimension)) . ' size was created';
									}
									else{
										$error++;
										$user_feedback['content'][] = str_replace('_', ' ',ucfirst($dimension)) . ' size was not created';
									} // end else
								} // end foreach
							}// end else
						} else{
							// image is not supported
							$error++;
							$user_feedback['content'][] = 'The type of image you have tried to upload is not supported.';
							$user_feedback['content'][] = 'We can only accept files that end in ' . join(', or ', $this->extensions);
						}
						
					
					} else{
						// data is missing
						$error++;
						$user_feedback['content'][] = 'Image was not uploaded due to the following problems:';
						
						// No file
						if(!is_uploaded_file($file['tmp_name'])){
							$error++;
							$user_feedback['content'][] = 'Image file was missing';
						}
						
						// Image too big
						if($image_is_too_big === true){
							$error++;
							$user_feedback['content'][] = 'The image you tried to upload was too big. The maximum size of image we can take is ' . self::convertBytes($this->_maxSize) . ' but your image is ' . self::convertBytes($file['size']);
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
		 *	update()
		 *	@param string $filename
		 */
		public function update($filename){
		
			// Error counter.
			$error = 0;
			
			$this->_filename = $filename;
			$new_image_details = getimagesize($this->_filename);
			
			$arrImage = explode('/', $this->_filename);
			$image_name = array_pop($arrImage);
			
			// copy image to new location
			copy($this->_filename, $this->_directory . $image_name);
			
			$this->width = $new_image_details[0];
			$this->height = $new_image_details[1];
			$this->channels = $new_image_details['channels'];
			$this->bits = $new_image_details['bits'];
			$this->mime = $new_image_details['mime'];
			
			// create resized images e.g /images/contents/large/x.jpg
			foreach($this->_dimensions as $dimension => $value){	
				if($resized = $this->resize($this->_filename,$dimension)){
					$user_feedback['content'][] = str_replace('_', ' ', ucfirst($dimension)) . ' size was created';
				} else{
					$error++;
					$user_feedback['content'][] = str_replace('_', ' ', ucfirst($dimension)) . ' size was not created';
				}
			}
			
		}
		
		/**
		 *	resize()
		 *	images need resizing to set dimensions (see __construct for these)
		 *	why? because a design will always need images at a set size and if you
		 *	start having different sized images, the design may break 
		 *	and/or look bad which means lost
		 *
		 *	@param string $filename
		 *	@param string $filesize
		 *	@return boolean
		 */
		public function resize($filename, $filesize = 'thumbnail'){
			//  sales
			$error = 0;
			
			// width of the resized image
			$new_width = $this->_dimensions[$filesize]['width'];
			// height of the resize image (if it hasn't been set work it out)
			$new_height = (!empty($this->_dimensions[$filesize]['height'])) ? $this->_dimensions[$filesize]['height'] : $this->createHeight($this->width, $this->height, $new_width);
			
			
			//if it's a thumbnail - then add an 's' on the end 
			//eg /images/products/thumbnails/1.jpg
			$filesize .= ($filesize == 'thumbnail') ? 's' : '';
	
			// add folder name of image size to the directory name 
			// e.g. /images/products/thumbnails/ or /images/products/medium/
			$new_filename = str_replace($this->_directory, $this->_directory . $filesize . '/', $filename);
			
			// directory size doesn't exist
			if(!is_dir($this->_directory . $filesize . '/')){
				mkdir($this->_directory . $filesize . '/', 0755);
			}

			// the original image file doesn't exist
			if(!file_exists($filename)) {
				$error++;
				return false;
			} else{
				// original image file exists
				// work out the image extension/type
				$this->setExtension($this->mime);
				
				//run method based on image type
				switch($this->extension){
				
					default:
					case 'jpg':
					case 'jpeg':
						$this->makeJpg($new_width, $new_height, $filename, $new_filename);
						break;
						
					case 'png':
						$this->makePng($new_width, $new_height, $filename, $new_filename);
						break;
						
					case 'gif':
						$this->makeGif($new_width, $new_height, $filename, $new_filename);
						break;
						
				}
				
				
				
				// new image exists - this has worked.
				if(file_exists($new_filename)){
					//echo $new_filename.'<br />';
					return true;
				} else{
					// new image doesn't exist - this hasn't worked
					//echo 'boo!';
					return false;
				}
				
			}
			
			//return $user_feedback;
			
		}
		
		/**
		 *	makeJpg()
		 */
		public function makeJpg($new_width, $new_height, $filename, $new_filename){
		
			// create new temporary empty JPG image
			$new_image = imagecreatetruecolor($new_width, $new_height);
			$image = imagecreatefromjpeg($filename);
			
			// put newly resize image into temporary image
			imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);
			//echo $new_filename;
			
			// make image				
			imagejpeg($new_image, $new_filename, $this->_resizeQuality);
			
			// take temporary images out of memory
			imagedestroy($new_image);
			imagedestroy($image);
			
		}
		
		/**
		 *	makePng()
		 */
		public function makePng($new_width, $new_height, $filename, $new_filename){
			// create new temporary empty PNG image
			
			$new_image = imagecreate($new_width, $new_height);
			$image = imagecreatefrompng($filename);
			
			// put newly resize image into temporary image
			imagecopyresized($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);
			
			
			// What if PNG is transparent? then what?
			
			
			
			// make image				
			imagepng($new_image, $new_filename, ($this->_resizeQuality/10), PNG_NO_FILTER);
			// take temporary images out of memory
			
			imagedestroy($new_image);
			imagedestroy($image);
		}
		
		/**
		 *	makeGif()
		 */
		public function makeGif($new_width, $new_height, $filename, $new_filename){
			
			// create new temporary empty GIF image
			$new_image = imagecreate($new_width, $new_height);
			$image = imagecreatefromgif($filename);
			
			// put newly resize image into temporary image
			imagecopyresized($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);
			
			// What if GIF is transparent? then what?
			
			// make image				
			imagegif($new_image, $new_filename);
			
			// take temporary images out of memory
			imagedestroy($new_image);
			imagedestroy($image);
		}
		
		/**
		 *	createHeight()
		 */
		public function createHeight($existing_width, $existing_height, $new_width){
			// height isn't present so find out what it should be
			
			// what ratio is the height compared to the width?
			$img_ratio = $existing_width/$existing_height;
			

			
			//exit('ratio: '.$img_ratio.' width: '.$new_width.' height: '.round($new_width * $img_ratio));
			
			//echo 'width = '.$new_width.'px so new height should be'. ($new_width / $img_ratio).'px';
				
			// multiply the given width by ratio to get 
			// an in proportion new height
			return ($img_ratio > 1) ? round($new_width / $img_ratio) : round($new_width / $img_ratio);
			
		}	
		
		
		/**
		 *	setAll()
		 */
		public function setAll(){
			// go to the specified images directory and put all the images into an array
		
		
			// what if we have lots of images?
		
		
			// open specified directory
			$open_directory = opendir($this->_directory);
			
			$i = 0; // counter

			while (false !== ($file = readdir($open_directory))) {
			
			  	// if not a subdirectory and if filename contains the string '.jpg/.png/.gif' 
				if(!is_dir($file) && (strpos($file, '.jpg') > 0 || strpos($file, '.gif') > 0|| strpos($file, '.png') > 0)) {
					
					// image file names will be like image_name_title.jpg so let's make them a bit easier to read e.g. 'image name title'
					$replace_old = array('_','-','%20','.jpg','.gif','.png');
					$friendly_file_name = str_replace($replace_old,' ',$file);
					// add images to the array
					$this->_files[$i] = array('filename' => $file, 'name'=> trim($friendly_file_name), 'src' => $this->_directory . $file);
					$i++;
			  	} // end if
		   	}  // end while
		   	// close the directory
		   	closedir($open_directory);
			
			//natsort($this->_files);
			
			$this->_total = sizeof($this->_files);
			
			$this->filter();
		}		
		
		
	}
	
?>