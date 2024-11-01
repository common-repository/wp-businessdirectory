<?php
/**
 * @package     JBD.Administrator
 * @subpackage  com_categories
 *
 * @copyright  Copyright (C) 2007 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html;
 */

defined('_JEXEC') or die('Restricted access');
use MVC\Utilities\ArrayHelper;

/**
 * Categories Component Category Model
 *
 * @package     JBD.Administrator
 * @subpackage  com_categories
 * @since       1.6
 */
class JBusinessDirectoryModelCategory extends JModelAdmin {
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_JBUSINESSDIRECTORY_CATEGORY';
	protected $tableHeader = array("category", "subcategories", "type");
	protected $categories;
	protected $header;
	protected $headerDifferences = array();
	protected $newCategoryCount = 0;
	protected $failedCategories = array();
	protected $failedSubcategories = array();
	protected $newSubCategoryCount = 0;
	protected $error_row = 0;

	/**
	 * The type alias for this content type. Used for content version history.
	 *
	 * @var      string
	 * @since    3.2
	 */


	/**
	 * Override parent constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JModelLegacy
	 * @since   3.2
	 */
	public function __construct($config = array()) {
		parent::__construct($config);

		//used for importing
		$categories = array();
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object $record A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record) {
		return true;
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param   object $record A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record) {
		return true;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string $type The table name. Optional.
	 * @param   string $prefix The class prefix. Optional.
	 * @param   array $config Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Category', $prefix = 'JBusinessTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication('administrator');

		if (!($type = $app->getUserState('com_jbusinessdirectory.categories.filter.type'))) {
			$type = JFactory::getApplication()->input->getInt('type', CATEGORY_TYPE_BUSINESS);
		}
		$this->setState('category.type', $type);

		$parentId = $app->input->getInt('parent_id');
		$this->setState('category.parent_id', $parentId);

		// Load the User state.
		$pk = $app->input->getInt('id');
		$this->setState('category.id', $pk);

		// Load the parameters.
		// $params = JComponentHelper::getParams('com_categories');
		// $this->setState('params', $params);
	}

	/**
	 * Method to get a category.
	 *
	 * @param   integer $pk An optional id of the object to get, otherwise the id from the model state is used.
	 *
	 * @return  mixed    Category data object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null) {
		$itemId = (!empty($itemId)) ? $itemId : (int)$this->getState('category.id');
		$false = false;

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
		$result = ArrayHelper::toObject($properties, 'JObject');

		return $result;
	}

	/**
	 * Method to get the row form.
	 *
	 * @param   array $data Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true) {
	}

	/**
	 * A protected method to get the where clause for the reorder
	 * This ensures that the row will be moved relative to a row with the same extension
	 *
	 * @param   JCategoryTable $table Current table instance
	 *
	 * @return  array           An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table) {
		return 'extension = ' . $this->_db->quote($table->extension);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData() {

		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_categories.edit.' . $this->getName() . '.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to preprocess the form.
	 *
	 * @param   JForm $form A JForm object.
	 * @param   mixed $data The data expected for the form.
	 * @param   string $group The name of the plugin group to import.
	 *
	 * @return  void
	 *
	 * @see     JFormField
	 * @since   1.6
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content') {
	}

	/**
	 * Check for duplicate alias and generate a new alias
	 * @param unknown_type $busienssId
	 * @param unknown_type $alias
	 */
	public function checkAlias($categoryId, $alias, $type) {
		$cateoryTable = $this->getTable();
		while ($cateoryTable->checkAlias($categoryId, $alias, $type)) {
			$alias = $alias . "-1";
		}
		return $alias;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array $data The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data) {
		$table = $this->getTable();
		$jinput = JFactory::getApplication()->input;
		$imported = isset($data['imported'])?true:false;

		$pk = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('category.id');
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

		$description = $jinput->get("description_" . $defaultLng, '', 'RAW');
		$name = $jinput->get("name_" . $defaultLng, '', 'RAW');
		$meta_title = $jinput->get("meta_title_" . $defaultLng, '', 'RAW');
		$meta_desc = $jinput->get("meta_description_" . $defaultLng, '', 'RAW');
		$meta_key = $jinput->get("meta_keywords_" . $defaultLng, '', 'RAW');

		if ((!empty($meta_key) && empty($data["meta_keywords"])) || !isset($data["meta_keywords"])) {
			$data["meta_keywords"] = $meta_key;
		}

		if ((!empty($meta_title) && empty($data["meta_title"])) || !isset($data["meta_title"])) {
			$data["meta_title"] = $meta_title;
		}

		if ((!empty($meta_desc) && empty($data["meta_description"])) || !isset($data["meta_description"])) {
			$data["meta_description"] = $meta_desc;
		}

		if (!empty($description) && empty($data["description"])) {
			$data["description"] = $description;
		}

		if (!empty($name) && empty($data["name"])) {
			$data["name"] = $name;
		}

		$data["alias"] = JBusinessUtil::getAlias($data["name"], $data["alias"]);
		$data["alias"] = $this->checkAlias($pk, $data["alias"], $data["type"]);

		// Load the row if saving an existing category.
		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
		}

