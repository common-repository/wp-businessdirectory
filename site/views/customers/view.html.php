<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The HTML Menus Menu Menus View.
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory

 */
require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewCustomers extends JBusinessDirectoryFrontEndView {
	protected $items;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$this->users		= JBusinessUtil::getAllUsers();
		$this->appSettings  = JBusinessUtil::getApplicationSettings();
		$this->actions      = JBusinessDirectoryHelper::getActions();
		$actualUser = JBusinessUtil::getUser();

		foreach ($this->users as $key => $user) {
			if ($user->value == $actualUser->ID) {
				unset($this->users[$key]);
			}
		}

		$layout = JFactory::getApplication()->input->get("layout");
		if (isset($layout)) {
			$tpl = $layout;
		}

		parent::display($tpl);
	}
}
