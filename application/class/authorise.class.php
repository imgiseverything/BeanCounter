<?php
/**
 *	=========================================================================
 *	
 *	Authorise Class
 *	-------------------------------------------------------------------------
 *	
 *	Log in/out users
 *	Authenticate users, make sure they only see what they are allowed to.
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
 *	@since		24/02/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	26/02/2010
 *	
 *  =========================================================================
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *	Variables
 *	Methods
 *		Construct
 *		Login	
 *		LoginCookie
 *		logout		
 *		Forgot	
 *		setSessions	
 *		setCookie		
 *		unsetCookie
 *	
 *	=========================================================================
 */

	class Authorise{
	
		// Variables		
		
		/**
		 *	@var	int
		 *	Length of the salt string
		 */
		const SALT_LENGTH = 9;
	
	
		/**
		 *	@var string
		 *	either 'logged-out' or 'logged-in';
		 */
		protected $_status;
		
		/**
		 *	@var string
		 *	access level - what can this user see
		 */
		protected $_level;
		
		/**
		 *	@var string
		 *	what is their name?
		 */
		protected $_name;
		
		/**
		 *	@var string
		 */
		protected $_client;
		
		/**
		 *	@var string
		 */
		protected $_lastLogin;
		
		/**
		 *	@var string
		 */
		protected $_loginTimestamp;
		
		/**
		 *	@var string
		 */
		protected $_email;
		
		/**
		 *	@var int (0|1)
		 */
		protected $_remember;
		
		
		/**
		 *	@var array
		 *	
		 */
		protected $_sql;
		
		/**
		 *	@var array
		 *	
		 */
		public $urls;
		
		/**
		 *	@var array
		 *	URLs that anyone can see
		 */
		public $free_access_urls = array(); 
		
		/**
		 *	@var object
		 */
		protected $_db;
		
		/**
		 *	@var object
		 */
		protected $_application;
		
		/**
		 *	@var string
		 */
		protected $_cookieName = 'beancounter';
		
		/**
		 *	@var string
		 */
		protected $_extraFields;
		
		
		/**
		 *	@var boolean
		 *	Should we authorise this user or not? Usually we should authorise.
		 */
		public $dontAuthorise = false;
		
		/**
		 *	construct
		 *	@param object $objApplication
		 */
		public function __construct($objApplication){
		
			global $db;
			
			$this->_db = $db;
			
			$this->_application = $objApplication;
		
			$this->_sql['main_table'] = 'user_client';
			
			// setURLs
			$this->setURLs();
			
			// setFreeAccessURLs
			$this->setFreeAccessURLs();			
			
			$this->setExtraFields();
		
			// Setup variables
			$this->_email = $this->_application->getParameter('email');
			$this->_remember = (!empty($_COOKIE[md5($this->_cookieName)])) ? read($_POST, 'remember', 1) : read($_POST, 'remember', '');

			
			
			// don't authorise when installing
			if(substr($_SERVER['REQUEST_URI'], 0, 8) == '/install'){
				$this->dontAuthorise = true;
			}
			
			$url_sections = explode('/', $_SERVER['REQUEST_URI']);
			
			// don't authorise when looking at an invoice
			if(
				(
				strpos($_SERVER['REQUEST_URI'], '/download/') !== false 
				|| strpos($_SERVER['REQUEST_URI'], '/pdf/') !== false
				)
				&& !empty($url_sections[3]) && !is_numeric($url_sections[3])
			){
				$this->dontAuthorise = true;
			}

			if($this->dontAuthorise !== true){
				$this->authoriseUser();
			}
			
		}
		
		/**
		 *	setURLs()
		 */
		public function setURLs(){
			$this->urls['login'] = '/login/';
			$this->urls['register'] = '/register/';
			$this->urls['forgot'] = '/forgot/';
			$this->urls['logout'] = '/logout/';
			$this->urls['welcome'] = '/';
			$this->urls['forward'] = (empty($_POST['forward'])) ? $this->urls['welcome'] : read($_POST, 'forward', $this->urls['welcome']);
			
		}
		
		/**
		 *	setFreeAccessURLs()
		 */
		public function setFreeAccessURLs(){
			$this->free_access_urls[] = '/inc/track.php';
		}
		
		/**
		 *	authoriseUser()
		 */
		public function authoriseUser(){
			// start session (if not already started - avoid PHP error messages)
			if(!isset($_SESSION)){
				session_start();
			}
						
			
		
			// Authorisation session variables
			$this->_id = (!empty($_SESSION)) ? read($_SESSION, 'id', '') : '';
			$this->_level = (!empty($_SESSION)) ?  read($_SESSION, 'level', '') : '';
			$this->_client = (!empty($_SESSION)) ?  read($_SESSION,'client', '') : '';
			$this->_name = (!empty($_SESSION)) ?  read($_SESSION, 'name', '') : '';
			$this->_lastLogin = (!empty($_SESSION)) ?  read($_SESSION, 'last_login', '') : '';
			$this->_loginTimestamp = (!empty($_SESSION)) ?  read($_SESSION, 'login_timestamp',' ') : '';
			
			
			// QUESTION: Should we log-out the user is logged in for too long?
		
			// setStatus
			$this->_status = ($this->_level && $this->_client && $this->_name) ? 'logged-in' : 'logged-out';
			
			// log in with the cookie (autoLogin)
			if(
				!empty($_COOKIE[md5($this->_cookieName)]) 
				&& empty($this->_id) && substr($_SERVER['REQUEST_URI'], 0, 7) != $this->urls['login'] 
				&& $_SERVER['REQUEST_URI'] != $this->urls['logout'] 
				&& read($_POST, 'action', '') != 'login'
			){
				$this->loginCookie();
			}

			// we're not on the log-in page nor the register page nor 
			// the forgot password yet the user isn't registered
			if(
				(
					substr($_SERVER['REQUEST_URI'], 0, strlen($this->urls['login'])) != $this->urls['login'] 
					&& substr($_SERVER['REQUEST_URI'], 0, strlen($this->urls['register'])) != $this->urls['register'] 
					&& substr($_SERVER['REQUEST_URI'], 0, strlen($this->urls['forgot']))  != $this->urls['forgot'] 
				) 
					&& $this->_status === 'logged-out'
			){
				$where_have_they_been = (read($_SERVER, 'REQUEST_URI', '') != '/') ? '?url=' . $_SERVER['REQUEST_URI'] : '';
				// send them to the log-in page 
				// (with a extra URL parameter so we can forward them on 
				// to the page they came from)
				redirect($this->urls['login'] . $where_have_they_been);
			} else if(
				(
					(
						substr($_SERVER['REQUEST_URI'], 0, strlen($this->urls['login'])) == $this->urls['login'] 
						|| substr($_SERVER['REQUEST_URI'], 0, strlen($this->urls['register'])) == $this->urls['register'] 
						|| $_SERVER['REQUEST_URI'] == $this->urls['forgot']
					)
				) 
				&& $this->_status === 'logged-in'
			){
				// user is logged in and on the login page
				// forward user onto the page they came from (when they were not logged in)
				redirect($this->urls['forward']);
			} else if($_SERVER['REQUEST_URI'] == '/logout/'){
				$this->logout();
			}
			
			
		}
		
		/**
		 *	login()
		 *	Log in in a user on request
		 *	@return array
		 */
		public function login(){
		
			// has user tried to login?
			$login_attempt = false;
			
			if(
				$_SERVER['REQUEST_METHOD'] == 'POST' 
				&& !empty($_POST['action']) 
				&& $_POST['action']  == 'login' 
				&& $this->_status == 'logged-out'
			){
				$login_attempt = true;
			}
			
			if($login_attempt === true){
		
				$error = 0; // error counter, increment for every error
				 
				// clean data: make nice for data input then turn 
				// field name into easy to use variable names: e.g. $email
				extract(cleanFields(array('email', 'password')));
				
				// Are all required fields present?
				$all_data_present = ($email && $password) ? true : false;
				
				if($all_data_present === true){
				
					$query = "SELECT password 
					FROM {$this->_sql['main_table']} 
					WHERE email = '$email' 
					AND status = '1' 
					LIMIT 1;";
					
					niceError($query);
					
					// User was recognised
					if($actual_password = $this->_db->get_var($query)){
						
						// does the supplied password match the database 
						// stored encrypted password?
						if($actual_password == self::generateHash($_POST['password'], $actual_password)){
						
							$query = "SELECT u.id, CONCAT(u.firstname,' ',u.surname) AS title, 
							u.client, u.date_last_login,  al.title AS level {$this->_extraFields} 
							FROM {$this->_sql['main_table']} u 
							LEFT JOIN access_level al ON al.id = u.access_level 
							WHERE email = '{$email}' 
							LIMIT 1;";
							niceError($query);
							
							if($user_details = $this->_db->get_row($query)){
							
								// update database to show when this customer last logged in
								$query = "UPDATE {$this->_sql['main_table']} SET date_last_login = Now() WHERE email = '{$email}' LIMIT 1";
								//niceError($query);
								
								if($login_time_updated = $this->_db->query($query)){
									// set sessions: to be used throughout the site for permissions/form processing etc
									if(!isset($_SESSION)){
										session_start();
									}
									
									$this->setSession($user_details);
									$user_feedback['content'] = 'You have successfully logged in ' . $_SESSION['name'];
																		
									// user has requested to be remembered
									if(isset($_POST['remember']) && $_POST['remember'] == 1){
										$this->setCookie($this->_email);
									} else{
										// user does not want to be remembered
										$this->unsetCookie();
									}
									
									// Now authorise the user - i.e. send them to the correct page.
									$this->authoriseUser();
								} else{
									$error++;
									$user_feedback['content'][] = 'Log-in attempt failed';
								}
							} else{
								$error++;
								$user_feedback['content'][] = 'Log-in attempt failed';
							}
						} else{
							$error++;
							$user_feedback['content'][] = 'Your password was not recognised';
							// increment the login attempt counter
						}
					} else{
						// email address wasn't present in database
						$error++;
						$user_feedback['content'][] = 'You have entered an unrecognised email address';
					}
				} else{ // missing data
					//$user_feedback['content'][] = 'You have missed some required fields';
					
					// No email
					if(!$email){
						$error++;
						$user_feedback['content'][] =  'You have not entered your email address';
					}
					
					// No password
					if(!$password){
						$error++;
						$user_feedback['content'][] =  'You have not entered your password ';
					}
				}
				
					
				// give user feedback
				$user_feedback['type'] = ($error > 0) ? 'error' : 'success';
				
				return $user_feedback;
			}
			
		}	
		
		/**
		 *	loginCookie()
		 *	Automatically login a user if the cookie is set.
		 *	Check the cookie first; obviously :)
		 */
		public function loginCookie(){
			
			$error = 0; // error counter
			
			$cookie = read($_COOKIE, md5($this->_cookieName), NULL);
			$cookie = $_COOKIE[md5($this->_cookieName)];

			if(!empty($cookie)){
			
			
				// Query
				$query = "SELECT u.id, CONCAT(u.firstname,' ',u.surname) AS title, u.client, u.date_last_login,  al.title AS level 
				{$this->_extraFields} 
				FROM {$this->_sql['main_table']} u 
				LEFT JOIN access_level al ON al.id = u.access_level 
				WHERE MD5(CONCAT(email,'" . SECRET_PHRASE . "')) = '{$cookie}' 
				AND u.status = '1' 
				LIMIT 1;";
				
				niceError($query); // DEBUGGING

				// Set session
				if($user_details = $this->_db->get_row($query)){
							
					// update database to show when this customer last logged in
					$query = "UPDATE {$this->_sql['main_table']} 
					SET date_last_login = Now() 
					WHERE MD5(CONCAT(email,'" . SECRET_PHRASE . "')) = '$cookie' 
					LIMIT 1";
					niceError($query); // DEBUGGING
					
					if($login_time_updated = $this->_db->query($query)){
						// set sessions: to be used throughout the site for 
						// permissions/form processing etc
						if(!isset($_SESSION)){
							session_start();
						}
						
						$this->setSession($user_details);
						$user_feedback['content'] = 'You have successfully logged in ' . $_SESSION['name'];
						
						// Now authorise the user - i.e. send them to the correct page.
						$this->authoriseUser();
					}  else{
						$error++;
					} 
					
				} // end if user exists
				
				// Remove the cookie if any problems have occurred
				if($error > 0){
					//$this->unsetCookie();
				}
				
			} // end if cookie exists
			
		}
		
		/**
		 *	logout()
		 *	log out users by killing the session and resetting cookies;
		 *	tell the user what happened then redirect user away to login page
		 */
		public function logout(){
 
			if(!isset($_SESSION)){
				session_start();
			}
			session_destroy();
			
			$this->unsetCookie();
			
			$this->_status = 'logged-out';
			
			// tell the user what's happened
			$user_feedback['content'] = 'You have successfully logged out';
			
			// 
			redirect('/login/?' . createFeedbackURL('success', $user_feedback['content']));
		}	
		
		/**
		 *	forgot()
		 *	email a new reset password on request
		 *	@return array
		 */
		public function forgot(){
		
			// run method if the correct form has been posted
			if(
				$_SERVER['REQUEST_METHOD'] == 'POST' 
				&& !empty($_POST['action']) 
				&& $_POST['action']  == 'forgot' 
				&& $this->_status == 'logged-out'
			){
		
				$error = 0; // set error as none and increment whenever one occurs
				
				// get user's password
				$email = $this->_db->escape($_POST['email']);
				
				$all_data_present = ($email && strpos($email, ';') === false) ? true : false;
				
				// are all variables present
				if($all_data_present === true){	
				
					// check user from database
					$query = "SELECT email 
					FROM {$this->_sql['main_table']} 
					WHERE email = '$email' 
					LIMIT 0, 1;";
					 // user with email exists
					if($user_exists = $this->_db->get_var($query)){
					
						// generate a new (random 5 character) password
						$new_password = substr(uniqid(), 7, 5);
						$new_hashed_password = self::generateHash($new_password);
						
						// Query: update password in database
						$query = "UPDATE {$this->_sql['main_table']} 
						SET password = '{$new__hashed_password}', date_edited = Now() 
						WHERE email = '{$email}' 
						LIMIT 1;";
						
						//niceError($query); // DEBUGGING
						
						if($password_updated = $this->_db->query($query)){
							
							
							// set up email message
							$forgot_message = 'Your password for ' . SITE_NAME . ' is : ' . $new_password;
							$email_headers = "From: " . SITE_NAME . " <" . EMAIL_ADDRESS . ">\nReply-To: " . SITE_NAME . " <" . EMAIL_ADDRESS . ">X-Mailer: PHP/" . phpversion();
							// try to send email - email sent
							if(@mail($_POST['email'], SITE_NAME, $forgot_message, $email_headers)){
								$user_feedback['content'][] = 'A new password has been created and emailed to your registered email address.';
							} else{ // email failed
								$error++;
								$user_feedback['content'][] = 'Your password was not sent due to a technical error. Please try again in 30 minutes.';
							}
						} else{ // technical error
							$error++;
							$user_feedback['content'][] = 'Password was not reset due to the following issues:';
							$user_feedback['content'][] = 'Your password was not sent due to a technical error. Please try again in 30 minutes.';
						} 
					} else{ // unrecognised email
						$error++;
						$user_feedback['content'][] = 'Password was not reset due to the following issues:';
						$user_feedback['content'][] = 'Your email address was not recognised. Are you sure you are registered with this site and that you have typed your email address correctly?';
					
					}
				} else{ // Missing required data
					$error++;
					$user_feedback['content'][] = 'Password was not reset due to the following issues:';
					
					// No email
					if(!$email){
						$user_feedback['content'][] = 'Your password was not reset because you have not entered your email address';
					} // end if no email
					
					// email, contain illegal character: possible injection
					if(strpos($email, ';') !== false){
						$user_feedback['content'][] = 'Your password was not reset because the email address entered (<em>' . $email . '</em>) was not recognised';
					} //end if illegal email
					
				} //end if missing data
				
				// Create feedback type: erro or success
				$user_feedback['type'] = ($error > 0) ? 'error' : 'success';
				
				// return feedback
				return $user_feedback;
				
			}//end if form post
			
		}
		
		/**
		 *	register
		 */
		public function register(){
			
			// run method if the correct form has been posted
			if(
				$_SERVER['REQUEST_METHOD'] == 'POST' 
				&& !empty($_POST['action']) 
				&& $_POST['action']  == 'register' 
				&& $this->_status == 'logged-out'
			){
			
				// use User object to add a new user
				
				// add extra fields
				//$_POST['status'] = '0';
				$_POST['access_level'] = '2';
				$objUser = new User(array(), '');
				$user_feedback = $objUser->add();
				
				// Adding a new user worked so send an email to the site owner telling them
				if(!empty($user_feedback['type']) && $user_feedback['type'] == 'success'){
				
					// change success message to be more relevant
					$user_feedback['content'] = 'Your registration request has been successful<br />';
					// initialise Contact object (for sending email)
					$objContact = new Contact();
					// turn off the automated reply (defaults to true)
					$objContact->send_automated_reply = false;
					
					// set up values for sending the email
					$name = read($_POST, 'firstname', '') . ' '. read($_POST, 'surname', '');
					$name = clean_xss($name);
					$email = read($_POST, 'email', 'unknown@emailaddress.com');
					$url = read($_POST, 'url', 'unknown-web-address'); // where did they come from? e.g. looking at an invoice?
					$subject = $this->_application->getApplicationName() . ' Registration request';
					$message = "A new registration request has been made by someone after trying to view the following page:\nhttp://" . $this->_application->getSiteUrl() . "{$url}\n\nTheir details are:\nName: {$name}\nEmail: {$email}\n\nReview and authorise them here:\n" . $this->_application->getApplicationName() . "\nhttp://" . $this->_application->getSiteUrl() . "/users/?status=0";
					// send the email
					$objContact->sendEmail($name, $email, $subject, $message);
					
				}
				
				return $user_feedback;
			
			}
			
		}
		
		/**
		 *	setSession()
		 *	@param array $user_details
		 */
		public function setSession($user_details){
		
			$_SESSION['id'] = $user_details->id;
			$_SESSION['name'] = $user_details->title;
			$_SESSION['client'] = $user_details->client;
			$_SESSION['level'] = $user_details->level;
			$_SESSION['last_login'] = $user_details->date_last_login;
			$_SESSION['login_timestamp'] = date('Y-m-d H:i:s');
			
		}
		
		/**
		 *	setCookie
		 *	@param string $email
		 */
		public function setCookie($email = ''){
			if($email){
				$encrypted_email = md5($email . SECRET_PHRASE);
				// set a cookie which stores the user email address for 30 days
				setcookie(md5($this->_cookieName), $encrypted_email, time()+60*60*24*30, "/");
			} else{
				setcookie(md5($this->_cookieName), '', 1, "/");
			}
		}
		
		/**
		 *	unsetCookie
		 *	if cookie exists, delete it by resetting it with a blank value;
		 */
		public function unsetCookie(){

			if(isset($_COOKIE[md5($this->_cookieName)])){
				$this->setCookie('');
			}
		}
		
		/**
		 *	setExtraFields()
		 *	used by child classes to get extra fields 
		 *	if the database table changes in a child class
		 */
		protected function setExtraFields(){
			$this->_extraFields = '';
		}
		
		/**
		 *	generateHash
		 *	@copyright	http://phpsec.org/articles/2005/password-hashing.html
		 *	@usage echo Authorise::generateHash($_POST['password']);
		 *	@usage echo Authorise::generateHash($_POST['password'], $database_result['password']);
		 *	@param	string	$plainText
		 *	@param	string	$salt
		 *	@return	string
		 */
		public static function generateHash($plainText, $salt = NULL){
		    
		    if($salt === NULL){
		        $salt = substr(md5(uniqid(rand(), true)), 0, constant('self::SALT_LENGTH'));
		    } else{
		        $salt = substr($salt, 0, constant('self::SALT_LENGTH'));
		    }
		
		    return $salt . sha1($salt . $plainText);
		    
		}
		
		
		
		/**
		 *	getStatus()
		 */
		public function getStatus(){
			return $this->_status;
		}
	
	
		/**
		 *	getId()
		 */
		public function getId(){
			return $this->_id;
		}
		
		/**
		 *	getLevel()
		 */
		public function getLevel(){
			return $this->_level;
		}
		
		/**
		 *	getName()
		 */
		public function getName(){
			return $this->_name;
		}
		
		/**
		 *	getClient()
		 */
		public function getClient(){
			return $this->_client;
		}
		
		/**
		 *	getLastLogin()
		 */
		public function getLastLogin(){
			return $this->_lastLogin;
		}
		
		/**
		 *	getLoginTimestamp()
		 */
		public function getLoginTimestamp(){
			return $this->_loginTimestamp;
		}
		
		/**
		 *	getEmail()
		 */
		public function getEmail(){
			return $this->_email;
		}
		
		/**
		 *	getRemember()
		 */
		public function getRemember(){
			return $this->_remember;
		}
		
		
		/**
		 *	getCookieName()
		 */
		public function getCookieName(){
			return $this->_cookieName;
		}
	
	
	}

?>