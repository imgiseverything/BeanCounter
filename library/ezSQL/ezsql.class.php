<?php

// Include ezSQL core
require_once('ezsqlcore.class.php');

/**
 *  @package	ezsql
 */
 



/*
 *  ezSQL error strings - mySQL
 */
$ezsql_mysql_str = array(
	1 	=> 	'Require $dbuser and $dbpassword to connect to a database server',
	2 	=> 	'Error establishing mySQL database connection. Correct user/password? Correct hostname? Database server running?',
	3 	=> 	'Require $dbname to select a database',
	4 	=> 	'mySQL database connection is not active',
	5 	=> 	'Unexpected error while trying to select database'
);



/*
 *   ezSQL Database specific class - mySQL
 */
if(!function_exists ('mysql_connect')){
	die('<b>Fatal Error:</b> ezSQL_mysql requires mySQL Lib to be compiled and or linked in to the PHP engine');
}
if(! class_exists('ezSQLcore')){
	die('<b>Fatal Error:</b> ezSQL_mysql requires ezSQLcore (ez_sql_core.php) to be included/loaded before it can be used');
}

/**
 *	ezSQL_mysql
 *	mySQL component (part of ezSQL databse abstraction library)
 *
 *	Core class containg common functions to manipulate query result
 *  sets once returned
 *
 *	@package	ezsql
 * 	@author		Justin Vincent (justin@visunet.ie) http://php.justinvincent.com
 *	@version	2.03
 */
	
	
class ezSQL extends ezSQLCore{


	/**
	 *	@var	boolean
	 */
	public $dbuser 		= false;
	
	/**
	 *	@var	boolean
	 */
	public $dbpassword 	= false;
	
	/**
	 *	@var	boolean
	 */
	public $dbname 	= false;
	
	/**
	 *	@var	boolean
	 */
	public $dbhost 	= false;

	/**
	 *  Constructor 
	 *	allow the user to perform a qucik connect at the
	 *  same time as initialising the ezSQL_mysql class
	 *	@param	string $dbuser
	 *	@param	string $dbpassword
	 *	@param	string $dbname
	 *	@param	string $dbhost
	 */
	public function __construct($dbuser = '', $dbpassword = '', $dbname = '', $dbhost = 'localhost'){

		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;
		
		parent::__construct();
	}

	/**
	 *	
	 *  Short hand way to connect to mySQL database server
	 *  and select a mySQL database at the same time
	 *	@param	string $dbuser
	 *	@param	string $dbpassword
	 *	@param	string $dbname
	 *	@param	string $dbhost
	 *	@return	string $return_val
	 */
	public function quick_connect($dbuser = '', $dbpassword = '', $dbname = '', $dbhost = 'localhost'){
		$return_val = false;
		if ( ! $this->connect($dbuser, $dbpassword, $dbhost,true) ) ;
		else if ( ! $this->select($dbname) ) ;
		else $return_val = true;
		return $return_val;
	}

	/**
	 *	connect
	 *  Try to connect to mySQL database server
	 *	@param	string $dbuser
	 *	@param	string $dbpassword
	 *	@param	string $dbhost
	 *	@return	string $return_val
	 */
	public function connect($dbuser = '', $dbpassword = '', $dbhost = 'localhost'){
		global $ezsql_mysql_str; $return_val = false;

		// Must have a user and a password
		if ( ! $dbuser )
		{
			$this->register_error($ezsql_mysql_str[1].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[1],E_USER_WARNING) : null;
		}
		// Try to establish the server database handle
		else if ( ! $this->dbh = @mysql_connect($dbhost,$dbuser,$dbpassword,true) )
		{
			$this->register_error($ezsql_mysql_str[2].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[2],E_USER_WARNING) : null;
		}
		else
		{
			$this->dbuser = $dbuser;
			$this->dbpassword = $dbpassword;
			$this->dbhost = $dbhost;
			$return_val = true;
		}

		return $return_val;
	}

