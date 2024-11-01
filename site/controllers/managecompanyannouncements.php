<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.DS.'com_jbusinessdirectory'.DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'announcements.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'announcements.php');
JTable::addIncludePath(DS.'components'.DS.'com_jbusinessdirectory'.DS.'tables');

class JBusinessDirectoryControllerManageCompanyAnnouncements extends JBusinessDirectoryControllerAnnouncements {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	public function __construct() {
		parent::__construct();
		$this->log = Logger::getInstance();
	}

	/**
	 * Delete an event or the associated reccuring events.
	 */
	public function deleteAnnouncement() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the model.
		$model = $this->getModel("ManageCompanyAnnouncement");
		$this->delete($model);

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view='.$this->input->get('view'));
	}
}
