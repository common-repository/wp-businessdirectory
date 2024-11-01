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

class JBusinessDirectoryViewManageMessages extends JBusinessDirectoryFrontEndView {
	public function __construct() {
		parent::__construct();
	}

	public function display($tpl = null) {
		$this->items		= $this->get('Messages');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

        $user = JBusinessUtil::getUser();
		$this-> nrUnreadMessages = JBusinessUtil::getTotalUserMessages($user->ID, true);

		$this->messageTypes = $this->get('MessageTypes');
		$this->actions = JBusinessDirectoryHelper::getActions();
		
		parent::display($tpl);
	}
}
