<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
use MVC\Plugin\PluginHelper;
use MVC\Factory;

class JBusinessDirectoryModelCustomer extends JModelLegacy {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Switch user from the admin(which will have access to this option) to any other user registered on the system
	 *
	 * @param $data array with all post data from form
	 * @return bool
	 *
	 * @since 4.9.0
	 */
	public function switchUser($data) {
		jimport('joomla.database.database.mysql');

		$db = JFactory::getDbo();
		$q = "SELECT * FROM `#__users` WHERE id = ".$data["user_id"];
		$newUser = $db->setQuery($q)->loadAssoc();

		PluginHelper::importPlugin('user');

		// Initiate log in
		$options = array('action' => 'core.login.site', 'remember' => false);
		if (Factory::getApplication()->triggerEvent('onUserLogin', array($newUser,$options))[0]) {
			return true;
		} else {
			return false;
		}
	}
}
