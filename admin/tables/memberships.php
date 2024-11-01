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

class TableMemberships extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_memberships', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getAllMemberships() {
		$db = JFactory::getDBO();
		$query = "select id as value, name as text from #__jbusinessdirectory_memberships 
                    where status = 1
                     order by name";
		$db->setQuery($query);
		return $db->loadObjectList();
	}


	public function getSelectedMembershipsList($companyId) {
		$db =JFactory::getDBO();
		$query = "select membership_id from #__jbusinessdirectory_company_membership  cc
                    inner join #__jbusinessdirectory_memberships c  on cc.membership_id=c.id  
                  where company_id=".$companyId;
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach ($list as $item) {
			$result[]=$item->membership_id;
		}

		return $result;
	}


	public function insertRelations($companyId, $membershipIds) {
		$db =JFactory::getDBO();
		if (empty($membershipIds)) {
			$query = "delete from #__jbusinessdirectory_company_membership where company_id =$companyId";
			$db->setQuery($query);
			if (!$db->execute()) {
				echo 'INSERT / UPDATE sql STATEMENT error !';
				return false;
			}
			return true;
		}

		$query = "insert into #__jbusinessdirectory_company_membership(company_id, membership_id) values ";
		foreach ($membershipIds as $membershipId) {
			$query = $query."(".$companyId.",".$membershipId."),";
		}
		$query =substr($query, 0, -1);
		$query = $query." ON DUPLICATE KEY UPDATE company_id=values(company_id), membership_id=values(membership_id) ";

		$db->setQuery($query);

		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		$filter ="(";
		foreach ($membershipIds as $membershipId) {
			$filter = $filter.$membershipId.",";
		}
		$filter =substr($filter, 0, -1);
		$filter = $filter.")";
		$query = "delete from #__jbusinessdirectory_company_membership where company_id =$companyId and membership_id not in $filter ";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}

		return true;
	}

	public function getCompanyMemberships($companyId) {
		$db        = JFactory::getDbo();
		$companyId = $db->escape($companyId);

		if (empty($companyId)) {
			return null;
		}
		
		$query = "SELECT c.* 
				  FROM #__jbusinessdirectory_memberships c
				  LEFT JOIN #__jbusinessdirectory_company_membership cc ON c.id=cc.membership_id
				  WHERE company_id=" . $companyId . " AND c.status = 1 AND c.show_in_front = 1
				  ORDER BY c.type,c.name";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}

	public function changeState($id, $value) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_memberships SET status = '$value' WHERE id = ".$id ;
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
}
