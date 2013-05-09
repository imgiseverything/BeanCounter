<?php
/**
 *	=========================================================================
 *	
 *	Vcard Class
 *	-------------------------------------------------------------------------
 *	
 *	Take an address and format it as a vcard, in case microformats:
 *	a: take off
 *	b: become useful :)
 *	
 *	=========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 	2008-2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.0	
 *	@author		philthompson.co.uk philthompson.co.uk
 *	@modified	 
 *	
 *	edited by:	Phil Thompson
 *  @modified	09/05/2013
 *
 *	-------------------------------------------------------------------------
 *
 *	@author Phil Thompson http://philthompson.co.uk
 *	@copyright 2009-2013 Phil Thompson
 *	
 *	=========================================================================
 *	
 *	Table of contents
 *	-------------------------------------------------------------------------
 *
 *	Variables
 * 	Construct
 * 	Methods
 *	
 *		setVcard
 *		setStreetAddress
 *		setLocality
 *		setRegion
 *		setPostalCode
 *		setCountryName
 *		setAddress
 *		setTel
 *		setEmail
 *		setName
 *		getVcard
 *		getAddress
 *	=========================================================================
 *
 */

class Vcard{

	// Variables
	
	/**
	 *	@var string
	 */
	protected $_vcard;
	
	/**
	 *	@var string
	 */
	protected $_fullName;
	
	/**
	 *	@var string
	 */
	protected $_emailAddress;
	
	/**
	 *	@var string
	 */
	protected $_streetAddress;
	
	/**
	 *	@var string
	 */
	protected $locality;
	
	/**
	 *	@var string
	 */
	protected $_region;
	
	/**
	 *	@var string
	 */
	protected $_postalCode;
	
	/**
	 *	@var string
	 */
	protected $_countryName;
	
	/**
	 *	@var string
	 *	full address
	 */
	protected $_address;
	
	
	/**
	 *	@var string
	 */
	protected $_telephoneNumber;
	
	/**
	 *	@var object
	 */
	protected $_site;
	
	/**
	 *	Constructor
	 *	@param (array|bool) $address (FALSE)
	 */
	public function __construct($address = false){
		
		global $objSite; // sorry mum :(
		
		$this->_site = $objSite;
		
		// setup variable usage
		$main_contact = (!empty($address['main_contact']) && $address['main_contact'] != ' ' &&  $address['main_contact'] != read($address, 'title', '')) ?  trim($address['main_contact']) . ',' :  '';
		
		$this->_fullName = (!empty($address)) ?  trim($main_contact.' '.read($address,  'title', '')) : read($this->_site->config, 'Website name', '');
		
		$this->_emailAddress = (!empty($address)) ?  read($address, 'email', '') : read($this->_site->config, 'Email address', '');
		
		$this->address1 = (!empty($address)) ?  trim(read($address, 'address1', '')) : trim(read($this->_site->config, 'Address line 1', ''));
		
		$this->address2 = (!empty($address)) ?  trim(read($address, 'address2', '')) : trim(read($this->_site->config, 'Address line 2', '')) . ' ' . trim(read($this->_site->config, 'Address line 3', ''));
		
		$this->address3 = (!empty($address)) ?  trim(read($address, 'address3', '')) : trim(read($this->_site->config, 'City/Town', ''));
		
		$this->address4 = (!empty($address)) ?  trim(read($address, 'address4', '')) : trim(read($this->_site->config, 'County/State', ''));
		
		$this->_postalCode = (!empty($address)) ?  trim(read($address, 'postal_code', '')) : trim(read($this->_site->config, 'Postal code', ''));
		
		$this->_countryName = (!empty($address)) ?  read($address, 'country_title', read($address, 'country', '')) : read($this->_site->config, 'Country', '');
		
		$this->_telephoneNumber = ($address) ?  read($address, 'telephone', '') : read($this->_site->config, 'Main telephone number', '');
		
		$this->setVcard();
		
	}
	
	/**	
	 *	setVcard()
	 */
	protected function setVcard(){
		
		// set up vcard values
		$this->setFullName();
		$this->setStreetAddress();
		$this->setLocality();
		$this->setRegion();
		$this->setPostalCode();
		$this->setCountryName();
		$this->setAddress();
		$this->setTelephoneNumber();
		$this->setEmailAddress();
	
		$this->_vcard = '<div class="vcard">' . "\n";
		$this->_vcard .= $this->_fullName;
		$this->_vcard .= $this->_emailAddress;
		$this->_vcard .= $this->_telephoneNumber;
		$this->_vcard .= '<div class="adr">' . "\n";
		$this->_vcard .= $this->_address;
		$this->_vcard .= '</div>' . "\n";
		$this->_vcard .= '</div>' . "\n";
		
	}	
	
	/**	
	 *	setStreetAddress
	 */
	protected function setStreetAddress(){	
	
		// if address line 1 is absent don't show the street_address value
		if(empty($this->address1)){
			$this->_streetAddress = '';	
		} else{
			$this->_streetAddress = '<div class="street-address">';
			$this->_streetAddress .= $this->address1 . ', ';
			$this->_streetAddress .= (!empty($this->address2) && $this->address2 != ' ') ? '<br />' . trim($this->address2) . ', ' : '';
			$this->_streetAddress .= '</div>';
    	} // end else
    	
	}
	
	/**	
	 *	setLocality
	 */
	protected function setLocality(){			
		$this->_locality = (!empty($this->address3)) ? '<div class="locality">' . trim($this->address3) . ',</div> ' . "\n" : '';
	}
	
	/**	
	 *	setRegion
	 */
	protected function setRegion(){			
		$this->_region = (!empty($this->address4)) ? '<div class="region">' . trim($this->address4) . ',</div> ' . "\n" : '';
	}
	
	/**	
	 *	setPostalCode
	 */
	protected function setPostalCode(){			
		$this->_postalCode = (!empty($this->_postalCode)) ? '<div class="postal-code">' . $this->_postalCode . ',</div> ' . "\n" : '';
	}
	
	/**	
	 *	setCountryName
	 */
	protected function setCountryName(){			
		$this->_countryName = (!empty($this->_countryName)) ? '<div class="country-name">' . $this->_countryName . '</div> ' . "\n" : '';
	}
	
	
	
	/**	
	 *	setAddress
	 */
	protected function setAddress(){
		$this->_address = $this->_streetAddress;
		$this->_address .= $this->_locality;
		$this->_address .= $this->_region;
		$this->_address .= $this->_postalCode;
		$this->_address .= $this->_countryName;
	}
	/**	
	 *	setTelephoneNumber
	 */
	protected function setTelephoneNumber(){			
		$this->_telephoneNumber = (!empty($this->_telephoneNumber)) ? '<div class="tel">Tel: <span class="value">' . $this->_telephoneNumber . '</span></div> ' . "\n" : '';
	}
	
	/**	
	 *	setEmailAddress
	 */
	protected function setEmailAddress(){			
		$this->_emailAddress = (!empty($this->_emailAddress)) ? '<div class="email"><a href="mailto:' . $this->_emailAddress . '" title="Email address">' . $this->_emailAddress . '</a></div> ' . "\n" : '';
	}
	
	/**	
	 *	setFullName
	 */
	protected function setFullName(){			
		$this->_fullName = '<div class="fn org">' . $this->_fullName . '</div> ' . "\n";
	}
	
	/**
	 *	getVcard()
	 */
	public function getVcard(){
		return $this->_vcard;
	}
	
	
	
	/**
	 *	getAddress()
	 */
	public function getAddress(){
		return $this->_address;
	}
	
	
}


?>