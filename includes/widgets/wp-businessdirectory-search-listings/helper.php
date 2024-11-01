<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

use MVC\Factory;
use MVC\Cache\CacheControllerFactoryInterface;

class modJBusinessDirectoryHelper {
	public static function getMainCategories() {
		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__jbusinessdirectory_categories 
				  WHERE parent_id=1 AND published=1 AND type=' . CATEGORY_TYPE_BUSINESS . '  
			      ORDER BY lft';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getSubCategories() {
		$db    = JFactory::getDBO();
		$query = 'SELECT c.* FROM #__jbusinessdirectory_categories c
                   INNER JOIN  #__jbusinessdirectory_categories  cc  ON c.parent_id = cc.id  
                   WHERE c.parent_id!=1 AND c.published=1  AND c.type=' . CATEGORY_TYPE_BUSINESS . '
                   ORDER BY c.lft';
		$db->setQuery($query, 0, 1000);
		$result = $db->loadObjectList();

		return $result;
	}

	public static function getTypes() {
		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__jbusinessdirectory_company_types ORDER BY ordering ASC';
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public static function getCities() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db          = JFactory::getDBO();
		$query       = "SELECT DISTINCT city, city AS id FROM #__jbusinessdirectory_companies
                  WHERE state = 1 AND city != ''
                    " . (($appSettings->show_secondary_locations) ? "
                  union
                  select distinct city, city as id from #__jbusinessdirectory_company_locations where city != ''
                    " : "") . "
                  ORDER BY city ASC";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getActivityCities() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDBO();

		$orderBy = ' name asc';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' ordering asc';
		}
		$query = 'SELECT id, name AS city FROM #__jbusinessdirectory_cities ORDER BY '.$orderBy;
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getRegions() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db          = JFactory::getDBO();
		$query       = "SELECT DISTINCT county, county AS id FROM #__jbusinessdirectory_companies 
				  WHERE state = 1 AND county != ''
                    " . (($appSettings->show_secondary_locations) ? "
                  union
                  select distinct county, county as id from #__jbusinessdirectory_company_locations where county != ''
                    " : "") . "
                  ORDER BY county ASC";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getCitiesRegions() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db          = JFactory::getDBO();
		$query       = "SELECT city, county 
                      FROM #__jbusinessdirectory_companies
                      WHERE state = 1 AND city != ''
                        " . (($appSettings->show_secondary_locations) ? "
                      union
                      select  city, county 
                        from #__jbusinessdirectory_company_locations where city != ''
                        " : "") . "
                  ORDER BY county ASC";

		$db->setQuery($query);
		$items = $db->loadObjectList();

		$result = array();
		foreach ($items as $item) {
			if (empty($result[$item->county])) {
				$result[$item->county] = array();
			}
			if (!in_array($item->city, $result[$item->county])) {
				$result[$item->county][] = $item->city;
			}
		}

