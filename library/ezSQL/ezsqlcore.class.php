<?php

/**
 *  @package	ezsql
 */

//  ezSQL Constants



/**
 *	@public constant
 */
define('EZSQL_VERSION', '2.03');

/**
 *	@public constant
 */
define('OBJECT', 'OBJECT', true);

/**
 *	@public constant
 */
define('ARRAY_A', 'ARRAY_A', true);

/**
 *	@public constant
 */
define('ARRAY_N', 'ARRAY_N', true);

/**
 *	@public constant
 */
define('EZSQL_CORE_ERROR', 'ezSQLcore can not be used by itself (it is designed for use by database specific modules).');


/**
 *	ezSQL
 *	ezSQL Core module - database abstraction library to make
 *	it very easy to deal with databases.
 *
 *	Core class containg common public functions to manipulate query result
 *  sets once returned
 *
 *	@package	ezsql
 * 	@author		Justin Vincent (justin@visunet.ie) http://php.justinvincent.com
 *	@version	2.03
 */


class ezSQLCore{

	/**
	 *	@var	boolean
	 *	same as $debug_all
	 */
	public $trace            = false;
	
	/**
	 *	@var	boolean
	 *	same as $trace
	 */
	public $debug_all        = false; 
	
	/**
	 *	@var	boolean
	 */
	public $debug_called     = false;
	
	/**
	 *	@var	boolean
	 */
	public $vardump_called   = false;
	
	/**
	 *	@var	boolean
	 */
	public $show_errors      = true;
	
	/**
	 *	@var	int
	 */
	public $num_queries      = 0;
	
	/**
	 *	@var	string
	 */
	public $last_query       = null;
	
	/**
	 *	@var	string
	 */
	public $last_error       = null;
	
	/**
	 *	@var	string
	 */
	public $col_info         = null;
	
	/**
	 *	@var	array
	 */
	public $captured_errors  = array();
	
	/**
	 *	@var	boolean
	 */
	public $cache_dir        = false;
	
	/**
	 *	@var	boolean
	 */
	public $cache_queries    = false;
	
	/**
	 *	@var	
	 */
	public $cache_inserts    = false;
	
	/**
	 *	@var	boolean
	 */
	public $use_disk_cache   = false;
	
	/**
	 *	@var	int
	 *	hours
	 */
	public $cache_timeout    = 24;

	// == TJH == default now needed for echo of debug public function
	public $debug_echo_is_on = true;
	
	
	/**
	 * Whether to use mysql_real_escape_string
	 *
	 * @since 2.8.0
	 * @access public
	 * @var bool
	 */
	var $real_escape = true;

	/**
	 *  Constructor
	 */
	public function __construct(){
		
	}

	/**
	 *	conenct
	 *  Connect to DB - over-ridden by specific DB class
	 */
	public function connect(){
		die(EZSQL_CORE_ERROR);
	}

	/**
	 *	select
	 *  Select DB - over-ridden by specific DB class
	 */
	public function select(){
		die(EZSQL_CORE_ERROR);
	}

	/**
	 *	query
	 *  Basic Query	- over-ridden by specific DB class
	 */
	public function query($query){
		die(EZSQL_CORE_ERROR);
	}

	/**
	 *	escape
	 *  Format a string correctly for safe insert - over-ridden by specific
	 *  DB class
	 */
	public function escape($data){
		die(EZSQL_CORE_ERROR);
	}

	/**
	 *	sysdate
	 *  Return database specific system date syntax
	 *  i.e. Oracle: SYSDATE Mysql: NOW()
	 */
	public function sysdate(){
		die(EZSQL_CORE_ERROR);
	}

	/**
	 *	register_error
	 *  Print SQL/DB error - over-ridden by specific DB class
	 *	@param	string	$err_str
	 */
	public function register_error($err_str){
		// Keep track of last error
		$this->last_error = $err_str;

		// Capture all errors to an error array no matter what happens
		$this->captured_errors[] = array
		(
			'error_str' => $err_str,
			'query'     => $this->last_query
		);
	}

	/**
	 *	show_errors
	 *  Turn error handling on or off..
	 */
	public function show_errors(){
		$this->show_errors = true;
	}

	/**
	 *	hide_errors
	 *	Turn error handling  off
	 */
	public function hide_errors(){
		$this->show_errors = false;
	}

	/**
	 *	flush
	 *  Kill cached query results
	 */
	public function flush(){
		// Get rid of these
		$this->last_result = null;
		$this->col_info = null;
		$this->last_query = null;
		$this->from_disk_cache = false;
	}

