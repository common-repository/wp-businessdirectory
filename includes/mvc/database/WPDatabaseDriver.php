<?php

/**
 * @package     JBD.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace MVC\Database;

defined ( 'JPATH_PLATFORM' ) or die ();

/**
 * MySQLi database driver
 *
 * @link https://secure.php.net/manual/en/book.mysqli.php
 * @since 12.1
 */
class WPDatabaseDriver extends JDatabaseDriver {
	/**
	 * The name of the database driver.
	 *
	 * @var string
	 * @since 12.1
	 */
	public $name = 'mysqli';
	
	/**
	 * The type of the database server family supported by this driver.
	 *
	 * @var string
	 * @since CMS 3.5.0
	 */
	public $serverType = 'mysql';
	
	/**
	 *
	 * @var mysqli The database connection resource.
	 * @since 11.1
	 */
	protected $connection;
	
	/**
	 * The character(s) used to quote SQL statement names such as table names or field names,
	 * etc.
	 * The child classes should define this as necessary. If a single character string the
	 * same character is used for both sides of the quoted name, else the first character will be
	 * used for the opening quote and the second for the closing quote.
	 *
	 * @var string
	 * @since 12.2
	 */
	protected $nameQuote = '`';
	
	/**
	 * The null or zero representation of a timestamp for the database driver.
	 * This should be
	 * defined in child classes to hold the appropriate value for the engine.
	 *
	 * @var string
	 * @since 12.2
	 */
	protected $nullDate = '0000-00-00 00:00:00';
	
	/**
	 *
	 * @var string The minimum supported database version.
	 * @since 12.2
	 */
	protected static $dbMinimum = '5.0.4';
	
	
	/**
	 * Wordpress database object
	 * @var unknown
	 */
	protected $wpdb;
	
	/**
	 * Constructor.
	 *
	 * @param array $options
	 *        	List of options used to configure the connection
	 *        	
	 * @since 12.1
	 */
	
	
	public function __construct($options) {
		// Get some basic values from the options.
		
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->tablePrefix = $wpdb->prefix;
		$options['prefix'] = $wpdb->prefix;
		
		// Finalize initialisation.
		parent::__construct ( $options );
	}
	
	/**
	 * Connects to the database if needed.
	 *
	 * @return void Returns void if the database connected successfully.
	 *        
	 * @since 12.1
	 * @throws RuntimeException
	 */
	public function connect() {
		
	}
	
	/**
	 * Disconnects the database.
	 *
	 * @return void
	 *
	 * @since 12.1
	 */
	public function disconnect() {
		
	}
	
	
	/**
	 * Method to get the first row of the result set from the database query as an object.
	 *
	 * @param   string  $class  The class name to use for the returned row object.
	 *
	 * @return  mixed   The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function loadObject($class = 'stdClass'){
	    $result = $this->execute();
	    if(empty($result)){
	        return null;
	    }
	    
	    if(is_array($result)){
	        $result = $result[0];
	    }
	    
	    return $result;
	}
	
	
	public function loadObjectList($key = '', $class = 'stdClass'){
		return $this->execute();
	}
	
	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * @param string $text
	 *        	The string to be escaped.
	 * @param boolean $extra
	 *        	Optional parameter to provide extra escaping.
	 *        	
	 * @return string The escaped string.
	 *        
	 * @since 12.1
	 */
	public function escape($text, $extra = false) {
		
	    if(is_array($text)){
	        return $text;
	    }
	    
	    $result = $this->wpdb->_real_escape($text);
		
		if ($extra) {
			$result = addcslashes ( $result, '%_' );
		}
		
		return $result;
	}
	
	/**
	 * Test to see if the MySQL connector is available.
	 *
	 * @return boolean True on success, false otherwise.
	 *        
	 * @since 12.1
	 */
	public static function isSupported() {
		return function_exists ( 'mysqli_connect' );
	}
	
	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return boolean True if connected to the database engine.
	 *        
	 * @since 12.1
	 */
	public function connected() {
		if (is_object ( $this->connection )) {
			return mysqli_ping ( $this->connection );
		}
		
		return false;
	}
	
	/**
	 * Drops a table from the database.
	 *
	 * @param string $tableName
	 *        	The name of the database table to drop.
	 * @param boolean $ifExists
	 *        	Optionally specify that the table must exist before it is dropped.
	 *        	
	 * @return JDatabaseDriverMysqli Returns this object to support chaining.
	 *        
	 * @since 12.2
	 * @throws RuntimeException
	 */
	public function dropTable($tableName, $ifExists = true) {
		$this->connect ();
		
		$query = $this->getQuery ( true );
		
		$this->setQuery ( 'DROP TABLE ' . ($ifExists ? 'IF EXISTS ' : '') . $query->quoteName ( $tableName ) );
		
		$this->execute ();
		
		return $this;
	}
	
