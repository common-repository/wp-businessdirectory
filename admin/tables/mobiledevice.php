<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableMobileDevice extends JTable {
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 5.0.0
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_mobile_devices', 'id', $db);
	}


	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function updatePushNotification($enable, $token) {
		$db = JFactory::getDbo();
		$token = $db->escape($token);

		$query = "update #__jbusinessdirectory_mobile_devices set enable_push_notifications = $enable where token = '$token'";

		$db->setQuery($query);
		return $db->execute();
	}

	public function updateFirebaseToken($token, $firebaseToken) {
		$db = JFactory::getDbo();
		$token = $db->escape($token);

		$query = "update #__jbusinessdirectory_mobile_devices set firebase_token = '$firebaseToken' where token = '$token'";

		$db->setQuery($query);
		return $db->execute();
	}

	public function deleteDeviceByToken($token) {
		$db = JFactory::getDbo();
		$token = $db->escape($token);

		$query = "delete from #__jbusinessdirectory_mobile_devices where token = '$token'";

		$db->setQuery($query);
		return $db->execute();
	}

	public function getDevicesByUser($userId) {
		$db = JFactory::getDbo();

		$query = "select * from #__jbusinessdirectory_mobile_devices where user_id=$userId";
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function getDeviceByToken($token) {
		$db = JFactory::getDbo();

		$query = "select * from #__jbusinessdirectory_mobile_devices where token = '$token'";
		$db->setQuery($query);

		return $db->loadObject();
	}

	public function getDeviceByDeviceId($deviceId) {
		$db = JFactory::getDbo();
		$deviceId = $db->escape($deviceId);

		$query = "select * from #__jbusinessdirectory_mobile_devices where device_id = '$deviceId'";
		$db->setQuery($query);

		return $db->loadObject();
	}
	
	public function getAllTokens() {
		$db = JFactory::getDbo();
		$query = "SELECT firebase_token FROM #__jbusinessdirectory_mobile_devices";
		$db->setQuery($query);
		$results = $db->loadObjectList();
		$tokens = array_map(function($result) {
			return $result->firebase_token;
		}, $results);
		return $tokens;
	}
}