	/**
	 *	get_var
	 *  Get one variable from the DB - see docs for more detail
	 *	@param string 	$query
	 *	@param int 		$x
	 *	@param int 		$y
	 */
	public function get_var($query = null,$x = 0,$y = 0){

		// Log how the public function was called
		$this->func_call = "\$db->get_var(\"$query\",$x,$y)";

		// If there is a query then perform it if not then use cached results..
		if ( $query ){
			$this->query($query);
		}

		// Extract public out of cached results based x,y vals
		if ( $this->last_result[$y] ){
			$values = array_values(get_object_vars($this->last_result[$y]));
		}

		// If there is a value return it else return null
		return (isset($values[$x]) && $values[$x]!=='')?$values[$x]:null;
	}

	/**
	 *	get_row
	 *  Get one row from the DB - see docs for more detail
	 *	@param string 	$query
	 *	@param object 	$output 
	 *	@param int 		$y
	 */
	public function get_row($query = null,$output = OBJECT,$y = 0){

		// Log how the public function was called
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";

		// If there is a query then perform it if not then use cached results..
		if ( $query ){
			$this->query($query);
		}

		// If the output is an object then return object using the row offset..
		if ( $output == OBJECT ){
			return $this->last_result[$y]?$this->last_result[$y]:null;
		}
		// If the output is an associative array then return row as such..
		elseif ( $output == ARRAY_A ){
			return $this->last_result[$y]?get_object_vars($this->last_result[$y]):null;
		}
		// If the output is an numerical array then return row as such..
		elseif ( $output == ARRAY_N ){
			return $this->last_result[$y]?array_values(get_object_vars($this->last_result[$y])):null;
		}
		// If invalid output type was specified..
		else{
			$this->print_error(" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N");
		}

	}

	/**
	 *	get_col
	 *  Function to get 1 column from the cached result set based in X index
	 *  see docs for usage and info
	 *	@param string 	$query
	 *	@param int 		$x
	 */
	public function get_col($query = null, $x = 0){

		// If there is a query then perform it if not then use cached results..
		if ( $query ){
			$this->query($query);
		}

		// Extract the column values
		for ( $i=0; $i < count($this->last_result); $i++ ){
			$new_array[$i] = $this->get_var(null,$x,$i);
		}

		return $new_array;
	}


	/**
	 *	get_results
	 *  Return the the query as a result set - see docs for more details
	 *	@param string $query
	 *	@param string $output
	 */
	public function get_results($query = null, $output = OBJECT){

		// Log how the public function was called
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		// If there is a query then perform it if not then use cached results..
		if ($query){
			$this->query($query);
		}

		// Send back array of objects. Each row is an object
		if ( $output == OBJECT ){
			return $this->last_result;
		}
		elseif ( $output == ARRAY_A || $output == ARRAY_N ){
			if ( $this->last_result ){
				$i = 0;
				foreach( $this->last_result as $row ){

					$new_array[$i] = get_object_vars($row);

					if ( $output == ARRAY_N ) {
						$new_array[$i] = array_values($new_array[$i]);
					}

					$i++;
				}

				return $new_array;
			} else {
				return null;
			}
		}
	}


	/**
	 *	get_col_info
	 *  Function to get column meta data info pertaining to the last query
	 *  see docs for more info and usage
	 *	@param string $info_type
	 *	@param int	$col_offset
	 */
	public function get_col_info($info_type = "name", $col_offset = -1){

		if ( $this->col_info ){
			if ( $col_offset == -1 )
			{
				$i=0;
				foreach($this->col_info as $col )
				{
					$new_array[$i] = $col->{$info_type};
					$i++;
				}
				return $new_array;
			}
			else
			{
				return $this->col_info[$col_offset]->{$info_type};
			}

		}

	}

	/**
	 *  store_cache
	 *	@param string $query
	 *	@param boolean	$is_insert
	 */
	public function store_cache($query,$is_insert){

		// The would be cache file for this query
		$cache_file = $this->cache_dir.'/'.md5($query);

		// disk caching of queries
		if ( $this->use_disk_cache && ( $this->cache_queries && ! $is_insert ) || ( $this->cache_inserts && $is_insert )){
			if ( ! is_dir($this->cache_dir) )
			{
				$this->register_error("Could not open cache dir: $this->cache_dir");
				$this->show_errors ? trigger_error("Could not open cache dir: $this->cache_dir",E_USER_WARNING) : null;
			}
			else{
				// Cache all result values
				$result_cache = array
				(
					'col_info' => $this->col_info,
					'last_result' => $this->last_result,
					'num_rows' => $this->num_rows,
					'return_value' => $this->num_rows,
				);
				error_log ( serialize($result_cache), 3, $cache_file);
			}
		}

	}

	/**
	 *  get_cache
	 *	@param string $query
	 */
	public function get_cache($query){

		// The would be cache file for this query
		$cache_file = $this->cache_dir.'/'.md5($query);

		// Try to get previously cached version
		if ( $this->use_disk_cache && file_exists($cache_file) ){
			// Only use this cache file if less than 'cache_timeout' (hours)
			if ( (time() - filemtime($cache_file)) > ($this->cache_timeout*3600) )
			{
				unlink($cache_file);
			}
			else
			{
				$result_cache = unserialize(file_get_contents($cache_file));

				$this->col_info = $result_cache['col_info'];
				$this->last_result = $result_cache['last_result'];
				$this->num_rows = $result_cache['num_rows'];

				$this->from_disk_cache = true;

				// If debug ALL queries
				$this->trace || $this->debug_all ? $this->debug() : null ;

				return $result_cache['return_value'];
			}
		}

	}

