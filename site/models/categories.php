<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');

require_once(BD_HELPERS_PATH.'/category_lib.php');

class JBusinessDirectoryModelCategories extends JModelList {
	public function __construct() {
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->categoryType = JFactory::getApplication()->input->get('categoryType', CATEGORY_TYPE_BUSINESS);
		$this->categoryId = JFactory::getApplication()->input->get('menuCategoryId');
		parent::__construct();
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Category', $prefix = 'JBusinessTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 *
	 * @return object with data
	 */
	public function getCategories() {
		$categoryService = new JBusinessDirectorCategoryLib();
		$categoryTable = $this->getTable();
		$categories = $categoryService->getAllCategories($this->categoryType);
		$categories = $categoryService->processCategories($categories);
		$startingLevel = 0;
		$path=array();
		$level =0;
		$categories["maxLevel"] = $categoryService->setCategoryLevel($categories, $startingLevel, $level, $path);
		
		//will check if the Category id is entered on menu item and it will return only the subcategories
		//if it is not it will show all categories based on category type
		if (!empty($this->categoryId)) {
			$categories = $categoryTable->getSubcategoriesByParentId($this->categoryId);
		}

		$cityName = JFactory::getApplication()->input->get('city');
		if (!empty($cityName)) {
			$companiesTable = $this->getTable("Company", 'JTable');
			$companies = $companiesTable->getCompaniesByCityName($cityName);
			if (empty($companies)) {
				return array();
			}
			$categoryIds = array();
			foreach ($companies as $company) {
				if (!empty($company->categories)) {
					$categories = explode('#|', $company->categories);
					foreach ($categories as $category) {
						$categoryDetails = explode("|", $category);
						$categoryIds[] = $categoryDetails[0];
					}
				}
			}
			if (count($categoryIds)) {
				$ids = $categoryIds;
				foreach ($categoryIds as $id) {
					$category = $categoryService->getCompleteCategoryById($id, CATEGORY_TYPE_BUSINESS);
					if ($category[0]->parent_id!=1 && !in_array($category[0]->parent_id, $ids)) {
						$ids[] = $category[0]->parent_id;
					}
				}
				$ids = implode(',', $ids);
				$categories = $categoryService->getCategories(CATEGORY_TYPE_BUSINESS, $ids);
			} else {
				return array();
			}
		}

		if ($this->appSettings->show_total_business_count) {
			$details = array();
			$details["enablePackages"] = $this->appSettings->enable_packages;
			$details["showPendingApproval"] = ($this->appSettings->enable_item_moderation=='0' || ($this->appSettings->enable_item_moderation=='1' && $this->appSettings->show_pending_approval == '1'));

			$listingsCount = $categoryTable->getCountPerCategory($details, $this->categoryType);
		}

		foreach ($categories as $category) {
			if (!is_array($category)) {
				$category = array($category);
				$category["subCategories"] = array();
			}
			if (isset($category[0]->id)) {
				$category[0]->nr_listings = isset($listingsCount[$category[0]->id]->nr_listings)?$listingsCount[$category[0]->id]->nr_listings:'0';

				switch ($this->categoryType) {
					case CATEGORY_TYPE_OFFER:
						$category[0]->link = JBusinessUtil::getOfferCategoryLink($category[0]->id, $category[0]->alias);
						if (!empty($category["subCategories"])) {
							foreach ($category["subCategories"] as $cat) {
								$cat[0]->link = JBusinessUtil::getOfferCategoryLink($cat[0]->id, $cat[0]->alias);
							}
						}
						break;
					case CATEGORY_TYPE_EVENT:
						$category[0]->link = JBusinessUtil::getEventCategoryLink($category[0]->id, $category[0]->alias);
						if (!empty($category["subCategories"])) {
							foreach ($category["subCategories"] as $cat) {
								$cat[0]->link = JBusinessUtil::getEventCategoryLink($cat[0]->id, $cat[0]->alias);
							}
						}
						break;
					default:
						$category[0]->link = JBusinessUtil::getCategoryLink($category[0]->id, $category[0]->alias);
						if (!empty($category["subCategories"])) {
							foreach ($category["subCategories"] as $cat) {
								$cat[0]->link = JBusinessUtil::getCategoryLink($cat[0]->id, $cat[0]->alias);
							}
						}
				}
			}
		}
		
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateCategoriesTranslation($categories);
		}

		if ($this->appSettings->category_order == ORDER_ALPHABETICALLY && count($categories)>0) {
			$categories = $categoryService->sortCategoryView($categories);
		}
		
		return $categories;
	}