	/**
	 * Get the number of affected rows by the last INSERT, UPDATE, REPLACE or DELETE for the previous executed SQL statement.
	 *
	 * @return integer The number of affected rows.
	 *        
	 * @since 12.1
	 */
	public function getAffectedRows() {
		$this->connect ();
		
		return mysqli_affected_rows ( $this->connection );
	}
	
	/**
	 * Method to get the database collation.
	 *
	 * @return mixed The collation in use by the database (string) or boolean false if not supported.
	 *        
	 * @since 12.2
	 * @throws RuntimeException
	 */
	public function getCollation() {
		$this->connect ();
		
		// Attempt to get the database collation by accessing the server system variable.
		$this->setQuery ( 'SHOW VARIABLES LIKE "collation_database"' );
		$result = $this->loadObject ();
		
		if (property_exists ( $result, 'Value' )) {
			return $result->Value;
		} else {
			return false;
		}
	}
	
	/**
	 * Method to get the database connection collation, as reported by the driver.
	 * If the connector doesn't support
	 * reporting this value please return an empty string.
	 *
	 * @return string
	 */
	public function getConnectionCollation() {
		$this->connect ();
		
		// Attempt to get the database collation by accessing the server system variable.
		$this->setQuery ( 'SHOW VARIABLES LIKE "collation_connection"' );
		$result = $this->loadObject ();
		
		if (property_exists ( $result, 'Value' )) {
			return $result->Value;
		} else {
			return false;
		}
	}
	
	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 * This command is only valid for statements like SELECT or SHOW that return an actual result set.
	 * To retrieve the number of rows affected by an INSERT, UPDATE, REPLACE or DELETE query, use getAffectedRows().
	 *
	 * @param resource $cursor
	 *        	An optional database cursor resource to extract the row count from.
	 *        	
	 * @return integer The number of returned rows.
	 *        
	 * @since 12.1
	 */
	public function getNumRows($cursor = null) {
		
	    $this->execute();
	    return $this->wpdb->num_rows;
	}
	
	/**
	 * Shows the table CREATE statement that creates the given tables.
	 *
	 * @param mixed $tables
	 *        	A table name or a list of table names.
	 *        	
	 * @return array A list of the create SQL for the tables.
	 *        
	 * @since 12.1
	 * @throws RuntimeException
	 */
	public function getTableCreate($tables) {
		$this->connect ();
		
		$result = array ();
		
		// Sanitize input to an array and iterate over the list.
		settype ( $tables, 'array' );
		
		foreach ( $tables as $table ) {
			// Set the query to get the table CREATE statement.
			$this->setQuery ( 'SHOW CREATE table ' . $this->quoteName ( $this->escape ( $table ) ) );
			$row = $this->loadRow ();
			
			// Populate the result array based on the create statements.
			$result [$table] = $row [1];
		}
		
		return $result;
	}
	
	/**
	 * Retrieves field information about a given table.
	 *
	 * @param string $table
	 *        	The name of the database table.
	 * @param boolean $typeOnly
	 *        	True to only return field types.
	 *        	
	 * @return array An array of fields for the database table.
	 *        
	 * @since 12.2
	 * @throws RuntimeException
	 */
	public function getTableColumns($table, $typeOnly = true) {
		
		$result = array();
		
		// Set the query to get the table fields statement.
		$this->setQuery ( 'SHOW FULL COLUMNS FROM ' . $this->quoteName ( $this->escape ( $table ) ) );
		$fields = $this->loadObjectList ();
		
		// If we only want the type as the value add just that to the list.
		if ($typeOnly) {
			foreach ( $fields as $field ) {
				$result [$field->Field] = preg_replace ( '/[(0-9)]/', '', $field->Type );
			}
		}		// If we want the whole field data object add that to the list.
		else {
			foreach ( $fields as $field ) {
				$result [$field->Field] = $field;
			}
		}
		
		return $result;
	}
	
	/**
	 * Get the details list of keys for a table.
	 *
	 * @param string $table
	 *        	The name of the table.
	 *        	
	 * @return array An array of the column specification for the table.
	 *        
	 * @since 12.2
	 * @throws RuntimeException
	 */
	public function getTableKeys($table) {
		$this->connect ();
		
		// Get the details columns information.
		$this->setQuery ( 'SHOW KEYS FROM ' . $this->quoteName ( $table ) );
		$keys = $this->loadObjectList ();
		
		return $keys;
	}
	
