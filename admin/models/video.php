<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');
use MVC\Utilities\ArrayHelper;

jimport('joomla.application.component.modeladmin');
/**
 * Video Model for Videos.
 *
 */
class JBusinessDirectoryModelVideo extends JModelAdmin {


	/**
	 * @var        string    The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JBUSINESSDIRECTORY_VIDEO';
	protected $mainSubcategory = 0;

	/**
	 * Model context string.
	 *
	 * @var        string
	 */
	protected $_context = 'com_jbusinessdirectory.video';

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
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Videos', $prefix = 'Table', $config = array()) {
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
		$this->setState('video.id', $id);
	}

	/**
	 * Method to get a menu item.
	 *
	 * @param   integer    The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null) {
		$itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('video.id');
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

		$companyCategoryTable = $this->getTable('CompanyCategory', 'JTable');
		if (!empty($itemId)) {
			$value->selCats = $companyCategoryTable->getSelectedVideoCategoriesList($itemId);
		} else {
			$value->selCats = array();
		}

		$value->selectedCategories = $companyCategoryTable->getSelectedVideoCategories($itemId);
		foreach ($value->selectedCategories as $cat) {
			$cat->name = str_repeat('- ', $cat->level - 1) . $cat->name;
		}
		$value->defaultAtrributes = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_LISTING);

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
			$item		= $this->getItem();
			// The type should already be set.
		}
		// Get the form.
		$form = $this->loadForm('com_jbusinessdirectory.video', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
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
		$data = JFactory::getApplication()->getUserState('com_jbusinessdirectory.edit.video.data', array());

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
		$id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('video.id');
		if (empty($data['id'])) {
			$data['id'] = 0;
		}
		$isNew = true;

		$input = JFactory::getApplication()->input;
		$video_name = $input->get("name", '', 'RAW');
		$video_url = $input->get("url", '', 'RAW');
		$video_description = $input->get("description", '', 'RAW');
		
		if (!empty($video_name) && empty($data["name"])) {
			$data["name"] = $video_name;
		}

		if (!empty($video_url) && empty($data["url"])) {
			$data["url"] = $video_url;
		}

		if (!empty($video_description) && empty($data["description"])) {
			$data["description"] = $video_description;
		}
		
		// delete main category if not present in data
		if (!isset($data["main_subcategory"])) {
			$data["main_subcategory"] = 0;
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

		$this->setState('video.id', $table->id);

		// if no category is selected, create a dummy relation with categoryId = -1 so that
		// the insertVideoRelations function deletes all other existing relations
		if (!isset($data['selectedSubcategories'])) {
			$data['selectedSubcategories'] = array(-1);
		}

		//save in video_category table
		$table = $this->getTable('CompanyCategory', "JTable");
		if (!empty($data["selectedSubcategories"])) {
			$table->insertVideoRelations($this->getState('video.id'), $data["selectedSubcategories"]);
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
