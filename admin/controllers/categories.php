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
 * The Categories List Controller
 *
 * @package     JBD.Administrator
 * @subpackage  com_categories
 * @since       1.6
 */
class JBusinessDirectoryControllerCategories extends JControllerAdmin {
	protected $newId;

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
	 * Proxy for getModel
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Category', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return  bool  False on failure or error, true on success.
	 *
	 * @since   1.6
	 */
	public function rebuild() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=categories', false));

		$model = $this->getModel();

		if ($model->rebuild()) {
			// Rebuild succeeded.
			$this->setMessage(JText::_('COM_CATEGORIES_REBUILD_SUCCESS'));

			return true;
		} else {
			// Rebuild failed.
			$this->setMessage(JText::_('COM_CATEGORIES_REBUILD_FAILURE'));

			return false;
		}
	}

	/**
	 * Save the manual order inputs from the categories list page.
	 *
	 * @return      void
	 *
	 * @since       1.6
	 * @see         JControllerAdmin::saveorder()
	 * @deprecated  4.0
	 */
	public function saveorder() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		JLog::add('CategoriesControllerCategories::saveorder() is deprecated. Function will be removed in 4.0', JLog::WARNING, 'deprecated');

		// Get the arrays from the Request
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder)) {
			parent::saveorder();
		} else {
			// Nothing to reorder
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));

			return true;
		}
	}

	public function publish() {
		$this->changeState();
	}

	public function unpublish() {
		$this->changeState();
	}

	/**
	 * Override the publish parent method to allow type parameter in link
	 *
	 * @return  void
	 *
	 */
	public function changeState() {
		// Get items to publish from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');
		$error = false;

		$model = $this->getModel();
	
		$children = $model->getAllChildrenCategory($cid[0]);
		
		if ($task!="publish") {
			foreach ($children as $child) {
				array_push($cid, $child->id);
			}
		}

		foreach ($cid as $id) {
			if (!$model->changeState($id, $value)) {
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
		}

		$ajax = $this->input->get('ajax');
		if (!empty($ajax)) {
			$response = array();
			$response["cid"] = $cid;
			$response["error"] = $error;

			echo json_encode($response);

			exit;
		}
	
		$type = $this->input->get('filter_type');
		$typeURL = ($type) ? '&filter_type=' . $type : '';
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $typeURL, false));
	}
	

	/**
	 * Deletes and returns correctly.
	 *
	 * @return  void
	 *
	 * @since   3.1.2
	 */
	public function delete() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'error');
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			ArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid)) {
				foreach ($cid as $id) {
					JBusinessDirectoryTranslations::deleteTranslationsForObject(CATEGORY_TRANSLATION, $id);
				}
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			} else {
				$this->setMessage($model->getError());
			}
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=categories', false));
	}
	
	public function importFromCsv() {
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=categories&layout=import', false));
	}
	
	public function showExportCsv() {
		$this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=categories&layout=export', false));
	}
	
	public function exportCategoriesCsv() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('Category');
		$model->exportCategoriesCsv();
		exit;
	}

	public function importCategoriesFromCsv() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$config = JBusinessUtil::getSiteConfig();
		$filePath = $config->tmp_path;
		$data = JFactory::getApplication()->input->post->getArray();
		//upload file
		$filePath = JBusinessUtil::uploadFile("csvFile", $data, $filePath);

		$model = $this->getModel();
		$model->importCategoriesFromCSV($filePath, $data["delimiter"]);
		$model->getImportStatus();
		
		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=categories');
	}

	public function importCategoriesFromTxtArea() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$data = JFactory::getApplication()->input->post->getArray();

		$csvContent = null;
		if (!empty($data["categoryData_ForImport"])) {
			$csvContent = explode("\n", $data["categoryData_ForImport"]);
		}

		$model = $this->getModel();
		$model->importCategoriesFromTextArea($csvContent, $data["delimiter"]);
		$model->getImportStatus();

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=categories');
	}

	public function duplicateCategories() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app      = JFactory::getApplication();
		$cid = $app->input->get('cid', array(), 'array');
		$vars = $app->input->post->get('catType', array(), 'array');
		$model = $this->getModel();
		$children = array();
		
		$parentTranslation = array();

		foreach ($cid as $key => $catid) {
			$model->setState('category.id', $catid);
			$item = $model->getItem();
						
			$parentId = $item->parent_id;
			if(!empty($parentTranslation[$parentId])){
				$parentId = $parentTranslation[$parentId];
			}

			$newItem = $this->prepareNewCategory( $catid, $parentId, $vars[0]);
			if ($this->saveDuplicate($newItem, $catid)) {
				$parentTranslation[$catid] = $this->newId;
			}
		}
	
		$this->setMessage(JText::plural('N_ITEMS_DUPLICATED', count($cid)), 'success');
		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=categories');
	}

	/**
	 *
	 * This function prepare the object and the state to save the new category
	 *
	 * @param $catId int the id of the item
	 * @param $parentId int parent id of the item. if the category is root than id=0
	 * @param $type int type of the category that will be duplicated (business,offer or event)
	 *
	 * @return mixed
	 *
	 * @since version
	 */
	public function prepareNewCategory($catId, $parentId, $type) {
		$model = $this->getModel();
		$model->setState('category.id', $catId);
		$item = $model->getItem();
		$item->parent_id = $parentId;
		$item->type = $type[0];
		$item->id = '';
		unset($item->lft);
		unset($item->rgt);
		unset($item->level);
		unset($item->path);
		unset($item->clickCount);
		$model->setState('category.id', '');
		return $item;
	}

	/**
	 *
	 * This function save the category data that will be send to it and also its translation if there is any
	 *
	 * @param $item Object with the category data to be saved
	 * @param $id int old id of the category that will be saved
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function saveDuplicate($item, $id) {
		$app      = JFactory::getApplication();
		$model = $this->getModel();
		$newItem = (array) $item;
		if (!$model->save($newItem)) {
			// Save the data in the session.
			$app->setUserState('com_jbusinessdirectory.edit.category.data', $newItem);
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			return false;
		} elseif (!empty($model->getErrors())) {
			$implodeErrors = implode('<br />', $model->getErrors());
			$this->setMessage(JText::sprintf('COM_JBUSINESSDIRECTORY_SAVED_WARNING', $implodeErrors), 'Warning');
		} else {
			// if save is OK then save also the translation of the category
			$newId = (int)$model->getState('category.id');
			$translations = JBusinessDirectoryTranslations::getAllTranslations(CATEGORY_TRANSLATION, $id);
			if (!empty($translations)) {
				$languages = JBusinessUtil::getLanguages();
				foreach ($languages as $lng) {
					$name = $translations[$lng . "_name"];
					$description = $translations[$lng];
					$shortDescription = $translations[$lng . "_short"];
					JBusinessDirectoryTranslations::saveTranslation(CATEGORY_TRANSLATION, $newId, $lng, $name, $shortDescription, $description);
				}
			}
		}
		$this->newId = $newId;
		return true;
	}
}
