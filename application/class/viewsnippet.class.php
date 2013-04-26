<?php
/**
 * ==========================================================================
 * 
 *  ViewSnippet Class
 *  -------------------------------------------------------------------------
 *
 *  automatically create HTML based on object data
 * 
 *  =========================================================================
 *	
 *	LICENSE:
 *	-------------------------------------------------------------------------
 *	
 *	@copyright 	2008-2009 (c)	Phil Thompson	http://philthompson.co.uk
 *	@license
 *	@version	1.0	
 *	@author		philthompson.co.uk
 *	@since		2008
 *	
 *	edited by:  Phil Thompson
 *	@modified	14/08/2010
 *	
 *	=========================================================================
 *	
 *	Table of Contents
 *	-------------------------------------------------------------------------
 *
 *
 *	
 *	methods
 *		autoViewById
 *		autoViewAll
 *		autoViewAllTable
 *
 */


	class ViewSnippet{
	
		// Variables
		
		/**
		 *	@var object
		 */
		protected $_dateFormat;
		
		/**
		 *	Constructor
		 */
		public function __construct(){
		
			// Local date formatting object - for easy pretty dates
			$this->_dateFormat = new DateFormat();
			
		}
		
		// Methods
		
		/**
		 *	autoViewById
		 *	@param object $objScaffold
		 *	@return string $html
		 */
		public function autoViewById($objScaffold){
		
			$html = '';
			
			$properties = $objScaffold->getProperties();
			// show item details by looping through all properties
			foreach($properties as $property => $value){
				
				// value is an  rray
				if(is_array($value)){
					$html .= validateContent('<strong>' . ucfirst(str_replace('_', ' ', $property)) . ':</strong> ');
					$html .= '<ul>';
					// loop through and show all values
					foreach($value as $item_value){
						$html .= '<li>' . stripslashes(ucfirst(read($item_value, 'title', ''))) . '</li>';
					}
					$html .= '</ul>';
				} else{
					// value is a string
					$fields_details = $objScaffold->getFieldsDetails();
					// it this is a date field, format it nicely
					if(!empty($fields_details[$property]['Type']) && $fields_details[$property]['Type'] == 'datetime'){
						$value = DateFormat::getDate('datetime', $value);
					}
					$html .= validateContent('<strong>' . ucfirst(str_replace('_', ' ', $property)) . ':</strong> '.stripslashes($value));
				}
			}
			
			return $html;
		}
		
		/**
		 *	autoViewAll
		 *	@param object $objScaffold
		 *	@return string $html
		 */
		public function autoViewAll($objScaffold){
			
			$i = 1; // counter
			
			$properties = $objScaffold->getProperties();
			
			$properties_size = sizeof($properties);
			
			// Tabular/Listings
			$html = '<p class="showing">' . getShowingXofX($objScaffold->getPerPage(), $objScaffold->getCurrentPage(), $properties_size, $objScaffold->getTotal()) . ' ' . $objScaffold->getNamePlural() . '</p>';
			
			$html .= '<ul id="' . strtolower($objScaffold->getNamePlural()) . '_list">' . "\n";
			
			// Loop through all properites show we can show basic details and links for each one
			foreach($properties as $property){
				$html .= '<li class="' . assignOrderClass($i, $properties_size) . '"><a href="' . $objScaffold->getFolder() . $property['id'] . '/">' . stripslashes($property['title']) . '</a></li>' . "\n";
				$i++; // increment counter
			}
			
			$html .= '</ul>' . "\n";
			
			
			return $html;
		}
		
		/**
		 *	autoViewAllTable
		 *	@param object $objScaffold
		 *	@param array $headings
		 *	@return string $html
		 */
		public function autoViewAllTable($objScaffold, $headings = array()){
			
			$i = 1; // counter
			
			$popup = '?mode=popup';
			
			$properties = $objScaffold->getProperties();
			
			$properties_size = sizeof($properties);
			
			// Tabular listings
			$html = '<p class="showing">' . getShowingXofX($objScaffold->getPerPage(), $objScaffold->getCurrentPage(), $properties_size, $objScaffold->getTotal()) . ' ' . $objScaffold->getNamePlural() . '</p>';
			
			// Make the title <th> sortable by a link
			// work out whether to show the Z-A link or the A-Z link
			$title_sort = (!empty($_GET['sort']) && $_GET['sort'] == 'title_za') ? 'title_az' :  'title_za';
			
			$html .= '<table id="' . strtolower($objScaffold->getNamePlural()) . '_list">' . "\n";
			$html .= '<thead>' . "\n";
			$html .= '<tr>' . "\n";
			$html .= '<th scope="col"><a href="' . $objScaffold->getFolder() . '?sort=' . $title_sort . '">Title</a></th>' . "\n";
			
			if(!empty($headings)){
				foreach($headings as $heading){
					$html .= '<th scope="col">' . $heading . '</th>';
				}
			}

			$html .= '</tr>' . "\n";
			$html .= '</thead>' . "\n";
			$html .= '<tbody>' . "\n";
			
			
			
			// Loop through all properites show we can show basic details and links for each one
			foreach($properties as $property){
			
				$title = (!empty($property['title'])) ? stripslashes($property['title']) : $property['id'];
			
				$html .= '<tr class="' . assignOrderClass($i, sizeof($properties)) . '">';
				$html .= '<td>
					<a href="' . $objScaffold->getFolder() . $property['id'] . '/">'. $title . '</a>
					<div class="group extra-options">
						<ul>
							<li><a href="' . $objScaffold->getFolder() . $property['id'] . '">View</a></li>
							<li><a href="' . $objScaffold->getFolder() . 'edit/' . $property['id'] . '/">Duplicate</a></li>
							<li><a href="' . $objScaffold->getFolder() . 'edit/' . $property['id'] . '/">Edit</a></li>
							<li><a href="' . $objScaffold->getFolder() . 'delete/' . $property['id'] . '/">Delete</a></li>
						</ul>
					</div>
					
				</td>';
				
				if(!empty($headings)){
					foreach($headings as $heading){
						$html .= '<td>' . read($property, $heading, '&nbsp;') . '</td>';
					}
				}

				$html .= '</tr>' . "\n";
				$i++; // increment counter
			}
			
			$html .= '</tbody>' . "\n";
			$html .= '</table>' . "\n";
			
			
			return $html;
		}
		
	
	}

?>