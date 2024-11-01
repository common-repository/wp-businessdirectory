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

jimport('joomla.application.component.modeladmin');

/**
 * Region Model
 */
class JBusinessDirectoryModelRegion extends JModelAdmin {
	/**
	 * @var     string    The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JBUSINESSDIRECTORY_REGION';

	protected $header = array();

	protected $headerDifferences = array();

	protected $failedImports = array();

	protected $error_row = 0;
	
	protected $importCount = 0;

	/**
	 * Model context string.
	 *
	 * @var        string
	 */
	protected $_context = 'com_jbusinessdirectory.region';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object    A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record) {
		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object    A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record) {
		return true;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    The table type to instantiate
	 * @param   string    A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Region', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$id = JFactory::getApplication()->input->get('id');
		$this->setState('region.id', $id);
	}

	/**
	 * Method to get a menu item.
	 *
	 * @param   integer    The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null) {
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('region.id');
		$false  = false;

		// Get a menu item row instance.
		$table = $this->getTable("Region");

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}

		$properties = $table->getProperties(1);
		$value      = ArrayHelper::toObject($properties, 'JObject');

		return $value;
	}

	/**
	 * Method to get the menu item form.
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm    A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		exit;
		// The folder and element vars are passed when saving the form.
		if (empty($data)) {
			$item = $this->getItem();
			// The type should already be set.
		}
		// Get the form.
		$form = $this->loadForm('com_jbusinessdirectory.region', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jbusinessdirectory.edit.region.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 *
	 * @return  boolean  True on success.
	 */
	public function save($data) {
		$id    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('region.id');
		$data['id'] = $id;
		$isNew = true;

		// Get a row instance.
		$table = $this->getTable();

		// Load the row if saving an existing item.
		if ($id > 0) {
			$table->load($id);
			$isNew = false;
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		$this->setState('region.id', $table->id);

		// Clean the cache
		$this->cleanCache();


		return true;
	}

	/**
	 * Deletes a region based on its ID
	 *
	 * @param $regionId int ID of the region
	 *
	 * @return mixed
	 */
	public function deleteRegion($regionId) {
		$regionsTable = $this->getTable("Region");
		return $regionsTable->delete($regionId);
	}

	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 */
	public function delete(&$itemIds) {
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		ArrayHelper::toInteger($itemIds);

		// Get a group row instance.
		$table = $this->getTable();

		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId) {
			if (!$table->delete($itemId)) {
				$this->setError($table->getError());
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Retrieves all countries
	 *
	 * @return mixed
	 */
	public function getCountries() {
		$result = array();
		$countriesTable = $this->getTable("Country");
		$countries = $countriesTable->getCountries();
		foreach ($countries as $country) {
			$result[$country->country_name] = $country;
		}
		return $result;
	}

	/**
	 * Check if headers on the file that will be imported are OK
	 * @return bool
	 */
	public function checkHeaders() {

		$this->tableHeader = array("country", "region", "city");
		$headerDifferences = array_diff($this->header, $this->tableHeader);
		if ($headerDifferences != null) {
			$this->headerDifferences = $headerDifferences;
			return false;
		}
		return true;
	}

	public function getImportStatus() {
		$result = new stdClass();
		$result->differences = $this->headerDifferences;
		$result->correctHeader = $this->tableHeader;
		$result->failedImports = $this->failedImports;
		$result->importCount = $this->importCount;

		if (!empty($result->differences)) {
			$msg = JText::_('LNG_NOT_RECOGNIZED_HEADERS')."<br />".implode(", ", $result->differences);
			JFactory::getApplication()->enqueueMessage($msg, 'warning');
			$msg = JText::_('LNG_ALLOWED_HEADER')."<br />".implode(", ", $result->correctHeader);
			$msg .= "<br /><br />";
			JFactory::getApplication()->enqueueMessage($msg, 'success');
		}

		if (!empty($result->failedImports)) {
			$message = JText::_('LNG_IMPORT_FAILED_FOR');
			JFactory::getApplication()->enqueueMessage(JText::_('LNG_IMPORT_FAILED_FOR'), 'warning');
			foreach ($result->failedImports as $item) {
				$message = $message.JFactory::getApplication()->enqueueMessage(JText::_('LNG_ROW') . " " . $item->row . "  " . JText::_('LNG_NAME') . " " . $item->name, 'warning');
			}
		}

		if ($result->importCount) {
			$message = JText::plural('COM_JBUSINESS_DIRECTORY_N_ITEMS_IMPORTED', $result->importCount);
			JFactory::getApplication()->enqueueMessage($message, 'success');
		}

		return $result;
	}

	/**
	 * Prepare the file content to be imported
	 * @param string $filePath
	 * @param string $delimiter
	 */
	public function importRegionsAndCitiesFromCsv($filePath, $delimiter) {
		$row = 1;
		ini_set("auto_detect_line_endings", "1");
		if (($handle = fopen($filePath, "r")) !== false) {
			while (($data = fgetcsv($handle, 9000, $delimiter)) !== false) {
				$importData = array();
				if ($row == 1) {
					$this->header = $data;
					$this->checkHeaders();
					$row++;
					continue;
				}
				$num = count($data);

				//echo "<p> $num fields in line $row: <br /></p>\n";
				for ($c = 0; $c < $num; $c++) {
					$importData[strtolower($this->header[$c])] = $data[$c];
				}

				$this->importRegion($importData);
				$modelCity = JModelLegacy::getInstance('City', 'JBusinessDirectoryModel');
				$modelCity->importCity($importData);
				$row++;
			}
		}
	}

	public function importRegion($data) {
		$datas = array();
		$table = $this->getTable();

		if (!empty($data["region"])) {
			$datas["name"] = isset($data["region"]) ? $data["region"] : "";

			$countries = $this->getCountries();
			$countryId = 0;
			if (isset($data["country"])) {
				if (isset($countries[$data["country"]])) {
					$countryId = $countries[$data["country"]]->id;
				}
			}
			$datas["country_id"] = $countryId;

			if(!empty($table->checkRegionExists($datas['name'], $datas['country_id']))) {
				return;
			}
			
			try {
				$this->setState('region.id', 0);
				$this->error_row++;
				if ($this->save($datas)) {
					$this->importCount++;
				} else {
					$failedImport = new stdClass();
					$failedImport->row = $this->error_row;
					array_push($this->failedImports, $failedImport);
				}
			} catch (Exception $e) {
				dump($e);
			}
		} else {
			$failedImport = new stdClass();
			$this->error_row++;
			$failedImport->name = JText::_('LNG_UNKNOWN');
			$failedImport->row = $this->error_row;
			array_push($this->failedImports, $failedImport);
		}
	}
}
