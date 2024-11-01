<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JTableUserNotifications extends JTable {

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_user_notifications', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}
	
	public function readNotification($notificationId) {
		$db =JFactory::getDBO();
		$query = " UPDATE #__jbusinessdirectory_user_notifications SET `read` = 1 where id = ".$notificationId;
		$db->setQuery($query);
		return $db->execute();
	}

	public function deleteNotification($notificationId) {
		$db =JFactory::getDBO();
		$sql = "delete from #__jbusinessdirectory_user_notifications where id=$notificationId";
		$db->setQuery($sql);
		return $db->execute();
	}
}