	public function getCategoriesList($keyword, $type = CATEGORY_TYPE_BUSINESS) {
		$suggestionList = array();

		$language = JBusinessUtil::getLanguageTag();
		
		$table      = $this->getTable();
		$categories = $table->getCategoriesList($keyword, $type, $language);

		if ($type == CATEGORY_TYPE_BUSINESS) {
			$table     = $this->getTable('Company', 'JTable');
			$companies = $table->getSuggestions($keyword, 0, 10);
			
			if(!empty($companies)){
				$suggestion        = new stdClass();
				$suggestion->value = "";
				$suggestion->label = JText::_("LNG_BUSINESS_LISTINGS");
				$suggestion->parent_class='small';
				$suggestion->header= 1 ;
				$suggestion->type  = SUGGESTION_TYPE_BUSINESS;
				$suggestionList[]  = $suggestion;

				foreach ($companies as $company) {
					$suggestion        = new stdClass();
					$suggestion->value = $company->name;
					$suggestion->label = $company->name;
					$suggestion->class = 'la la-building';
					$suggestion->parent_class='item-listing';
					$suggestion->type  = SUGGESTION_TYPE_BUSINESS;
					$suggestionList[]  = $suggestion;
				}
			}
		} elseif ($type == CATEGORY_TYPE_OFFER) {
			$table     = $this->getTable('Offer', 'JTable');
			$offers    = $table->getSuggestions($keyword, 0, 10);

			foreach ($offers as $offer) {
				$suggestion        = new stdClass();
				$suggestion->value = $offer->name;
				$suggestion->label = $offer->name;
				$suggestion->class = 'la la-tag';
				$suggestion->parent_class='item-offer';
				$suggestion->type  = SUGGESTION_TYPE_OFFER;
				$suggestionList[]  = $suggestion;
			}
		} elseif ($type == CATEGORY_TYPE_EVENT) {
			$table     = $this->getTable('Event', 'JTable');
			$events    = $table->getSuggestions($keyword, 0, 10);

			foreach ($events as $event) {
				$suggestion        = new stdClass();
				$suggestion->value = $event->name;
				$suggestion->label = $event->name;
				$suggestion->class = 'la la-calendar ';
				$suggestion->parent_class='item-event';
				$suggestion->type  = SUGGESTION_TYPE_EVENT;
				$suggestionList[]  = $suggestion;
			}
		}

		if(!empty($categories)){

			$suggestion        = new stdClass();
			$suggestion->value = "";
			$suggestion->label = JText::_("LNG_CATEGORIES");
			$suggestion->parent_class='small';
			$suggestion->header= 1;
			$suggestion->type  = SUGGESTION_TYPE_CATEGORY;
			$suggestionList[]  = $suggestion;

			foreach ($categories as $category) {
				$suggestion        = new stdClass();
				$suggestion->value = $category->value;
				$suggestion->label = $category->label;
				$suggestion->id    = $category->id;
				$suggestion->class = 'la la-list';
				$suggestion->parent_class='item-category';
				$suggestion->type  = SUGGESTION_TYPE_CATEGORY;
				$suggestionList[]  = $suggestion;
			}
		}

		$suggestionList = json_encode($suggestionList);
		return $suggestionList;
	}

	public function getCategoryType() {
		return $this->categoryType;
	}

	/**
	 * Retrieves the subcategories of a category and generates a drop down list populated by these subcategories
	 * The default selected category in this drop down list is determined by the $catId param
	 * @param $parentId
	 * @param $type
	 * @param $level
	 * @param $catId
	 * @return string
	 */
	public function getSubcategoriesByParentIdAjax($parentId, $type, $level, $catId, $token) {
		$categoriesTable = $this->getTable('Category', 'JBusinessTable');
		$categories = $categoriesTable->getSubcategoriesByParentId($parentId, $type);

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateCategoriesTranslation($categories);
		}
		
		$output = '';
		if (!empty($categories)) {
			$level++;
			$output .= '<div class="form-field categories-form-field" id="' . $level . '">';
			$output .= '<select name="categorySearch" id="categories-' . $level . '" onchange="showCategorySelect'.$token.'(' . $level . ')">';
			$output .= '<option value="0">'.JText::_("LNG_ALL_CATEGORIES").'</option>';
			foreach ($categories as $category) {
				$selected = (!empty($catId)&&$catId==$category->id)?'selected':'';
				$output .= '<option value="' . $category->id . '" '.$selected.' >' . $category->name . '</option>';
			}
			$output .= '</select>';
			$output .= '</div>';
		}

		return $output;
	}

	/**
	 * Returns an array that represent a path in the category tree, which is comprised of all the parents of the
	 * category whose id is being passed as a param
	 * @param $id
	 * @param array $categories
	 * @return array
	 */
	public function getAllParentsById($id, $categories = array()) {
		$categoryTable = $this->getTable('Category', 'JBusinessTable');
		$category = $categoryTable->getCategoryById($id);

		if ($category->parent_id!=0) {
			array_push($categories, $category);
			return $this->getAllParentsById($category->parent_id, $categories);
		} else {
			return $categories;
		}
	}
}
