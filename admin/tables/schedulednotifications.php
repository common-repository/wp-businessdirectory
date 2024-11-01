<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableScheduledNotifications extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_scheduled_notifications', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}


	// Todo: Change the query based on type to check for vehicle_insurance_expiration_date or garage
	public function getInsuranceNotifyTokens() {
		$db = JFactory::getDBO();

		$query = "
			SELECT * FROM #__jbusinessdirectory_scheduled_notifications s
			WHERE s.type = ".SCHEDULED_NOTIFICATION_TYPE_INSURANCE_EXPIRATION."
			AND s.status = ".NOTIFICATION_STATUS_ACTIVE."";
		$db->setQuery($query);
		$notifications = $db->loadAssocList();

		$groups = array();
		foreach($notifications as $notification) {
			$frequency = $notification['frequency'];
			$query = "
				SELECT v.*, d.firebase_token , IF(v.vehicle_insurance_expiration_date = CURDATE(), 0, DATEDIFF(v.vehicle_insurance_expiration_date, CURDATE())) AS expiration_days
				FROM #__jbusinessdirectory_vehicles AS v
				LEFT JOIN #__jbusinessdirectory_mobile_devices d on d.user_id = v.user_id
				WHERE d.enable_push_notifications <> 0
				HAVING expiration_days in ($frequency)
			";
			$db->setQuery($query);
			$vehicles = $db->loadAssocList();

			foreach($vehicles as $row) {
				$id = $row['id'];
				if (!isset($groups[$id])) {
					$groups[$id] = array(
						'name' => $notification['name'],
						'plate_number' => $row['plate_number'],
						'title' => $notification['title'],
						'body' => $notification['body'],
						'type' => $notification['type'],
						'status' => $notification['status'],
						'frequency' => $notification['frequency'],
						'created' => $notification['created'],
						'tokens' => array(),
					);
				}

				$tokens = explode(',', $row['firebase_token']);
				$groups[$id]['tokens'] = array_merge($groups[$id]['tokens'], $tokens);
				$groups[$id]['expiration_days'] = $row['expiration_days'];
			}
		}

		return $groups;
	}


	public function getInspectionNotifyTokens() {
		$db = JFactory::getDBO();

		$query = "
			SELECT * FROM #__jbusinessdirectory_scheduled_notifications s
			WHERE s.type = ".SCHEDULED_NOTIFICATION_TYPE_INPECTION_EXPIRATION."
			AND s.status = ".NOTIFICATION_STATUS_ACTIVE."";
		$db->setQuery($query);
		$notifications = $db->loadAssocList();

		$groups = array();
		foreach($notifications as $notification) {
			$frequency = $notification['frequency'];
			$query = "
				SELECT v.*, d.firebase_token , IF(v.pig_next_date = CURDATE(), 0, DATEDIFF(v.pig_next_date, CURDATE())) AS expiration_days
				FROM #__jbusinessdirectory_vehicles AS v
				LEFT JOIN #__jbusinessdirectory_mobile_devices d on d.user_id = v.user_id
				WHERE d.enable_push_notifications <> 0
				HAVING expiration_days in ($frequency)
			";
			$db->setQuery($query);
			$vehicles = $db->loadAssocList();

			foreach($vehicles as $row) {
				$id = $row['id'];
				if (!isset($groups[$id])) {
					$groups[$id] = array(
						'name' => $notification['name'],
						'plate_number' => $row['plate_number'],
						'title' => $notification['title'],
						'body' => $notification['body'],
						'type' => $notification['type'],
						'status' => $notification['status'],
						'frequency' => $notification['frequency'],
						'created' => $notification['created'],
						'tokens' => array(),
					);
				}

				$tokens = explode(',', $row['firebase_token']);
				$groups[$id]['tokens'] = array_merge($groups[$id]['tokens'], $tokens);
				$groups[$id]['expiration_days'] = $row['expiration_days'];
			}
		}

		return $groups;
	}
}
