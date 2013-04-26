<?php
/**
 *	
 *	Pagination Class
 *	
 *	Create numbered listings of pages
 *	Should produce results like so:
 *	First  Previous		1 2 3 4 5 6 7 8 9		Next  Last
 *	
 *	Pagination needs a few variables to work
 *	
 *	1. The total number of results e.g. 100
 *	2. The number of results to be shown per page e.g 20
 *	3. The page we are currently looking at e.g. 3
 *	
 *	Given the above variables there would be 5 pages, we'd be looking at 
 *	number 3 which should results 41-60
 *	
 *	Has two modes (for URL structure):
 *	1 clean (e.g. /page2/) set it like so: $_cleanUrls = true
 *	2 dirty (e.g. .php?page=2) set it like so: $_cleanUrls = false
 *	
 *	
 *	Clean mode requires mod_rewrite to work.
 *	
 *	@package	bean counter	
 *	@copyright 	2008-2010 (c)	Phil Thompson	http://philthompson.co.uk
 *	@version	1.1	
 *	@author		philthompson.co.uk
 *	@since		10/01/2008
 *	
 *	
 *	
 *	Contents
 *	
 *	Variables
 *
 *	Methods
 *		Constructor
 *		getCurrentURL	
 *		setNextLink	
 *		setPreviousLink	
 *		setFirstLink
 *		setLastLink
 *		setPagination
 *		getPagination
 */
	
class Pagination{

	// Variables
	
	/**
	 *	@var  int
	 *	total number of records there are
	 */
	private $_totalResults; 
	
	/**
	 *	@var 	int
	 *	total records to show on a page
	 */
	private $_perPage = 20;
	
	/**
	 *	@var 	int
	 *	the number of the page we're currrently on
	 */
	private $_currentPage = 1;
	
	/**
	 *	@var 	string
	 *	URL of page we are currently on
	 */
	private $_currentUrl;
	
	/**
	 *	@var 	string
	 *	?x=y&z=x	
	 */
	private $_queryString;
	
	/**
	 *	@var 	int	
	 *	total number of pages needed
	 */
	private $_totalPages = 1;
	
	// The following variables are the actual pagination 
	// HTML that has been created (or not)
	
	/**
	 *	@var 	string	
	 *	HTML for the link to the next page (coiuld be inactive)
	 */
	private $_nextLink;
	
	/**
	 *	@var  	string	
	 *	HTML for the link to the previous page (coiuld be inactive)
	 */
	private $_previousLink;
	
	/**
	 *	@var 	string	
	 *	HTML for the link to the last page (could be NULL)
	 */
	private $_lastLink;
	
	/**
	 *	@var 	string
	 *	HTML for the link to the first page (could be NULL)
	 */
	private $_firstLink;
	
	/**
	 *	@var 	string	
	 *	The actual whole HTML of the pagination links
	 */
	private $_pagination;
	
	/**
	 *	@var	boolean
	 *	Should we use clean URLs e.g. /page2/ or
	 * 	not e.g. /?page=2
	 */
	private	$_cleanUrls = true;
	
	/**
	 *	Constructor
	 *	@param int $totalResults
	 *	@param int $perPage
	 *	@param int $currentPage (default = 1)
	 *	@param string $currentUrl (default = NULL)
	 *	
	 */
	public function __construct($totalResults, $perPage = 20, $currentPage = 1, $currentUrl = NULL, $cleanUrls = true){
		
		// Set local variables
		$this->_totalResults = $totalResults;
		$this->_perPage = $perPage;
		$this->_currentPage = $currentPage;
		if($this->_totalResults > 0 && $this->_perPage > 0){
			$this->_totalPages = ceil($this->_totalResults / $this->_perPage);
		}
		
		$this->_currentUrl = $currentUrl; 
		
		$this->_cleanUrls = $cleanUrls; 
		
		// Recreate page's URL to append pagination 
		// query string on the end (e.g. ?page=3)
		$this->getCurrentURL();
		
		// Create pagination HTML
		$this->setPagination();
		
	}
	