	/**
	 *	vardump
	 *  Dumps the contents of any input variable to screen in a nicely
	 *  formatted and easy to understand way - any type: Object, Var or Array
	 *	@param mixed $mixed
	 */
	public function vardump($mixed = ''){

		// Start outup buffering
		ob_start();

		echo "<table><tr><td>";
		echo "<pre>";

		if ( ! $this->vardump_called ){
			echo "<b>ezSQL</b> (v".EZSQL_VERSION.") <b>Variable Dump..</b>\n\n";
		}

		$var_type = gettype ($mixed);
		print_r(($mixed?$mixed:"<span class=\"highlight\">No Value / False</span>"));
		echo "\n\n<b>Type:</b> " . ucfirst($var_type) . "\n";
		echo "<b>Last Query</b> [$this->num_queries]<b>:</b> ".($this->last_query?$this->last_query:"NULL")."\n";
		echo "<b>Last Function Call:</b> " . ($this->func_call?$this->func_call:"None")."\n";
		echo "<b>Last Rows Returned:</b> ".count($this->last_result)."\n";
		echo "</pre></td></tr></table>".$this->donation();
		echo "\n<hr>";

		// Stop output buffering and capture debug HTML
		$html = ob_get_contents();
		ob_end_clean();

		// Only echo output if it is turned on
		if ( $this->debug_echo_is_on ){
			echo $html;
		}

		$this->vardump_called = true;

		return $html;

	}

	/**
	 *	dumpvar
	 *  Alias for the above public function
	 *	@param mixed $mixed
	 */
	public function dumpvar($mixed){
		$this->vardump($mixed);
	}

	/**
	 *	debug
	 *  Displays the last query string that was sent to the database & a
	 * 	table listing results (if there were any).
	 * 	(abstracted into a seperate file to save server overhead).
	 */
	public function debug(){
		// Don't run if we're live
		if (MODE == 'LIVE') {
			return false;
		}

		// Start outup buffering
		ob_start();


		// Only show ezSQL credits once..
		if ( ! $this->debug_called ){
			echo "<h1>Debug mode for ezSQL (v".EZSQL_VERSION.")</h1><hr />\n";
		}

		if ( $this->last_error ){
			echo "<p>Last Error -- [<b>$this->last_error</b>]</p>";
		}

		if ( $this->from_disk_cache ){
			echo "<p><b>Results retrieved from disk cache</b></p>";
		}

		echo "<h2>Query [$this->num_queries]</h2>";
		echo "<p class=\"highlight\">[$this->last_query]</p>";

		echo "<h3>Query results</h3>";

		if ( $this->col_info ){

			// =====================================================
			// Results top rows

			echo "<table id=\"table_$this->num_queries\">";
			echo "<thead>";
			echo "<tr>";
			echo "<th scope=\"col\">(row)</th>";


			for ( $i=0; $i < count($this->col_info); $i++ )
			{
				echo "<th scope=\"col\">{$this->col_info[$i]->type} {$this->col_info[$i]->max_length}<br>{$this->col_info[$i]->name}</th>";
			}

			echo "</tr>";
			echo "</thead>";
			echo "<tbody style=\"display: none;\">";

			// ======================================================
			// print main results

		if ( $this->last_result ){
			$i=0;
			foreach ( $this->get_results(null,ARRAY_N) as $one_row )
			{
				$i++;
				echo "<tr ".Application::assignOrderClass($i,sizeof($this->get_results(null,ARRAY_N)))."><td>$i</td>";

				foreach ( $one_row as $item )
				{
					echo "<td>$item</td>";
				}

				echo "</tr>";
			}

		} // if last result
		else{
			echo "<tr><td colspan=".(count($this->col_info)+1).">No Results</td></tr>";
		}
		echo "</tbody>";
		echo "</table>";

		} // if col_info
		else{
			echo "<p>No Results</p>";
		}

		echo "<hr />";

		// Stop output buffering and capture debug HTML
		$html = ob_get_contents();
		ob_end_clean();

		// Only echo output if it is turned on
		if ( $this->debug_echo_is_on ){
			echo $html;
		}

		$this->debug_called = true;

		return $html;

	}
	
	
	public function cacheOn($time = 24){
		$this->cache_queries = true;
		$this->use_disk_cache = true;
		$this->cache_timeout = $time;
	}
	
	public function cacheOff(){
		$this->cache_queries = false;
		$this->use_disk_cache = false;
	}
	
	/**
	 * donation
	 */
	public function donation(){}


}
