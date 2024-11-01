<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');
require_once(BD_HELPERS_PATH.'/category_lib.php');

class modJBusinessCategoriesHelper {
	public function getCategories($params, $categoriesIds = array(), $showListingsCount = false) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$categoryService = new JBusinessDirectorCategoryLib();
		$jinput = JFactory::getApplication()->input;
		
		if (!empty($categoriesIds)) {
			$categoriesIds = implode(",", $categoriesIds);
		} else {
			$categoriesIds = null;
		}
		
		$categories = $categoryService->getCategories(CATEGORY_TYPE_BUSINESS, $categoriesIds);

		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$categoryTable = JTable::getInstance('Category', 'JBusinessTable');

		$showRelated = $params->get('showrelated');
		if($showRelated){
			$categories = array();
			$categoryId = $jinput->getId('categoryId');
			if(empty($categoryId)){
				$categoryId = $jinput->getId('categorySearch');
			}
			
			$category = $categoryTable->getCategoryById($categoryId);
			if(!empty($category) && !empty($category->keywords)){
				$keywords = explode(",",$category->keywords);
				$cats = $categoryTable->getRelatedCategories($categoryId, $keywords);
				if(!empty($cats)){
					$catIds = array_map(function ($c) {
						return $c->id;
					},  $cats);
					$catIds = implode(',',$catIds);
					$categories = $categoryService->getCategories(CATEGORY_TYPE_BUSINESS, $catIds);
				}
			}
		}

		if (!empty($categories)) {
			if ($appSettings->show_total_business_count && $showListingsCount) {
				$details = array();
	
				$details["enablePackages"] = $appSettings->enable_packages;
				$details["showPendingApproval"] =  ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
	
				$listingsCount = $categoryTable->getCountPerCategory($details, CATEGORY_TYPE_BUSINESS);
	
				foreach ($categories as &$category) {
					if (isset($category[0]->id)) {
						$category[0]->nr_listings = isset($listingsCount[$category[0]->id]->nr_listings) ? $listingsCount[$category[0]->id]->nr_listings : '0';
					}
				}
			}
			
			$linkQuoteRequests = $params->get('linkquoterequests');
			
			foreach ($categories as &$category) {
				if (isset($category[0]->id)) {
					$catId = $category[0]->id;
					$category[0]->link = $linkQuoteRequests?"javascript:jbdQuoteRequest.init($catId)":JBusinessUtil::getCategoryLink($category[0]->id, $category[0]->alias);
					if (!empty($category["subCategories"])) {
						foreach ($category["subCategories"] as &$cat) {
							$cat[0]->link = JBusinessUtil::getCategoryLink($cat[0]->id, $cat[0]->alias);
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
		$categories = $categoryService->getCategories(CATEGORY_TYPE_BUSINESS, $categoriesIds);
		return $categories;
	}

	public function getCategoriesByIdsOnSlider($params, $categoriesIds) {
		$categoryService = new JBusinessDirectorCategoryLib();
		if (!empty($categoriesIds)) {
			$categoriesIds = implode(",", $categoriesIds);
		}
		$categories = $categoryService->getAllCategories(CATEGORY_TYPE_BUSINESS, $categoriesIds);
		$newCategories = array();
		
		$linkQuoteRequests = $params->get('linkquoterequests');
		foreach ($categories as $category) {
			$newCategories[$category->id] = array($category,"subCategories"=>array());
			
			if (isset($category->id)) {
				$catId = $category->id;
				$category->link = $linkQuoteRequests?"javascript:jbdQuoteRequest.init($catId)":JBusinessUtil::getCategoryLink($category->id, $category->alias);
			}
		}
		return $newCategories;
	}
}