	/**
	 * Method to get an array of all tables in the database.
	 *
	 * @return array An array of all the tables in the database.
	 *        
	 * @since 12.2
	 * @throws RuntimeException
	 */
	public function getTableList() {
		$this->connect ();
		
		// Set the query to get the tables statement.
		$this->setQuery ( 'SHOW TABLES' );
		$tables = $this->loadColumn ();
		
		return $tables;
	}
	
	/**
	 * Get the version of the database connector.
	 *
	 * @return string The database connector version.
	 *        
	 * @since 12.1
	 */
	public function getVersion() {
		$this->connect ();
		
		return mysqli_get_server_info ( $this->connection );
	}
	
	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 *
	 * @return mixed The value of the auto-increment field from the last inserted row.
	 *         If the value is greater than maximal int value, it will return a string.
	 *        
	 * @since 12.1
	 */
	public function insertid() {
	    return $this->wpdb->insert_id;
	}
	
	/**
	 * Locks a table in the database.
	 *
	 * @param string $table
	 *        	The name of the table to unlock.
	 *        	
	 * @return JDatabaseDriverMysqli Returns this object to support chaining.
	 *        
	 * @since 12.2
	 * @throws RuntimeException
	 */
	public function lockTable($table) {
		$this->setQuery ( 'LOCK TABLES ' . $this->quoteName ( $table ) . ' WRITE' )->execute ();
		
		return $this;
	}
	
	/**
	 * Execute the SQL statement.
	 *
	 * @return mixed A database cursor resource on success, boolean false on failure.
	 *        
	 * @since 12.1
	 * @throws RuntimeException
	 */
	public function execute() {
		
		// Take a local copy so that we don't modify the original query and cause issues later
	 	$query = $this->replacePrefix ( ( string ) $this->sql );
	 	
	 	$result = null;

	 	// TODO: removed from if condition ($this->sql instanceof JDatabaseQueryMysqli) && ... need to be checked again
		if (($this->limit > 0 || $this->offset > 0)) {
			$query .= ' LIMIT ' . $this->offset . ', ' . $this->limit;
		}
		
		$this->wpdb->show_errors();
		
		//echo($query);
		//echo "<br/>";
		//extract the sql type from the sql string
		$sqlTypeString = explode(" ",$query);
		if(is_array($sqlTypeString)){
		    $sqlTypeString = $sqlTypeString[0];
		}else{
		    $sqlTypeString = "";
		}
		
		//determine the type of query that has to be done
		if ((($this->sql instanceof JDatabaseQueryMysqli) && ($this->sql->type =="insert" || $this->sql->type =="update" || $this->sql->type =="delete")) || stripos($sqlTypeString,"insert")!==false || stripos($sqlTypeString,"update")!==false || stripos($sqlTypeString,"delete")!==false){
		    $result = $this->wpdb->query($query);

		    if (!empty($this->wpdb->last_error)) {
		    	throw new \Exception($this->wpdb->last_error, 1);
		    }
		    
		    $result = true;
		    
		} else {
		    $result = $this->wpdb->get_results($query);
		    
		    if (is_array($result)) {
		        foreach ($result as $item) {
		            $this->stripObjectSlashes($item);
		        }
		    } else {
		        $this->stripObjectSlashes($result);
		    }
		}

		//exit;
		
		//dump($result);
		
		//exit;
		return $result;
	}
	
	private function stripObjectSlashes(&$object){
	    $properties = get_object_vars($object);
	    foreach($properties as $key=>$val){
			if(!empty($val)){
	        	$object->$key = stripslashes($val);
			}
	    }
	}
	
	
	/**
	 * Renames a table in the database.
	 *
	 * @param string $oldTable
	 *        	The name of the table to be renamed
	 * @param string $newTable
	 *        	The new name for the table.
	 * @param string $backup
	 *        	Not used by MySQL.
	 * @param string $prefix
	 *        	Not used by MySQL.
	 *        	
	 * @return JDatabaseDriverMysqli Returns this object to support chaining.
	 *        
	 * @since 12.2
	 * @throws RuntimeException
	 */
	public function renameTable($oldTable, $newTable, $backup = null, $prefix = null) {
		$this->setQuery ( 'RENAME TABLE ' . $oldTable . ' TO ' . $newTable )->execute ();
		
		return $this;
	}
	
