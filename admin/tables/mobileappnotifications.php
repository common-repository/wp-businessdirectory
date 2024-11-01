<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableMobileAppNotifications extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_mobile_app_notifications', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}


	public function getNotificationsByUser($userId, $limitstart, $limit) {
		$db = JFactory::getDbo();
	
		$query = "
			SELECT mn.*, un.`read`, un.notification_id, un.id AS id, COUNT(un2.id) AS unread_notifications
			FROM #__jbusinessdirectory_mobile_app_notifications mn
			LEFT JOIN #__jbusinessdirectory_user_notifications un ON mn.id = un.notification_id
			LEFT JOIN #__jbusinessdirectory_user_notifications un2 ON un2.`read` = 0 AND un2.user_id = $userId AND un2.notification_id = mn.id
			LEFT JOIN #__users u ON u.id = un.user_id
			WHERE un.user_id = $userId AND u.registerDate < mn.created
			GROUP BY mn.id
			ORDER BY un.id DESC";
		$db->setQuery($query, $limitstart, $limit);

		return $db->loadObjectList();
	}
	
	

	public function storeUserNotifications($notificationId, $firebaseTokens, $registered, $userId) {

		$db = JFactory::getDbo();

		if ($userId !== null) {
			$userIds = array($userId);
		} else if(!$registered) {
			$query = "select distinct user_id from #__jbusinessdirectory_mobile_devices WHERE firebase_token IN ('".implode("','", $firebaseTokens)."')";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			$userIds = array_map(function($result) {
				return $result->user_id;
			}, $results);
		} else {
			$results = JBusinessUtil::getAllUsers();
			$userIds = array_map(function($result) {
				return $result->value;
			}, $results);
		}

		foreach($userIds as $userId) {
			$insertUserNotificationsQuery = "insert into #__jbusinessdirectory_user_notifications (notification_id, user_id) VALUES  ($notificationId, $userId)";
			$db->setQuery($insertUserNotificationsQuery);
            $db->execute();
		}
	}
}
