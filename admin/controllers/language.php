<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryControllerLanguage extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	public function editLanguage() {
		$code = JFactory::getApplication()->input->getString('code');
		$link = 'index.php?option=com_jbusinessdirectory&view=language&layout=edit&code='.$code;
		$this->setRedirect($link);
	}

	public function apply() {
		$code = JFactory::getApplication()->input->getString('code');
		$model = $this->getModel('language');
		$msg = $model->saveLanguage();
		$link = 'index.php?option=com_jbusinessdirectory&tmpl=component&controller=language&view=language&task=language.editLanguage&code='.$code;
		$this->setRedirect($link, $msg);
	}

	public function save($key = null, $urlVar = null) {
		$model = $this->getModel('language');
		$msg = $model->saveLanguage();
		$link = 'index.php?option=com_jbusinessdirectory&view=applicationsettings';
		$this->setRedirect($link, $msg);
	}

	public function create() {
		$link = 'index.php?option=com_jbusinessdirectory&view=language&layout=create';
		$this->setRedirect($link);
	}

	public function store() {
		$code = JFactory::getApplication()->input->getString('code');
		$content = JFactory::getApplication()->input->getString('content');
		$model = $this->getModel('language');

		if (empty($code) || empty($content)) {
			if (empty($content)) {
				$msg = JFactory::getApplication()->enqueueMessage(JText::_('LNG_CONTENT_CANNOT_BE_BLANK'), 'error');
			}
			if (empty($code)) {
				$msg = JFactory::getApplication()->enqueueMessage(JText::_('LNG_CODE_NOT_SPECIFIED'), 'error');
			}
			$this->setRedirect('index.php?option=com_jbusinessdirectory&view=language&layout=create', $msg);
		} else {
			$msg = $model->createLanguage();
			$link = 'index.php?option=com_jbusinessdirectory&view=applicationsettings';
			$this->setRedirect($link, $msg);
		}
	}

	public function send_email() {
		$code = JFactory::getApplication()->input->getString('code');
		$model = $this->getModel('language');
		$msg = $model->send_email($code);
		$link = 'index.php?option=com_jbusinessdirectory&tmpl=component&controller=language&view=language&view=language&task=language.editLanguage&code='.$code;
		$this->setRedirect($link, $msg);
	}

	public function remove() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('language');
		$app =JFactory::getApplication();
		
		// Get items to remove from the request.
		$codes = $this->input->get('cid', array(), 'array');
		
		if (!is_array($codes) || count($codes) < 1) {
			$msg = $app->enqueueMessage(JText::_('LNG_NO_LANGUAGES_SELECTED'), 'error');
			$link = 'index.php?option=com_jbusinessdirectory&view=applicationsettings';
			$this->setRedirect($link, $msg);
		} else {
			foreach ($codes as $code) {
				$msg = $model->deleteFolder($code);
			}
			$link = 'index.php?option=com_jbusinessdirectory&view=applicationsettings';
			$this->setRedirect($link, $msg);
		}
	}

	public function cancel() {
		$msg = JText::_('LNG_OPERATION_CANCELLED', true);
		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=applicationsettings', $msg);
	}
}
