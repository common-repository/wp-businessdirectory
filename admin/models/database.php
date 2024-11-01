<?php
/**
 * @package     JBD.Administrator
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     https://www.gnu.org/licenses/agpl-3.0.en.html; see LICENSE.txt
 */

defined('_JEXEC') or die;

use MVC\Registry\Registry;

JLoader::register('InstallerModel', __DIR__ . '/extension.php');
JLoader::register('JoomlaInstallerScript', JPATH_ADMINISTRATOR . '/components/com_admin/script.php');

/**
 * Installer Database Model
 *
 * @since  1.6
 */
class JBusinessDirectoryModelDatabase extends JModelList {
	protected $_context = 'com_jbusinessdirectory.database';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'name', $direction = 'asc') {
		$app = JFactory::getApplication();
		$this->setState('message', $app->getUserState('com_installer.message'));
		$this->setState('extension_message', $app->getUserState('com_installer.extension_message'));
		$app->setUserState('com_installer.message', '');
		$app->setUserState('com_installer.extension_message', '');

		
		
		// Prepare the utf8mb4 conversion check table
		$this->prepareUtf8mb4StatusTable();

		parent::populateState($ordering, $direction);
	}

	/**
	 * Fixes database problems.
	 *
	 * @return  void
	 */
	public function fix() {
		// $db = $this->getDbo();
		
		// $installationSQL = $this->getInstallationDBSchema();
		// $installationSQL = $db->replacePrefix($installationSQL);
		
		// $this->dbDelta($installationSQL);
		
		// if (!$changeSet = $this->getItems()) {
		// 	return false;
		// }
		// $this->fixSchemaVersion($changeSet);
		$this->updateDefaultData();
		$this->fixPackageFieldsTable();
		//$this->fixUpdateVersion();	
		//$this->updateLastSchemaCheck();		
		
		return true;
	}

	/**
	 * Update schema version
	 *
	 * @return void
	 */
	public function updateSchemaVersion(){
		if (!$changeSet = $this->getItems()) {
			return false;
		}
		$this->fixSchemaVersion($changeSet);
		return true;
	}
	
	/**
	 * Gets the changeset object.
	 *
	 * @return  JSchemaChangeset
	 */
	public function getInstallationDBSchema() {
		$installationSQLPath = JPATH_COMPONENT_ADMINISTRATOR . '/sql/install.sql';
		$installationSQL="";
		try {
			$installationSQL = file_get_contents($installationSQLPath);
		} catch (RuntimeException $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			return false;
		}
		
		return $installationSQL;
	}
	
	/**
	 * Gets the changeset object.
	 *
	 * @return  JSchemaChangeset
	 */
	public function getItems() {
		$folder = JPATH_COMPONENT_ADMINISTRATOR . '/sql/updates/';
		
		try {
			$changeSet = JSchemaChangeset::getInstance($this->getDbo(), $folder);
		} catch (RuntimeException $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			exit;
			return false;
		}
		
		return $changeSet;
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  boolean
	 *
	 * @since   3.0.1
	 */
	public function getPagination() {
		return true;
	}

	/**
	 * Get version from #__schemas table.
	 *
	 * @return  mixed  the return value from the query, or null if the query fails.
	 *
	 * @throws Exception
	 */
	public function getSchemaVersion() {
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('version_id')
			->from($db->quoteName('#__schemas')." as s")
			->join('LEFT', $db->quoteName('#__extensions')."as e on s.extension_id = e.extension_id")
			->where("e.element = 'com_jbusinessdirectory'");
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Fix schema version if wrong.
	 *
	 * @param   JSchemaChangeSet  $changeSet  Schema change set.
	 *
	 * @return   mixed  string schema version if success, false if fail.
	 */
	public function fixSchemaVersion($changeSet) {
		$db = $this->getDbo();
		// Get correct schema version -- last file in array.
		$schema = $changeSet->getSchema();

		// Check value. If ok, don't do update.
		if ($schema == $this->getSchemaVersion()) {
			return $schema;
		}
		
		// Add new row.
		$query =" update #__schemas set version_id = ". $db->quote($schema)." where extension_id in (select extension_id from #__extensions where element='com_jbusinessdirectory')";
		$db->setQuery($query);
		
		try {
			$db->execute();
		} catch (JDatabaseExceptionExecuting $e) {
			return false;
		}

		return $schema;
	}

	/**
	 * Get current version from #__extensions table.
	 *
	 * @return  mixed   version if successful, false if fail.
	 */
	public function getUpdateVersion() {
		$table = JTable::getInstance('Extension');
		$table->load('700');
		$cache = new Registry($table->manifest_cache);

		return $cache->get('version');
	}

	/**
	 * Fix Joomla version in #__extensions table if wrong (doesn't equal JVersion short version).
	 *
	 * @return   mixed  string update version if success, false if fail.
	 */
	public function fixUpdateVersion() {
		$table = JTable::getInstance('Extension');
		$table->load('700');
		$cache = new Registry($table->manifest_cache);
		$updateVersion = $cache->get('version');
		$cmsVersion = new JVersion;

		if ($updateVersion == $cmsVersion->getShortVersion()) {
			return $updateVersion;
		}

		$cache->set('version', $cmsVersion->getShortVersion());
		$table->manifest_cache = $cache->toString();

		if ($table->store()) {
			return $cmsVersion->getShortVersion();
		}

		return false;
	}

	/**
	 * For version 2.5.x only
	 * Check if com_config parameters are blank.
	 *
	 * @return  string  default text filters (if any).
	 */
	public function getDefaultTextFilters() {
		$table = JTable::getInstance('Extension');
		$table->load($table->find(array('name' => 'com_config')));

		return $table->params;
	}

	/**
	 * For version 2.5.x only
	 * Check if com_config parameters are blank. If so, populate with com_content text filters.
	 *
	 * @return  mixed  boolean true if params are updated, null otherwise.
	 */
	public function fixDefaultTextFilters() {
		$table = JTable::getInstance('Extension');
		$table->load($table->find(array('name' => 'com_config')));

		// Check for empty $config and non-empty content filters.
		if (!$table->params) {
			// Get filters from com_content and store if you find them.
			$contentParams = JComponentHelper::getParams('com_content');

			if ($contentParams->get('filters')) {
				$newParams = new Registry;
				$newParams->set('filters', $contentParams->get('filters'));
				$table->params = (string) $newParams;
				$table->store();

				return true;
			}
		}
	}

	
	public function dbDelta($queries = '', $execute = true) {
		$db = $this->getDbo();
		
		// Separate individual queries into an array
		if (! is_array($queries)) {
			$queries = explode(';', $queries);
			$queries = array_filter($queries);
		}
		
		$cqueries   = array(); // Creation Queries
		$iqueries   = array(); // Insertion Queries
		$for_update = array();
		
		// Create a tablename index for an array ($cqueries) of queries
		foreach ($queries as $qry) {
			if (preg_match('|CREATE TABLE ([^ ]*)|', $qry, $matches)) {
				$cqueries[ trim($matches[1], '`') ] = $qry;
				$for_update[ $matches[1] ]            = 'Created table ' . $matches[1];
			} elseif (preg_match('|CREATE DATABASE ([^ ]*)|', $qry, $matches)) {
				array_unshift($cqueries, $qry);
			} elseif (preg_match('|INSERT INTO ([^ ]*)|', $qry, $matches)) {
				$iqueries[] = $qry;
			} elseif (preg_match('|UPDATE ([^ ]*)|', $qry, $matches)) {
				$iqueries[] = $qry;
			} else {
				// Unrecognized query type
			}
		}
		
		$text_fields = array( 'tinytext', 'text', 'mediumtext', 'longtext' );
		$blob_fields = array( 'tinyblob', 'blob', 'mediumblob', 'longblob' );
		
		foreach ($cqueries as $table => $qry) {
			$tablefields = null;
			//avoid error when table doesn't exist.
			try {
				$db->setQuery("Describe $table");
				$tablefields = $db->loadObjectList();
			} catch (Exception $e) {
				//do nothing
			}
			
			if (! $tablefields) {
				continue;
			}
			
			// Clear the field and index arrays.
			$cfields = $indices = $indices_without_subparts = array();
			
			// Get all of the field names in the query from between the parentheses.
			preg_match('|\((.*)\)|ms', $qry, $match2);
			$qryline = trim($match2[1]);
			
			// Separate field lines into an array.
			$flds = explode("\n", $qryline);
			
			// For every field line specified in the query.
			foreach ($flds as $fld) {
				$fld = trim($fld, " \t\n\r\0\x0B,"); // Default trim characters, plus ','.
				
				// Extract the field name.
				preg_match('|^([^ ]*)|', $fld, $fvals);
				$fieldname            = trim($fvals[1], '`');
				$fieldname_lowercased = strtolower($fieldname);
				
				// Verify the found field name.
				$validfield = true;
				switch ($fieldname_lowercased) {
					case '':
					case 'primary':
					case 'index':
					case 'fulltext':
					case 'unique':
					case 'key':
					case 'spatial':
						$validfield = false;
						
						/*
						 * Normalize the index definition.
						 *
						 * This is done so the definition can be compared against the result of a
						 * `SHOW INDEX FROM $table_name` query which returns the current table
						 * index information.
						 */
						
						// Extract type, name and columns from the definition.
						// phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
						preg_match(
							'/^'
							.   '(?P<index_type>'             // 1) Type of the index.
							.       'PRIMARY\s+KEY|(?:UNIQUE|FULLTEXT|SPATIAL)\s+(?:KEY|INDEX)|KEY|INDEX'
							.   ')'
							.   '\s+'                         // Followed by at least one white space character.
							.   '(?:'                         // Name of the index. Optional if type is PRIMARY KEY.
							.       '`?'                      // Name can be escaped with a backtick.
							.           '(?P<index_name>'     // 2) Name of the index.
							.               '(?:[0-9a-zA-Z$_-]|[\xC2-\xDF][\x80-\xBF])+'
							.           ')'
							.       '`?'                      // Name can be escaped with a backtick.
							.       '\s+'                     // Followed by at least one white space character.
							.   ')*'
							.   '\('                          // Opening bracket for the columns.
							.       '(?P<index_columns>'
							.           '.+?'                 // 3) Column names, index prefixes, and orders.
							.       ')'
							.   '\)'                          // Closing bracket for the columns.
							. '$/im',
							$fld,
							$index_matches
						);
						// phpcs:enable
						
						// Uppercase the index type and normalize space characters.
						$index_type = strtoupper(preg_replace('/\s+/', ' ', trim($index_matches['index_type'])));
						
						// 'INDEX' is a synonym for 'KEY', standardize on 'KEY'.
						$index_type = str_replace('INDEX', 'KEY', $index_type);
						
						// Escape the index name with backticks. An index for a primary key has no name.
						$index_name = ('PRIMARY KEY' === $index_type) ? '' : '`' . strtolower($index_matches['index_name']) . '`';
						
						// Parse the columns. Multiple columns are separated by a comma.
						$index_columns = $index_columns_without_subparts = array_map('trim', explode(',', $index_matches['index_columns']));
						
						// Normalize columns.
						foreach ($index_columns as $id => &$index_column) {
							// Extract column name and number of indexed characters (sub_part).
							preg_match(
								'/'
								. '`?'                      // Name can be escaped with a backtick.
								. '(?P<column_name>'    // 1) Name of the column.
								. '(?:[0-9a-zA-Z$_-]|[\xC2-\xDF][\x80-\xBF])+'
								. ')'
								. '`?'                      // Name can be escaped with a backtick.
								. '(?:'                     // Optional sub part.
								. '\s*'                 // Optional white space character between name and opening bracket.
								. '\('                  // Opening bracket for the sub part.
								. '\s*'             // Optional white space character after opening bracket.
								. '(?P<sub_part>'
								. '\d+'         // 2) Number of indexed characters.
								. ')'
								. '\s*'             // Optional white space character before closing bracket.
								. '\)'                 // Closing bracket for the sub part.
								. ')?'
								. '/',
								$index_column,
								$index_column_matches
							);
							
							// Escape the column name with backticks.
							$index_column = '`' . $index_column_matches['column_name'] . '`';
							
							// We don't need to add the subpart to $index_columns_without_subparts
							$index_columns_without_subparts[ $id ] = $index_column;
							
							// Append the optional sup part with the number of indexed characters.
							if (isset($index_column_matches['sub_part'])) {
								$index_column .= '(' . $index_column_matches['sub_part'] . ')';
							}
						}
						
						// Build the normalized index definition and add it to the list of indices.
						$indices[]                  = "{$index_type} {$index_name} (" . implode(',', $index_columns) . ')';
						$indices_without_subparts[] = "{$index_type} {$index_name} (" . implode(',', $index_columns_without_subparts) . ')';
						
						// Destroy no longer needed variables.
						unset($index_column, $index_column_matches, $index_matches, $index_type, $index_name, $index_columns, $index_columns_without_subparts);
						
						break;
				}
				
				// If it's a valid field, add it to the field array.
				if ($validfield) {
					$cfields[ $fieldname_lowercased ] = $fld;
				}
			}
			
			
			// For every field in the table.
			foreach ($tablefields as $tablefield) {
				$tablefield_field_lowercased = strtolower($tablefield->Field);
				$tablefield_type_lowercased  = strtolower($tablefield->Type);
				
				// If the table field exists in the field array ...
				if (array_key_exists($tablefield_field_lowercased, $cfields)) {
					// Get the field type from the query.
					preg_match('|`?' . $tablefield->Field . '`? ([^ ]*( unsigned)?)|i', $cfields[ $tablefield_field_lowercased ], $matches);
					$fieldtype            = $matches[1];
					$fieldtype_lowercased = strtolower($fieldtype);
					
					// Is actual field type different from the field type in query?
					if ($tablefield->Type != $fieldtype) {
						$do_change = true;
						if (in_array($fieldtype_lowercased, $text_fields) && in_array($tablefield_type_lowercased, $text_fields)) {
							if (array_search($fieldtype_lowercased, $text_fields) < array_search($tablefield_type_lowercased, $text_fields)) {
								$do_change = false;
							}
						}
						
						if (in_array($fieldtype_lowercased, $blob_fields) && in_array($tablefield_type_lowercased, $blob_fields)) {
							if (array_search($fieldtype_lowercased, $blob_fields) < array_search($tablefield_type_lowercased, $blob_fields)) {
								$do_change = false;
							}
						}
						
						if ($do_change) {
							// Add a query to change the column type.
							$cqueries[]                                      = "ALTER TABLE {$table} CHANGE COLUMN `{$tablefield->Field}` " . $cfields[ $tablefield_field_lowercased ];
							$for_update[ $table . '.' . $tablefield->Field ] = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
						}
					}
					
					// Get the default value from the array.
					if (preg_match("| DEFAULT '(.*?)'|i", $cfields[ $tablefield_field_lowercased ], $matches)) {
						$default_value = $matches[1];
						if ($tablefield->Default != $default_value) {
							// Add a query to change the column's default value
							$cqueries[]                                      = "ALTER TABLE {$table} ALTER COLUMN `{$tablefield->Field}` SET DEFAULT '{$default_value}'";
							$for_update[ $table . '.' . $tablefield->Field ] = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
						}
					}
					
					// Remove the field from the array (so it's not added).
					unset($cfields[ $tablefield_field_lowercased ]);
				} else {
					// This field exists in the table, but not in the creation queries?
				}
			}
			
			// For every remaining field specified for the table.
			foreach ($cfields as $fieldname => $fielddef) {
				// Push a query line into $cqueries that adds the field to that table.
				$cqueries[]                              = "ALTER TABLE {$table} ADD COLUMN $fielddef";
				$for_update[ $table . '.' . $fieldname ] = 'Added column ' . $table . '.' . $fieldname;
			}
			
			$db->setQuery("SHOW INDEX FROM $table");
			$tableindices = $db->loadObjectList();
			
			if ($tableindices) {
				// Clear the index array.
				$index_ary = array();
				
				// For every index in the table.
				foreach ($tableindices as $tableindex) {
					// Add the index to the index data array.
					$keyname                             = strtolower($tableindex->Key_name);
					$index_ary[ $keyname ]['columns'][]  = array(
						'fieldname' => $tableindex->Column_name,
						'subpart'   => $tableindex->Sub_part,
					);
					$index_ary[ $keyname ]['unique']     = ($tableindex->Non_unique == 0) ? true : false;
					$index_ary[ $keyname ]['index_type'] = $tableindex->Index_type;
				}
				
				// For each actual index in the index array.
				foreach ($index_ary as $index_name => $index_data) {
					// Build a create string to compare to the query.
					$index_string = '';
					if ($index_name == 'primary') {
						$index_string .= 'PRIMARY ';
					} elseif ($index_data['unique']) {
						$index_string .= 'UNIQUE ';
					}
					if ('FULLTEXT' === strtoupper($index_data['index_type'])) {
						$index_string .= 'FULLTEXT ';
					}
					if ('SPATIAL' === strtoupper($index_data['index_type'])) {
						$index_string .= 'SPATIAL ';
					}
					$index_string .= 'KEY ';
					if ('primary' !== $index_name) {
						$index_string .= '`' . $index_name . '`';
					}
					$index_columns = '';
					
					// For each column in the index.
					foreach ($index_data['columns'] as $column_data) {
						if ($index_columns != '') {
							$index_columns .= ',';
						}
						
						// Add the field to the column list string.
						$index_columns .= '`' . $column_data['fieldname'] . '`';
					}
					
					// Add the column list to the index create string.
					$index_string .= " ($index_columns)";
					
					// Check if the index definition exists, ignoring subparts.
					if (! (($aindex = array_search($index_string, $indices_without_subparts)) === false)) {
						// If the index already exists (even with different subparts), we don't need to create it.
						unset($indices_without_subparts[ $aindex ]);
						unset($indices[ $aindex ]);
					}
				}
			}
			
			// For every remaining index specified for the table.
			foreach ((array) $indices as $index) {
				// Push a query line into $cqueries that adds the index to that table.
				$cqueries[]   = "ALTER TABLE {$table} ADD $index";
				$for_update[] = 'Added index ' . $table . ' ' . $index;
			}
			
			// Remove the original table creation query from processing.
			unset($cqueries[ $table ], $for_update[ $table ]);
		}
		
		$allqueries = array_merge($cqueries);

		if ($execute && !empty($allqueries)) {			
			foreach ($allqueries as $query) {
				try {
					if(!empty($query)){
						$db->setQuery($query);
						$tablefields = $db->execute();
					}
				} catch (Exception $e) {
					//print_r($e);
				}
			}
		}

		return $allqueries;
	}

	/**
	 * Update the database schema to the latest update sql version
	 *
	 * @return void
	 */
	public function updateLastSchemaCheck() {

		$db = $this->getDbo();
		$schema = $this->getSchemaVersion();
		$query = "INSERT INTO #__jbusinessdirectory_application_settings (`name`, `value`, `text`, `description`) VALUES 
					('last_schema_check_version', '".$schema."', 'LNG_SCHEMA_CHECK_VERSION', 'LNG_SCHEMA_CHECK_VERSION_DESC')";
		$query .= " ON DUPLICATE KEY UPDATE value=values(value)";
		
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}

		return true;
	}

	/** 
	 * Update default data
	 */
	public function updateDefaultData(){
		$db = $this->getDbo();

		$updateSQLPath = JPATH_COMPONENT_ADMINISTRATOR . '/sql/update_default.sql';
		$updateSQL="";
		try {
			$updateSQL = file_get_contents($updateSQLPath);
			$updateSQLs = explode(";",$updateSQL);
		
			$updateSQLs = array_filter($updateSQLs);
			foreach($updateSQLs as $sql){
				$sql = trim($sql);
				if(!empty($sql)){
					$db->setQuery($sql);
					if (!$db->execute()) {
						return false;
					}
				}
			}
		} catch (RuntimeException $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			return false;
		}
	}

	/** 
	 * Check #__jbusinessdirectory_package_fields table if 'int' column exists and rename it to 'id'
	 */
	public function fixPackageFieldsTable(){
		$db = $this->getDbo();
		$columns = $db->getTableColumns('#__jbusinessdirectory_package_fields');
		$keys = array_keys($columns);

		if (in_array("int", $keys))
		{
			$query = "ALTER TABLE `#__jbusinessdirectory_package_fields` 
						CHANGE COLUMN `int` `id` INT(11) NOT NULL AUTO_INCREMENT";
			$db->setQuery($query);
			if (!$db->execute()) {
				return false;
			}
		}
	}

}
