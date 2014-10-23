<?php
/**
 *	 ===================================================================================
 *	 
 *	 Common Functions
 *	 -----------------------------------------------------------------------------------
 *	 
 *	 These generic functions are used often throughout this website
 *	 
 *	 ===================================================================================
 *		
 *		
 *	 Contents
 *	 -----------------------------------------------------------------------------------
 *	
 *	 read()
 *	 redirect()
 *	 drawRSSFeed()
 *	 cleanString()
 *	 getQueryLimits()
 *	 drawFeedback()
 *	 createFeedbackURL()
 *	 niceError()
 *	 limitWords()
 *	 highlightSearchTerms()
 *		replaceSelectedTags()
 *	 createCache()
 *		clearCache()
 *	 validateContent()
 *	 cleanUpWord()
 *	 trackUserFeedback()
 *	 drawDropDown()
 *	 getDropDownOptions()
 *	 drawRadioOptions()
 *	 drawCheckBoxes
 *	 formVariables()
 *	 cleanFields()
 *	 assignOrderClass()
 *	 isURLSelected()
 *	 isChecked()
 *	 isSelected()
 *	 id2Title()
 *	 autoload()
 *	 currency()
 *	xhandler()
 *	sort_array()
 *	clean_xss()
 *	reduceFilesize()
 *	_file_get_contents() - cURL replacement for Dreamhost
 *	print_x() - print_r with <pre> tags
 *	calculateVAT
 *	calculateVATFlatRate
 *	array_unshift_associative	
 *	===================================================================================		
 */

	/** 
	 *	Read():
	 *	check for variable: if it doesn't exist return it's user set default value
	 *	@param $object (string)
	 *	@param $variable (string)
	 *	@param $default (string)
	 *	@return $object[$variable] (array)
	 */
	function read($object,$variable,$default) {
		if(!isset($object[$variable])){
			return $default;
		}
		return $object[$variable];
	}
	
	/** 
	 *	Redirect()
	 *	Send a user to a new place
	 *	@param $link (string)
	 */
	function redirect($link) {
		//header ('HTTP/1.1 301 Moved Permanently'); // 301 redirect: SEO friendly
		header("Location: $link");
		exit();
	}

	/** 
	 *	drawRSSFeed()
	 *	Convert RSS feed into unordered list
	 */	
	function drawRSSFeed($link, $show_date = false, $total = 5) {
		$html = "\n";
			$html .= '<ul class="feed">'."\n\t";
			$feedXml = simplexml_load_file($link); 
			$i = 1;
			foreach ($feedXml->channel->item as $article){
				if($i <= $total){
					$html .= '<li>';
					if($show_date === true){
						$html .= '<em class="date">'.getDDMMYYYY($article->pubDate).'</em> ';
					}
				   $html .= '<a href="' . (string)$article->link . '">' . stripslashes((string)$article->title) . '</a>';
				   $html .= '</li>' . "\n";
				   $i++; 
				}
			} 
			$html .= "</ul>\n\t";
		return $html;
	}	
	
	/**
	 *	cleanString()
	 *	CREATE URL-friendly strings
	 */
	function cleanString($string){
	
		// Strip everything but letters, numbers and spaces from the title
		$string = preg_replace("/[^A-Za-z0-9 ]/", "", trim($string));
		// Replace spaces and underscores with dashes
		$string = str_replace(array(" ", "_"), '-', $string);
		// Make lowercase
		$string = strtolower($string);	
			
		return $string;
	}
	

	/**
	 *	getQueryLimits()
	 *	returns the limits for the query based on the current page
	 *	and the total to be shown per page
	 */
	function getQueryLimits($per_page, $current_page){
		
		if(is_array($current_page)){
			$current_page = 1;
		}
	
	
		if($current_page == 0 || $current_page == 1 || !$current_page){ // current page is 1 or 0 or is missing
			$limit = '0,' . $per_page;
		} else{
			$limit = ($current_page - 1) * $per_page . ',' . $per_page; // work out limits
		}

		return $limit;
	}
	
	/**
	 *	getShowingXofX()
	 *	returns the limits for the query based on the current 
	 *	page and the total to be shown per page
	 */
	function getShowingXofX($per_page, $current_page, $page_total, $grand_total){
		
		if($current_page == 0 || $current_page == 1 || !$current_page){ // current page is 1 or 0 or is missing
			$per_page = ($page_total < $per_page) ? $page_total : $per_page;
			$limit =  'Showing 1 to ' . $per_page . ' of ' . $grand_total; // work out limits
		} else{
			$limit = 'Showing ' . ($current_page-1) * $per_page . ' to ' . ((($current_page-1) * $per_page) +$page_total) . ' of ' . $grand_total; // work out limits
		}
		
		if($grand_total == 0){
			$limit = 'Showing 0 ';
		}
		
		
		return $limit;
	}
	
	
	/**
	 *	drawFeedback()
	 *	create (human understandable) user feedback
	 *	Needs array with 2 values $user_feedback['type'] and $user_feedback['message'] the later of which
	 *	can be an array and should be if it's an error.
	 */
	function drawFeedback($user_feedback){
	
		if($user_feedback['content']) {
			if($user_feedback['type'] == 'error'){
				$title = 'Warning';
				$type_class = ' ' . strtolower($user_feedback['type']);
			} else if ($user_feedback['type'] == 'success'){
				$title = 'Success';
				$type_class = ' ' . strtolower($user_feedback['type']);
			} else{
				$title = 'Feedback';
				$type_class = '';
			}
			$html ='<div class="feedback' . $type_class . '">' . "\n";
			$html .= '<h3>' . $title . '</h3>' . "\n";
			
			if(is_array($user_feedback['content']) && sizeof($user_feedback['content']) > 1){ // more than one message in feedback so loop
				$html .= '<ul>' . "\n";
				foreach($user_feedback['content'] as $item){
					$html .= '<li>' . stripslashes($item) . '</li>' . "\n";
				}
				$html .= '</ul>' . "\n";
			} else if(is_array($user_feedback['content']) && sizeof($user_feedback['content']) == 1){ // an array with only only one item so show it
				$html .= '<p>' . stripslashes($user_feedback['content'][0]) . '</p>' . "\n";
			} else{ // only one item so show it
				$html .= '<p>' . stripslashes($user_feedback['content']) . '</p>' . "\n";
			}
			$html .= '</div>' . "\n";
		} else{
			$html  = '';
		}
		
		return $html;
		
	}
	
	/**
	 *	createFeedbackURL()
	 *	convert user feedback into URL friendly variables
	 */	
	function createFeedbackURL($feedback_type,$message){
		if(is_array($message)){
			$message_url = '';
			foreach($message as $msg){
				$message_url .= '&content[]=' . urlencode($msg);
			}
		} else{
			$message_url = '&content=' . urlencode($message);
		}	
			$new_url = 'type=' . $feedback_type.$message_url;
		return $new_url;
	}
	
	/**
	 *	niceError()
	 *	draws a nice looking so users aren't distressed
	 */	
	function niceError($content, $show = false){
	
		if($show === false){
			$show = (isset($_GET['DEBUG'])) ? true : false;
		}
		
		$html ='<div class="feedback error">' . "\n";
		$html .= '<h3>Technical error</h3>' . "\n";
		$html .= '<p>An error has occurred on this website. Please ignore this message as our technical team has been made aware of this error.</p>' . "\n";
		if(is_array($content) || is_object($content)){ 
			// more than one message in feedback so loop
			$html .= '<p><pre>' . "\n";
			foreach($content as $item => $value){
				$html .= "{$item}: \n";
			}
			$html .= '</pre></p>' . "\n";
		} else{ // only one item so show it
			$html .= '<p>' . $content . '</p>' . "\n";
		}
		$html .= '</div>' . "\n";
		// spew out the error *provided this isn't the live server*
		if(MODE != 'live' && $show === true){
			echo $html;
		}
	}
	
	/**
	 *	limitWords()
	 *	truncates content to show a set number of words
	 */	
	function limitWords($content, $cutoff){
		///strip tags...
		// and prevent line breaks, images, etc from being counted...
		$content = strip_tags(nl2br(html_entity_decode($content))); 
		
		$wordcount = str_word_count($content);
		$wordindex = str_word_count($content, 1, '.,-\'"\\/?&!£$%^*()_-+=#~{[]}:;|1234567890');
		$wordlimit = ($wordcount < $cutoff) ? $wordcount : ($cutoff - 1);
		
		if($wordcount > $wordlimit){
			$wordindex = array_slice($wordindex, 0, $wordlimit);
			$content = implode(' ', $wordindex) . '&hellip;';
		}
		return $content;	
	}

		
	/**
	 *	validateContent()
	 *	clean up content: if no </p> is present: 
	 *	we assume there's no HTML, add <p>'s and 
	 *	convert line breaks to <br />s
	 */	
	function validateContent($html){
		$valid_html = (stripos($html, '</p>') === false && stripos($html, '</table>') === false && stripos($html, '</li>') === false) ? '<p>' . nl2br($html) . '</p>' : $html;
		$valid_html = str_replace(array('<p>&nbsp;</p>', '<p></p>'), '', $valid_html);
		
		return cleanUpWord($valid_html) . "\n";
	}
	
	/**
	 *	cleanUpWord
	 *	Fuck you Microsoft Word you add loads of extra characters that we just can't deal with
	 */	
	function cleanUpWord($html){
		// bad values 
		$bad = array("“", "”", "’");
		// good replacement values
		$good = array('"', '"', "'");
		// replace bad with good
		$html = str_replace($bad, $good, $html);
		
		return $html;
	}
	
	/**
	 *	trackUserFeedback()
	 *	create google analytics (GA) tracking code to 
	 *	track system generated user feedback in GA
	 */	
	function trackUserFeedback($user_feedback){
	
		if(isset($user_feedback)){
	
			if($user_feedback['type'] && !empty($user_feedback['content']) && is_array($user_feedback['content'])){ 
				// it's and array so loop through and include all messages
				$tracking_code = '';
				foreach($user_feedback['content'] as $feedback_item){
					$tracking_code .= 'urchinTracker("user_feedback/' . $user_feedback['type'] . '/' . $feedback_item . '");' . "\n";
				}
			} else if($user_feedback['type'] && !empty($user_feedback['content'])){ // just print out the one message
				$tracking_code = 'urchinTracker("user_feedback/' . $user_feedback['type'] . '/' . $user_feedback['content'] . '");'."\n";
			}
		} else{
			$tracking_code = false;
		}
		
		return (isset($tracking_code)) ? $tracking_code : '';
	}
	
	/**
	 *	drawDropDown()
	 *	take an array of values and a current value and drap the <option>s 
	 *	to create a <select> menu
	 *	
	 */	
	function drawDropDown($options, $current = ''){
		
		$html = '';
		
		// make options alphabetical
		//sort($options);
		
		// loop through options
		foreach($options as $value => $text){
			$pre_selected = '';
			// is the option selected?
			if(is_array($current)){
				$pre_selected = (in_array($value, $current)) ? ' selected="selected"' : '';
			} else{
				$pre_selected = ($current == $value) ? ' selected="selected"' : '';
			}
			// add <option> to result
			$html .= '<option value="' . $value . '"' . $pre_selected . '>' . $text . '</option>' . "\n";
		}
		
		return $html;
	}
	
	/**
	 *	getDropDownOptions()
	 *	get an array from a table of items - must have an id and title
	 *	this array is used in the drawDropDown() function
	 *	
	 *	NOTE: future iterations, should get this data from a 
	 *	cached source - leave the database alone
	 *	
	 */	
	function getDropDownOptions($table, $default = false, $value_field = 'id'){
		
		$options = array();
		if($default){
			$options[''] = $default; 
		}
		
		if($table){
		
			$options_array = getFieldsFromTable($table, 'title');
			
			
			// has query worked/got results
			if($options_array){

				// loop through results
				foreach($options_array as $option){
					// create array from results
					// only show value if either the row doesn't have a status or the status is 1 (active) AND the row
					// is user selectable (Y for yes) or doesn't have that value at all
					if((!isset($option->status) || $option->status  == 1) && (!isset($option->user_selectable) || $option->user_selectable  == 'Y')){
						$options[$option->{$value_field}] = stripslashes($option->title);
					}
				}
			}
		}
		
		return $options;
	}
	
	/**
	 *	drawRadioOptions()
	 *	take an array of values and a current value and draw radio buttons
	 *	
	 */	
	function drawRadioOptions($field, $options, $current){
		
		$html = '';
		
		// make options alphabetical
		//sort($options);
		
		// loop through options
		foreach($options as $value => $text){
			$pre_selected = '';
			// is the option selected?
			$pre_selected = ($current == $value) ? ' checked="checked"' : '';
			$id = $field . '-' . $value;
			// add <option> to result
			$html .= '<div class="field">
			<input type="radio" class="checkbox" name="' . $field . '" id="' . $id . '" value="' . $value . '"' . $pre_selected . ' /><label for="' . $id . '" class="checklabel">' . $text . '</label></div>' . "\n";
		}
		
		return $html;
	}
	
	/**
	 *	drawCheckboxes()
	 *	take an array of values and a current value and draw
	 *	out a lot of checkboxes
	 */	
	function drawCheckboxes($field, $options, $current = ''){
		
		$html = '';
		
		// make options alphabetical
		//sort($options);
		
		// loop through options
		foreach($options as $value => $text){
			$pre_selected = '';
			// is the option selected?
			if(is_array($current)){
				$pre_selected = (in_array($value, $current)) ? ' selected="selected"' : '';
			} else{
				$pre_selected = ($current == $value) ? ' selected="selected"' : '';
			}
			
			$id = $field . '-' . $value;
			
			$text = (is_array($text)) ? $text['title'] : $text;
			
			$html .= '<div class="field">
			<input type="checkbox" class="checkbox" name="' . $field . '[]" id="' . $id . '" value="' . $value . '"' . $pre_selected . ' /><label for="' . $id . '" class="checklabel">' . $text . '</label></div>' . "\n";
		}
		
		return $html;
	}
	
	
	/**
	 *	getFieldsFromTable()
	 *	Grab and array of data from a given table
	 */
	function getFieldsFromTable($table = false, $order_by = 'title', $status = 'live'){
	
		global $db;
	
		$options_array = array();
		
		if($table){
			
			$status_filter = ($status == 'live') ? " AND `status` = 1 " : "";
			
			// Query
			$query = "SELECT * FROM `{$table}` WHERE 1 {$status_filter} ORDER BY `{$order_by}` ASC LIMIT 0, 500;";
			
			niceError($query);
			
			// Cache data
			$objCache = new Cache('dropdown.cache', 1, $table);
			if($objCache->getCacheExists() === true){
				$options_array = $objCache->getCache();
			} else{
				$options_array = $db->get_results($query);
				$objCache->createCache($options_array);
			}
			// End cache
		}
		
		return $options_array;
	}
	
	/**
	 *	formVariables()
	 *	take array of fields and create user friendly names for them 
	 *	e.g. $email to be used as such
	 *	<input value="<?php echo $email; ?>" /> without creating PHP errors
	 *	@param array $name
	 *	@return array $form_variables
	 */
	function formVariables($names = array()){
		global $action, $properties; // action: edit, delete or add - usually
		
		$form_variables = array();
		
		// loop through array
		foreach($names as $name){
			// Put value into named array
			// If we're edit mode, get the object data unless a 
			// POST variable exists. 
			// If we're not in edit mode, just show empty 
			// values unless a POST variable exists.
			$form_variables[$name] = ($action == 'edit' || $action == 'duplicate' || substr($action, 0, strlen('edit')) == 'edit') ? stripslashes(read($_POST, $name, read($properties, $name, ''))) : stripslashes(read($_POST, $name, ''));
		}
		
		// return values which must be extracted later 
		return $form_variables;
		
	}
	
	/**
	 *	cleanFields()
	 *	prepare data for database by
	 *	run all fields through EZ-SQL's escape* function
	 *	* this could be replaced by any other function
	 */	
	function cleanFields($fields){
		global $db;
		
		$bad_words = array('%C2%A3', '?£', 'Â£', '£', '€', 'Ã', "‘", "‘", '“', '”', '™', '…', '–', ' & ');
		$replacements = array('&pound;', '&pound;', '&pound;', '&pound;', '&euro;', '', "&quot;", "&quot;", "&quot;", "&quot;",'&trade;', '&hellip;', 'ndash;', ' &amp; ');
		
	
		if(is_array($fields)){
			$field_array = array();
			
			foreach($fields as $field){
				$field_array[$field] = $db->escape(str_replace($bad_words, $replacements, read($_POST, $field, '')));
				//echo $field_array[$field] . '<hr />' . "\n\n";
			}
			return $field_array;
		}
	}
	
	/**
	 *	assignOrderClass
	 *	for CSS styling, all items in a list* must have a class e.g.
	 *	first, last, even, oddd, etc
	 *	Can be used to assign padding, borders, commas to certain items
	 *	
	 *	NOTE: a list doesn't have to be a <li> it can be a group of <div>s or anything
	 */	
	function assignOrderClass($position, $total){
		
		$class = '';
		
		// Assign 'first' and 'last' classes
		// but only if the list if greater than 1
		if($total > 1){
			if($position == 0){ // should this be 0 or 1?
				$class = 'first ';
			} else if($position == $total){
				$class = 'last ';
			}
		}
		
		// Assign odd or even
		$class .= ($position%2 == 0) ? 'odd' : 'even';
		
		return $class;
	
	}
	

	/**
	 * 	isURLSelected()
	 * 	add a class to a link/object if its link is 
	 * 	currently selected	
	 *
	 *	@param 	string 	$url	the element's href value
	 *	@param 	boolean $nude 	show class attribute or not
	 *	@return	string	$class
	 */
	function isURLSelected($url, $nude = false){
	
		$class = '';
	
		if($url){
			// does supplied URL sit inside the actual URL we're on?
			if(strpos($_SERVER['REQUEST_URI'], $url) !== false){
				// create class value: if 'nude' is true, just show the 
				// class text not the 'class="etc"' text too
				$class = ($nude === true) ? 'selected' : ' class="selected"';
			}
		
		}
		
		return $class;
		
		
	}

	
	/**
	 *	isChecked()
	 *	if the value of the form checkbox/radio button field 
	 *	matches the value of the given variable then mark 
	 *	it as checked
	 */
	function isChecked($field_value, $actual_value){
	
		//echo $field_value;
		$checked = ($field_value == $actual_value) ? ' checked="checked"' : '';
		
		return $checked;
	}
	
	/**
	 *	isSelected()
	 *	if the value of the form <option> field matches the 
	 *	value of the given variable then mark it as selected
	 */
	function isSelected($field_value, $actual_value){
	
		//echo $field_value;
		$selected = ($field_value == $actual_value) ? ' selected="selected"' : '';
		
		return $selected;
	}
	
	/**
	 *	id2Title()
	 *	take an id and via the database table convert into 
	 *	a human readbale name
	 */	
	function id2Title($id, $table){
		global $db;
		
		if($id && $table){
			$query = "SELECT title FROM $table WHERE id = '$id' LIMIT 1;";
			// niceError($query); Debugging - echo SQL
			return $db->get_var($query);
		} else{
			return false;
		}
	}
	
	/**
	 *	autoload()
	 */
	function __autoload($class_name){
	
		$class_file = APPLICATION_PATH . "/class/" . strtolower($class_name) . ".class.php";
		

		$swift_class_file = LIBRARY_PATH . '/swiftmailer/classes/' . str_replace('_', '/', $class_name) . '.php';
		
		
		
		// class exists
		if(file_exists($class_file)){
			require_once($class_file);
		} else if(file_exists($swift_class_file)){
			require_once($swift_class_file);
		} else{
			// class doesn't exist
			exit("Class {$class_name} not found!");
		}
	}
	
	/**
	 *	currency
	 *	@param float $value
	 *	@param string $format
	 *	@return string
	 */	
	function currency($value, $format = 'English'){
	
		global $hide_figures;
		
	
		switch($format){
		
			// format as british 1,000.00
			default:
			case 'English':
			case 'UK':
			case 'GBP':
				$currency = CURRENCY . number_format($value, 2, '.', ',');
				
				if($hide_figures === true){
					$currency = CURRENCY . preg_replace('/([0-9])/', 'x', number_format($value, 2, '.', ','));
				}
				
				break;
			case 'Euro':
			case 'EUR':
				$currency = number_format($value, 2, ',', '.') . CURRENCY;
				break;
			
		}
		
		// now remove the minus sign in front of it
		$currency = str_replace('-', '', $currency);

		
		
		
		return $currency;
	}
	
	/** 
	 *	errors 
	 */
	if(!function_exists('xhandler')){
		function xhandler($number, $string, $file, $line, $context){
			global $objError;
			
			if(strpos($string, 'Table') !== false && strpos($string, "doesn't exist") !== false && strpos($_SERVER['REQUEST_URI'],  '/install') !== 0){
				// No database errors = send to install scren
				header('Location: /install/');
				exit;
			} else if(strpos($string, 'Table') !== false && strpos($string, "doesn't exist") !== false && strpos($_SERVER['REQUEST_URI'],  '/install') === 0){
				// no database and on the install screen = do nothing
			} else{
				$objError->logError($number, $string, $file, $line, $context);
			}
		}
	}
	
	/**
	 *	sort_array
	 *	http://nefariousdesigns.co.uk/archive/2005/09/sorted/
	 */
	function sort_array($arr_data, $str_column, $bln_desc = false){
	  $arr_data = (array) $arr_data;
	  $str_column = (string) trim($str_column);
	  $bln_desc = (bool) $bln_desc;
	  $str_sort_type = ($bln_desc) ? SORT_DESC : SORT_ASC;
	
	  foreach ($arr_data as $key => $row){
		${$str_column}[$key] = $row[$str_column];
	  }
	
	  array_multisort($$str_column, $str_sort_type, $arr_data);
	
	  return $arr_data;
	}
	
	/**
	 *	clean_xss
	 *	remove XSS vulnerabilities
	 *	needs work
	 */
	function clean_xss($value){
			
		$xss = array('alert(','<script>','/script','/SCRIPT','String.fromCharCode','HTTP-EQUIV','http-equiv','CONTENT-TYPE','/HEAD','http://','HREF','-->','<!--','<>','SCRIPT','88,83,83','()',';','//','")',"')",'SRC=','.js','FRAMESET','INPUT TYPE','DYNSRC','LOWSRC','exp/**','javascript:','url=','mocha:[','classid','AllowScriptAccess','expression(','background-image','@import','-moz','namespace=','java\0script','SCR\0IPT','onload','xss:expr/*XSS*/ession()','ilayer','bgsound');
		return htmlentities(str_replace($xss,'',urldecode($value)));
	}
	
	/**
	 *	reduceFilesize
	 *	take CSS/JavaScript (or even HTML) and remove all
	 *	spaces, tabs and linebreaks to reuce filesize
	 */
	function reduceFilesize($string, $type = 'css'){
		$string = str_replace(array("\n", "\t"), '', $string);
		$string = str_replace(array(": ", ", "), array(':',','), $string);
		
		return $string;
	}
	
	/**
	 * _file_get_contents
	 *
	 *
	 * @param: file - url to be grabbed
	 * @param: $timeout -  set to zero for no timeout
	 */
	function _file_get_contents($file, $timeout=5){
		global $objApplication;
		$ch = curl_init();
		$timeout = 5;
		
		// remove document root from URL and add in proper web address
		$file = (substr($file, 0, 4) != 'http') ? 'http://' . $objApplication->getSiteUrl() . str_replace(SITE_PATH, '', $file) : $file;
		curl_setopt ($ch, CURLOPT_URL, $file);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file = curl_exec($ch);
		curl_close($ch);
		
		return $file;
	}
	
	/**
	 * print_x
	 *
	 * @param: $array - array to be printed out
	 */
	 function print_x($array){
	 	echo "<pre>";
	 	print_r($array);
	 	echo "</pre>";
	 }
	 
	 /**
	  *	form_success
	  * has a form been successfully sent?
	  * @user_feedback - array
	  */
	 function form_success($user_feedback = ''){
	 	$form_success = (!isset($user_feedback) || $user_feedback['type'] != 'success') ? false : true;
	 	return $form_success;
	 }
	 
	 
	 /**
	  *	calculateVAT
	  *	Work out how much VAT was paid from grand total
	  *	@param	float	grand total e.g 100
	  *	@param	float	VAT rate e.g. 17.5 or 20
	  *	@return	float	
	  */
	 function calculateVAT($total_paid, $vat_rate){
	 	$minus_vat = (( 100 / ( 100 + $vat_rate )) * $total_paid);
		$vat_paid = ($total_paid - $minus_vat);
		return $vat_paid;
	 }
	 
	 
	 /**
	  *	calculateVATFlatRate
	  *	Work out how much VAT was paid from grand total
	  *	@param	float	grand total e.g 100
	  *	@param	float	VAT rate e.g. 17.5 or 20
	  *	@param	float	VAT rate e.g. 14.5 or 9
	  *	@return	float	
	  */
	 function calculateVATFlatRate($total_paid, $vat_rate, $vat_flat_rate){
		$total_minus_flat_rate_vat = $total_paid-($total_paid*($vat_flat_rate/100));
		$vat_paid = ($total_paid - $total_minus_flat_rate_vat);
		return $vat_paid;
	 }
	 
	/**
	 *	array_unshift_associative
	 *	like array_unshift but works with an associative array
	 *	@param	array
	 *	@param	string
	 *	@param	mixed
	 *	@return array
	 */
	function array_unshift_associative(&$array, $key, $value){
	    $array = array_reverse($array, true);
	    $array[$key] = $value;
	    $array = array_reverse($array, true);
	    return $array;
    } 



?>