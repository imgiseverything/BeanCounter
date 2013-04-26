<?php
/**
 *	=========================================================================
 *	
 *	Contact Class
 *	-------------------------------------------------------------------------
 *	
 *	Allows email to be send from a web form to a nominated person
 *	It then sends an automated response back to the sender
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
 *	@since		02/2007
 *	
 *	edited by: 	Phil Thompson
 *	@modified	13/04/2009
 *	
 *	=========================================================================
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *	
 *	Variables
 *	
 *	Constrcutor
 *	
 *	Methods
 *		
 *		setDetails
 *		setRandomHash
 *		setHeaders
 *		setBody
 *		
 *		checkForSpam()
 *		checkValidEmail()
 *		sendEmail()
 *		sendAutomatedReply()
 *	
 *	=========================================================================
 *
 */

	class Contact {
	
		// Variables
		
		/**
		 *	@var string
		 */
		public $headers;
		
		/**
		 *	@var string
		 */
		public $body;
		
		/**
		 *	@var string
		 */
		public $test_email;
		
		/**
		 *	@var string
		 */
		public $site_name;
		
		/**
		 *	@var string
		 */
		public $email_address;
		
		/**
		 *	@var string
		 */
		public $tech_email;
		
		/**
		 *	@var string
		 */
		public $backup_email;
		
		/**
		 *	@var string
		 */
		public $site_url;
		
		/**
		 *	@var string
		 */
		protected $_randomHash;
		
		/**
		 *	@var string
		 */
		public $subject;
		
		/**
		 *	@var string
		 */
		public $send_automated_reply;
		
		/**
		 *	@var bool
		 */
		public $bcc_yourself;
		
		/**
		 *	@var string
		 */
		public $spam;
		
		/**
		 *	@var object
		 */
		protected $_application;
		
		/**
		 *	@var object
		 */
		protected $_dateFormat;

		
		/**
	 	 *	construct
	 	 *	@param object $objApplication
	 	 */
		public function __construct($objApplication){
		
			// set the mode to debug or live. debug will send the form 
			// to the developer and 'live' will send the form to the client.
			$this->mode = (empty($this->mode)) ? 'debug' : $this->mode; // 'debug' or 'live'
			
			$this->_application = $objApplication;
			
			// Local date formatting object - for easy pretty dates
			$this->_dateFormat = new DateFormat();
			
			$this->setDetails();
			
			/* PHP (php.ini) settings */
			
			// Postfix fix
			ini_set('sendmail_path', "/usr/sbin/sendmail -t -i");
			// Your mail server
			ini_set("SMTP", "mail." . $this->site_url);
			// Please specify the return address to use
			ini_set('sendmail_from', $this->email_address);
			
			/* End PHP (php.ini) settings */
			
			// setRandomHash
			$this->setRandomHash();
		}
		
		/**
		 *	setDetails
		 */
		public function setDetails(){
		
			// email of the web developer who created the form
			$this->test_email = EMAIL_ADDRESS;
			
			// Name of this website
			$this->site_name = SITE_NAME;
			
			// Main email address e.g. hello@example.com
			$this->email_address = ($this->mode == 'debug') ? $this->test_email : EMAIL_ADDRESS;
			
			// Who's it going to?
			$this->to  = $this->email_address;
			
			// Technician's email address
			$this->tech_email = ($this->mode == 'debug') ? $this->test_email : TECH_EMAIL; 
			
			// email address to be Bcc'd
			//$this->backup_email = ($this->mode == 'debug') ? $this->test_email : BACKUP_EMAIL; 
			
			// web address of this website (URL) e.g. www.example.com
			$this->site_url = $this->_application->getSiteUrl(); 
			
			$this->send_automated_reply = true;
			$this->bcc_yourself = false;
		}
		
		/**
		 *	setRandomHash()
		 *	create a boundary string. It must be unique
		 *	so we use the MD5 algorithm to generate a random hash
		 */
		public function setRandomHash(){
			$this->_randomHash = md5(date('r', time())); 
		}
		
		/**
		 *	setHeaders()
		 *	@param string $from_email(NULL) 
		 *	@param string $from_email (NULL)	 
		 *	@param bool $reset (false)
		 */
		public function setHeaders($from_email = '', $from_name = '', $reset = false){
		
			$from_name = (!empty($from_name)) ? $from_name : $this->site_name;
			$from_email = (!empty($from_email)) ? $from_email : $this->email_address;
		
			if(empty($this->headers) || $reset === true){
				// Bcc yourself
				if($this->bcc_yourself === true){
					$this->headers = "From: \"" . $from_name . "\" <noreply@" . $_SERVER['HTTP_HOST'] . ">\nReply-To: \"" . $this->site_name . "\" <" . $from_email . ">\nBcc: \"" . $this->site_name."\" <" . $this->email_address . ">\nContent-Type: multipart/alternative; boundary=\"PHP-alt-" . $this->_randomHash . "\"\nMIME-Version: 1.0\nX-Mailer: PHP/" . phpversion(); 
				} else{
					// don't Bcc yourself
					$this->headers = "From: \"" . $from_name . "\" <noreply@" . $_SERVER['HTTP_HOST'] . ">\nReply-To: \"" . $this->site_name . "\" <" . $from_email . ">\nContent-Type: multipart/alternative; boundary=\"PHP-alt-" . $this->_randomHash . "\"\nMIME-Version: 1.0\nX-Mailer: PHP/" . phpversion(); 
				} // end else
			} // end if
			
		}
		
		
		/**
		 *	setBody()
		 *	@param string $content
		 *	@param bool $convert_line_breaks (false)
		 */
		public function setBody($content, $convert_line_breaks = false){
		
				// start of message
				$email_message['start'] = "--PHP-alt-" . $this->_randomHash . "\nContent-Type: text/plain; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit;\n\n";
				// message middle bit - separate HTML and plain text
				$email_message['middle'] = "\n\n--PHP-alt-" . $this->_randomHash . "\nContent-Type: text/html; charset=\"iso-8859-1\"\nContent-Transfer-Encoding: 7bit\n\n";
				// End of message
				$email_message['end'] = "\n\n--PHP-alt-" . $this->_randomHash . "--\n\n";
				
				// HTML message
				$email_message['html'] = ($convert_line_breaks === true) ? nl2br($content) : $content;
			
				// Plain text version fof message
				$email_message['text'] = strip_tags($email_message['html']);
				
				// put email parts together
				$this->body = $email_message['start'] . stripslashes($email_message['text']) . $email_message['middle'] . stripslashes($email_message['html']) . $email_message['end'];
				
		}
	
			
		/**
		 *	checkForSpam()
		 *	prevents email injection attacks by checking supplied
		 *  string for common spam characteristics and return the string
		 *	with those elements removed
		 *
		 *	@param string $string 
		 *			email body content or subject or email address
		 *	@return string $safe
		 */
		public function checkForSpam($string) {
			// 
			$safe = (preg_replace(array("/%0a/", "/%0d/", "/Content-Type:/i", "/bcc:/i", "/to:/i", "/cc:/i" ), "", $string ) );
			if($safe != $string){
				$subject = 'Mail injection attempt';
				$message = 'On ' . $_SERVER['HTTP_HOST'] . ' in ' . $_SERVER['PHP_SELF'] . ' at line ' . __LINE__ . ' from ' . $_SERVER['REMOTE_ADDR'] . '. String [when cleaned] was ' . $safe;
				$headers = "From: " . $this->site_name . " web server <" . $this->email_address . "> \r\n";
				$headers .= 'X-Mailer: PHP/' . phpversion();
				@mail($this->tech_email,$subject, $message, $headers);// send email to tech guy showing the problem
				header("HTTP/1.0 403 Forbidden");
			   // exit; 
			}
			return $safe;
		}
		
		/**
		 *	checkValidEmail()
		 *	Is a supplied email valid?
		 *	@copyright - http://www.ilovejackdaniels.com/php/email-address-validation
		 *	@param string $email
		 */
		public function checkValidEmail($email){

			// First, we check that there's one @ symbol, and that the lengths are right
			if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
				// Email invalid because wrong number of characters in one section, 
				// or wrong number of @ symbols.
				return false;
			} // end if
			
			// Split it into sections to make life easier
			$email_array = explode("@", $email);
			$local_array = explode(".", $email_array[0]);
			
			for ($i = 0; $i < sizeof($local_array); $i++){
				if (!ereg("^(([A-Za-z0-9!$%&'*+/=?^_`{|}~-][A-Za-z0-9!$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
					return false;
				} // end if
			} // end for
			
			// Check if domain is IP. If not, it should be valid domain name
			if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])){
			
				$domain_array = explode(".", $email_array[1]);
				if (sizeof($domain_array) < 2) {
					return false; // Not enough parts to domain
				} // end if
				
				for ($i = 0; $i < sizeof($domain_array); $i++) {
					if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
						return false;
					} // end if
				} // end for
				
			} // end if
			return true;
		}
		
		/**
		 *	sendEmail()
		 *	@param string $name
		 *	@param string $email
		 *	@param string $subject
		 *	@param string $message
		 *	@return array $user_feedback
		 */
		public function sendEmail($name, $email, $subject, $message){
		
			 // set error counter. 
			 // Increment everytime an error occurs
			$error = 0;
			
			// First, make sure the form was posted from a browser.
			if(!isset($_SERVER['HTTP_USER_AGENT'])){ 
				die("Forbidden - You are not authorised to do this");
				exit(); 
			} // end if
			
			//proceed if all required elements are present
			if($name && $email && $this->checkValidEmail($email) && $message){
			
				// contact details of sender (required)
				$result = "Contact us from " . $this->site_url . ":\n\nName: $name\n"; 				
				// Name of sender
				$name = $this->checkForSpam($name);
				// email fo sender
				$email = $this->checkForSpam($email);
				
				// setHeaders
				$this->setHeaders($email, $name);
				// setBody - check for spam first
				$message = $this->checkForSpam($message);
				$this->setBody($message, true);				
				
				if(@mail($this->to, $subject, $this->body, $this->headers)){
					// send automated reply
					if($this->send_automated_reply === true){
						$this->sendAutomatedReply($name, $email);
					} // end if
					$user_feedback['content'][] = 'Your message has been sent.';
					
					// Debugging
					niceError('To: ' . $this->to);
					niceError('Subject: ' . $subject);
					niceError('Header: <br />' . $this->headers);
					niceError('Body: <br />' . $this->body);
					
				} else{
					$error++;
					$user_feedback['content'] = 'This website encountered a technical error and your email was not sent. Please try again';
				}
		
			} else{
			
				// otherwise there must be missing required fields so show errors
			
				// create user friendly error messages
				$error++;
				$user_feedback['content'][] = 'Your message hasn\'t been sent due to the following errors:';
				
				if(!$name){
					$error++;
					$user_feedback['content'][] = 'You haven\'t entered your name';
				} // end if
				if(!$email){
					$error++;
					$user_feedback['content'][] = 'You haven\'t entered your email address';
				} // end if
				if(!$message){
					$error++;
					$user_feedback['content'][] = 'You haven\'t entered a message';
				} // end if
				$user_feedback['content'][] = 'Please correct your errors and try again.';
			} // end else
			
			$user_feedback['type'] = ($error > 0) ? 'error' : 'success';
			
			// now send back user feedback message;
			return $user_feedback;
			
		}
	
		/**
		 *	sendAutomatedReply
		 *	@param string $name
		 *	@param string $email
		 */
		public function sendAutomatedReply($name, $email) {
		
			// Automated response email body
			$reply = "<p>Hello {$name}</p> \n\n";
			$reply .= "<p>Thanks for contacting " . $this->site_name . ", we'll try and get back in touch with you as soon as we can.</p>\n\n";
			$reply .= "<p>Best wishes,</p>\n\n<p>" . $this->site_name . "<br>\n<a href=\"mailto:" . $this->email_address . "\">" . $this->email_address . "</a><br>\n<a href=\"http://" . $this->site_url . "/\">http://" . $this->site_url . "/</a></p>";
			
			// email subject
			$subject  = "Hello from " . $this->site_name;
			//setRandomHash
			$this->setRandomHash();
			//setHeaders
			$this->setHeaders('', '', true);
			//setBody
			$this->setBody($reply, false);
		
			// sendEmail
			@mail($email, $subject, $this->body, $this->headers);
		}
		
	}

?>