<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryControllerCategories extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	public function __construct() {
		parent::__construct();
	}

	public function displayCategories() {
		parent::display();
	}
	
	public function getCategories() {
		$keyword = JFactory::getApplication()->input->getString('term', null);
		$type = JFactory::getApplication()->input->get('type', CATEGORY_TYPE_BUSINESS);
		$type = intval($type);
		
		$type = intval($type);
		if (empty($keyword)) {
			JFactory::getApplication()->close();
		}
		
		$categoriesModel = $this->getModel("Categories");
		
		$categoriesList = $categoriesModel->getCategoriesList($keyword, $type);
		header('Content-Type: application/json');
		echo $categoriesList;
		
		JFactory::getApplication()->close();
	}

	/**
	 * Get's the id, type, level and parentId of a category and calls the respective method in the model
	 * The level is used to determine the order the select box is shown on the view
	 * The parentId is used to fetch all the subcategories in order to populate the drop down list
	 * The category id is used to determine which category is selected by default inside this drop down list
	 * The type is used to fetch only the categories of that type
	 */
	public function getSubcategoriesByParentIdAjax() {
		$jinput = JFactory::getApplication()->input;

		$parentId = $jinput->get('parentId', null);
		$type = $jinput->get('categoryType');
		$level = $jinput->get('level');
		$catId = $jinput->get('categoryId', null);
		$token = $jinput->get('token', null);
		
		$model = $this->getModel('Categories');
		$result = $model->getSubcategoriesByParentIdAjax($parentId, $type, $level, $catId, $token);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function getAllParentsByIdAjax() {
		$id = JFactory::getApplication()->input->get('categoryId', null);

		$model = $this->getModel('Categories');
		$result = $model->getAllParentsById($id);

		$this->appSettings = JBusinessUtil::getApplicationSettings();
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateCategoriesTranslation($result);
		}
		
		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}
}
