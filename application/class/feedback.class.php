<?php
/**
 *	=========================================================================
 *	
 *	Feedback Class
 *	-------------------------------------------------------------------------
 *	Provide formatted user feedback
 *	
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
 *	@since		02/02/2009
 *	
 *	edited by:  Phil Thompson
 *	@modified	12/04/2009
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

	class Feedback{
	
		// Variables
		
		/**
		 *	@var array
		 */
		protected $_feedback = array();
		
		/**
		 *	@var string
		 */
		protected $_feedbackHTML;
		
		/**
		 *	@var array
		 */
		protected $_tags= array();
		
		/**
		 *	Constructor
		 *	@param array $feedback
		 */
		public function __construct(){
			
			// Local variable objects
			if(!empty($_SESSION['feedback'])){
				$this->_feedback = $_SESSION['feedback'];
				unset($_SESSION['feedback']);
			}
			
			// HTML tag settings
			// Set the HTML to make sense
			$this->_tags['heading'] = 'h3'; // h2, h3, h4, etc
			$this->_tags['list'] = 'ul';// ul or ol
			
			
						
			$this->setFeedback();
			
		}
		
		// Methods	
		
		
		/**
		 *	setFeedback()
		 *	create (human understandable) user feedback
		 *	Needs array with 2 values $this->_feedback['type'] and 
		 *	$this->_feedback['content'] the later of which
		 *	can be an array and should be if it's an error.
		 */
		public function setFeedback(){
	
				if(!empty($this->_feedback['content']) && !empty($this->_feedback['type'])) {

					
					switch($this->_feedback['type']){
						
						default:
							$title = 'Feedback';
							$type_class = '';
							break;
							
						case 'error':
							$title = 'Warning';
							$type_class = ' ' . strtolower($this->_feedback['type']);
							break;
							
						case 'success':
							$title = 'Success';
							$type_class = ' ' . strtolower($this->_feedback['type']);
							break;
						
					}
					
					$this->_feedbackHTML = '<div class="group feedback' . $type_class . '">' . "\n";
					$this->_feedbackHTML .= '<' . $this->_tags['heading'] . '>' . $title . '</' . $this->_tags['heading'] . '>' . "\n";
					
					// more than one message in feedback so show <ul>
					if(is_array($this->_feedback['content']) && sizeof($this->_feedback['content']) > 1){ 
					
						$this->_feedbackHTML .= '<' . $this->_tags['list'] . '>' . "\n";
						foreach($this->_feedback['content'] as $item){
							$this->_feedbackHTML .= '<li>' . stripslashes($item) . '</li>' . "\n";
						}
						$this->_feedbackHTML .= '</' . $this->_tags['list'] . '>' . "\n";
						
					} else if(is_array($this->_feedback['content']) && sizeof($this->_feedback['content']) == 1){
					
						// an array with only only one item so show a <p>
						$this->_feedbackHTML .= '<p>' . stripslashes($this->_feedback['content'][0]) . '</p>' . "\n";
						
					} else{ 
					
						// only one item so show it
						$this->_feedbackHTML .= '<p>' . stripslashes($this->_feedback['content']) . '</p>' . "\n";
						
					}
					
					$this->_feedbackHTML .= '</div>' . "\n";
					
				} else{
					$this->_feedbackHTML  = '';
				}
				
		}
		
		
		/**
		 *	getFeedback()
		 *	@return $this->_feedbackHtml
		 */
		public function getFeedback(){
			return $this->_feedbackHTML;
		}
		
	
	
	}
?>