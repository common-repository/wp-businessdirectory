<?php
/**
 * @package     JBD.Administrator
 * @subpackage  com_categories
 *
 * @copyright  Copyright (C) 2007 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html;
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The Category Controller
 *
 * @package     JBD.Administrator
 * @subpackage  com_categories
 * @since       1.6
 */
class JBusinessDirectoryControllerCategory extends JControllerForm {

	/**
	 * Constructor.
	 *
	 * @param  array   $config  An optional associative array of configuration settings.
	 *
	 * @since  1.6
	 * @see    JController
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
	}
	
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 */
	public function display($cachable = false, $urlparams = false) {
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=categories', false));
	}
	

	/**
	 * Method to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowAdd($data = array()) {
		return true;
	}

	/**
	 * Method to check if you can edit a record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'parent_id') {
		return true;
	}


	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') {
		$append = parent::getRedirectToItemAppend($recordId);

		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToListAppend() {
		$append = parent::getRedirectToListAppend();

		return $append;
	}

	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	public function save($key = null, $urlVar = null) {
	
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$jinput = JFactory::getApplication()->input;

		$app      = JFactory::getApplication();
		$model = $this->getModel('category');
		
		$data = $jinput->post->getArray();
		$data['description'] = $jinput->get('description', '', 'raw');
		$context  = 'com_jbusinessdirectory.edit.category';
		$task     = $this->getTask();
		$recordId = $recordId = $jinput->getInt('id');

	
		if (!$model->save($data)) {
			// Save the data in the session.
			$app->setUserState('com_jbusinessdirectory.edit.category.data', $data);
				
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
				
			return false;
		} elseif (!empty($model->getErrors())) {
			$implodeErrors = implode('<br />', $model->getErrors());
			$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_SAVED_WARNING', $implodeErrors), 'Warning');
		} else {
			$this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_CATEGORY_SAVE_SUCCESS'));
		}
	
		// Redirect the user and adjust session state based on the chosen task.
		switch ($task) {
			case 'apply':
				$recordId = $model->getState("category.id");
				// Set the row data in the session.
				$this->holdEditId($context, $recordId);
				$app->setUserState('com_jbusinessdirectory.edit.company.data', null);
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId).'&filter_type='.$data["type"], false));
				break;
	
			default:
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_jbusinessdirectory.edit.company.data', null);
					
				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend().'&filter_type='.$data["type"], false));
				break;
		}
	}
}
