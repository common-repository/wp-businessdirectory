<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

use MVC\Session\Session;
use MVC\Utilities\ArrayHelper;

class JBusinessDirectoryControllerCompanies extends JControllerLegacy {
	/**
	 * Class constructor.
	 *
	 * @param   array $config A named array of configuration variables.
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
	 * @param   boolean            If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController        This object to support chaining.
	 * @since   1.6
	 */
	public function display($cachable = false, $urlparams = false) {
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string $name   The model name. Optional.
	 * @param   string $prefix The class prefix. Optional.
	 * @param   array  $config Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Companies', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)) {
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
		$cid =  $this->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JBUSINESSDIRECTORY_NO_COMPANY_SELECTED'), 'warning');
		} else {
			// Get the model.
			$model = $this->getModel("Company");

			// Make sure the item ids are integers
			ArrayHelper::toInteger($cid);

			// Remove the items.
			if (!$model->delete($cid)) {
				$this->setMessage($model->getError());
			} elseif (!empty($model->getErrors())) {
				$implodeErrors = implode('<br />', $model->getErrors());
				$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_DELETED_WARNING', $implodeErrors), 'Warning');
			} else {
				$this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_COMPANIES_DELETED', count($cid)));
			}
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=companies');
	}
	
	public function sendNotificationEmail() {

		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');
		
		if (!is_array($cid) || count($cid) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JBUSINESSDIRECTORY_NO_COMPANY_SELECTED'), 'warning');
		} else {
			// Get the model.
			$model = $this->getModel("Company");
			
			// Make sure the item ids are integers
			ArrayHelper::toInteger($cid);
			// Remove the items.
			if (!$model->sendNotificationEmail($cid)) {
				$this->setMessage($model->getError(), "error");
			} elseif (!empty($model->getErrors())) {
				$implodeErrors = implode('<br />', $model->getErrors());
				$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_SAVED_WARNING', $implodeErrors), 'Warning');
			} else {
				$this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_COMPANIES_NOTIFIED', count($cid)));
			}
		}
		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=companies');
	}

	public function importFromCsv() {
		JFactory::getApplication()->input->set("layout", "import");
		parent::display();
	}

	public function showExportCsv() {
		JFactory::getApplication()->input->set("layout", "export");
		parent::display();
	}

