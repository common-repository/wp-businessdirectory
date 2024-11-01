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
 * Attribute Model for Attributes.
 *
 */
class JBusinessDirectoryModelAttribute extends JModelAdmin {
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JBUSINESSDIRECTORY_ATTRIBUTE';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jbusinessdirectory.attribute';

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
	public function getTable($type = 'Attribute', $prefix = 'JTable', $config = array()) {
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
		$jinput = JFactory::getApplication()->input;
		if (!($attributeType = $app->getUserState('com_jbusinessdirectory.attributes.filter.attribute_type'))) {
			$attributeType = $jinput->getInt('type', '1');
		}
		$this->setState('attribute.type', $attributeType);

		$id = $jinput->getInt('id');

		$this->setState('attribute.id', $id);
	}

	/**
	 * Method to get a menu item.
	 *
	 * @param   integer	The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null) {
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('attribute.id');
		$false	= false;

		$this->appSettings = JBusinessUtil::getApplicationSettings();

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

		$companyCategoryTable = $this->getTable('CompanyCategory');
		$value->selectedCategories = $companyCategoryTable->getSelectedAttributeCategories($itemId);
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateCategoriesTranslation($value->selectedCategories);
		}
		
		return $value;
	}

	
	public function getAttributeOptions() {
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('attribute.id');
		$row = $this->getTable('AttributeOptions');
		$attributeOptions = $row->getAttributeOptions($itemId);
		return $attributeOptions;
	}
	
	/**
	 * Get attribute types
	 */
	public function getAttributeTypes() {
		$row = $this->getTable('AttributeTypes');
		$attributeTypes = $row->getAttributeTypes();
		return $attributeTypes;
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
		$form = $this->loadForm('com_jbusinessdirectory.attribute', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
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
		$data = JFactory::getApplication()->getUserState('com_jbusinessdirectory.edit.attribute.data', array());

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
		$id	= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('attribute.id');
		if (empty($data['id'])) {
			$data['id'] = 0;
		}
		$isNew = true;

		//trim space for the submitted values
		foreach ($data as &$item) {
			if (!is_array($item)) {
				$item = trim($item);
			}
		}
		
		$defaultLng = JBusinessUtil::getLanguageTag();
		$name = JFactory::getApplication()->input->get("name_".$defaultLng, '', 'RAW');
		
		if (!empty($name) && empty($data["name"])) {
			$data["name"] = $name;
		}
		
		$data["code"]= JBusinessUtil::getAlias($data["name"], $data["code"]);
		
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

		$this->setState('attribute.id', $table->id);

		// Clean the cache
		$this->cleanCache();

		// if no category is selected, create a dummy relation with categoryId = -1 so that
		// the insertAttributeRelations function deletes all other existing relations
		if (!isset($data['categories'])) {
			$data['categories'] = array(-1);
		}

		if (!empty($data["categories"])) {
			$categoriesTable = $this->getTable('CompanyCategory');
			$categoriesTable->insertAttributeRelations($this->getState('attribute.id'), $data["categories"]);
		}

		if (!$this->saveAttributeOptions($data)) {
			return false;
		}
		
		JBusinessDirectoryTranslations::saveTranslations(ATTRIBUTE_TRANSLATION, $table->id, 'description_');
		
		return true;
	}
	
	public function saveAttributeOptions($data) {
		$optionsIds = $data['option_id'];
		$rowOpt = $this->getTable("AttributeOptions");
		
		if(!empty($optionsIds)){
			$optionsIds = array_filter($optionsIds);
		}
		
		$ids = implode(",", $optionsIds);
		if ($this->getState('attribute.id')) {
			$rowOpt->deleteAtributeOptions($this->getState('attribute.id'), $ids);
		}
		
		$optionsArray = $data['option_name'];
		$optionIcons = $data['icon'];
		
		foreach ($optionsArray as $key => $value) {
			$rowOpt = $this->getTable("AttributeOptions");
			$rowOpt->id = $optionsIds[$key];
			$rowOpt->name = $value;
			$rowOpt->ordering = $key;
			$rowOpt->icon = $optionIcons[$key];
			if ($rowOpt->icon=="la la-500px") {
				$rowOpt->icon= "";
			}
			$rowOpt->attribute_id = $this->getState('attribute.id');
			if (!$rowOpt->store()) {
				$application = JFactory::getApplication();
				$application->enqueueMessage($rowOpt->getError(), 'error');
				return false;
			}
		}
		return true;
	}
	
	public function state() {
		$query = 	' SELECT * FROM #__jbusinessdirectory_emails WHERE email_id = '.$this->_email_id;
	
		$this->_db->setQuery($query);
		$item = $this->_db->loadObject();
	
		$query = 	" UPDATE #__jbusinessdirectory_emails SET is_default = IF(email_id = ".$this->_email_id.", 1, 0)
		WHERE email_type = '".$item->email_type."'
		";
		$this->_db->setQuery($query);
		if (!$this->_db->execute()) {
			return false;
		}
		return true;
	}
	
	
	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
	 * @return  mixed  Returns null or true on success, array with not deleted values or false on failure.
	 */
	public function delete(&$itemIds) {
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		ArrayHelper::toInteger($itemIds);
		$app = JFactory::getApplication('administrator');
		$filterType = $app->getUserState('com_jbusinessdirectory.attributes.filter.attribute_type');

		if ($filterType == ATTRIBUTE_TYPE_OFFER) {
			$stockConfig = $this->getTable("OfferStockConfig");
		}

		// Get a group row instance.
		$table = $this->getTable();
		$used = array();
		// Iterate the items to delete each one.
		foreach ($itemIds as $key => $itemId) {
			if ($filterType == ATTRIBUTE_TYPE_OFFER) {
				if ($stockConfig->checkUsageOfAttribute($itemId)->nrUsed > 0) {
					$used[] = $itemId;
					unset($itemIds[$key]);
					continue;
				}
			}
			if (!$table->delete($itemId)) {
				$this->setError($table->getError());
				return false;
			}
			JBusinessDirectoryTranslations::deleteTranslationsForObject(ATTRIBUTE_TRANSLATION, $itemId);
		}

		if (!empty($itemIds) && $filterType == ATTRIBUTE_TYPE_OFFER) {
			$rowOpt = $this->getTable("attributeoptions");
			$idsS = implode(',', $itemIds);
			$rowOpt->deleteAllAtributeOptions($idsS);
		} elseif ($filterType != ATTRIBUTE_TYPE_OFFER) {
			$rowOpt = $this->getTable("attributeoptions");
			$idsS = implode(',', $itemIds);
			$rowOpt->deleteAllAtributeOptions($idsS);
		}

		// Clean the cache
		$this->cleanCache();
		if ($filterType == ATTRIBUTE_TYPE_OFFER) {
			if (!empty($used)) {
				return $used;
			} else {
				return null;
			}
		}

		return true;
	}
	
	public function changeState($id) {
		$this->populateState();
	
		$companiesTable = $this->getTable("Attribute");
		return $companiesTable->changeState($id);
	}
}
