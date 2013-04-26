<?php
/*
 *	=========================================================================
 *	
 *	InvoiceEmail Class	
 *	-------------------------------------------------------------------------
 *	
 *	Allows email to be sent from a web form to a nominated person
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
 *	@since		01/02/2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	15/03/2012
 *	
 *	=========================================================================
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *	
 *	Variables
 *	
 *	Constructor
 *	
 *	Methods
 *		sendEmail
 *	
 *	=========================================================================
 *	
 */
 	
 	
require_once(LIBRARY_PATH . '/swiftmailer/swift_required.php');

require_once(LIBRARY_PATH . '/swiftmailer/classes/Swift/SmtpTransport.php');
require_once(LIBRARY_PATH . '/swiftmailer/classes/Swift/Transport/IoBuffer.php');
require_once(LIBRARY_PATH . '/swiftmailer/classes/Swift/Transport/StreamBuffer.php');
require_once(LIBRARY_PATH . '/swiftmailer/classes/Swift/StreamFilters/StringReplacementFilterFactory.php');

	
class InvoiceEmail {

	/**
	 *	@var	string
	 *	live or debug
	 */
	public $mode = 'live';
	
	/**
	 *	@var	string
	 *	SMTP host (deliverhq.com)
	 */
	protected $_email_smtp = EMAIL_SMTP;
		
	/**
	 *	@var	string
	 *	SMTP username (deliverhq.com)
	 */
	protected $_email_username = EMAIL_USERNAME;
		
	/**
	 *	@var	string
	 *	SMTP password (deliverhq.com)
	 */
	protected $_email_password = EMAIL_PASSWORD;
		
	/**
	 *	@var	mixed
	 *	string or array of email addresses to be emailed
	 */
	protected $_to;
	
	/**
	 *	@var	string
	 *	file path to attachment (HTML invoice)
	 */
	protected $_attachment;
	
	/**
	 *	@var	object
	 *	
	 */
	protected $_application;
	
	/**
	 * Constructor
	 *	@param object $objApplication
	 *	@param mixed string or array of email addresses to email
	 *	@param mixed string or boolean path to attachment
	 *	@param (boolean|string) $attachment_type (FALSE)
	 */
	public function __construct($objApplication, $to, $attachment = false, $attachment_type = false){
		
		$this->_application = $objApplication;
				
		$this->setDetails();
	
		$this->_to = $to;

		$this->_attachment = $attachment;
	
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
		
		// web address of this website (URL) e.g. www.example.com
		$this->site_url = $this->_application->getSiteUrl(); 
		
		$this->send_automated_reply = true;
		$this->bcc_yourself = false;
	}
	
	
	
	/**
	 * 	sendEmail()
	 *
	 * 	@param string $subject - email subject heading
	 * 	@param string $message - email body content
	 *	@return array $user_feedback
	 */
	public function sendEmail($subject, $message){
	
		// Set error counter. 
		// Increment everytime an error occurs
		$error = 0;
		
		// First, make sure the form was posted from a browser.
		if(!isset($_SERVER['HTTP_USER_AGENT'])){ 
			die("Forbidden - You are not authorised to do this");
			exit(); 
		}
		
		// proceed  but only if all required elements are present
		if($message && $subject){
			
			
			// clean up some HTML/text anomalies
			$this->body = str_replace('£', '&pound;', $message);
			//$this->body = 'Email content';
			
			
			// Send the email with SWIFT mailer
			
			$this->headers = "From: " . $this->site_name . " <noreply@" . $_SERVER['HTTP_HOST'] . ">\nReply-To: \"" . $this->site_name . "\" <" . $this->email_address . ">\nBcc: " . $this->site_name . " <" . $this->email_address . ">";
			
			
			// Create email transportation method - if we have SMTP settings use them/otherwise use php's mail() function
			if(!empty($this->_email_smtp) && !empty($this->_email_username) && !empty($this->_email_password)){
				$transport = Swift_SmtpTransport::newInstance($this->_email_smtp, 25)->setUsername($this->_email_username)->setPassword($this->_email_password);
			} else{
				$transport = Swift_MailTransport::newInstance();
			}
			  
			$mailer = Swift_Mailer::newInstance($transport);
			 
			// Create the message
			$message = Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom(array('noreply@beancounterapp.com' => $this->site_name))
			->setReplyTo(array($this->email_address => $this->site_name))
			->setBcc(array($this->email_address => $this->site_name))
			->setTo($this->_to)
			->setBody(stripslashes($message))
			->attach(Swift_Attachment::fromPath($this->_attachment)->setFilename('invoice.html'));

			if($result = $mailer->send($message)){
				$user_feedback['content'][] = 'This item has been emailed to ' . join(', ', $this->_to);
			} else{
				$error++;
				$user_feedback['content'] = 'This website encountered a technical error and your email was not sent. Please try again';
			}
	
		} else{
			
			// There must be missing required fields so show errors
		
		
			// Create user friendly error messages
			$error++;
			$user_feedback['content'][] = 'Your message hasn\'t been sent due to the following errors:';
			
			if(!$subject){
				$error++;
				$user_feedback['content'][] = 'You haven\'t entered a subject';
			}
			
			if(!$message){
				$error++;
				$user_feedback['content'][] = 'You haven\'t entered a message';
			}
			
			$user_feedback['content'][] = 'Please correct your errors and try again.';
			
		}
		
		$user_feedback['type'] = ($error > 0) ? 'error' : 'success';
	
		// Now send back user feedback message;
		return $user_feedback;
		
	}

	
	
	
	
}
