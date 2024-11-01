<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class modJBusinessOfferSearchHelper {
	public static function getMainCategories() {
		$db = JFactory::getDBO();
		$query = ' SELECT * FROM #__jbusinessdirectory_categories 
					where parent_id=1 and published=1 and type='.CATEGORY_TYPE_OFFER.' 
					order by lft';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getTypes() {
		$db = JFactory::getDBO();
		$query = ' SELECT * FROM #__jbusinessdirectory_company_offer_types order by ordering asc';
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
	
	public static function getSubCategories() {
		$db = JFactory::getDBO();
		$query = ' SELECT c.* FROM #__jbusinessdirectory_categories c
                   inner join  #__jbusinessdirectory_categories  cc  on c.parent_id = cc.id  
                   where c.parent_id!=1 and c.published=1 and c.published=1 and c.type='.CATEGORY_TYPE_OFFER.'
                   order by c.lft';
		$db->setQuery($query, 0, 1000);
		$result = $db->loadObjectList();

		return $result;
	}

	public static function getCities() {
		$db = JFactory::getDBO();
		$query = ' SELECT distinct city FROM #__jbusinessdirectory_company_offers where state =1 and city!="" order by city asc';
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public static function getRegions() {
		$db = JFactory::getDBO();
		$query = 'SELECT distinct county FROM #__jbusinessdirectory_company_offers where state =1 and county!="" order by county asc';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getActivityRegions() {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db    = JFactory::getDBO();

		$orderBy = ' name asc';
		if($appSettings->cities_regions_order == ORDER_BY_ORDER) {
			$orderBy = ' ordering asc';
		}
		
		$query = 'select distinct name as county FROM #__jbusinessdirectory_regions order by '.$orderBy;
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getProvinces() {
		$db = JFactory::getDBO();
		$query = "select distinct province FROM #__jbusinessdirectory_company_offers 
					where state=1 and province!=''
                  	order by province asc";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getCountries() {
		$db = JFactory::getDBO();
		$query = "select distinct c.id, c.country_name FROM #__jbusinessdirectory_countries c
                    inner join #__jbusinessdirectory_company_offers cp on c.id = cp.countryId
                      where country_name!=''
                  order by country_name asc";
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
		
		$query = ' SELECT distinct name as city FROM #__jbusinessdirectory_cities order by '.$orderBy;
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getOffers($maxOffers, $itemType = OFFER_TYPE_OFFER) {
		if (isset($_REQUEST["offer-search-results"])) {
			return $_REQUEST["offer-search-results"];
		}

		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$offersTable = JTable::getInstance('Offer', 'JTable');
		$appSettings = JBusinessUtil::getApplicationSettings();
		$searchDetails = array();
		$searchDetails["enablePackages"] = $appSettings->enable_packages;
		$searchDetails["itemType"] = $itemType;
		$searchDetails["showPendingApproval"] = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));

		$offers =  $offersTable->getOffersByCategories($searchDetails, 0, $maxOffers);

		foreach ($offers as $offer) {
			$attributesTable =  JTable::getInstance('OfferAttributes', 'JTable');
			$offer->customAttributes = $attributesTable->getOfferAttributes($offer->id);
		}

		return $offers;
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
				$attribute->attributeValue =$atrributesValues[$attribute->id];
			}
		}
		return $attributes;
	}
}