	public function exportCompaniesCsv() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('Companies');
		$model->exportCompaniesCSV();
		exit;
	}

	public function importCompaniesFromTxtArea() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$data = JFactory::getApplication()->input->post->getArray();

		$csvContent = null;
		if (!empty($data["companyData_ForImport"])) {
			$csvContent = explode("\n", $data["companyData_ForImport"]);
		}

		$model = $this->getModel("Company");
		$model->importCompaniesFromTextArea($csvContent, $data["delimiter"]);
		$model->getImportStatus();

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=companies');
	}

	public function getCompaniesByStringAjax() {
		$str = JFactory::getApplication()->input->getString('term', null);

		header("Content-Type: application/json", true);
		echo json_encode(JBusinessUtil::getCompaniesByString($str));
		exit;
	}

	public function unpublish() {
		$model = $this->getModel('Company');

		if ($model->changeState()) {
			$this->setMessage(JText::_('LNG_ERROR_CHANGE_STATE'), 'warning');
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=companies');
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
		
		$model = $this->getModel('Company');

		if (!$model->changeState($cid[0])) {
			$this->setMessage(JText::_('LNG_ERROR_CHANGE_STATE'), 'warning');
			$error = true;
		} else {
			$this->text_prefix = "COM_JBUSINESSDIRECTORY";
			if ($value === 1) {
				$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
			} elseif ($value === 0) {
				$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
			} elseif ($value === 2) {
				$ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
			}
			
			if ($ntext !== null) {
				$this->setMessage(\JText::plural($ntext, count($cid)));
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
		

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=companies');
	}

	/**
	 * Cancel subscription
	 *
	 * @return void
	 */
	public function cancelSubscription() {
		$app = JFactory::getApplication();

		$subscriptionId = $app->input->getInt('subscriptionId');
		try {
			SubscriptionService::cancelSubscription($subscriptionId);

			$this->setMessage(JText::_('LNG_SUBSCRIPTION_CANCELED'));
		} catch (Exception $e) {
			$this->setMessage($e->getMessage());
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=companies');
	}


	/**
	 * Start the import process
	 *
	 * @return void
	 */
	public function importCompaniesFromCsv() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$config   = JBusinessUtil::getSiteConfig();
		$filePath = $config->tmp_path;
		$data     = JFactory::getApplication()->input->post->getArray();

		//upload file
		$filePath = JBusinessUtil::uploadFile("csvFile", $data, $filePath);

		$model = $this->getModel("Company");
		$model->importCompaniesFromCSV($filePath, $data["delimiter"], 0, 10000);
		$model->getImportStatus();

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=companies');
	}

	/**
	 * Prepare all things needed for import
	 *
	 * @return void
	 */
	public function prepareImport(){
		
		$config   = JBusinessUtil::getSiteConfig();
		$filePath = $config->tmp_path;
		$data     = JFactory::getApplication()->input->post->getArray();

		//upload file
		$filePath = JBusinessUtil::uploadFile("csvFile", $data, $filePath);

		//creating the importer
		$model = $this->getModel("Company");
		// Get the importer state.
		$state = $model->getBatchState();

		$state->filePath = $filePath;
		$state->delimiter = $data["delimiter"];
		$state->update_existing = $data['update_existing'];

		$state->batchSize   = 100;
		$state->batchOffset = 0;
		$state->totalItems  = 0;
		$state->failed = false;

		// Set the importer state.
		$model->setBatchState($state);

		$app = JFactory::getApplication();
		// Close the application.
		$app->close();
	}

	/**
	 * Run the preparation for importing
	 *
	 * @return void
	 */
	public function startImportingAjax() {

		// Put in a buffer to silence noise.
		ob_start();
		
		// Add the importer language to JS
		JText::script('LNG_AN_ERROR_HAS_OCCURED');
		JText::script('LNG_MESSAGE_RETRIEVED');

		// Start the importer.
		try {
			$model = $this->getModel("Company");

			$model->prepareImport();
			// Get the importer state.
			$state = $model->getBatchState();
			$state->start = 1;

			// Send the response.
			$this->sendResponse($state);
		} catch (Exception $e) {
			dump($e);
			$this->sendResponse($e);
		}

		$app = JFactory::getApplication();
		// Close the application.
		$app->close();
	}

	/**
	 * Method to run the next batch of content through the importer.
	 *
	 * @return  void
	 *
	 * @since   5.5.0
	 */
	public function processBatch() {

		// Check for a valid token. If invalid, send a 403 with the error message.
		JSession::checkToken('request') or $this->sendResponse(new Exception(JText::_('JINVALID_TOKEN'), 403));

		// Put in a buffer to silence noise.
		ob_start();

		// Remove the script time limit.
		@set_time_limit(0);

		//creating the importer
		$model = $this->getModel("Company");
		// Get the importer state.
		$state = $model->getBatchState();

		// Reset the batch offset.
		$state->batchOffset = 0;

		// Update the importer state.
		$model->setState($state);

		// Start the importer.
		try {
			$model->startImport();

			// Get the importer state.
			$state = $model->getBatchState();
			$state->start = 0;
			$state->complete = 0;

			
			$status = $model->getImportStatus();
			$state->failed = $status->error;
			$state->message = $status->statusMessage;
			
			// Send the response.
			$this->sendResponse($state);
		} catch (Exception $e) {
			// Send the response.
			$this->sendResponse($e);
		}
	}

	
	/**
	 * Method to handle a send a JSON response. The body parameter
	 * can be an Exception object for when an error has occurred or
	 * a JObject for a good response.
	 *
	 * @param   mixed  $state  JObject on success, Exception on error. [optional]
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function sendResponse($state = null) {
		// This method always sends a JSON response
		$app = JFactory::getApplication();
		$app->mimeType = 'application/json';


		// Create the response object.
		$response = $this->createResponse($state);

		// Add the buffer.
		$response->buffer =  ob_end_clean();

		// Send the JSON response.
		$app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
		$app->sendHeaders();
		echo json_encode($response);

		// Close the application.
		$app->close();
	}

	/**
	 * Build the response object based on the provided details.
	 *
	 *
	 * @param [type] $state
	 * @return void
	 */
	public function createResponse($state) {
		$response = new stdClass;

		// The old token is invalid so send a new one.
		$response->token = JFactory::getSession()->getFormToken();

		// Check if we are dealing with an error.
		if($state->failed){
			$response->error = true;
			$response->header = JText::_('LNG_AN_ERROR_HAS_OCCURED');	
			$response->message = $state->message;
		}else if ($state instanceof Exception) {
			// Log the error
			JLog::add($state->getMessage(), JLog::ERROR);

			// Prepare the error response.
			$response->error = true;
			$response->header = JText::_('LNG_AN_ERROR_HAS_OCCURED');
			$response->message = $state->getMessage();
		} else {
			// Prepare the response data.
			$response->batchSize = (int) $state->batchSize;
			$response->batchOffset = (int) $state->batchOffset;
			$response->totalItems = (int) $state->totalItems;

			$response->startTime = $state->startTime;
			$response->endTime = JFactory::getDate()->toSql();

			$response->start = !empty($state->start) ? (int) $state->start : 0;
			
			if ($response->totalItems <= 0) {
				$response->header = JText::_('LNG_IMPORT_COMPLETE');
				$response->message = JText::_('LNG_IMPORT_COMPLETE_MESSAGE');
			} else {
				$response->header = JText::_('LNG_IMPORT_RUNNING');
				$response->message = JText::sprintf('LNG_IMPORT_RUNNING_MESSAGE', $response->totalItems);
			}
		}
		return $response;
	}

	/**
	 * Genereate the pdf export
	 *
	 * @return void
	 */
	public function generateListingsPDF() {
		// Get the model.
		$model = $this->getModel("Companies");

		// Show the PDF file.
		$model->generateListingsPDF();
		exit();
	}

	public function saveOrderAjax() {
	// Get the input
	$pks = $this->input->post->get('cid', array(), 'array');
	$order = $this->input->post->get('order', array(), 'array');

	// Sanitize the input
	$pks = ArrayHelper::toInteger($pks);
	$order = ArrayHelper::toInteger($order);

	// Get the model
	$model = $this->getModel();

	// Save the ordering
	$return = $model->saveorder($pks, $order);

	if ($return)
	{
	echo '1';
	}

	// Close the application
	JFactory::getApplication()->close();
	}
}