		//if its not new then publish or no the child depending on the parent
		if (!$isNew) {
			$children_array = array($data["id"]);
			$tasks = array('publish' => 1, 'unpublish' => 0);
			if ($data["published"] == 1) {
				$task = "publish";
			} else {
				$task = "unpublish";
			}
			$value = ArrayHelper::getValue($tasks, $task, 0, 'int');
			$children = $this->getAllChildrenCategory($data["id"]);
			foreach ($children as $child) {
				array_push($children_array, $child->id);
			}
			$this->publish($children_array, $value);
		}
		//if its not new then publish or no the child depending on the parent
		if (!$isNew) {
			$children_array = array($data["id"]);
			$tasks = array('publish' => 1, 'unpublish' => 0);
			if ($data["published"] == 1) {
				$task = "publish";
			} else {
				$task = "unpublish";
			}
			$value = ArrayHelper::getValue($tasks, $task, 0, 'int');
			$children = $this->getAllChildrenCategory($data["id"]);
			foreach ($children as $child) {
				array_push($children_array, $child->id);
			}
			$this->publish($children_array, $value);
		}

		// Set the new parent id if parent id not matched OR while New/Save as Copy .
		if ($table->parent_id != $data['parent_id'] || $data['id'] == 0) {
			$table->setLocation($data['parent_id'], 'last-child');
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

		// Rebuild the path for the category:
		if (!$table->rebuildPath($table->id)) {
			$this->setError($table->getError());
			return false;
		}

		// Rebuild the paths of the category's children:
		if (!$table->rebuild($table->id, $table->lft, $table->level, $table->path)) {
			$this->setError($table->getError());
			return false;
		}

		JBusinessDirectoryTranslations::saveTranslations(CATEGORY_META_TRANSLATION, $table->id, '', true);
		JBusinessDirectoryTranslations::saveTranslations(CATEGORY_TRANSLATION, $table->id, 'description_', false, $imported, $data);

		$this->setState($this->getName() . '.id', $table->id);

		return $table->id;
	}


	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array &$pks A list of the primary keys to change.
	 * @param   integer $value The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 */
	public function publish(&$pks, $value = 1) {
		if (parent::publish($pks, $value)) {
			return true;
		}
		return false;
	}

	public function changeState($id, $value) {
		$table = $this->getTable("Category");
		return $table->changeState($id, $value);
	}

	/**
	 * Method rebuild the entire nested set tree.
	 *
	 * @return  boolean  False on failure or error, true otherwise.
	 *
	 * @since   1.6
	 */
	public function rebuild() {
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->rebuild()) {
			$this->setError($table->getError());
			return false;
		}

