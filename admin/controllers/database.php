<?php
/**
 * @package     JBD.Administrator
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     https://www.gnu.org/licenses/agpl-3.0.en.html; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Installer Database Controller
 *
 * @since  2.5
 */
class JBusinessDirectoryControllerDatabase extends JControllerLegacy {
	/**
	 * Tries to fix missing database updates
	 *
	 * @return  void
	 *
	 * @since   2.5
	 * @todo    Purge updates has to be replaced with an events system
	 */
	public function fix() {
		// Check for request forgeries.
		//$this->checkToken();
		
		$input = JFactory::getApplication()->input;
		$view = $input->get('view'); 

		$model = $this->getModel('database');
		$result = $model->fix();

		// Refresh versionable assets cache
		//JFactory::getApplication()->flushAssets();

		if ($result) {
			$this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_DATABASE_FIX_SUCCESS'));
		} else {
			$this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_DATABASE_FIX_FAILURE'), 'warning');
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view='.$view, false));
		return;
	}

	/**
	 * Update the database schema to the latest update sql version
	 *
	 * @return void
	 */
	public function updateSchemaVersion(){
		//$this->checkToken();
		
		$input = JFactory::getApplication()->input;
		$view = $input->get('view'); 

		$model = $this->getModel('database');
		$result = $model->updateSchemaVersion();

		if ($result) {
			$this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_DATABASE_FIX_SUCCESS'));
		} else {
			$this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_DATABASE_FIX_FAILURE'), 'warning');
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view='.$view, false));
		return;
	}
}
