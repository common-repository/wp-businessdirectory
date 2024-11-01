<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');


// No direct access
defined('_JEXEC') or die('Restricted access');

use MVC\Utilities\ArrayHelper;

class JBusinessDirectoryControllerUpdates extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	public function __construct() {
		parent::__construct();
		
		$language = JFactory::getLanguage();
		$language_tag 	= $language->getTag();
		
		$language->load(
			'com_installer',
			dirname(BD_LANGUAGE_FOLDER_PATH),
			$language_tag,
			true
		);
	}

	public function back() {
		$this->setRedirect('index.php?option=com_jbusinessdirectory&page=jbd_businessdirectory');
	}
	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	public function apply() {
		$msg = $this->saveSettings();
		$link = 'index.php?option=com_jbusinessdirectory&controller=applicationsettings&view=applicationsettings';
		$this->setRedirect($link, $msg);
	}

	
	public function saveOrder() {
		$model = $this->getModel('updates');
		$data = JFactory::getApplication()->input->post->getArray();

		if ($model->store($data)) {
			$msg = JText::_('LNG_ORDER_SAVED', true);
		} else {
			$msg = JText::_('LNG_ERROR_SAVING_ORDER', true);
		}
		
		$link = 'index.php?option=com_jbusinessdirectory&view=updates';
		$this->setRedirect($link, $msg);
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	public function cancel() {
		$msg = JText::_('LNG_OPERATION_CANCELLED', true);
		$this->setRedirect('index.php?option=com_jbusinessdirectory', $msg);
	}
	public function find() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the caching duration
		$component = JComponentHelper::getComponent('com_installer');
		$params = $component->params;
		$cache_timeout = $params->get('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;
		
		$module = JComponentHelper::getComponent('com_jbusinessdirectory');
		
		// Find updates
		$model	= $this->getModel('updates');
		$model->findUpdates(array($module->id), $cache_timeout);
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=updates', false));
	}
	
	public function update() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('updates');
		$uid   = JFactory::getApplication()->input->get('cid', array(), 'array');

		ArrayHelper::toInteger($uid, array());
		if ($model->update($uid)) {
			$cache = JFactory::getCache('mod_menu');
			$cache->clean();
		}
	
		$app = JFactory::getApplication();
		$redirect_url = $app->getUserState('com_jbusinessdirectory.redirect_url');
		if (empty($redirect_url)) {
			$redirect_url = JRoute::_('index.php?option=com_jbusinessdirectory&view=updates', false);
		} else {
			// Wipe out the user state when we're going to redirect
			$app->setUserState('com_jbusinessdirectory.redirect_url', '');
			$app->setUserState('com_jbusinessdirectory.message', '');
			$app->setUserState('com_jbusinessdirectory.extension_message', '');
		}
		$this->setRedirect($redirect_url);
	}
	
	public function getVersionStatusAjax() {
		$model = $this->getModel('updates');
		$result = $model->getVersionStatus();
		echo $result;
		exit;
	}
}
