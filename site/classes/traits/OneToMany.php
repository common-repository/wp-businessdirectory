<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

trait OneToMany
{
	protected $oneToManyPrefix = "onetomany";

	/**
	 * Deletes existing records and creates new children entries related to a parent entity.
	 *
	 * @param $params array of params
	 * [
	 *  `parentId`                    int    ID of the parent entity
	 *  `parentField`                 string name of the foreign key to the parent entity
	 *  `childrenTable`               string name of the children table without (#__jbusinessdirectory_) prefix
	 *  `childrenTableInstanceName`   string [optional] name of the Joomla Table Instance for the child entity
	 *  `childrenTableInstancePrefix` string [optional] name of the Joomla Table Instance prefix (defaults to JTable)
	 * ]
	 *
	 * @param $data array of input data, children fields must be formatted like `$oneToManyPrefix_$childrenTable_$fieldName`
	 *
	 * @return bool
	 * @throws Exception
	 *
	 * @since 5.5.0
	 */
	public function saveOneToManyChildren($params, $data) {
		try {
			$params = $this->parseOneToManyParams($params);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}

		$childrenTable = $params['childrenTable'];
		$parentField = $params['parentField'];
		$parentId = $params['parentId'];
		$childrenTableInstanceName = $params['childrenTableInstanceName'];
		$childrenTableInstancePrefix = $params['childrenTableInstancePrefix'];

		// delete existing child entries related to parent entity
		try {
			$this->deleteOneToManyChildren($parentId, $parentField, $childrenTable);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}

		$fields = [];
		$columns = [];

		// Parse input data and get the fields associated to the child entity
		foreach ($data as $key => $items) {
			if (strpos($key, $this->oneToManyPrefix."_".$childrenTable) !== false) {
				$fieldName = $this->getOneToManyFieldName($key, $childrenTable);
				$columns[] = $fieldName;
				$fields[] = $key;
			}
		}

		// create new entries based on all the input data
		if(!empty($data[$fields[0]])){
			$n = count($data[$fields[0]]);
			for ($i = 0; $i < $n; $i++) {
				$table = $this->getTable($childrenTableInstanceName, $childrenTableInstancePrefix);

				foreach ($fields as $k => $field) {
					$fieldName = $columns[$k];
					$table->$fieldName = $data[$field][$i];
				}

				$table->$parentField = $parentId;

				if (!$table->check()) {
					throw new Exception($table->getError());
				}
				if (!$table->store()) {
					throw new Exception($table->getError());
				}
			}
		}

		return true;
	}

	/**
	 * Deletes all existing child entries related to the parent entity
	 *
	 * @param $parentId    int     ID of the parent
	 * @param $parentField string  name of the foreign key pointing to the parent entity
	 * @param $table       string  name of the child table
	 *
	 * @return mixed
	 * @throws Exception
	 *
	 * @since 5.5.0
	 */
	public function deleteOneToManyChildren($parentId, $parentField, $table) {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$conditions = [
			$db->quoteName($parentField). " = $parentId"
		];

		$query->delete($db->quoteName("#__jbusinessdirectory_".$table));
		$query->where($conditions);

		try {
			$db->setQuery($query);
			$result = $db->execute();
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}

		return $result;
	}

	/**
	 * Parse the actual field name for the children from the raw data input
	 *
	 * @param $rawField string raw field name for the child entity ($prefix_$table_$field)
	 * @param $table    string name of the child table
	 *
	 * @return bool|string
	 *
	 * @since 5.5.0
	 */
	private function getOneToManyFieldName($rawField, $table) {
		$prefixLength = strlen($this->oneToManyPrefix);
		$fieldName = substr($rawField, $prefixLength + strlen($table) + 2);

		return $fieldName;
	}

	/**
	 * Check all params, throws error if mandatory params are missing and creates default values for the
	 * optional params if not supplied.
	 *
	 * @param $params array
	 *
	 * @return array of params
	 * @throws Exception
	 *
	 * @since 5.5.0
	 */
	private function parseOneToManyParams($params) {
		if (!isset($params['childrenTable'])) {
			throw new Exception('Children Table param not specified!');
		}

		if (!isset($params['parentField'])) {
			throw new Exception('Parent Field param not specified!');
		}

		if (!isset($params['parentId'])) {
			throw new Exception('Parent ID param not specified!');
		}

		if (!isset($params['childrenTableInstanceName'])) {
			$tmp = explode('_', $params['childrenTable']);
			$params['childrenTableInstanceName'] = ucfirst($tmp[0]).''.ucfirst($tmp[1]);
		}

		if (!isset($params['childrenTableInstancePrefix'])) {
			$params['childrenTableInstancePrefix'] = 'JTable';
		}

		return $params;
	}

}