		return $result;
	}

	public static function getActivityRegions() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDBO();

		$orderBy = ' name asc';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' ordering asc';
		}
		
		$query = 'SELECT id, name AS county FROM #__jbusinessdirectory_regions ORDER BY '.$orderBy;
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getProvinces() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db          = JFactory::getDBO();
		$query       = "SELECT DISTINCT province FROM #__jbusinessdirectory_companies WHERE state = 1 AND province != ''
                    " . (($appSettings->show_secondary_locations) ? "
                  UNION
                  Select distinct province FROM #__jbusinessdirectory_company_locations where province != ''
                    " : "") . "
                  ORDER BY province ASC";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getCountries() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db          = JFactory::getDBO();
		$query       = "SELECT DISTINCT c.id, c.country_name FROM #__jbusinessdirectory_countries c
                    INNER JOIN #__jbusinessdirectory_companies cp ON c.id = cp.countryId
                      WHERE country_name!=''
                    " . (($appSettings->show_secondary_locations) ? "
                  UNION
                  select distinct c.id, c.country_name FROM #__jbusinessdirectory_countries c
                    inner join #__jbusinessdirectory_company_locations lo on lo.countryId = c.id
                      where country_name!=''
                    " : "") . "
                  ORDER BY country_name ASC";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getCompanies($maxListings, $moduleId, $params) {
		if (isset($_REQUEST["search-results"])) {
			return $_REQUEST["search-results"];
		}

		//load items through cache mechanism
		$companies = null;
		$cache     = Factory::getCache('mod_jbusiness_listings_search', '');
		$key       = 'mod_jbusiness_listings' . $moduleId;
		if(!empty($cache)){
			if ($cache->contains($key) && $params->get("cache")) {
				$companies = $cache->get($key);
			} else {
				$companies = self::getCompaniesFromDB($maxListings);
				$cache->store($companies, $key);
			}
		}

		return $companies;
	}

	public static function getCompaniesFromDB($maxListings) {
		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$companiesTable                            = JTable::getInstance('Company', 'JTable');
		$appSettings                               = JBusinessUtil::getApplicationSettings();
		$searchDetails                             = array();
		$searchDetails["enablePackages"]           = $appSettings->enable_packages;
		$searchDetails["showPendingApproval"]      = ($appSettings->enable_item_moderation == '0' || ($appSettings->enable_item_moderation == '1' && $appSettings->show_pending_approval == '1'));
		$searchDetails["showSecondayLocationsMap"] = $appSettings->show_secondary_locations;
		$searchDetails["showCustomMarkers"]        = $appSettings->show_custom_markers;

		$companies = $companiesTable->getCompaniesByNameAndCategories($searchDetails, 0, $maxListings);

		foreach ($companies as $company) {
			$company->packageFeatures = array();
			if (!empty($company->features)) {
				$company->packageFeatures = explode(",", $company->features);
			}
			$attributesTable           = JTable::getInstance('CompanyAttributes', 'JTable');
			$company->customAttributes = $attributesTable->getCompanyAttributes($company->id);

			if ($appSettings->limit_cities_regions) {
				$company->regions = $companiesTable->getCompanyRegions($company->id);
				if (!empty($company->regions)) {
					$company->county = $company->regions[0]->name;
				}

				$company->cities  = $companiesTable->getCompanyCities($company->id);
				if (!empty($company->cities)) {
					$company->city = $company->cities[0]->name;
				}
			}
		}
		return $companies;
	}

	public static function getCustomAttributes($attributes, $atrributesValues) {
		$attributes = array_filter($attributes);
		if (empty($attributes)) {
			return;
		}

		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$attributesTable = JTable::getInstance('Attribute', 'JTable');

		$attributes = implode(",", array_filter($attributes));
		$attributes = $attributesTable->getAttributesConfiguration($attributes);

		foreach ($attributes as $attribute) {
			if (isset($atrributesValues[$attribute->id])) {
				$attribute->attributeValue = $atrributesValues[$attribute->id];
			}
		}
		return $attributes;
	}

	/**
	 * Filter categories
	 */
	public static function filterCategories($availableCategories){
		$appSettings = JBusinessUtil::getApplicationSettings();

		$categories = self::getMainCategories();
		$subCategories = self::getSubCategories();
	
		if ($appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateCategoriesTranslation($categories);
			JBusinessDirectoryTranslations::updateCategoriesTranslation($subCategories);
		}
	
		$availableCategories = array_filter($availableCategories);
		if (!empty($availableCategories)) {
			foreach ($categories as $key => $category) {
				if (!in_array($category->id, $availableCategories)) {
					unset($categories[$key]);
				}
			}
			foreach ($subCategories as $key => $category) {
				if (!in_array($category->id, $availableCategories)) {
					unset($subCategories[$key]);
				}
			}
		}
		
		$subcategoriesNoParent = array();
		foreach ($categories as $category) {
			foreach ($subCategories as $key => $subCat) {

				$found = false;
				foreach ($categories as $cat) {
					if ($cat->id == $subCat->parent_id) {
						$found = true;	
					}
				}

				if(!$found){
					//$subcategoriesNoParent[] = $cat;
				}else if ($category->id == $subCat->parent_id) {
					if (!isset($category->subcategories)) {
						$category->subcategories = array();
					}
					$category->subcategories[] = $subCat;
					unset($subCategories[$key]);
				}
			}
		}

		$categories = array_merge($categories, $subcategoriesNoParent);

		return $categories;
	}
}
