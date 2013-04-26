<?php
/**
 *	=========================================================================
 *	
 *	Backup Class
 *	-------------------------------------------------------------------------
 *	
 *	Walk through the database and back everything up
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
 *	@since		07/12/2008
 *	
 *	@lastmodified	13/11/2009
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
 *	setFilename
 *	getFilename
 *	setTables
 *	getTables
 *	setText
 *	setTableType
 *	setCreateSQL
 *	setData
 *	setDataText
 *	saveBackup	
 *	
 *	=========================================================================
 */

	ini_set("memory_limit", "80M");
	
	class Backup{
	
		// Variables
		
		/**
		 *	@var object
		 */
		private $_db;
		
		/**
		 *	@var array
		 */
		private $_tables = array();
		
		/**
		 *	@var string
		 */
		private $_text;
		
		/**
		 *	@var string
		 */
		private $_filename;
		
		
		
		/**
		 *	Constructor
		 *	@param object $db
		 */
		public function __construct($db){

			// Local variable objects
			$this->_db = $db;
			
			$this->setFilename();
			
			$this->setTables();
			
			$this->setText();
			
		}
		
		
		// Methods
		
		/**
		 *	setFilename
		 */
		private function setFilename(){
			$this->_filename = 'database_backup_' . date('Ymd') . '.sql';
		}
		
		/**
		 *	getFilename
		 */
		public function getFilename(){
			return $this->_filename;
		}
	
				
		/**
		 *	setTables
		 *	look through database and grab all the database tables
		 */
		private function setTables(){
			$query = "SHOW TABLES;";
			
			if($tables = $this->_db->get_results($query)){
				foreach($tables as $database => $table){
					$this->_tables[] = $table->{'Tables_in_' . $this->_db->dbname};
				}
			}

		}
		
		/**
		 *	getTables()
		 *	@return array $_tables
		 */
		public function getTables(){
			return $this->_tables;
		}
		
		/**
		 *	setText()
		 *	Loop through database tables and
		 *	# Work out if it's a table or other
		 *	# Grab the recreation SQL
		 *	# Grab all their data
		 *	# Drop the them
		 *	# Recreate them
		 *	# Add data in
		 *
		 */	
		private function setText(){
		
			$this->_text = 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";' . "\n\n";
			
			
			if(!empty($this->_tables)){
			
				$max = sizeof($this->_tables);
				
				for($i = 0; $i < $max; $i++){
				
					// Table, View or Trigger?
					$type = $this->setTableType($this->_tables[$i]);
				
					// Grab the recreation SQL					
					$create_table = $this->setCreateSQL($this->_tables[$i]);					
					
					// Grab all their data
					if($type == 'TABLE' && $this->_tables[$i] != 'errors'){
						$data = $this->setData($this->_tables[$i]);
						$dataText = $this->setDataText($data);
					} else{
						$dataText = '';
					}					
				
					// User friendly comment
					$this->_text .= "/**\n *  DROP/CREATE/INSERT FOR `{$this->_tables[$i]}`\n */\n";
					// Drop table
					$this->_text .= "DROP {$type} IF EXISTS `{$this->_tables[$i]}`;\n";
					// Recreate Table;
					$this->_text .= "{$create_table};\n";
					// Add Data (if it exists)
					if($type == 'TABLE' && !empty($dataText)){
						$this->_text .= "INSERT INTO `{$this->_tables[$i]}`\nVALUES\n{$dataText};\n\n";
					}
					
				}

				
				// Now force the user to download it
				$this->forceDownload();
			}
			
		}
		
		
		/**
		 *	setTableType
		 * 	Work out if this is a table, view or a trigger
		 *	@param string $table
		 *	@return string $type
		 */
		private function setTableType($table){
			$query = "SHOW CREATE TABLE `{$table}`;";
			$result = $this->_db->get_row($query);
			
			// default type is a table
			$type = 'TABLE';
			
			if(isset($result->{'Create Table'})){
				$type = 'TABLE';
			} else if(isset($result->{'Create View'})){
				$type = 'VIEW';
			} else if(isset($result->{'Create Trigger'})){
				$type = 'TRIGGER';
			}
			
			return $type;
		}
		
		
		/**
		 *	setCreateSQL
		 *	get the SQL to be able to create the table/view/trigger
		 *	@param	string	$table - Name of the table
		 *	@return	string	$data
		 */
		private function setCreateSQL($table){
		
			$query = "SHOW CREATE TABLE `{$table}`;";
			$result = $this->_db->get_row($query);			
			
			if(isset($result->{'Create Table'})){
				// Table
				$data = $result->{'Create Table'};
			} else if(isset($result->{'Create View'})){
				// View			
				$data = $result->{'Create View'};
				
				// remove the SQL SECURITY DEFINER message from the 
				// CREATE VIEW query, otherwise all kind of problems
				// come up on importing to a different server/database.
				$pattern = "(ALGORITHM=UNDEFINED DEFINER=)([\`a-zA-Z0-9\`@\.\`/ ]*)(SQL SECURITY DEFINER)";
				$data = eregi_replace($pattern, '', $data);
				
			} else if(isset($result->{'Create Trigger'})){
				// Trigger - Erm... needs testing
				$data = $result->{'Create Trigger'};
			}
			
			
			// Make it UTF8
			$data = str_replace('latin1', 'utf8', $data);
			
			return $data;
			
		}
		
		
		/**
		 *	setData
		 *	Get all data from specific MySQL table
		 *	@param string $table - Name of the table
		 *	@return array $results
		 */
		private function setData($table){
			$query = "SELECT * FROM `{$table}`";
			$results = $this->_db->get_results($query, "ARRAY_A");
			return $results;
		}
		
		/**
		 *	setDataText
		 *	Create the SQL values queries from supplied data
		 *	e.g. (1,2,'string','YYYY-mm-dd 00:00:00');
		 *
		 *	@data  array - all data
		 *	@return string  $sql_values
		 */
		private function setDataText($data){

			$row = array();
			$all_rows = array();			
			
			for($i = 0; $i < sizeof($data); $i++){
				foreach($data[$i] as $item){
					$item = str_replace("'", '"', $item);
					$row[$i][] = "'{$item}'";
				}
				$all_rows[] = "(" . join(', ', $row[$i]) . ")";
			}			
			
			$sql_values = join(", \n", $all_rows);
			//niceError($sql_values);
			return $sql_values;
			
		}
		
		/**
		 *	forceDownload
		 *	display backup file and force it to download
		 */
		private function forceDownload(){
			if(!empty($this->_text)){
				
				//header ("Expires: Mon, 26 Jul 2009 05:00:00 GMT");
				header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
				header ("Cache-Control: no-cache, must-revalidate");
				header ("Pragma: no-cache");
				header ("Content-type: plain/text");
				header ("Content-Disposition: attachment; filename=\"" . $this->_filename . "\"" );
				header ("Content-Description: PHP/INTERBASE Generated Data" );
				exit($this->_text);
			}
		}		
	
	
	}
	
?>