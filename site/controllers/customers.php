<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryControllerCustomers extends JControllerLegacy {
	/**
	 * Display the view
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.6
	 */
	public function display($cachable = false, $urlparams = false) {
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Customers', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function back() {
		$this->setRedirect('index.php?option=com_jbusinessdirectory');
	}

	/**
	 *  switch users
	 */
	public function switchUser() {
		$app = JFactory::getApplication();
		$data = JFactory::getApplication()->input->post->getArray();
		$model = $this->getModel("Customer");
		if ($model->switchUser($data)) {
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions', true));
		} else {
			$this->setMessage(JText::_('LNG_ERROR_SWITCHING_USERS'), 'warning');
			$app->redirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=useroptions'));
		}
	}
}
