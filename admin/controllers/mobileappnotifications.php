<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * The Company Controller
 *
 */
class JBusinessDirectoryControllerMobileAppNotifications extends JControllerForm {
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 */
	public function display($cachable = false, $urlparams = false) {
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=mobileappnotifications', false));
	}


	public function sendNotifications() {
		$data = JFactory::getApplication()->input->getArray();
		$model = $this->getModel();

		$notification          = array();
		$notification["title"] = $data["notification_title"];
		$notification["body"]  = $data["notification_body"];
		$notification["type"]  = $data["notification_type"];

		if($notification["type"] == NOTIFICATION_TYPE_TOPIC) {
			$result = NotificationService::sendTopicNotifications($notification, $data["notification_topic"]);
		} else {
			$registered = true;
			JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_jbusinessdirectory/tables');
			$table = JTable::getInstance('MobileDevice', 'JTable');
			$receivers = $table->getAllTokens();
			$result = NotificationService::sendGeneralNotification($notification, $receivers, $registered);
		}

		if($result) {
			$msg = JText::_('LNG_NOTIFICATION_SENT_SUCCESS');			
		} else {
			$msg = JText::_('LNG_NOTIFICATION_SENT_ERROR');
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=mobileappnotifications', $msg);
	}

}