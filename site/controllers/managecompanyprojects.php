<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'projects.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'projects.php');
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');

class JBusinessDirectoryControllerManageCompanyProjects extends JBusinessDirectoryControllerProjects {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	public function __construct() {
		parent::__construct();
		$this->log = Logger::getInstance();
	}

	/**
	 * Removes an item
	 */
	public function delete() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('id');

		$cid = intval($cid);

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
				->delete($db->quoteName('#__jbusinessdirectory_company_projects'))
				->where('id = '. (int) $cid);

		$db->setQuery($query);
		$result = $db->execute();

		// set the message
		if (!$result) {
			$this->setMessage(JText::_('LNG_ERROR'));
		} else {
			$this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_PROJECTS_DELETED', 1));
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=managecompanyprojects');
	}
}