		$db = JFactory::getDBO();
		$query = "UPDATE #__jbusinessdirectory_categories SET type=0, published=1 WHERE id=1 ";
		$db->setQuery($query);
		$db->execute();

		return true;
	}

	/**
	 * Method to save the reordered nested set tree.
	 * First we save the new order values in the lft values of the changed ids.
	 * Then we invoke the table rebuild to implement the new ordering.
	 *
	 * @param   array $idArray An array of primary key ids.
	 * @param   integer $lft_array The lft value
	 *
	 * @return  boolean  False on failure or error, True otherwise
	 *
	 * @since   1.6
	 */
	public function saveorder($idArray = null, $lft_array = null) {
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->saveorder($idArray, $lft_array)) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Prepare the file content to be imported
	 * @param string $filePath
	 * @param string $delimiter
	 */
	public function importCategoriesFromCSV($filePath, $delimiter) {
		$this->initializeImport();
		$type = 0;
		$row = 1;
		ini_set("auto_detect_line_endings", "1");
		if (($handle = fopen($filePath, "r")) !== false) {
			while (($data = fgetcsv($handle, 9000, $delimiter)) !== false) {
				$categoryData = array();

				if ($row == 1) {
					$this->header = $data;
					$this->checkHeaders();
					$row++;
					continue;
				}
				$num = count($data);

				//echo "<p> $num fields in line $row: <br /></p>\n";
				$row++;
				for ($c = 0; $c < $num; $c++) {
					$categoryData[strtolower($this->header[$c])] = $data[$c];
				}

				if ($type != $categoryData["type"]) {
					$type = $categoryData["type"];
					$this->categories = $this->getCategories($type);
				}

				$this->importCategory($categoryData);
			}
		}
	}

	/**
	 * Prepare the text area content to be imported
	 * @param array $csvContent
	 * @param string $delimiter
	 */
	public function importCategoriesFromTextArea($csvContent, $delimiter) {
		$this->initializeImport();
		$row = 1;
		$type = 0;
		foreach ($csvContent as $key => $content) {
			$data = str_getcsv($content, $delimiter);
			$categoryData = array();

			if ($row == 1) {
				$this->header = $data;
				$this->checkHeaders();
				$row++;
				continue;
			}
			$num = count($data);
			for ($c = 0; $c < $num; $c++) {
				$categoryData[strtolower($this->header[$c])] = $data[$c];
			}

			if ($type != $categoryData["type"]) {
				$type = $categoryData["type"];
				$this->categories = $this->getCategories($type);
			}

			$this->importCategory($categoryData);
			$row++;
		}
	}

	/**
	 * initialize the import
	 */
	public function initializeImport() {
		require_once(BD_HELPERS_PATH.'/category_lib.php');
		$service = new JBusinessDirectorCategoryLib();
		$service->createRootElement();
	}

	/**
	 * Check if headers on the file that will be imported are OK
	 * @return bool
	 */
	public function checkHeaders() {
		$languages = JBusinessUtil::getLanguages();
		foreach ($languages as $lng) {
			array_push($this->tableHeader, "category_$lng");
			array_push($this->tableHeader, "subcategories_$lng");
		}
		$headerDifferences = array_diff($this->header, $this->tableHeader);
		if ($headerDifferences != null) {
			$this->headerDifferences = $headerDifferences;
			return false;
		}
		return true;
	}

	/**
	 * Import a category based on provided data
	 * @param array $categories
	 */
	public function importCategory($categoryData) {

		if (!empty($categoryData["category"]) && !empty($categoryData["type"])) {
			$this->error_row++;
			$categoryId = 0;

			if (!isset($this->categories[$categoryData["category"]])) {
				$categoryId = $this->addCategory($categoryData["category"], 1, $categoryData["type"], $categoryData);
				if ($categoryId == false) {
					$failedCategory = new stdClass();
					$failedCategory->category = $categoryData["category"];
					$failedCategory->row = $this->error_row;
					array_push($this->failedCategories, $failedCategory);
				} else {
					$this->categories = $this->getCategories($categoryData["type"]);
					$this->newCategoryCount++;
				}
			} else {
				$categoryId = $this->categories[$categoryData["category"]];
			}

			
			//if category failed on insert then none of its subcategories will be saved
			if ($categoryId) {
				if (!empty($categoryData["subcategories"])) {
					$subcategories = explode(",", $categoryData["subcategories"]);
					foreach ($subcategories as $ind=>$subcategory) {
						if (!empty($subcategory)) {
							$subCatId = $this->addCategory($subcategory, $categoryId, $categoryData["type"], $categoryData, $ind);
							if ($subCatId == false) {
								$failedSubCategory = new stdClass();
								$failedSubCategory->subcategory = $categoryData["category"];
								$failedSubCategory->row = $this->error_row;
								array_push($this->failedSubcategories, $failedSubCategory);
							} else {
								$this->newSubCategoryCount++;
								$this->categories = $this->getCategories($categoryData["type"]);
							}
						}
					}
				}
			}
		}
	}

	public function exportCategoriesCSV() {
		$jinput = JFactory::getApplication()->input;
		$delimiter = $jinput->getString("delimiter", ",");

		$categoryTable = $this->getTable("Category", "JBusinessTable");
		$categories = $categoryTable->getCategoriesForExport();
		$languages = JBusinessUtil::getLanguages();

		$csv_output = "category" . $delimiter;

		foreach ($languages as $lng) {
			$csv_output.="category_$lng".$delimiter;
		}

		$csv_output .=  "subcategories" . $delimiter ;

		foreach ($languages as $lng) {
			$csv_output.="subcategories_$lng".$delimiter;
		}

		$csv_output .=  "type" . "\n";
			
		foreach ($categories as $category) {
			if ($category->name == "Root" || $category->name == "root") {
				continue;
			}
			
			$translations = JBusinessDirectoryTranslations::getAllTranslations(CATEGORY_TRANSLATION, $category->id);
		

			$name = "\"$category->name\"" . $delimiter;
			foreach ($languages as $lng) {
						$langContentName = isset($translations[$lng . "_name"]) ? $translations[$lng . "_name"] : "";
						$langContentName = str_replace(array("\r\n", "\r", "\n"), '<br />', $langContentName);
						$langContentName = str_replace('"', '""', $langContentName);

						$name .= "\"".$langContentName."\"".$delimiter;
			}
			
			$subcategoriesIds = explode(",", (string)$category->subcategories_ids);
			$subcategoryName = "\"$category->subcategories\"".$delimiter;
			
			foreach ($languages as $lng) {
				$subcategoryName .= "\"" ;
				$lngCatArray = [];
				foreach ($subcategoriesIds as $subcategoryId) {
					$translationsSubcat = JBusinessDirectoryTranslations::getAllTranslations(CATEGORY_TRANSLATION, $subcategoryId);
					$subLangContentName = isset($translationsSubcat[$lng . "_name"]) ? $translationsSubcat[$lng . "_name"] : "";
					
					if ($subLangContentName != "") {
						array_push($lngCatArray, $subLangContentName);
					}

					$subcats = implode(",", $lngCatArray);
				}
				$subcategoryName .= $subcats ."\"".$delimiter;
			}
			
			$csv_output .= $name. $subcategoryName. "\"$category->type\"";
			$csv_output .= "\n";
		}

		$fileName = "jbusinessdirectory_categories";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=" . $fileName . ".csv");
		print $csv_output;
	}

	public function getCategories($type) {
		$categoryService = new JBusinessDirectorCategoryLib();
		$categoryTable = JTable::getInstance("Category", "JBusinessTable");
		$categories = $categoryTable->getAllCategories($type);
		$result = array();
		foreach ($categories as $category) {
			$result[$category->name] = $category->id;
		}
		return $result;
	}

	public function addCategory($name, $parentId, $type, $data, $index = 0) {
		if (!isset($name) || strlen(trim($name)) < 2) {
			return;
		}

		$category = array();
		$category["id"] = 0;
		$category["parent_id"] = $parentId;
		$category["name"] = $name;
		$category["alias"] = "";
		$category["type"] = $type;
		$category["published"] = 1;
		$category["imported"] = true;

		$languages = JBusinessUtil::getLanguages();
		foreach ($languages as $lng) {
			if( $parentId == 1){
				$category["name_".strtolower($lng)] = isset($data["category_".strtolower($lng)]) ? $data["category_".strtolower($lng)] : "";
			}else{
				$catNames = isset($data["subcategories_".strtolower($lng)]) ? $data["subcategories_".strtolower($lng)] : "";
				if(!empty($catNames)){
					$catNames = explode(",",$catNames);
					$category["name_".strtolower($lng)] = $catNames[$index];
				}
				 
			}
		}

		return $this->save($category);
	}

	public function getCategoryTypes() {
		JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/components/com_jbusinessdirectory/models', 'Categories');
		$model = JModelLegacy::getInstance('Categories', 'JBusinessDirectoryModel', array('ignore_request' => true));
		$types = $model->getCategoryTypes();

		return $types;
	}

	public function getImportStatus() {
		$result = new stdClass();
		$result->differences = $this->headerDifferences;
		$result->correctHeader = $this->tableHeader;
		$result->failedCategories = $this->failedCategories;
		$result->failedSubcategories = $this->failedSubcategories;
		$result->newCategories = $this->newCategoryCount;
		$result->newSubCategoryCount = $this->newSubCategoryCount;

		if ($result->newSubCategoryCount) {
			$message = JText::plural('COM_JBUSINESS_DIRECTORY_N_SUBCATEGORIES_IMPORTED', $result->newSubCategoryCount);
			JFactory::getApplication()->enqueueMessage($message, 'success');
		}

		if (!empty($result->differences)) {
			$msg = JText::_('LNG_NOT_RECOGNIZED_HEADERS') . "<br />" . implode(", ", $result->differences);
			JFactory::getApplication()->enqueueMessage($msg, 'warning');
			$msg = JText::_('LNG_ALLOWED_HEADER') . "<br />" . implode(", ", $result->correctHeader);
			$msg .= "<br /><br />";
			JFactory::getApplication()->enqueueMessage($msg, 'success');
		}

		if (!empty($result->failedCategories)) {
			$msg = JText::_('LNG_IMPORT_FAILED_FOR_CATEGORIES');

			foreach ($result->failedCategories as $item) {
				$msg .= JFactory::getApplication()->enqueueMessage(JText::_('LNG_ROW') . " " . $item->row . "  " . JText::_('LNG_CATEGORY_NAME') . " " . $item->category);
			}
			JFactory::getApplication()->enqueueMessage($msg, 'warning');
		}

		if (!empty($result->failedSubcategories)) {
			$msg = JText::_('LNG_IMPORT_FAILED_FOR_SUBCATEGORIES');

			foreach ($result->failedSubcategories as $item) {
				$msg .= JFactory::getApplication()->enqueueMessage(JText::_('LNG_ROW') . " " . $item->row . "  " . JText::_('LNG_SUBCATEGORY_NAME') . " " . $item->subcategory);
			}
			JFactory::getApplication()->enqueueMessage($msg, 'warning');
		}

		if ($result->newCategories) {
			$message = JText::plural('COM_JBUSINESS_DIRECTORY_N_CATEGORIES_IMPORTED', $result->newCategories);
			JFactory::getApplication()->enqueueMessage($message, 'success');
		}

		return $result;
	}

	/**
	 * Return all child categories of a parent category
	 * @param $parentId
	 * @return mixed
	 */
	public function getAllChildrenCategory($parentId) {

		// Get a menu item row instance.
		$table = $this->getTable();

		$children = $table->getSubcategoriesByParentId($parentId);
		return $children;
	}
}
