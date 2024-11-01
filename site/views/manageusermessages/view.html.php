<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

// following translations will be used in js
JText::script('COM_JBUSINESS_DIRECTORY_COMPANY_MESSAGE_CONFIRM_DELETE');

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewManageUserMessages extends JBusinessDirectoryFrontEndView {
	public function __construct() {
		$this->userDashboard = true;
		parent::__construct();
	}

	public function display($tpl = null) {
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->messageTypes = $this->get('MessageTypes');
		$this->actions = JBusinessDirectoryHelper::getActions();
		
		parent::display($tpl);
	}
}
