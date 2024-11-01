<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');
use MVC\Utilities\ArrayHelper;
use MVC\Session\Session;

class JBusinessDirectoryControllerReviewQuestions extends JControllerAdmin {

	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since   1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);

		$this->registerTask('unpublish', 'changeState');
		$this->registerTask('publish', 'changeState');
	}


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
	public function getModel($name = 'ReviewQuestion', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function back() {
		$this->setRedirect('index.php?option=com_jbusinessdirectory&page=jbd_businessdirectory');
	}

	/**
	 * Removes an item
	 */
	public function delete() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_("COM_JBUSINESSDIRECTORY_NO_ITEM_SELECTED"), 'error');
		} else {
			// Get the model.
			$model = $this->getModel("ReviewQuestion");

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			ArrayHelper::toInteger($cid);

			// Remove the items.
			if (!$model->delete($cid)) {
				$this->setMessage($model->getError());
			} elseif (!empty($model->getErrors())) {
				$implodeErrors = implode('<br />', $model->getErrors());
				$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_DELETED_WARNING', $implodeErrors), 'Warning');
			} else {
				$this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_ITEMS_DELETED', count($cid)));
			}
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=reviewquestions');
	}

	public function unpublish() {
		$model = $this->getModel('ReviewQuestion');

		if ($model->changeState()) {
			$this->setMessage(JText::_('LNG_ERROR_CHANGE_STATE'), 'warning');
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=reviewquestions');
	}

	public function changeState() {
		// Check for request forgeries
		//Session::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid   = $this->input->get('cid', array(), 'array');
		$data  = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task  = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');
		$error = false;

		$model = $this->getModel('ReviewQuestion');

		foreach ($cid as $item) {
			if (!$model->changeState($item, $value)) {
				$this->setMessage(JText::_('LNG_ERROR_CHANGE_STATE'), 'warning');
				$error = true;
			} else {
				if ($value == 1) {
					$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
				} elseif ($value == 0) {
					$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
				} elseif ($value == 2) {
					$ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
				} else {
					$ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
				}

				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}

		$ajax = $this->input->get('ajax');
		if (!empty($ajax)) {
			$response = array();
			$response["cid"] = $cid[0];
			$response["error"] = $error;

			echo json_encode($response);

			exit;
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=reviewquestions');
	}
}
