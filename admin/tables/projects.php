<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

use MVC\Utilities\ArrayHelper;

class TableProjects extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_company_projects', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}


	public function getCompanyProjects($companyId, $limitstart = 0, $limit = 0) {
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);

		$query = "select co.*, GROUP_CONCAT( DISTINCT op.id,'|',op.projectId,'|',op.picture_info,'|',op.picture_path,'|',op.picture_enable ORDER BY op.id separator '#|') as pictures
					from #__jbusinessdirectory_company_projects co
					left join  #__jbusinessdirectory_company_projects_pictures op on co.id=op.projectId and op.picture_enable = 1
					where company_id=$companyId and status=1
					group by co.id
					order by co.id";

		//echo($query);
		$db->setQuery($query, $limitstart, $limit);
		$result = $db->loadObjectList();
		//dump($result);
		//dump($this->_db->getError());
		return $result;
	}

	public function getCompanyTotalProjects($companyId, $id) {
		
		$db =JFactory::getDBO();
		$companyId = $db->escape($companyId);

		$query = "select count(*) as cnt
					from #__jbusinessdirectory_company_projects co
					where company_id=$companyId and id != $id";

		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result->cnt;
	}

	public function getProject($projectId) {
		$db =JFactory::getDBO();
		$query = "select co.*, GROUP_CONCAT( DISTINCT op.id,'|',op.projectId,'|',op.picture_info,'|',op.picture_path,'|',op.picture_enable ORDER BY op.id separator '#|') as pictures
					from #__jbusinessdirectory_company_projects co
					left join  #__jbusinessdirectory_company_projects_pictures op on co.id=op.projectId  and op.picture_enable = 1
					where co.id=$projectId
					group by co.id
					order by co.id";
		$db->setQuery($query);
		//dump($query);
		return $db->loadObject();
	}

	public function changeState($id, $value) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_company_projects SET status = '$value' WHERE id = ".$id ;
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table. The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.  If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success.
	 *
	 */
	public function publish($pks = null, $state = 1, $userId = 0) {
		$k = $this->_tbl_key;

		// Sanitize input.
		ArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks)) {
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
			$checkin = '';
		} else {
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		// Update the publishing state for rows with the given primary keys.
		$query = $this->_db->getQuery(true)
				->update($this->_db->quoteName($this->_tbl))
				->set($this->_db->quoteName('status') . ' = ' . (int) $state)
				->where('(' . $where . ')' . $checkin);
		$this->_db->setQuery($query);

		try {
			$this->_db->execute();
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
			return false;
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks)) {
			$this->status = $state;
		}

		$this->setError('');

		return true;
	}

	public function deleteAllDependencies($itemId) {
		$db =JFactory::getDBO();
		$sql = "delete from #__jbusinessdirectory_company_projects_pictures where projectId = $itemId";
		$db->setQuery($sql);
		$db->execute();

		return true;
	}
}