	/**
	 *	select
	 *  Try to select a mySQL database
	 *	@param	string $dbname	
	 *	@return	string $return_val
	 */
	public function select($dbname = ''){
		global $ezsql_mysql_str; $return_val = false;

		// Must have a database name
		if ( ! $dbname )
		{
			$this->register_error($ezsql_mysql_str[3].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[3],E_USER_WARNING) : null;
		}

		// Must have an active database connection
		else if ( ! $this->dbh )
		{
			$this->register_error($ezsql_mysql_str[4].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[4],E_USER_WARNING) : null;
		}

		// Try to connect to the database
		else if ( !@mysql_select_db($dbname, $this->dbh) )
		{
			// Try to get error supplied by mysql if not use our own
			if ( !$str = @mysql_error($this->dbh))
				  $str = $ezsql_mysql_str[5];

			$this->register_error($str.' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($str,E_USER_WARNING) : null;
		}
		else
		{
			$this->dbname = $dbname;
			$return_val = true;
		}

		return $return_val;
	}

	/**
	 * Weak escape, using addslashes()
	 *
	 * @see addslashes()
	 * @since 2.8.0
	 * @access private
	 *
	 * @param string $string
	 * @return string
	 */
	function _weak_escape( $string ) {
		return addslashes( $string );
	}

	/**
	 * Real escape, using mysql_real_escape_string() or addslashes()
	 *
	 * @see mysql_real_escape_string()
	 * @see addslashes()
	 * @since 2.8
	 * @access private
	 *
	 * @param  string $string to escape
	 * @return string escaped
	 */
	function _real_escape( $string ) {
		if ( isset($this->dbh) && $this->real_escape )
			return mysql_real_escape_string( $string, $this->dbh );
		else
			return addslashes( $string );
	}

	/**
	 * Escape data. Works on arrays.
	 *
     * @uses wpdb::_escape()
     * @uses wpdb::_real_escape()
	 * @since  2.8
	 * @access private
	 *
	 * @param  string|array $data
	 * @return string|array escaped
	 */
	function _escape( $data ) {
		if ( is_array( $data ) ) {
			foreach ( (array) $data as $k => $v ) {
				if ( is_array($v) )
					$data[$k] = $this->_escape( $v );
				else
					$data[$k] = $this->_real_escape( $v );
			}
		} else {
			$data = $this->_real_escape( $data );
		}

		return $data;
	}

	/**
	 * Escapes content for insertion into the database using addslashes(), for security.
	 *
	 * Works on arrays.
	 *
	 * @since 0.71
	 * @param string|array $data to escape
	 * @return string|array escaped as query safe string
	 */
	function escape( $data ) {
		if ( is_array( $data ) ) {
			foreach ( (array) $data as $k => $v ) {
				if ( is_array( $v ) )
					$data[$k] = $this->escape( $v );
				else
					$data[$k] = $this->_weak_escape( $v );
			}
		} else {
			$data = $this->_weak_escape( $data );
		}

		return $data;
	}
	
	
	/**
	 * Escapes content by reference for insertion into the database, for security
	 *
	 * @uses wpdb::_real_escape()
	 * @since 2.3.0
	 * @param string $string to escape
	 * @return void
	 */
	function escape_by_ref( &$string ) {
		$string = $this->_real_escape( $string );
	}

	/**
	 * Prepares a SQL query for safe execution. Uses sprintf()-like syntax.
	 *
	 * The following directives can be used in the query format string:
	 *   %d (decimal number)
	 *   %s (string)
	 *   %% (literal percentage sign - no argument needed)
	 *
	 * Both %d and %s are to be left unquoted in the query string and they need an argument passed for them.
	 * Literals (%) as parts of the query must be properly written as %%.
	 *
	 * This function only supports a small subset of the sprintf syntax; it only supports %d (decimal number), %s (string).
	 * Does not support sign, padding, alignment, width or precision specifiers.
	 * Does not support argument numbering/swapping.
	 *
	 * May be called like {@link http://php.net/sprintf sprintf()} or like {@link http://php.net/vsprintf vsprintf()}.
	 *
	 * Both %d and %s should be left unquoted in the query string.
	 *
	 * <code>
	 * wpdb::prepare( "SELECT * FROM `table` WHERE `column` = %s AND `field` = %d", 'foo', 1337 )
	 * wpdb::prepare( "SELECT DATE_FORMAT(`field`, '%%c') FROM `table` WHERE `column` = %s", 'foo' );
	 * </code>
	 *
	 * @link http://php.net/sprintf Description of syntax.
	 * @since 2.3.0
	 *
	 * @param string $query Query statement with sprintf()-like placeholders
	 * @param array|mixed $args The array of variables to substitute into the query's placeholders if being called like
	 * 	{@link http://php.net/vsprintf vsprintf()}, or the first variable to substitute into the query's placeholders if
	 * 	being called like {@link http://php.net/sprintf sprintf()}.
	 * @param mixed $args,... further variables to substitute into the query's placeholders if being called like
	 * 	{@link http://php.net/sprintf sprintf()}.
	 * @return null|false|string Sanitized query string, null if there is no query, false if there is an error and string
	 * 	if there was something to prepare
	 */
	function prepare( $query = null ) { // ( $query, *$args )
		if ( is_null( $query ) )
			return;

		$args = func_get_args();
		array_shift( $args );
		// If args were passed as an array (as in vsprintf), move them up
		if ( isset( $args[0] ) && is_array($args[0]) )
			$args = $args[0];
		$query = str_replace( "'%s'", '%s', $query ); // in case someone mistakenly already singlequoted it
		$query = str_replace( '"%s"', '%s', $query ); // doublequote unquoting
		$query = preg_replace( '|(?<!%)%s|', "'%s'", $query ); // quote the strings, avoiding escaped strings like %%s
		array_walk( $args, array( &$this, 'escape_by_ref' ) );
		return @vsprintf( $query, $args );
	}

	/**
	 *	sysdate
	 *  Return mySQL specific system date syntax
	 *  i.e. Oracle: SYSDATE Mysql: NOW()
	 *	@return string
	 */
	public function sysdate(){
		return 'NOW()';
	}

	/**
	 *	query
	 *  Perform mySQL query and try to detirmin result value
	 *	@param	string $query	
	 *	@return	string $return_value
	 */
	public function query($query){

		// Initialise return
		$return_val = 0;

		// Flush cached values..
		$this->flush();

		// For reg expressions
		$query = trim($query);

		// Log how the public function was called
		$this->func_call = "\$db->query(\"$query\")";

		// Keep track of the last query for debug..
		$this->last_query = $query;

		// Count how many queries there have been
		$this->num_queries++;

		// Use core file cache function
		if ( $cache = $this->get_cache($query) )
		{
			return $cache;
		}

		// If there is no existing database connection then try to connect
		if ( ! isset($this->dbh) || ! $this->dbh )
		{

			$this->connect($this->dbuser, $this->dbpassword, $this->dbhost);
			$this->select($this->dbname);
		
		}

		// Perform the query via std mysql_query function..
		$this->result = @mysql_query($query, $this->dbh);

		// If there is an error then take note of it..
		if ( $str = @mysql_error($this->dbh) )
		{
			$is_insert = true;
			$this->register_error($str);
			$this->show_errors ? trigger_error($str . "\nFull query: \"" . str_replace(array("\n", "\r", "chr(13)",  "\t", "\0", "\x0B", "  "), ' ', $query) . "\"\nURL:" . $_SERVER['REQUEST_URI'], E_USER_WARNING) : null;
			//$this->show_errors ? trigger_error($str, E_USER_WARNING) : null;
			return false;
		}

		// Query was an insert, delete, update, replace
		$is_insert = false;
		if ( preg_match("/^(insert|delete|update|replace)\s+/i",$query) )
		{
			$this->rows_affected = @mysql_affected_rows();

			// Take note of the insert_id
			if ( preg_match("/^(insert|replace)\s+/i",$query) )
			{
				$this->insert_id = @mysql_insert_id($this->dbh);
			}

			// Return number fo rows affected
			$return_val = $this->rows_affected;
		}
		// Query was a select
		else if(is_resource($this->result))
		{

			// Take note of column info
			$i=0;
			while ($i < @mysql_num_fields($this->result))
			{
				$this->col_info[$i] = @mysql_fetch_field($this->result);
				$i++;
			}

			// Store Query Results
			$num_rows=0;
			while ( $row = @mysql_fetch_object($this->result) )
			{
				// Store relults as an objects within main array
				$this->last_result[$num_rows] = $row;
				$num_rows++;
			}
			
			@mysql_free_result($this->result);

			// Log number of rows the query returned
			$this->num_rows = $num_rows;

			// Return number of rows selected
			$return_val = $this->num_rows;
		}

		// disk caching of queries
		$this->store_cache($query,$is_insert);

		// If debug ALL queries
		$this->trace || $this->debug_all ? $this->debug() : null ;

		return $return_val;

	}

}