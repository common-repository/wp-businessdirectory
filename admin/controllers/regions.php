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

/**
 * Class Regions Controller
 */
class JBusinessDirectoryControllerRegions extends JControllerLegacy {
	/**
	 * Display the view
	 *
	 * @param bool $cachable
	 * @param bool $urlparams
	 *
	 * @return void This object to support chaining.
	 * @since  1.6
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
	public function getModel($name = 'Regions', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)) {
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
		$cid = $this->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JBUSINESSDIRECTORY_NO_REGION_SELECTED'), 'error');
		} else {
			// Get the model.
			$model = $this->getModel("Region");

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
				$this->setMessage(JText::plural('COM_JBUSINESSDIRECTORY_N_REGIONS_DELETED', count($cid)));
			}
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=regions');
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

	/**
	 * Method to retrieve counties by country (ajax)
	 */
	public function getRegionsByCountryAjax() {
		$countryId = (int) JFactory::getApplication()->input->get('countryId');
		$model  = $this->getModel('Regions');
		$result = $model->getRegions($countryId);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}
	
	public function importFromCsv() {
		JFactory::getApplication()->input->set("layout", "import");
		parent::display();
	}

	public function showExportCsv() {
		JFactory::getApplication()->input->set("layout", "export");
		parent::display();
	}

	public function exportRegionsAndCitiesCsv() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('regions');
		$model->exportRegionsAndCitiesCSV();
		exit;
	}

	public function importRegionsAndCitiesFromCsv() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$config = JBusinessUtil::getSiteConfig();
		$filePath = $config->tmp_path;
		$data = JFactory::getApplication()->input->post->getArray();
		
		//upload file
		$filePath = JBusinessUtil::uploadFile("csvFile", $data, $filePath);
		
		$model = $this->getModel("Region");
		$model->importRegionsAndCitiesFromCsv($filePath, $data["delimiter"]);

		$model->getImportStatus();
		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=regions');
	}
}
