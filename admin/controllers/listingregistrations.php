<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
use MVC\Utilities\ArrayHelper;
use MVC\Session\Session;

jimport('joomla.application.component.controlleradmin');

class JBusinessDirectoryControllerListingRegistrations extends JControllerAdmin {

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
	public function getModel($name = 'ListingRegistrations', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function back() {
		$this->setRedirect('index.php?option=com_jbusinessdirectory&page=jbd_businessdirectory');
	}

	public function aprove() {
		$model = $this->getModel('ListingRegistrations');
		$error = false;
		$cid   = $this->input->get('cid', array(), 'array');
		$isCP   = $this->input->get('front', false);
		$ajax = $this->input->get('ajax');

		foreach ($cid as $id) {
			if (!$model->changeAprovalState($id, LISTING_JOIN_STATUS_APPROVED)) {
				$this->setMessage(JText::_('LNG_ERROR_CHANGE_STATE'), 'warning');
				$error = true;
			} else {
				$this->setMessage(\JText::plural("COM_JBUSINESSDIRECTORY_N_ITEMS_APPROVED", count($cid)));
			}
		}


		if (!empty($ajax)) {
			$response = array();
			$response["cid"] = $cid[0];
			$response["error"] = $error;

			echo json_encode($response);

			exit;
		}

		$view = 'listingregistrations';
		if ($isCP) {
			$view = 'managelistingregistrations';
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view='.$view, false));
	}

	public function disaprove() {
		$model = $this->getModel('ListingRegistrations');
		$error = false;
		$cid   = $this->input->get('cid', array(), 'array');
		$isCP   = $this->input->get('front', false);
		$ajax = $this->input->get('ajax');

		foreach ($cid as $id) {
			if (!$model->changeAprovalState($id, LISTING_JOIN_STATUS_DISAPPROVED)) {
				$this->setMessage(JText::_('LNG_ERROR_CHANGE_STATE'), 'warning');
				$error = true;
			} else {
				$this->setMessage(\JText::plural("COM_JBUSINESSDIRECTORY_N_ITEMS_DISAPPROVED", count($cid)));
			}
		}

		if (!empty($ajax)) {
			$response = array();
			$response["cid"] = $cid[0];
			$response["error"] = $error;

			echo json_encode($response);
			exit;
		}
		$view = 'listingregistrations';
		if ($isCP) {
			$view = 'managelistingregistrations';
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view='.$view, false));
	}

	/**
	 * Removes an item
	 */
	public function delete() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JBUSINESS_DIRECTORY_LINKED_LISTING_DELETED'), 'error');
		} else {
			// Get the model.
			$model = $this->getModel("ListingRegistrations");

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

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=listingregistrations');
	}
}
