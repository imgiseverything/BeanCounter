<?php

/**
 *	System Error class
 *
 *	Customised error logging system
 *	
 *	@package		bean counter
 *	@since			27/01/2010
 *	@author			philthompson.co.uk
 *	@copyright		philthompson.co.uk/mediaburst.co.uk
 *	@version 		1.1	
 *
 *
 *	Contents
 *	
 *	Class variables
 *	Methods
 *		Constructor
 *		customHandler
 *		debugSQL
 *		showError
 *		setErrorReporting
 *		setDatabaseErrors
 *		logErrors
 */
 
 
class SystemError{

	// Variables
	
	/**
	 *	@var array
	 *	List of common database connection error messages
	 *	If we come across one fo these we know the database
	 *	is broken and as we need the database to function
	 *	we can shut the site down if it is absent
	 */
	public $databaseErrors = array();
	
	
	/**
	 *	constructor
	 */
	public function __construct(){
	
		$this->setErrorReporting();

		$this->databaseErrors = array(
			"mysql_connect()",
			"mysql_connect() [function.mysql-connect]: Access denied for user",
			"mysql_connect() [function.mysql-connect]: Unknown MySQL server host",
			"Error establishing mySQL database connection. Correct user/password? Correct hostname? Database server running?",
			"mySQL database connection is not active",
			"No database selected",
			"Lost connection to MySQL server at 'reading initial communication packet', system error: 61",
			"Access denied for user"
		);
		
	}
	
	/**
	 *	customHandler
	 *	@param	string	$number
	 *	@param	string	$string
	 *	@param	string	$file
	 *	@param	string	$line
	 *	@param	string	$context
	 */
	public function customHandler($number, $string, $file, $line, $context){
		
		// check for database error
		foreach($this->databaseErrors as $error){
			if(strpos($string, $error) !== false){
				$_GET['system_error'] = true;
				self::showError('error', 'There is no available connection to the database');
				// Log the error somewhere
				$this->logError($number, $string, $file, $line, $context);
				// Don't execute PHP internal error handler
				return true;
			}
		}
		
		
		
		// Different actions for different error types
		switch ($number) {
			case E_USER_ERROR:
				$_GET['system_error'] = true;
		        self::showError('error', $string);
				break;
				
			case E_USER_WARNING:
			    echo "<b>My WARNING</b> [$number] $string<br />\n";
			    break;
			
			case E_USER_NOTICE:
			    echo "<b>My NOTICE</b> [$number] $string<br />\n";
			    break;
			
			case E_NOTICE:
			default:
			
			    echo "<b>Unknown error type:</b> [$number]<br /> $string<br />$file on $line<br />\n";
			    break;
		}
		
		
		// Log the error somewhere
		$this->logError($number, $string, $file, $line, $context);
		
		// Don't execute PHP internal error handler
		return true;
		
		
	}
	
	/**
	 *	debugSQL
	 *	Print out the query if debug mode is on
	 *	@param	string	$sql
	 *	@param	array	$details
	 *	@param	boolean	$force_show
	 */
	public static function debugSQL($sql, $details = array(), $force_show = false){
		
		
		if((DEBUG_MODE == 'on' || $force_show === true) && MODE != 'live'){
			echo '<div class="feedback feedback-error">';	
			echo '<h2>' . $sql . '</h2>';	
			if(!empty($details)){
				echo '<ul>';	
				foreach($details as $key => $value){
					echo '<li><b>' .  $key . '</b>: ' . $value . '</li>';
				}
				echo '</ul>';
			}
			echo '<p><small>This is a database debugging message it won\'t be on the live server</small></p>';
			echo '</div>';
		}
		
	}
	
	
	/**	
	 *	show404
	 *	If someone tries to access page that doesn't exist trigger this error
	 *	@param	string	$type
	 *	@param	string	$message
	 */
	public static function showError($type, $message = ''){
		
		switch($type){
			default:
				$default_message = 'An error has occurred';
				break;
				
			case '404':
				header("HTTP/1.0 404 Not Found");
				$default_message = 'Content could not be found';
				break;
			
			case 401:
			case 403:
				header("HTTP/1.0 401 Unauthorized");
				$default_message = 'You are not authorised to view this content';
				break;
			
		}
		
		if(!empty($message)){
			$_GET['message'] = $message;
		} else{
			$_GET['message'] = $default_message;
		}
		
		
		
		include(VIEWS_PATH . 'error.php');
		exit;
	}
	
	/**
	 *	setErrorReporting()
	 *	check which server we're on and decide which error reporting 
	 *	levels to show based on that information
	 */
	public function setErrorReporting(){
	
		switch(MODE){
		
			// Live website e.g. www.example.com
			case 'live';
			case 'production'; 
				ini_set("display_errors", "no");
				error_reporting(0);
				break;
				
			// Staging website e.g. test.example.com
			case 'staging';
				ini_set("display_errors", "yes");
				error_reporting(E_ALL);
				break;
			
			// Local Testing website e.g. http://localhost/example/
			case 'local':
			default:
				ini_set("display_errors", "yes");
				error_reporting(E_ALL);
				break;
				
		} 
		
		// use this error for ALL (E_ALL) errors 
		set_error_handler('custom_error_handler', E_ALL); 
		
	}
	
	
	/**
	 *	logError
	 *	Write the error to a log file
	 *	@param	string	$number
	 *	@param	string	$string
	 *	@param	string	$file
	 *	@param	string	$line
	 *	@param	string	$context
	 */
	public function logError($number, $string, $file, $line, $context){
	
		$date =  date('d/m/y H:i:s');
		
		$error = "Date:{$date}\nError no: {$number}\n";
		$error .= "String:{$string}\nFile:{$file}\nLine:{$line}\n\n";
	
		$filename = APPLICATION_PATH . 'error.log';
		
		
		if(is_writable($filename)){
			
			if($file_handler = fopen($filename, 'a')){
			
				if(fwrite($file_handler, $error) !== false) {
					// hooray it worked
				}
				
				fclose($file_handler);
			}
		}
		
	}
	
}

/**
 *	custom_error_handler
 *	@param	string	$number
 *	@param	string	$string
 *	@param	string	$file
 *	@param	string	$line
 *	@param	string	$context
*/
function custom_error_handler($number, $string, $file, $line, $context){
	$objSystemError = new SystemError();
	$objSystemError->customHandler($number, $string, $file, $line, $context);
}

?>