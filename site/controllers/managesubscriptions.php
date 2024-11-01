<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\Utilities\ArrayHelper;

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'subscriptions.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'subscriptions.php');

class JBusinessDirectoryControllerManageSubscriptions extends JBusinessDirectoryControllerSubscriptions {

	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->log = Logger::getInstance();
	}

		/**
	 * Cancel subscription
	 *
	 * @return void
	 */
	public function cancelSubscription() {
		$app = JFactory::getApplication();

		$subscriptionId = $app->input->getInt('subscriptionId');
		try {
			SubscriptionService::cancelSubscription($subscriptionId);

			$this->setMessage(JText::_('LNG_SUBSCRIPTION_CANCELED'));
		} catch (Exception $e) {
			$this->setMessage($e->getMessage());
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=managesubscriptions');
	}
}