	/**
	 * Select a database for use.
	 *
	 * @param string $database
	 *        	The name of the database to select for use.
	 *        	
	 * @return boolean True if the database was successfully selected.
	 *        
	 * @since 12.1
	 * @throws RuntimeException
	 */
	public function select($database) {
		$this->connect ();
		
		if (! $database) {
			return false;
		}
		
		if (! mysqli_select_db ( $this->connection, $database )) {
			throw new JDatabaseExceptionConnecting ( 'Could not connect to MySQL database.' );
		}
		
		return true;
	}
	
	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * @return boolean True on success.
	 *        
	 * @since 12.1
	 */
	public function setUtf() {
		// If UTF is not supported return false immediately
		if (! $this->utf) {
			return false;
		}
		
		// Make sure we're connected to the server
		$this->connect ();
		
		// Which charset should I use, plain utf8 or multibyte utf8mb4?
		$charset = $this->utf8mb4 ? 'utf8mb4' : 'utf8';
		
		$result = @$this->connection->set_charset ( $charset );
		
		/**
		 * If I could not set the utf8mb4 charset then the server doesn't support utf8mb4 despite claiming otherwise.
		 * This happens on old MySQL server versions (less than 5.5.3) using the mysqlnd PHP driver. Since mysqlnd
		 * masks the server version and reports only its own we can not be sure if the server actually does support
		 * UTF-8 Multibyte (i.e. it's MySQL 5.5.3 or later). Since the utf8mb4 charset is undefined in this case we
		 * catch the error and determine that utf8mb4 is not supported!
		 */
		if (! $result && $this->utf8mb4) {
			$this->utf8mb4 = false;
			$result = @$this->connection->set_charset ( 'utf8' );
		}
		
		return $result;
	}
	
	/**
	 * Method to commit a transaction.
	 *
	 * @param boolean $toSavepoint
	 *        	If true, commit to the last savepoint.
	 *        	
	 * @return void
	 *
	 * @since 12.2
	 * @throws RuntimeException
	 */
	public function transactionCommit($toSavepoint = false) {
		$this->connect ();
		
		if (! $toSavepoint || $this->transactionDepth <= 1) {
			if ($this->setQuery ( 'COMMIT' )->execute ()) {
				$this->transactionDepth = 0;
			}
			
			return;
		}
		
		$this->transactionDepth --;
	}
	
	/**
	 * Method to roll back a transaction.
	 *
	 * @param boolean $toSavepoint
	 *        	If true, rollback to the last savepoint.
	 *        	
	 * @return void
	 *
	 * @since 12.2
	 * @throws RuntimeException
	 */
	public function transactionRollback($toSavepoint = false) {
		$this->connect ();
		
		if (! $toSavepoint || $this->transactionDepth <= 1) {
			if ($this->setQuery ( 'ROLLBACK' )->execute ()) {
				$this->transactionDepth = 0;
			}
			
			return;
		}
		
		$savepoint = 'SP_' . ($this->transactionDepth - 1);
		$this->setQuery ( 'ROLLBACK TO SAVEPOINT ' . $this->quoteName ( $savepoint ) );
		
		if ($this->execute ()) {
			$this->transactionDepth --;
		}
	}
	
	/**
	 * Method to initialize a transaction.
	 *
	 * @param boolean $asSavepoint
	 *        	If true and a transaction is already active, a savepoint will be created.
	 *        	
	 * @return void
	 *
	 * @since 12.2
	 * @throws RuntimeException
	 */
	public function transactionStart($asSavepoint = false) {
		$this->connect ();
		
		if (! $asSavepoint || ! $this->transactionDepth) {
			if ($this->setQuery ( 'START TRANSACTION' )->execute ()) {
				$this->transactionDepth = 1;
			}
			
			return;
		}
		
		$savepoint = 'SP_' . $this->transactionDepth;
		$this->setQuery ( 'SAVEPOINT ' . $this->quoteName ( $savepoint ) );
		
		if ($this->execute ()) {
			$this->transactionDepth ++;
		}
	}
	
	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param mixed $cursor
	 *        	The optional result set cursor from which to fetch the row.
	 *        	
	 * @return mixed Either the next row from the result set or false if there are no more rows.
	 *        
	 * @since 12.1
	 */
	protected function fetchArray($cursor = null) {
		return mysqli_fetch_row ( $cursor ? $cursor : $this->cursor );
	}
	
	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param mixed $cursor
	 *        	The optional result set cursor from which to fetch the row.
	 *        	
	 * @return mixed Either the next row from the result set or false if there are no more rows.
	 *        
	 * @since 12.1
	 */
	protected function fetchAssoc($cursor = null) {
		$query = $this->replacePrefix ( ( string ) $this->sql );
		$this->wpdb->show_errors();
		$result = $this->wpdb->get_row($query);
		
		return $result;
	}
	
	
	public function loadResult(){
	    
	    $query = $this->replacePrefix ( ( string ) $this->sql );

	    $this->wpdb->show_errors();
	    $result = $this->wpdb->get_row($query);
	    
	    return $result;
	}
	
