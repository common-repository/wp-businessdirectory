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
 * Report Model for Companies.
 *
 */
class JBusinessDirectoryModelReport extends JModelAdmin {

	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JBUSINESSDIRECTORY_PACKAGE';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context	= 'com_jbusinessdirectory.report';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record) {
		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record) {
		return true;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	*/
	public function getTable($type = 'Report', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}


	/**
	 * Method to get a menu item.
	 *
	 * @param   integer	The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null) {
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('report.id');
		$false	= false;

		// Get a menu item row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return $false;
		}

		$properties = $table->getProperties(1);
		$value = ArrayHelper::toObject($properties, 'JObject');
		
		return $value;
	}
	
	
	/**
	 * Method to get the menu item form.
	 *
	 * @param   array  $data		Data for the form.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return  JForm	A JForm object on success, false on failure
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
		$form = $this->loadForm('com_jbusinessdirectory.report', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
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
		$data = JFactory::getApplication()->getUserState('com_jbusinessdirectory.edit.report.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
	
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 * @return  boolean  True on success.
	 */
	public function save($data) {
		$id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('report.id');
		if (empty($data['id'])) {
			$data['id'] = 0;
		}
		$isNew = true;

		if (!isset($data["selected_params"])) {
			$data["selected_params"] = array('name');
		}

		if (!isset($data["custom_params"])) {
			$data["custom_params"] = array();
		}

		if ($data["type"] == 1) {
			if (!empty($data["selected_conference_params"])) {
				$data["selected_params"] = implode(",", $data["selected_conference_params"]);
				$data["custom_params"] = '';
			} else {
				$data["selected_params"] = 'conferenceName';
				$data["custom_params"] = '';
			}
		} else if($data["type"] == 2) {
			if (!empty($data["selected_offer_params"]) || !empty($data["custom_params"])) {
				$data["selected_params"] = implode(",", $data["selected_offer_params"]);
				$data["custom_params"] = implode(",", $data["custom_params"]);
			} else {
				$data["selected_params"] = 'name';
				$data["custom_params"] = '';
			}
		} else {
			if (!empty($data["selected_params"]) || !empty($data["custom_params"])) {
				$data["selected_params"] = implode(",", $data["selected_params"]);
				$data["custom_params"] = implode(",", $data["custom_params"]);
			} else {
				$data["selected_params"] = 'name';
				$data["custom_params"] = '';
			}
		}

		if (!empty($data['dateRange'])) {
			$startEndDates = explode(':', $data['dateRange']);
			$data["start_date"] = JBusinessUtil::convertToMysqlFormat($startEndDates[0]);
			$data["end_date"] = JBusinessUtil::convertToMysqlFormat($startEndDates[1]);
		} else {
			$data["start_date"] = '0000-00-00';
			$data["end_date"] = '0000-00-00';
		}

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

		$this->setState('report.id', $table->id);

		if (!empty($data["features"])) {
			$table->insertRelations($table->id, $data["features"]);
		}
		
		// Clean the cache
		$this->cleanCache();

		return true;
	}


	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
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
}
