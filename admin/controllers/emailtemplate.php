<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * The Company Controller
 *
 */
class JBusinessDirectoryControllerEmailTemplate extends JControllerForm {
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 */
	public function display($cachable = false, $urlparams = false) {
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=emailtemplates', false));
	}

	public function add() {
		$app = JFactory::getApplication();
		$context = 'com_jbusinessdirectory.edit.emailtemplate';
	
		$result = parent::add();
		if ($result) {
			$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=emailtemplate'. $this->getRedirectToItemAppend(), false));
		}
	
		return $result;
	}
	
	
	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.

	 */
	public function cancel($key = null) {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		$app = JFactory::getApplication();
		$context = 'com_jbusinessdirectory.edit.emailtemplate';
		$result = parent::cancel();
	}
	
	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 */
	public function edit($key = null, $urlVar = null) {
		$app = JFactory::getApplication();
		$result = parent::edit();
	
		return true;
	}
	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	public function save($key = null, $urlVar = null) {
		
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app      = JFactory::getApplication();
		$model = $this->getModel('emailtemplate');
		$input = JFactory::getApplication()->input;
		$data = $input->post->getArray();
		$context  = 'com_jbusinessdirectory.edit.emailtemplate';
		$task     = $this->getTask();
		$data["id"] = $data["email_id"];
		$recordId = $input->getInt('email_id');

		$data['email_content'] = $_POST['email_content']; 

		if (!$model->save($data)) {
			// Save the data in the session.
			$app->setUserState('com_jbusinessdirectory.edit.emailtemplate.data', $data);
			
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
			
			return false;
		} elseif (!empty($model->getErrors())) {
			$implodeErrors = implode('<br />', $model->getErrors());
			$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_SAVED_WARNING', $implodeErrors), 'Warning');
		} else {
			$this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_EMAIL_TEMPLATE_SAVE_SUCCESS'));
		}
		
		// Redirect the user and adjust session state based on the chosen task.
		switch ($task) {
			case 'apply':
				// Set the row data in the session.
				$recordId = $model->getState('emailtemplate.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState('com_jbusinessdirectory.edit.emailtemplate.data', null);
				
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
				break;

			default:
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_jbusinessdirectory.edit.emailtemplate.data', null);
							
				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
				break;
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.6
	 */
	public function batch($model = null) {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		// Set the model
		$model = $this->getModel('EmailTemplate', '', array());
	
		$vars = $this->input->post->get('batch', array(), 'array');
		$cid  = $this->input->post->get('cid', array(), 'array');
		
		// Attempt to run the batch operation.
		if ($model->batch($vars, $cid, null)) {
			$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_BATCH'));
		} else {
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
		}
		
		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=emailtemplates' . $this->getRedirectToListAppend(), false));
	
		return parent::batch($model);
	}
}
