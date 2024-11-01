<?php
/**
 * @package    JBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS . 'components' . 'com_jbusinessdirectory' . DS . 'tables');
JTable::addIncludePath(DS . 'components' . 'com_jbusinessdirectory' . DS . 'models');

class JBusinessDirectoryControllerManageScheduledNotifications extends JControllerLegacy {

	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->log = Logger::getInstance();
	}

	public function sendScheduledPushNotifications() {
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_jbusinessdirectory/tables');
		$table = JTable::getInstance('ScheduledNotifications', 'JTable');
		
		$batchSize = 50; // Number of notifications to process at once
	
		// Process insurance and inspection notifications in one loop
		foreach (['insurance', 'inspection'] as $type) {
			$notifications = $table->{"get{$type}NotifyTokens"}();
			
			$startIndex = 0;
			$endIndex = $batchSize;
			
			while ($startIndex < count($notifications)) {
				$batch = array_slice($notifications, $startIndex, $batchSize);
	
				foreach ($batch as $notification) {
					$notification["title"] = str_replace('{plate_number}', $notification['plate_number'], $notification['title']);
					$notification["title"] = str_replace('{expiration_days}', $notification['expiration_days'], $notification['title']);
					$notification["body"] = str_replace('{plate_number}', $notification['plate_number'], $notification['body']);
					$notification["body"] = str_replace('{expiration_days}', $notification['expiration_days'], $notification['body']);
					NotificationService::sendGeneralNotification($notification, $notification['tokens']);
				}
	
				$startIndex = $endIndex;
				$endIndex += $batchSize;
			}
		}
	}
	
}
