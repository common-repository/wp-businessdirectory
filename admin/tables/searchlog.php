<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JTableSearchLog extends JTable {
	/**
	 * Constructor
	 *
	 * @param JDatabaseDriver $db Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_search_logs', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getLogsToArchive($limitstart = 0, $limit = 0) {
		$db    = JFactory::getDBO();

		$query = "select max(id) as last_id, item_type, object_type,date, count(*) as item_count,has_text,value
                    from #__jbusinessdirectory_search_logs
                    GROUP BY object_type,item_type,date,has_text,value";

		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function archiveSearchLogs($dataToArchive) {
		$db =JFactory::getDBO();
		$query = "insert into #__jbusinessdirectory_search_logs_archive(item_type,object_type,date,has_text,value,item_count) values ";
		foreach ($dataToArchive as $data) {
			$query = $query . "('" . $db->escape($data->item_type) . "','" . $db->escape($data->object_type) . "','" . $db->escape($data->date) . "','" . $db->escape($data->has_text) . "','" . $db->escape($data->value) . "','" . $db->escape($data->item_count) . "'),";
		}
		$query = substr($query, 0, -1);

		$query .= "on duplicate key update item_count = item_count + $data->item_count";

		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		$id = end($dataToArchive)->last_id;
		$query = "delete from #__jbusinessdirectory_search_logs where id <= $id";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		return true;
	}

	/**
	 * delete item by its ID
	 *
	 * @param null $id
	 * @return bool
	 * @since 5.4.0
	 */
	public function delete($id = null) {
		$db =JFactory::getDBO();
		$query = 'delete from #__jbusinessdirectory_search_logs_archive where id = ' .$id ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	public function getTotalNumberOfSearchLogs() {
        $db =JFactory::getDBO();
        $query = "select count(*) as nr_logs from #__jbusinessdirectory_search_logs ";
        $db->setQuery($query);
        $result = $db->loadResult();

		return (int)$result->nr_logs;
    }

	public function deleteByDate($startDate, $endDate){
		$db =JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_search_logs_archive where date <= '$endDate'
				  AND date >= '$startDate'";
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

}