	/**
	 *  getCurrentURL
	 *  
	 *	The URL is so important - if we get it wrong, the whole thign collapses
	 *	We need a format like this example.com/folder/webpage/page4/
	 *	
	 *	If a query string exists we want to show
	 *	example.com/folder/webpage/page4/?query=string
	 *	
	 *	This means we have to put the page number identifier spliced inbetween the end 
	 *	of the URL and the query string  
 	 *	and ensure we don't get this:
	 *	example.com/folder/webpage/page4/page2/page3/ as we moved through the pages
	 *	 
	 */ 
	private function getCurrentURL(){
		
		// if current URL (which it invariably will be) is empty 
		// use $_SERVER['REQUEST_URI]]
		$this->_currentUrl = (!$this->_currentUrl) ? $_SERVER['REQUEST_URI'] : $this->_currentUrl;
		
		if($this->_cleanUrls === true){
			$this->_currentUrl = str_replace('.php', '/', $this->_currentUrl);
		}
		
		// remove existing page number references - so we avoid URLS like 
		// example.com/folder/webpage/page4/page2/page3/
		$this->_currentUrl = preg_replace('(page[0-9]+/)', '', $this->_currentUrl);
		
		//$this->_currentUrl = preg_replace('(page=[0-9])', '', $this->_currentUrl);
		

		// the presence of a question mark indicates query string 
		// but where in the string is the ?
		// Split the URL into URL and Query string parts
		// Query string is everything after and including the question mark
		// Current URL is everything before the question mark

		$placeOfQS = strpos($this->_currentUrl, '?');  

		$this->_queryString = ($placeOfQS !== false) ? substr($this->_currentUrl, -(strlen($this->_currentUrl) - $placeOfQS)) : '';			
		$this->_currentUrl = ($placeOfQS !== false) ? substr($this->_currentUrl, 0, $placeOfQS) : $this->_currentUrl;
		
		// when the last character isn't a / add it on
		if(substr($this->_currentUrl, -1) != '/' && $this->_cleanUrls === true){
			$this->_currentUrl .= '/';
		}
		
	}
	
	/**
	 *	setNextLink()
	 */
	private function setNextLink(){
		
		if($this->_currentPage == $this->_totalPages){
			// we're on the last page - so there is no 'next' link
			// for UI's sake we'll keep the HTML but add a 'inactive' class
			// so we can hide it or style it differently with CSS
			$nextLinkHref = $this->_currentUrl . 'page';
			$nextLinkHref .= $this->_totalPages . '/';
			$class = ' inactive'; 
		} else if($this->_totalPages == 0){
			// there's not a next page
			$nextLinkHref = $this->_currentUrl;
			$class = ' inactive';
		} else{
			// there is a next page - next page number is 1 more than the current page.
			$nextLinkHref = $this->_currentUrl . 'page';
			$nextLinkHref .= ($this->_currentPage + 1) . '/';
			$class = '';
		}
		
		// next link HTML
		$this->_nextLink = '<li class="bookend next' . $class . '"><a href="' . $nextLinkHref . $this->_queryString . '">Next</a></li>' . "\n\t";
		
	}
	
	/**
	 *	setPreviousLink
	 */
	private function setPreviousLink(){

		
		if($this->_currentPage == 1 || $this->_currentPage == 0){
			// we're on page 1 - don't show a link
			$previousLinkHTML = $this->_currentUrl;
		} else{
			// // we're on page 2 or greater - previous page number is current page minus 1
			$previousLinkHTML = $this->_currentUrl . 'page';
			$previousLinkHTML .= ($this->_currentPage-1) . '/';
		}
		
		
		// Set an 'inactive' class as a CSS/Javascript hook
		$class = ($this->_currentPage == 1 || $this->_currentPage == 0) ? ' inactive' : '';
		
		$this->_previousLink = '<li class="bookend previous' . $class . '"><a href="' . $previousLinkHTML . $this->_queryString . '">Previous</a></li>' . "\n\t";	
		
	}
	
