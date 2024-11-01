<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
require_once BD_HELPERS_PATH.'/category_lib.php' ;

class modJBusinessCategoriesEventsHelper {
	public function getCategories($categoriesIds = array(), $showListingsCount = false) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$categoryService = new JBusinessDirectorCategoryLib();
	
		if (!empty($categoriesIds)) {
			$categoriesIds = implode(",", $categoriesIds);
		} else {
			$categoriesIds = null;
		}
	
		$categories = $categoryService->getCategories(CATEGORY_TYPE_EVENT, $categoriesIds);
	
		if (!empty($categories)) {
			if ($appSettings->show_total_business_count && $showListingsCount) {
				$details = array();
	
				$details["enablePackages"] = $appSettings->enable_packages;
				$details["showPendingApproval"] =  ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
	
				JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
				$categoryTable = JTable::getInstance('Category', 'JBusinessTable');
				$listingsCount = $categoryTable->getCountPerCategory($details, CATEGORY_TYPE_EVENT);
	
				foreach ($categories as &$category) {
					if (!is_array($category)) {
						$category = array($category);
						$category["subCategories"] = array();
					}
					if (isset($category[0]->id)) {
						$category[0]->nr_listings = isset($listingsCount[$category[0]->id]->nr_listings) ? $listingsCount[$category[0]->id]->nr_listings : '0';
					}
				}
			}
			 
			foreach ($categories as &$category) {
				if (isset($category[0]->id)) {
					$category[0]->link = JBusinessUtil::getEventCategoryLink($category[0]->id, $category[0]->alias);
					if (!empty($category["subCategories"])) {
						foreach ($category["subCategories"] as $cat) {
							$cat[0]->link = JBusinessUtil::getEventCategoryLink($cat[0]->id, $cat[0]->alias);
						}
					}
				}
			}
		}
	
		return $categories;
	}

	public function getCategoriesByIdsOnMenu($categoriesIds) {
		$categoryService = new JBusinessDirectorCategoryLib();
		if (!empty($categoriesIds)) {
			$categoriesIds = implode(",", $categoriesIds);
		}
		$categories = $categoryService->getCategories(CATEGORY_TYPE_EVENT, $categoriesIds);
		return $categories;
	}

	public function getCategoriesByIdsOnSlider($categoriesIds) {
		$categoryService = new JBusinessDirectorCategoryLib();
		if (!empty($categoriesIds)) {
			$categoriesIds = implode(",", $categoriesIds);
		}
		$categories = $categoryService->getAllCategories(CATEGORY_TYPE_EVENT, $categoriesIds);
		$newCategories = array();
		foreach ($categories as $category) {
			$newCategories[$category->id] = array($category,"subCategories"=>array());
		}
		return $newCategories;
	}
}
