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
 * Company Model for Companies.
 *
 */
class JBusinessDirectoryModelReviewabuse extends JModelAdmin {
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JBUSINESSDIRECTORY_REVIEWABUSE';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context	= 'com_jbusinessdirectory.reviewabuse';

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
	public function getTable($type = 'Reviewabuse', $prefix = 'JTable', $config = array()) {
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
		$id = JFactory::getApplication()->input->getInt('id');
		$this->setState('reviewabuse.id', $id);
	}

	/**
	 * Method to get a menu item.
	 *
	 * @param   integer	The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null) {
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('reviewabuse.id');
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
		$reviewTable = $table = $this->getTable("Review");
		
		$review = $reviewTable->getReview($value->reviewId);
		$value->subject = $review->subject;

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
		$form = $this->loadForm('com_jbusinessdirectory.reviewabuse', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
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
		$data = JFactory::getApplication()->getUserState('com_jbusinessdirectory.edit.reviewabuse.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data) {
		$id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('reviewabuse.id');
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

		$this->setState('reviewabuse.id', $table->id);
	
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

	public function getStates() {
		$states = array();
		$state = new stdClass();
		$state->value = 0;
		$state->text = JTEXT::_("LNG_INACTIVE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 1;
		$state->text = JTEXT::_("LNG_ACTIVE");
		$states[] = $state;
	
		return $states;
	}

	public function changeState($id) {
		$this->populateState();
		$reviewsTable = $this->getTable();
		return $reviewsTable->changeState($id);
	}
}