	public function loadAssoc()
	{
		$query = $this->replacePrefix ( ( string ) $this->sql );
		$result = $this->wpdb->get_row($query);
		
		if(empty($result)){
		    return false;
		}
		$this->stripObjectSlashes($result);
		
		return $result;
	}
	
	
	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param mixed $cursor
	 *        	The optional result set cursor from which to fetch the row.
	 * @param string $class
	 *        	The class name to use for the returned row object.
	 *        	
	 * @return mixed Either the next row from the result set or false if there are no more rows.
	 *        
	 * @since 12.1
	 */
	protected function fetchObject($cursor = null, $class = 'stdClass') {
		return mysqli_fetch_object ( $cursor ? $cursor : $this->cursor, $class );
	}
	
	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param mixed $cursor
	 *        	The optional result set cursor from which to fetch the row.
	 *        	
	 * @return void
	 *
	 * @since 12.1
	 */
	protected function freeResult($cursor = null) {
		mysqli_free_result ( $cursor ? $cursor : $this->cursor );
		
		if ((! $cursor) || ($cursor === $this->cursor)) {
			$this->cursor = null;
		}
	}
	
	/**
	 * Unlocks tables in the database.
	 *
	 * @return JDatabaseDriverMysqli Returns this object to support chaining.
	 *        
	 * @since 12.1
	 * @throws RuntimeException
	 */
	public function unlockTables() {
		$this->setQuery ( 'UNLOCK TABLES' )->execute ();
		
		return $this;
	}
	
	/**
	 * Internal function to check if profiling is available
	 *
	 * @return boolean
	 *
	 * @since 3.1.3
	 */
	private function hasProfiling() {
		try {
			$res = mysqli_query ( $this->connection, "SHOW VARIABLES LIKE 'have_profiling'" );
			$row = mysqli_fetch_assoc ( $res );
			
			return isset ( $row );
		} catch ( Exception $e ) {
			return false;
		}
	}
	
	/**
	 * Does the database server claim to have support for UTF-8 Multibyte (utf8mb4) collation?
	 *
	 * libmysql supports utf8mb4 since 5.5.3 (same version as the MySQL server). mysqlnd supports utf8mb4 since 5.0.9.
	 *
	 * @return boolean
	 *
	 * @since CMS 3.5.0
	 */
	private function serverClaimsUtf8mb4Support() {
		$client_version = mysqli_get_client_info ();
		$server_version = $this->getVersion ();
		
		if (version_compare ( $server_version, '5.5.3', '<' )) {
			return false;
		} else {
			if (strpos ( $client_version, 'mysqlnd' ) !== false) {
				$client_version = preg_replace ( '/^\D+([\d.]+).*/', '$1', $client_version );
				
				return version_compare ( $client_version, '5.0.9', '>=' );
			} else {
				return version_compare ( $client_version, '5.5.3', '>=' );
			}
		}
	}
	
	/**
	 * Return the actual SQL Error number
	 *
	 * @return integer The SQL Error number
	 *        
	 * @since 3.4.6
	 */
	protected function getErrorNumber() {
		return ( int ) mysqli_errno ( $this->connection );
	}
	
	/**
	 * Return the actual SQL Error message
	 *
	 * @return string The SQL Error message
	 *        
	 * @since 3.4.6
	 */
	protected function getErrorMessage() {
		$errorMessage = ( string ) mysqli_error ( $this->connection );
		
		// Replace the Databaseprefix with `#__` if we are not in Debug
		if (! $this->debug) {
			$errorMessage = str_replace ( $this->tablePrefix, '#__', $errorMessage );
		}
		
		return $errorMessage;
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see \MVC\Database\JDatabaseDriver::loadColumn()
	 */
	public function loadColumn($offset = 0)
	{

	    $result = $this->execute();
	    $array = array();
	    
	    // Get all of the rows from the result set as arrays.
	    foreach($result as $row)
	    {
	        $rowA = (array)($row);
	        $rowA = array_values($rowA);
	        $array[] = $rowA[$offset];
	    }
	    
	    return $array;
	}

}