	/**
	 *	setLastLink()
	 */
	private function setLastLink(){
		$this->_lastLink = '';
		
		if($this->_totalPages > 10){
			$this->_lastLink = '<li class="bookend last"><a href="'. $this->_currentUrl . 'page' . $this->_totalPages . '/'  . $this->_queryString . '">Last</a></li>';
		}
	}
	
	/**
	 *	setFirstLink()
	 */
	private function setFirstLink(){
		$this->_firstLink = '';
		
		if($this->_totalPages > 10){
			$this->_firstLink = '<li class="bookend first"><a href="'. $this->_currentUrl . $this->_queryString . '">First</a></li>';
		}
	}
	
	/**
	 * 	setPagination()
	 *	Create the pagination HTML
	 *	Should produce an unordered list of page numer plus previous/next links:
		 *	<ul class="pagination">
		 *		<li><a href="page1/">1</a></li>
		 *		<li><a href="page2/">2</a></li>
		 *	</ul>
	 */
	private function setPagination(){
	
		$this->_pagination = '';
		
	
		// there are multiple pages, so create pagination links
		if($this->_totalPages > 1){
	
			// set bookmark links e.g. first, last, next and previous page links
			$this->setFirstLink();
			$this->setLastLink();
			$this->setPreviousLink();
			$this->setNextLink();
	
			// Start pagination HTML
			$this->_pagination .= '<ul class="pagination">' . "\n\t";
			
			// Add previous and first links								
			$this->_pagination .= $this->_firstLink;
			$this->_pagination .= $this->_previousLink;
				
			// Add numbered links
			for($i = 1; $i <= $this->_totalPages; $i++){
			
				// set class to show current page
				$class = ($i == $this->_currentPage) ? ' class="selected"' :  '';
				
				// create page number link href value
				if($i == 1){
					$linkHref = $this->_currentUrl;
				} else{
					$linkHref = $this->_currentUrl . 'page' . $i . '/';
				}

				
				// Only show page numbers that are 5 numbers either way
				//	e.g. if 13 was he current page we'd see
				//	8 9 10 11 12 13 14 15 16 17 18
				// as opposed to showing all the page numbers
				
				$print_link = false;
				
				if($this->_totalPages < 11 || ($this->_currentPage < 11 && $i < 11)){				
					$print_link = true;
				}
				
				if($this->_totalPages > 10 && ($i <= ($this->_currentPage + 5) && $i >= ($this->_currentPage -5))){
					$print_link = true;
				}
				
				if($this->_totalPages > 10 && $i < 5 && $this->_currentPage > 9){
					$print_link = false;
				}
				
				//this->_totalPages < 11 || ($this->_totalPages > 10 && ($i <= ($this->_currentPage + 5) && $i >= ($this->_currentPage -5)))
				
				if($print_link === true){
					$this->_pagination .= '<li' . $class . '><a href="' . $linkHref . $this->_queryString . '">' . $i . '</a></li>' . "\n\t";
				}
			}
				
			// Add Next and Last link
			$this->_pagination .= $this->_nextLink;
			$this->_pagination .= $this->_lastLink;
			
			
			// Close off list HTML
			$this->_pagination .= '</ul>' . "\n\t";
			
		}
		
		
		if($this->_cleanUrls === false){
			$this->_pagination = str_replace('?', '&amp;', $this->_pagination);
			$this->_pagination = preg_replace('/page([0-9]+)\//', '?page=\1', $this->_pagination);
			$this->_pagination = str_replace(array('/&amp;', '.php&amp;'), array('/?', '.php?'), $this->_pagination);
		}
		
		
		
	}
	
	/**
	 *	getPagination()
	 */
	public function getPagination(){
		return $this->_pagination;
	}
	
}


?>