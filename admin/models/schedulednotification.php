<?php
/**
 * @package    JBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.modeladmin');

/**
 * ScheduledNotification Model
 *
 * @since 5.1.0
 */
class JBusinessDirectoryModelScheduledNotification extends JModelAdmin {
	/**
	 * @var        string    The prefix to use with controller messages.
	 * @since   5.1.0
	 */
	protected $text_prefix = 'COM_JBUSINESSDIRECTORY_SCHEDULED_NOTIFICATION';

	/**
	 * Model context string.
	 *
	 * @var        string
	 * @since   5.1.0
	 */
	protected $_context = 'com_jbusinessdirectory.schedulednotification';

	/**
	 * JBusiness Application Settings
	 *
	 * @var bool|stdClass
	 * @since 5.1.0
	 */
	private $appSettings;

	/**
	 * JBusinessDirectoryModelScheduledNotification constructor.
	 *
	 * @param array $config
	 *
	 * @since 5.1.0
	 */
	public function __construct($config = array()) {
		$this->appSettings = JBusinessUtil::getApplicationSettings();

		parent::__construct($config);
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object    A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since   5.1.0
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
	 * @since   5.1.0
	 */
	protected function canEditState($record) {
		return true;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   string    The table type to instantiate
	 * @param   string    A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 * @since   5.1.0
	 */
	public function getTable($type = 'ScheduledNotifications', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   5.1.0
	 */
	protected function populateState() {
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$id = JFactory::getApplication()->input->getInt('id');
		$this->setState('schedulednotification.id', $id);
	}

	/**
	 * Method to get the menu item form.
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm    A JForm object on success, false on failure
	 * @since   5.1.0
	 */
	public function getForm($data = array(), $loadData = true) {
		exit;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   5.1.0
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jbusinessdirectory.edit.schedulednotification.data', array());

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
	 * @since   5.1.0
	 */
	public function save($data) {
		$id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('schedulednotification.id');
		if (empty($data['id'])) {
			$data['id'] = 0;
		}
		$isNew = true;
		// Get a row instance.
		$table = $this->getTable();

		// Load the row if saving an existing item.
		if ($id > 0) {
			$table->load($id);
			$isNew = false;
		}

		if (empty($data["frequency"])) {
			$data["frequency"] = "";
		} else {
			$data["frequency"] = implode(",", $data["frequency"]);
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
		
		$this->setState('schedulednotification.id', $table->id);

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	
	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   5.1.0
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

	public function &getItem($itemId = null) {
		$itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('schedulednotification.id');
		$false = false;
        
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

		if (!empty($value->frequency)) {
			$value->frequency = explode(",", $value->frequency);
		} else {
			$value->frequency = array();
		}

		return $value;
	}
}
