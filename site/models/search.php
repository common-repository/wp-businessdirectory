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
JTable::addIncludePath(DS.'components'.DS.'com_jbusinessdirectory'.DS.'tables');
require_once(BD_HELPERS_PATH.'/category_lib.php');
require_once BD_CLASSES_PATH.'/attributes/attributeservice.php';

class JBusinessDirectoryModelSearch extends JModelList {
	public function __construct() {
		parent::__construct();
		$this->context="com_jbusinessdirectory.listing.search";
		
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->mobileAppSettings = JBusinessUtil::getMobileAppSettings();
		
		$this->searchFilter = array();
		
		$this->prepareSearchAttribtues();

		$app = JFactory::getApplication();
		// Get pagination request variables
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $this->appSettings->dir_list_limit, 'int');
		$limitstart = $app->input->getInt('limitstart', 0, 'uint');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Companies', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function prepareSearchAttribtues() {
		$jinput = JFactory::getApplication()->input;

		$this->keyword = $jinput->getString('searchkeyword');
		if(!empty($this->keyword)){
			$this->keyword = str_replace("=", "", $this->keyword);
		}

		$categorySuggestion = $jinput->getInt('categorySuggestion');
		$suggestionType     = $jinput->getInt('suggestionType');
		if (!empty($categorySuggestion)) {
			if ($suggestionType == SUGGESTION_TYPE_CATEGORY) {
				$this->keyword        = '';
				$this->categorySearch = $categorySuggestion;
			}
		}

		$this->membershipSearch = $jinput->getInt('membershipSearch', null);		
		$this->packageSearch = $jinput->getInt('packageSearch', null);		
		$this->keywordLocation = $jinput->getString('searchkeywordLocation');
		$this->categoryId = $jinput->getInt('categoryId', null);
		$this->menuCategoryId = $jinput->getInt('menuCategoryId', null);
		
		$this->citySearch = $jinput->getString('citySearch', null);
		$this->typeSearch = $jinput->getInt('typeSearch', null);
		$this->regionSearch = $jinput->getString('regionSearch', null);
		$this->provinceSearch = $jinput->getString('provinceSearch', null);
		$this->countrySearch = $jinput->getInt('countrySearch', null);
		$this->onlyLocal = $jinput->getInt('only-local', null);
		$this->localActive = $jinput->getInt('local-active', null);
		$this->mobileCategoriesFilter = $jinput->getInt('mobileCategoriesFilter', null);
		$this->mobileSearch = $jinput->getInt('mobileSearch', null);

		if($this->mobileSearch) {
			$this->latitude = $jinput->getString('latitude', null);
			$this->longitude = $jinput->getString('longitude', null);
		}

		if($this->appSettings->limit_cities_regions){
			$this->countrySearch = $jinput->getString('countrySearch', null);
			if(!is_numeric($this->countrySearch)){
				$country = JBusinessUtil::getCountryByName($this->countrySearch);
				if(!empty($country)){
					$this->countrySearch = $country->id;
				}else{
					$this->countrySearch = "";
				}
			}
			
			if(!is_numeric($this->regionSearch)){
				$region = JBusinessUtil::getRegionByNameAndCountry($this->countrySearch, $this->regionSearch);
				if(!empty($region)){
					$this->regionSearch = $region->id;
				}else{
					$this->regionSearch = "";
				}
			}
			
			if(!is_numeric($this->citySearch)){
				$this->citySearch = $jinput->getString('citySearch', null);
				$city = JBusinessUtil::getCityByNameAndRegion($this->regionSearch, $this->citySearch);
				if(!empty($city)){
					$this->citySearch = $city->id;
				}else{
					$this->citySearch = "";
				}
			}
		}

		if (isset($_COOKIE['default-country']) && empty($this->countrySearch)) {
			$this->countrySearch = $_COOKIE['default-country'];
		}

		if($this->countrySearch == -1){
			$this->countrySearch = null;
		}
		
		if (empty($this->categorySearch)) {
			$this->categorySearch = $jinput->getId('categorySearch');
		}
		$this->zipCode = $jinput->getString('zipcode');
		$this->radius = $jinput->getInt('radius');
		$this->preserve = $jinput->getInt('preserve', null);
		$this->letter = $jinput->getString('letter', null);
		$this->areaSearch = $jinput->getString('areaSearch', null);
		$this->starRating = $jinput->getString('starRating', null);
		$this->filterByFav = $jinput->getInt('filter-by-fav', null);
		$this->orderBy = $jinput->getString("orderBy", $this->appSettings->order_search_listings);

		$allowedValues = $this->getSortByConfiguration(true);
		if (!JBusinessUtil::validateOrderBy($this->orderBy, $allowedValues)) {
			$this->orderBy = $this->appSettings->order_search_listings;
		}

		$this->resetSearch = $jinput->getInt('resetSearch', null);
		$this->categories = JFactory::getApplication()->input->getString("categories");
		
		if (isset($this->categorySearch) && empty($this->categoryId)) {
			$this->categoryId = $this->categorySearch;
		}
		if (!empty($this->menuCategoryId) && empty($this->categoryId) && !isset($this->preserve)) {
			$this->categoryId = $this->menuCategoryId;
		}

		$session = JFactory::getSession();
		
		$reload = $jinput->getInt('reload', null);
		if (empty($reload)) {
			if (!empty($this->categorySearch) || !empty($this->menuCategoryId) || !empty($this->resetSearch)) {
				$session->set('categorySearch', $this->categorySearch);
				$session->set('searchkeyword', "");
				$session->set('searchkeywordLocation', "");
				$session->set('typeSearch', "");
				$session->set('citySearch', "");
				$session->set('starRating', "");
				$session->set('regionSearch', "");
				$session->set('countrySearch', "");
				$session->set('zipcode', "");
				$session->set('customAtrributes', "");
				$session->set('letter', "");
				$session->set('radius', "");
				$session->set('provinceSearch', "");
				$session->set('areaSearch', "");
				$session->set('filter-by-fav', "");
				$session->set('filterParams', "");
				$session->set('location', "");
				$session->set('geo-latitude', "");
				$session->set('geo-longitude', "");
				$session->set('geo-country',"");
				$session->set('geolocation',"");
				$session->set('categories',"");
			}
			
			$session->set('onlyLocal', 0);

			if (!empty($this->resetSearch)) {
				$session->set('categoryId', $this->categoryId);
			}
	
			$session->set("listing-search", true);
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$lang = JFactory::getLanguage();
			if (!empty($menu->getActive()) && $menu->getActive() != $menu->getDefault($lang->getTag())) {
				$session->set("menuItemId", $menu->getActive()->id);
			}

			if (isset($this->categories)) {
				$session->set('categories', $this->categories);
			}
					
			if (isset($this->categoryId)) {
				$this->categoryId = intval($this->categoryId);
				$session->set('categoryId', $this->categoryId);
			}
			
			if (isset($this->typeSearch)) {
				$this->typeSearch = intval($this->typeSearch);
				$session->set('typeSearch', $this->typeSearch);
			}
			
			if (isset($this->citySearch)) {
				$session->set('citySearch', $this->citySearch);
			}

			if (isset($this->onlyLocal)) {
				$session->set('onlyLocal', $this->onlyLocal);
			}
			
			if (isset($this->starRating)) {
				$session->set('starRating', $this->starRating);
			}
			
			if (isset($this->regionSearch)) {
				$session->set('regionSearch', $this->regionSearch);
			}
	
			if (isset($this->provinceSearch)) {
				$session->set('provinceSearch', $this->provinceSearch);
			}
			
			if (isset($this->countrySearch)) {
				$this->countrySearch = intval($this->countrySearch);
				$session->set('countrySearch', $this->countrySearch);
			}

			if (isset($this->keyword)) {
				$this->keyword = trim($this->keyword);
				$session->set('searchkeyword', $this->keyword);
			}

			if (isset($this->keywordLocation)) {
				$session->set('searchkeywordLocation', $this->keywordLocation);
			}
			
			if (isset($this->zipCode)) {
				$this->zipCode = trim($this->zipCode);
				$session->set('zipcode', $this->zipCode);
			}
			
			if (isset($this->radius)) {
				$this->radius = intval($this->radius);
				$session->set('radius', $this->radius);
			}
			
			if (isset($this->letter)) {
				$session->set('letter', $this->letter);
			}
			
			if (isset($this->filterByFav)) {
				$session->set('filter-by-fav', $this->filterByFav);
			}
		}

		$this->categories = $session->get('categories');
		$this->categoryId = $session->get('categoryId');
		
		$this->keyword = $session->get('searchkeyword');
		$this->keywordLocation = $session->get('searchkeywordLocation');
		$this->typeSearch = $session->get('typeSearch');
		$this->citySearch = $session->get('citySearch');
		$this->onlyLocal = $session->get('onlyLocal');
		$this->starRating = $session->get('starRating');
		$this->letter = $session->get('letter');
		$this->regionSearch = $session->get('regionSearch');
		$this->provinceSearch = $session->get('provinceSearch');
		$this->countrySearch = $session->get('countrySearch');
		$this->categorySearch = $session->get('categorySearch');
		
		$this->zipCode = $session->get('zipcode');
		$this->radius = $session->get('radius');
		$this->filterByFav = $session->get('filter-by-fav');

		$this->location = null;
		$this->geoCountryCode = "";
		
		$geolocation = $jinput->getString('geolocation', null);
		if (isset($geolocation)) {
			$session->set("geolocation", $geolocation);
		}
		$geolocation = $session->get("geolocation");
		
		$this->locationDetected = $jinput->getInt('location-detected', null);
		if (isset($this->locationDetected)) {
			$session->set("location-detected", $this->locationDetected);
		}
		$this->locationDetected  = $session->get("location-detected");
		
		// test if geo location is determined and set location array
		if ($geolocation || $this->locationDetected) {
			$geoLatitutde = $jinput->getString('geo-latitude', null);
			$geoLongitude = $jinput->getString('geo-longitude', null);
			$geoCountry =  $jinput->getString('geo-country', null);
		
			if (!empty($geoLatitutde)) {
				$session->set('geo-latitude', $geoLatitutde);
			}
			if (!empty($geoLongitude)) {
				$session->set('geo-longitude', $geoLongitude);
			}
			if (!empty($geoCountry)) {
				$session->set('geo-country', $geoCountry);
			}
			$geoLatitutde = $session->get('geo-latitude');
			$geoLongitude = $session->get('geo-longitude');
			$geoCountry = $session->get('geo-country');

			$this->geoCountryCode = !empty($geoCountry)?JBusinessUtil::getCountryIDByCode($geoCountry):"";

			if (!empty($geoLatitutde) && !empty($geoLongitude)) {
				$this->location =  array();
				$this->location["latitude"] = $geoLatitutde;
				$this->location["longitude"] = $geoLongitude;
			}
		}
		
		$this->featured = $jinput->getInt('featured', null);
		
		$this->enablePackages = $this->appSettings->enable_packages;
		$this->showPendingApproval = ($this->appSettings->enable_item_moderation=='0' || ($this->appSettings->enable_item_moderation=='1' && $this->appSettings->show_pending_approval == '1'));
		$this->showSecondayLocationsMap =  $this->appSettings->show_secondary_locations_search;

		if (!empty($this->zipCode) && !$this->locationDetected) {
			$this->location = JBusinessUtil::getCoordinates($this->zipCode);
		}

		if (!empty($this->location)) {
			$session->set("location", $this->location);
		}
		
		//prepare custom attributes
		$data = $jinput->post->getArray();
		if (empty($data)) {
			$data = $jinput->get->getArray();
		}
		
		//custom attributes preparation
		if (isset($this->preserve)) {
			$session->set('customAtrributes', "");
		}
		
		$this->customAtrributes = array();
		foreach ($data as $key=>$value) {
			if (strpos($key, "attribute")===0) {
				$attributeId = explode("_", $key);
				$attributeId = $attributeId[1];
				if (!empty($value)) {
					if (!isset($this->customAtrributes[$attributeId])) {
						$this->customAtrributes[$attributeId] = [];
					}

					if(is_array($value)){
						$this->customAtrributes[$attributeId] = $value;
					}else{
						$this->customAtrributes[$attributeId][] = $value;
					}
				}
				$session->set('customAtrributes', "");
			}
		}

		if (!empty($this->customAtrributes)) {
			foreach ($this->customAtrributes as &$customAttribute) {
				if (is_array($customAttribute)) {
					$customAttribute = implode(",", $customAttribute);
				}
			}
			$session->set('customAtrributes', $this->customAtrributes);
		}

		if (!empty($params["custom-attributes"]) && empty($this->customAtrributes)) {
			$this->customAtrributes = $params["custom-attributes"][0];
			$session->set('customAtrributes', $this->customAtrributes);
		}

		$searchViewId = $jinput->getString("list_layout", $this->appSettings->search_result_view);

		//add in this array all search view ids that have images
		$imagesViewIds = array(7);
		$searchImages = true;
		if (!empty($searchViewId)) {
			if (!in_array($searchViewId, $imagesViewIds)) {
				$searchImages = false;
			}
		}
		$this->searchImages = $searchImages;

		$session->set('showCustomMarkers', intval($this->appSettings->show_custom_markers));
		$this->showCustomMarkers = $session->get('showCustomMarkers');
		
		$this->customAtrributes = $session->get('customAtrributes');
	}
	
	public function getCategoryId() {
		return $this->categoryId;
	}
	
	public function getSearchParams() {
		$categories = $this->getSelectedCategories();
		if($this->mobileCategoriesFilter) {
			$categories = $this->getMobileCategoriesFilter();
			if(empty($categories)) {
				$categories = explode(',', JFactory::getApplication()->input->getString('categoriesIds'));
			}
		}
		
		$categoryService = new JBusinessDirectorCategoryLib();
		$categoriesIds = array();
		if (!empty($categories) && (($this->appSettings->search_type==1 && $this->appSettings->enable_search_filter==1) || $this->mobileCategoriesFilter)) {
			foreach ($categories as $category) {
				$categoriesLevel= array();
				$cats = $categoryService->getCategoryLeafs($category, CATEGORY_TYPE_BUSINESS);
				//dump($category);
				//dump($cats);
				if (isset($cats)) {
					$categoriesLevel = array_merge($categoriesLevel, $cats);
				}
				$categoriesLevel[] = $category;
				$categoriesIds[] = implode(",", $categoriesLevel);
			}
		} elseif (!empty($this->categoryId) && ($this->appSettings->search_type==0 || $this->appSettings->enable_search_filter==0) || (!empty($this->categoryId) && $this->appSettings->search_filter_type ==1)) {
			$categoriesIds = $categoryService->getCategoryLeafs($this->categoryId, CATEGORY_TYPE_BUSINESS);
				
			if (isset($this->categoryId) && $this->categoryId !=0) {
				if (isset($categoriesIds) && count($categoriesIds) > 0) {
					$categoriesIds[] = $this->categoryId;
				} else {
					$categoriesIds = array($this->categoryId);
				}
			}
			$categoriesIds = array_filter($categoriesIds);
			$categoriesIds = array(implode(",", $categoriesIds));
		}

		if($this->mobileCategoriesFilter && !empty($categoriesIds)) {
			$categoriesIds = array('0' => implode(',', $categoriesIds));
		}
		
		$searchDetails = array();
		$searchDetails["keyword"] = $this->keyword;
		$searchDetails["keywordLocation"] = $this->keywordLocation;
		$searchDetails["categoriesIds"] = $categoriesIds;

		if (!empty($this->location)) {
			$searchDetails["latitude"]  = $this->location["latitude"];
			$searchDetails["longitude"] = $this->location["longitude"];
		}

		if (!empty($this->geoCountryCode)) {
			$this->countrySearch = $this->geoCountryCode;
		}
		
		$radius = $this->radius;
		if ($this->appSettings->metric==0) {
			$radius  = $radius * 0.621371;
		}

		$params = $this->getSelectedParams();
		$db =JFactory::getDBO();
		if (!empty($params["type"])) {
			$this->typeSearch = $db->escape($params["type"][0]);
		}

		if (!empty($params["country"])) {
			$this->countrySearch = $db->escape($params["country"][0]);
		}

		if (!empty($params["region"])) {
			$this->regionSearch = $params["region"][0];
		}

		if (!empty($params["province"])) {
			$this->provinceSearch = $params["province"][0];
		}

		if (!empty($params["city"])) {
			$this->citySearch = $params["city"][0];
		}

		if (!empty($params["starRating"])) {
			$this->starRating = $params["starRating"][0];
		}

		if (!empty($params["area"])) {
			$this->areaSearch = $params["area"][0];
		}

		if (!empty($params["membership"])) {
			$this->membershipSearch = $params["membership"][0];
		}

		if (!empty($params["package"])) {
			$this->packageSearch = $params["package"][0];
		}

		if (!empty($params["letter"])) {
			$this->letter = $db->escape($params["letter"][0]);
		}

		if (!empty($params["custom-attributes"])) {
			$this->customAtrributes = $params["custom-attributes"][0];
		}

		$moreParams = $this->getMoreParams();
		$session = JFactory::getSession();
		if (!empty($moreParams)) {
			foreach ($moreParams as $key => $param) {
				$searchDetails[$key] = $param;
			}
			$session->set('moreFilters', $moreParams);
		} else {
			$session->set('moreFilters', null);
		}

		$searchDetails["radius"] = $radius;
		$searchDetails["typeSearch"] = $this->typeSearch;
		$searchDetails["citySearch"] = $this->citySearch;
		$searchDetails["starRating"] = $this->starRating;
		$searchDetails["regionSearch"] = $this->regionSearch;
		$searchDetails["countrySearch"] = $this->countrySearch;
		
		if($this->onlyLocal){
			if(isset($this->citySearch)){
				$searchDetails["regionSearch"]="";
				$searchDetails["countrySearch"]="";
			}
		}

		$searchDetails["enablePackages"] = $this->enablePackages;
		$searchDetails["showPendingApproval"] = $this->showPendingApproval;
		$searchDetails["orderBy"] = $this->orderBy;
		$searchDetails["facetedSearch"] = $this->appSettings->search_type;
		$searchDetails["zipcCodeSearch"] = $this->appSettings->zipcode_search_type;
		$searchDetails["limit_cities_regions"] = $this->appSettings->limit_cities_regions;
		$searchDetails["customAttributes"] = $this->customAtrributes;
		$searchDetails["featured"] = $this->featured;
		$searchDetails["showSecondayLocationsMap"] = $this->showSecondayLocationsMap;
		$searchDetails["multilingual"] = $this->appSettings->enable_multilingual;
		$searchDetails["letter"] = $this->letter;
		$searchDetails["membershipSearch"] = $this->membershipSearch;
		$searchDetails["packageSearch"] = $this->packageSearch;
		$searchDetails["displayMembership"] = $this->appSettings->listings_display_info == MEMBERSHIPS;
		$searchDetails["areaSearch"] = $this->areaSearch;
		$searchDetails["provinceSearch"] = $this->provinceSearch;
		$searchDetails["searchImages"] = $this->searchImages;
		$searchDetails["showCustomMarkers"] = $this->showCustomMarkers;
		$searchDetails["filter-by-fav"] = $this->filterByFav;
		$searchDetails["zip-code"] = $this->zipCode;

		if($this->mobileSearch) {
			$searchDetails["latitude"]  = $this->latitude;
			$searchDetails["longitude"] = $this->longitude;
		}

		// dump($searchDetails);
		// exit;
		return $searchDetails;
	}
	
	
	/**
	 * Method to get a cache id based on the search results.
	 *
	 * This is necessary because the different search parameters are used
	 *
	 * @param   string  $id  An identifier string to generate the cache id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   12.2
	 */
	protected function getCacheId($params, $id = '') {
		if (!empty($params)) {
			$params = array_filter($params);
			foreach ($params as $param) {
				if (is_array($param)) {
					$id .= ':'.implode(",", $param);
				} else {
					$id .= ':'.$param;
				}
			}
		}
		// Add the list state to the store id.
		$id .= ':'.$this->getState('limitstart');
		$id .= ':'.$this->getState('limit');
		$id .= ':'.$this->getState('list.ordering');
		$id .= ':'.$this->getState('list.direction');
	
		return md5($this->context . ':' . $id);
	}
	
	/**
	 * Retrieve the listings search results. (from database or from elastic search - depending on the general search)
	 *
	 * @param [type] $searchDetails
	 * @return void
	 */
	public function getCompanies($searchDetails,$limitstart,$limit){
		$companiesTable = $this->getTable("Company");
		$listings = null;

		if ($this->appSettings->enable_elastic_search) {
			$elasticSearch = new ElasticSearchIndexerListings($searchDetails,$limitstart, $limit);
			$result = $elasticSearch->search($searchDetails, $limitstart, $limit);
			if(!empty($result)){
				$this->_total = $result->total;
				$listings = $result->items;
				$this->result = $result;
			}
		}else{
			if(empty($this->_total)){
				$this->getTotal();
			}

			$limitstart = $limitstart > $this->_total ? ($this->_total - $limit) : $limitstart;
			if($limitstart<0){
				$limitstart = 0;
			}
			$this->setState('limitstart', $limitstart);

			$listings = $companiesTable->getCompaniesByNameAndCategories($searchDetails, $limitstart, $limit);
		}

		return $listings;
	}

	/**
	 * Retrieve all items without limitation
	 *
	 * @return void
	 */
	public function getAllItems(){
		return $this->getItems(0,0,true);
	}
	
	/**
	 * Retrieve search results items
	 * @return multitype:
	 */
	public function getItems($limitstart = 0, $limit = 0, $allItems = false) {
		$companiesTable = $this->getTable("Company");
		$searchDetails = $this->getSearchParams();

		if(empty($limitstart)){
			$limitstart = $this->getState('limitstart');
		}

		if(empty($limit)){
			$limit = $this->getState('limit');
		}
		
		if($allItems){
			$limitstart = 0;
			$limit = 300;
			$searchDetails["allItems"] = true;
		}

		JBusinessUtil::logSearch($searchDetails, SEARCH_LOG_TYPE_LISTING);
		$companies = array();
		if ($this->appSettings->enable_cache) {
			$cacheIdentifier = $this->getCacheId($searchDetails, "Items");
			try {
				$cache = JCache::getInstance();
				$companies = $cache->get($cacheIdentifier);
				if (empty($companies)) {
					$companies = $this->getCompanies($searchDetails, $limitstart, $limit);
					$cache->store($companies, $cacheIdentifier);
				}
			} catch (RuntimeException $e) {
				$this->setError($e->getMessage());
				return null;
			}
		}

		if (empty($companies)) {
			$companies = $this->getCompanies($searchDetails, $limitstart, $limit);
		}
		
		$attributeConfig = JBusinessUtil::getAttributeConfiguration(DEFAULT_ATTRIBUTE_TYPE_LISTING);

		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateBusinessListingsTranslation($companies);
			JBusinessDirectoryTranslations::updateBusinessListingsSloganTranslation($companies);
		}

		foreach ($companies as $company) {
			if (!empty($company->locations)) {
				$locations = explode("#", $company->locations);
				$distances = isset($company->secondaryDistances) ? explode(',', $company->secondaryDistances) : array();
				if (!empty($distances)) {
					$distance =  floatval($distances[0]);
					foreach ($locations as $k => $location) {
						$tmp = array();
						$loc = explode("|", $location);
						$address = JBusinessUtil::getLocationAddressText($loc[2], $loc[3], $loc[9], $loc[4], $loc[5], $loc[8], $loc[6]);
						if (floatval($distances[$k]) < floatval($company->distance) && floatval($distances[$k]) <= floatval($distance)) {
							$company->bestMatchLocation = $address;
						}
					}
				}
			}

			JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jbusinessdirectory/models', 'Companies');
			$comanyModel = JModelLegacy::getInstance('Companies', 'JBusinessDirectoryModel', array('ignore_request' => true));

			$company->business_hours = $comanyModel->getWorkingDays($company);


			if(empty($company->time_zone)){
				$company->time_zone = $this->appSettings->default_time_zone;
			}

			$company->enableWorkingStatus = false;
			if (!empty($company->business_hours) && $company->opening_status == COMPANY_OPEN_BY_TIMETABLE) {
				foreach ($company->business_hours as $day) {
					if ($day->workHours["status"] == '1') {
						$company->enableWorkingStatus = true;
					}
				}
			}

			if ($company->enableWorkingStatus) {
				$company->workingStatus = $comanyModel->getWorkingStatus($company->business_hours, $company->time_zone, $company->opening_status);
			} else {
				$company->workingStatus = false;
			}
			
			$company->packageFeatures= array();
			if (!empty($company->features)) {
				$company->packageFeatures = explode(",", $company->features);
				$company->packageFeatures = array_filter($company->packageFeatures);
			}
			
			$categoryId = null;
			if ($this->appSettings->enable_attribute_category) {
				$categoryId = -1;
				if (!empty($company->mainSubcategory)) {
					$categoryId = $company->mainSubcategory;
				}
			}
			
			if (!empty($companies)) {
				$attributesTable = $this->getTable('CompanyAttributes');
				$customAttributes = $attributesTable->getCompanyAttributes($company->id, $categoryId);
				
				$attributes = array();
				foreach ($customAttributes as $attr) {
					if ($attr->show_in_list_view) {
						$attributes[]=$attr;
					}
				}
				$company->customAttributes = $attributes;
			}
			
			//remove the attributes that are set to hidden
			$company = JBusinessUtil::updateItemDefaultAtrributes($company, $attributeConfig);
			
			if (empty($company->latitude) && empty($company->longitude)) {
				$company->distance = 0;
			}

			if (!empty($company->distance) && $this->appSettings->metric == 0) {
				$company->distance = $company->distance * 1.6;
			}

			if (!empty($company->pictures)) {
				$pictures = [];

				$tmpPictures = explode(',', $company->pictures);
				foreach ($tmpPictures as $key=>$val) {
					$picture = new stdClass();

					$tmpPicture = explode('#', $val);
					$picture->picture_path = $tmpPicture[0];
					$picture->picture_title = !empty($tmpPicture[1]) ? $tmpPicture[1] : "";
					$picture->picture_info = !empty($tmpPicture[2]) ? $tmpPicture[2] : "";

					$pictures[] = $picture;
				}

				$company->pictures = $pictures;
			}

			if ($this->appSettings->limit_cities_regions) {
				$table = $this->getTable('Company');
				$company->regions = $table->getCompanyRegions($company->id);
				if (!empty($company->regions)) {
					$company->county = $company->regions[0]->name;
				}
				$company->cities = $table->getCompanyCities($company->id);
				if (!empty($company->cities)) {
					$company->city = $company->cities[0]->name;
				}
			}

			if (!empty($company->categories)) {
				$company->categories = explode('#|', $company->categories);
				foreach ($company->categories as $k=>&$category) {
					$category = explode("|", $category);
				}
			}

			//exploding memberhips
			if (!empty($company->memberships)) {
				$company->memberships = explode('#|', $company->memberships);
				foreach ($company->memberships as $k=>&$membership) {
					$membership = explode("|", $membership);
				}
			}

			//TODO change the approach - very poor
			$maxCategories = !empty($company->categories)?count($company->categories):0;
			if ($this->appSettings->enable_packages) {
				$table = $this->getTable("Package");
				$package = $table->getCurrentActivePackage($company->id);
				$company->package = $package;
				if (!empty($package->max_categories) && $maxCategories > (int)$package->max_categories) {
					$maxCategories = (int)$package->max_categories;
				}
			} elseif (!empty($this->appSettings->max_categories)) {
				$maxCategories = $this->appSettings->max_categories;
			}

			if ($this->appSettings->search_result_view == 6 && !empty($company->categories)) {
				foreach ($company->categories as $k2=>$category2) {
					if (empty($category2[3]) || $category2[3] == "None") {
						unset($company->categories[$k2]);
					}
				}
			}

			if (!empty($company->categories)) {
				$company->categories = array_slice($company->categories, 0, $maxCategories);
			}

			if (GET_DATA_FROM_YELP) {
				$yelpData = JBusinessUtil::getYelpData($company->yelp_id, false);
				if (isset($yelpData->error) || empty($company->yelp_id)) {
					$company->review_score = 0;
				} else {
					$company->review_score = $yelpData->rating;
				}
			}

		}
		
		if ($this->appSettings->mix_results) {
			$companies = $this->shuffleListings($companies);
		}
		
		$_REQUEST["search-results"] = $companies;

		return $companies;
	}

	public function orderByMemberships($items){
		$index = 0;
		$i = 0;
		$processing = true;
		$processed = 0;
		$positionFound = 0;
		while($processing || $i<2000){
			foreach($items as $idx=>$item){
				if(!empty($item->memberships) && $idx>$index){
					if($processed == 0){
						$positionFound = $idx - $idx%3;
					}else{
						$positionFound ++;
					}

					$val = $item->memberships[$idx];
					$item->memberships[$idx] = $item->memberships[$positionFound];
					$item->memberships[$positionFound] = $val;
			
					$index = $idx;
					$processed++;

					if($processed == 3){
						$processed = 0;
					}
				}
			}

			$i++;
		}

	}
	
	/**
	 * Change the order the listings within their interval
	 * 
	 * @param unknown $items
	 */
	public function shuffleListings($items){
		
		$result = array();
		$iResult = array();
		$featured = false;
		$packageId = null;
		
		foreach($items as $item){
			if($item->featured !=$featured || ($this->appSettings->enable_packages && $packageId != $item->package_id)){
				$featured = $item->featured;
				$packageId = $item->package_id;

				shuffle($iResult);
				$result = array_merge($result, $iResult);

				//reset the temporary array
				$iResult = array();
			}
			$iResult[] = $item;
		}
		
		shuffle($iResult);
		$result = array_merge($result, $iResult);
	
		return $result;
	}
	
	public function getTotal() {
		// Load the content if it doesn't already exist
		
		if (empty($this->_total)) {
			$searchDetails = $this->getSearchParams();
			$companiesTable = $this->getTable("Company");
				
			if ($this->appSettings->enable_cache) {
				$cacheIdentifier = $this->getCacheId($searchDetails, "getTotal");
				try {
					$cache = JCache::getInstance();
					$this->_total = $cache->get($cacheIdentifier);
					if (empty($this->_total)) {
						$this->_total = $companiesTable->getTotalCompaniesByNameAndCategories($searchDetails);
						$cache->store($this->_total, $cacheIdentifier);
					}
				} catch (RuntimeException $e) {
					$this->setError($e->getMessage());
					return null;
					;
				}
			}
		
			if (empty($this->_total)) {
				$this->_total = $companiesTable->getTotalCompaniesByNameAndCategories($searchDetails);
			}
		}
		
		return $this->_total;
	}
	
	public function getPagination() {
		$jinput = JFactory::getApplication()->input;
		
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			require_once(BD_HELPERS_PATH.'/dirpagination.php');
			$this->_pagination = new JBusinessDirectoryPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
			$this->_pagination->setAdditionalUrlParam('option', 'com_jbusinessdirectory');
			$this->_pagination->setAdditionalUrlParam('controller', 'search');
			
			if (!empty($this->categoryId)) {
				$this->_pagination->setAdditionalUrlParam('categoryId', $this->categoryId);
			}
			$this->_pagination->setAdditionalUrlParam('categoryId', $this->categoryId);
			
			if (!empty($this->categorySearch)) {
				$this->_pagination->setAdditionalUrlParam('categorySearch', $this->categorySearch);
			}
			$categories = $jinput->getString("categories");
			if (!empty($categories)) {
				$this->_pagination->setAdditionalUrlParam('categories', $categories);
			}
			
			$orderBy = $jinput->getString("orderBy", $this->appSettings->order_search_listings);
			if (!empty($orderBy)) {
				$this->_pagination->setAdditionalUrlParam('orderBy', $orderBy);
			}
			
			if (!empty($this->keyword)) {
				$this->_pagination->setAdditionalUrlParam('searchkeyword', $this->keyword);
			}

			if (!empty($this->citySearch)) {
				$this->_pagination->setAdditionalUrlParam('citySearch', $this->citySearch);
			}

			if (!empty($this->starRating)) {
				$this->_pagination->setAdditionalUrlParam('starRating', $this->starRating);
			}
			
			if (!empty($this->zipCode)) {
				$this->_pagination->setAdditionalUrlParam('zipcode', $this->zipCode);
			}
			
			if (!empty($this->regionSearch)) {
				$this->_pagination->setAdditionalUrlParam('regionSearch', $this->regionSearch);
			}

			if (!empty($this->provinceSearch)) {
				$this->_pagination->setAdditionalUrlParam('provinceSearch', $this->provinceSearch);
			}
			
			if (!empty($this->countrySearch)) {
				$this->_pagination->setAdditionalUrlParam('countrySearch', $this->countrySearch);
			}
			
			if (!empty($this->typeSearch)) {
				$this->_pagination->setAdditionalUrlParam('typeSearch', $this->typeSearch);
			}
			
			if (!empty($this->letter)) {
				$this->_pagination->setAdditionalUrlParam('letter', $this->letter);
			}
			
			if (!empty($this->radius)) {
				$this->_pagination->setAdditionalUrlParam('radius', $this->radius);
			}

			if (!empty($this->resetSearch)) {
				$this->_pagination->setAdditionalUrlParam('resetSearch', $this->resetSearch);
			}

			if (!empty($this->customAtrributes)) {
				foreach ($this->customAtrributes as $key=>$val) {
					$this->_pagination->setAdditionalUrlParam('attribute_'.$key, $val);
				}
			}
			
			if (!empty($this->preserve)) {
				$this->_pagination->setAdditionalUrlParam('preserve', $this->preserve);
			}
			
			$this->_pagination->setAdditionalUrlParam('view', 'search');
		}
		return $this->_pagination;
	}
	

	/**
	 * Calls the corresponding function to build the search results
	 *
	 * @return void
	 */
	public function getSearchFilter(){
		if ($this->appSettings->enable_elastic_search) {
			return $this->getSearchFilterES();
		}else{
			return $this->getSearchFilterDB();
		}
	}

	/**
	 * Create the ElasticSearch search filter results
	 *
	 * @return void
	 */
	public function getSearchFilterES() {
		if (empty($this->appSettings->search_filter_fields)) {
			return;
		}

		if (!empty($this->appSettings->search_filter_fields)) {
			$this->appSettings->search_filter_fields = explode(",", $this->appSettings->search_filter_fields);
		}


		$statistics  = $this->result->statistics;
		//dump($statistics);
		if (in_array("cities", $this->appSettings->search_filter_fields)) {
			$cityStatistics = $statistics["city_filter"];
			$cities = array();
			foreach ($cityStatistics["buckets"] as $stat) {

				$cityObj = new stdClass();
				$cityObj->cityName = $stat["key"];
				$cityObj->city     = $stat["key"];
				$cityObj->nr_listings = $stat["doc_count"];
				$cities[$stat["key"]] = $stat["key"];
				$cities[$stat["key"]]->nr_listings = $stat["doc_count"];
			}
			$this->searchFilter["cities"] = $cities;
		}

		return $this->searchFilter;
	}
	
	/**
	 * Creates the database search filter results
	 */

	public function getSearchFilterDB() {
		$startTime = microtime(true); // Gets current microtime as one long string
		if (empty($this->appSettings->search_filter_fields)) {
			return;
		}
		
		$companiesTable = $this->getTable("Company");
		$searchDetails = $this->getSearchParams();
		$searchDetails["facetedSearch"] = $this->appSettings->search_type;
		$searchDetailsCategories = $searchDetails["categoriesIds"];

		if (!empty($this->appSettings->search_filter_fields)) {
			$this->appSettings->search_filter_fields = explode(",", $this->appSettings->search_filter_fields);
		}

		if (in_array("categories", $this->appSettings->search_filter_fields)) {
			$categoryService = new JBusinessDirectorCategoryLib();

			$category = array();
			if (!empty($this->categoryId) && $this->appSettings->search_type != 1) {
				$category = $categoryService->getCompleteCategoryById($this->categoryId, CATEGORY_TYPE_BUSINESS);
			} else {
				$category["subCategories"] = $categoryService->getCategories();
				$category["path"] = array();
			}
			
			if (empty($category["subCategories"])) {
				$searchDetails["categoriesIds"] = array($category[0]->parent_id);
			}

			if ($this->appSettings->search_type == 1) {
				$searchDetails["categoriesIds"] = null;
			}

			//$categoriesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, 'category');
			$categoriesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, 'category');
			//dump($categoriesTotal);
			if (empty($categoriesTotal) && !(empty($category["subCategories"]) && !empty($category[0])) && $this->appSettings->search_type != 1) {
				if (isset($category[0]) && $category[0]->parent_id != 1) {
					$category = $categoryService->getCompleteCategoryById($category[0]->parent_id, CATEGORY_TYPE_BUSINESS);
				} else {
					$category["subCategories"] = $categoryService->getCategories(CATEGORY_TYPE_BUSINESS);
					$category["path"] = array();
				}
				if (isset($category[0])) {
					$searchDetails["categoriesIds"] = array($category[0]->parent_id);
				}
				$categoriesTotal = $companiesTable->getTotalCompaniesByObject(array(), 'category');
			}

			//filter categories by general settings
			$filteredCategories = $this->appSettings->search_categories;
			$filteredCategories = array_filter($filteredCategories);
			if(!empty($filteredCategories) && $this->appSettings->show_top_filter){
				$filteredTotal = array();
				foreach ($categoriesTotal as $categoryTotal) {
					if (in_array($categoryTotal->id, $filteredCategories)) {
						$filteredTotal[] = $categoryTotal;
					}
				}

				$categoriesTotal = $filteredTotal;
			}

			$subcategories = '';
			$enableSelection = false;
			if ($this->appSettings->enable_multilingual) {
				$categoryTranslations = JBusinessDirectoryTranslations::getCategoriesTranslations();
				foreach ($category["path"] as &$path) {
					if (!empty($categoryTranslations[$path[0]])) {
						$path[1] = $categoryTranslations[$path[0]]->name;
					}
				}
			}

			if (isset($category["path"])) {
				$this->searchFilter["path"] = $category["path"];
			}

			if (!empty($category["subCategories"])) {
				$subcategories = $category["subCategories"];
			} else {
				if ($category[0]->parent_id == 1) {
					$subcategories = $categoryService->getCategories();
					$this->searchFilter["enableSelection"] = 1;
					$enableSelection = true;
				} else if(!empty($category["path"])){
					$parentCategories = $category["path"];
					if (!empty($parentCategories)) {
						$categoryId = $parentCategories[count($parentCategories)][0];
						//dump($categoryId);
						$parentCategory = $categoryService->getCompleteCategoryById($categoryId, CATEGORY_TYPE_BUSINESS);
						$subcategories = $parentCategory["subCategories"];
						$this->searchFilter["enableSelection"] = 1;
						$enableSelection = true;
					}
				}
			}
			
			$categories = array();
			if (!empty($subcategories)) {
				if ($this->appSettings->enable_multilingual) {
					JBusinessDirectoryTranslations::updateCategoriesTranslation($subcategories);
				}
				
				foreach ($subcategories as $cat) {
					if (!is_array($cat)) {
						continue;
					}

					$childCategoryIds = $categoryService->getCategoryChilds($cat);

					if (count($childCategoryIds) == 0) {
						$childCategoryIds = array($cat[0]->id);
					} else {
						$mainCat = array($cat[0]->id);
						$childCategoryIds = array_merge($mainCat, $childCategoryIds);
					}

					$companies = array();
					$companiesNumber = 0;
					foreach ($categoriesTotal as $categoryTotal) {
						if (in_array($categoryTotal->id, $childCategoryIds)) {
							$companiesNumber += $categoryTotal->nr_listings;
						}
					}

					if ($companiesNumber > 0) {
						$this->searchFilter["categories"][] = array($cat, $companiesNumber);
					}
				}
			}

			$searchDetails["categoriesIds"] = $searchDetailsCategories;
			$searchDetails["facetedSearch"] = 0;

			//$startTime = microtime(true); // Gets current microtime as one long string

			if (!empty($this->searchFilter["categories"]) && $this->appSettings->category_order == ORDER_ALPHABETICALLY) {
				$this->searchFilter["categories"] = $categoryService->sortCategories($this->searchFilter["categories"], false, false);

				foreach($this->searchFilter["categories"] as &$cat){
					$cat[0]['subCategories'] = $categoryService->sortCategories($cat[0]['subCategories'], true, false, true, true);
				}
			}

			//$endTime = microtime(true) - $startTime; // And this at the end of your code
			//echo 'Sort operation ended successfully and took '.round($endTime, 4).' seconds to run!';
		}
		
		if (in_array("cities", $this->appSettings->search_filter_fields)) {
			$citiesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "city");
			
			$cities = array();
			foreach ($citiesTotal as $city) {
				if (!empty(trim($city->cityName))) {
					$city->city = mb_convert_encoding($city->city, 'UTF-8', 'UTF-8');
					if (!isset($cities[$city->city])) {
						$cities[$city->city] = $city;
						$cities[$city->city]->nr_listings = (int)$city->nr_listings;
					} else {
						$cities[$city->city]->nr_listings += $city->nr_listings;
					}
				}
				
				if (!empty($city->secCityNames) && isset($city->secCityNames)) {
					$secLocationCities = explode(',', $city->secCityNames);
					$secLocationCities = array_filter($secLocationCities);
					foreach ($secLocationCities as $secLocationCity) {
						if ($secLocationCity != $city->cityName) {
							if (isset($cities[$secLocationCity])) {
								$cities[$secLocationCity]->nr_listings += 1;
							} else {
								$cityObj = new stdClass();
								$cityObj->cityName = mb_convert_encoding($secLocationCity, 'UTF-8', 'UTF-8');
								$cityObj->city     = mb_convert_encoding($secLocationCity, 'UTF-8', 'UTF-8');
								$cityObj->nr_listings = 1;
								$cities[$secLocationCity] = $cityObj;
							}
						}
					}
				}
			}

			
			$this->searchFilter["cities"] = $cities;
		}

		if (in_array("regions", $this->appSettings->search_filter_fields)) {
			$regionsTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "region");
			$regions = array();
			foreach ($regionsTotal as $region) {
				if (!empty(trim($region->regionName))) {
					$region->regionName = mb_convert_encoding($region->regionName, 'UTF-8', 'UTF-8');
					if (!isset($regions[$region->region])) {
						$regions[$region->region] = $region;
						$regions[$region->region]->nr_listings = (int)$region->nr_listings;
					} else {
						$regions[$region->region]->nr_listings += $region->nr_listings;
					}
				}

				if (!empty($region->secRegionNames) && isset($region->secRegionNames)) {
					$secondaryRegions = explode(',', $region->secRegionNames);
					$secondaryRegions = array_filter($secondaryRegions);
					foreach ($secondaryRegions as $secLocationRegion) {
						if ($secLocationRegion != $region->regionName) {
							if (isset($regions[$secLocationRegion])) {
								$regions[$secLocationRegion]->nr_listings += 1;
							} else {
								$regionObj = new stdClass();
								$regionObj->regionName =  mb_convert_encoding($secLocationRegion, 'UTF-8', 'UTF-8');
								$regionObj->region     =  mb_convert_encoding($secLocationRegion, 'UTF-8', 'UTF-8');
								$regionObj->nr_listings = 1;
								$regions[$secLocationRegion] = $regionObj;
							}
						}
					}
				}
			}
			$this->searchFilter["regions"] = $regions;
		}

		if (in_array("starRating", $this->appSettings->search_filter_fields)) {
			$ratingsTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "starRating");
			$ratings = array();
			foreach ($ratingsTotal as $rating) {
				if (!empty($rating->reviewScore)) {
					if (!isset($ratings[$rating->reviewScore])) {
						$ratings[$rating->reviewScore] = $rating;
						$ratings[$rating->reviewScore]->nr_listings = (int)$rating->nr_listings;
					} else {
						$ratings[$rating->reviewScore]->nr_listings += $rating->nr_listings;
					}
				}
			}

			$this->searchFilter["starRating"] = $ratings;
		}

		if (in_array("countries", $this->appSettings->search_filter_fields)) {
			$countriesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "country");
			$countries = array();
			foreach ($countriesTotal as $country) {
				if (!empty($country->countryName)) {
					if (!isset($countries[$country->countryId])) {
						$countries[$country->countryId] = $country;
						$countries[$country->countryId]->nr_listings = (int)$country->nr_listings;
					} else {
						$countries[$country->countryId]->nr_listings += $country->nr_listings;
					}
				}

				if (!empty($country->secCountryNames) && isset($country->secCountryNames)) {
					$allCountries = $this->getCountries();
					$secondaryCountry = explode(',', $country->secCountryNames);
					$secondaryCountry= array_filter($secondaryCountry);
					foreach ($secondaryCountry as $secLocationCountry) {
						if ($secLocationCountry != $country->countryId) {
							if (isset($countries[$secLocationCountry])) {
								$countries[$secLocationCountry]->nr_listings += 1;
							} else {
								$countryObj = new stdClass();
								$countryObj->countryName = $allCountries[$secLocationCountry]->country_name;
								$countryObj->countryId = $secLocationCountry;
								$countryObj->nr_listings = 1;
								$countries[$secLocationCountry] = $countryObj;
							}
						}
					}
				}
			}

			if ($this->appSettings->enable_multilingual) {
				foreach ($countries as $countryData) {
					$country = new stdClass();
					$country->id = $countryData->countryId;
					$country->country_name = $countryData->countryName;

					JBusinessDirectoryTranslations::updateEntityTranslation($country, COUNTRY_TRANSLATION);
					$countryData->countryName = $country->country_name;
				}
			}

			$this->searchFilter["countries"] = $countries;
		}

		if (in_array("types", $this->appSettings->search_filter_fields)) {
			$typesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "type");
			$types = array();
			if (!empty($typesTotal)) {
				if ($this->appSettings->enable_multilingual) {
					JBusinessDirectoryTranslations::updateTypesTranslation($typesTotal);
				}
				$companiesTable = $this->getTable("Company");
				$companyTypes = $companiesTable->getCompanyTypes();

				$typeArray = array();
				foreach ($typesTotal as $key => $type) {
					$ids = explode(',', $type->typeId);
					if (count($ids) > 1) {
						foreach ($companyTypes as $index => $compType) {
							if (in_array($compType->value, $ids)) {
								$newObject = new stdClass();
								$newObject->typeName = $compType->text;
								$newObject->nr_listings = $type->nr_listings;
								//$newObject->activity_radius = $type->activity_radius;
								$newObject->typeId = $compType->value;
								$typeArray[] = $newObject;
							}
						}
						unset($typesTotal[$key]);
					}
				}

				foreach ($typeArray as $array) {
					$typesTotal[] = $array;
				}
				$companyTypeTable = $this->getTable('CompanyTypes');
				foreach ($typesTotal as $type) {
					if (!empty($type->typeName)) {
						if (!isset($types[$type->typeId])) {
							$types[$type->typeId] = $type;
							$types[$type->typeId]->nr_listings = (int)$type->nr_listings;
							$types[$type->typeId]->ordering = $companyTypeTable->getCompanyType($type->typeId)->ordering;
						} else {
							$types[$type->typeId]->nr_listings += $type->nr_listings;
						}
					}
				}
				
				// if (!empty($types) and count($types)>1) {
				//  	uasort($types, function ($a, $b) {
				//  		return $a->ordering > $b->ordering?1:0;
				//  	});
				// }

				// $types = array_values($types);
			}
			$this->searchFilter["types"] = $types;
		}

		if (in_array("area", $this->appSettings->search_filter_fields)) {
			$areasTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "area");
			$areas = array();
			if (!empty($areasTotal)) {
				foreach ($areasTotal as $area) {
					if (!empty($area->areaName)) {
						if (!isset($areas[$area->areaName])) {
							$areas[$area->areaName] = $area;
							$areas[$area->areaName]->nr_listings = (int)$area->nr_listings;
						} else {
							$areas[$area->areaName]->nr_listings += $area->nr_listings;
						}
					}
					if (!empty($area->secAreaNames) && isset($area->secAreaNames)) {
						$secondaryArea = explode(',', $area->secAreaNames);
						foreach ($secondaryArea as $secLocationArea) {
							if ($secLocationArea != $area->areaName) {
								if (isset($areas[$secLocationArea])) {
									$areas[$secLocationArea]->nr_listings += 1;
								} else {
									$areaObj = new stdClass();
									$areaObj->areaName = $secLocationArea;
									$areaObj->nr_listings = 1;
									$areas[$secLocationArea] = $areaObj;
								}
							}
						}
					}
				}
			}
			$this->searchFilter["areas"] = $areas;
		}

		if (in_array("province", $this->appSettings->search_filter_fields)) {
			$provincesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "province");
			$provinces = array();
			if (!empty($provincesTotal)) {
				foreach ($provincesTotal as $province) {
					if (!empty($province->provinceName)) {
						if (!isset($provinces[$province->provinceName])) {
							$provinces[$province->provinceName] = $province;
							$provinces[$province->provinceName]->nr_listings = (int)$province->nr_listings;
						} else {
							$provinces[$province->provinceName]->nr_listings += $province->nr_listings;
						}
					}
					if (!empty($province->secProvinceNames) && isset($province->secProvinceNames)) {
						$secondaryProvince = explode(',', $province->secProvinceNames);
						$secondaryProvince = array_filter($secondaryProvince);
						foreach ($secondaryProvince as $secLocationProvince) {
							if ($secLocationProvince != $province->provinceName) {
								if (isset($provinces[$secLocationProvince])) {
									$provinces[$secLocationProvince]->nr_listings += 1;
								} else {
									$provinceObj = new stdClass();
									$provinceObj->provinceName = $secLocationProvince;
									$provinceObj->nr_listings = 1;
									$provinces[$secLocationProvince] = $provinceObj;
								}
							}
						}
					}
				}
			}
			$this->searchFilter["provinces"] = $provinces;
		}

		if (in_array("memberships", $this->appSettings->search_filter_fields)) {

			$membershipsTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "memberships");

			$memberships = array();
			if (!empty($membershipsTotal)) {
				foreach ($membershipsTotal as $membership) {
					if (!empty($membership->membership_id)) {
						if (!isset($memberships[$membership->membership_id])) {
							$memberships[$membership->membership_id] = $membership;
							$memberships[$membership->membership_id]->nr_listings = (int)$membership->nr_listings;
						} else {
							$memberships[$membership->membership_id]->nr_listings += $membership->nr_listings;
						}
					}
				}
			}

			$this->searchFilter["memberships"] = $memberships;
		}

		if (in_array("packages", $this->appSettings->search_filter_fields) && $this->appSettings->enable_packages) {

			$packagesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "packages");

			$packages = array();
			if (!empty($packagesTotal)) {
				foreach ($packagesTotal as $package) {
					if (!empty($package->package_id)) {
						if (!isset($packages[$package->package_id])) {
							$packages[$package->package_id] = $package;
							$packages[$package->package_id]->nr_listings = (int)$package->nr_listings;
						} else {
							$packages[$package->package_id]->nr_listings += $package->nr_listings;
						}
					}
				}
			}

			$this->searchFilter["packages"] = $packages;
		}

		$attributeTable = $this->getTable("Attribute", "JTable");
		$customAttributes = $attributeTable->getSearchAttributeValues();
		$customAttributesIds = array_map(function ($c) {
			return $c->id;
		},  $customAttributes);

		$intersect = array_intersect($customAttributesIds, $this->appSettings->search_filter_fields);
		if (!empty($intersect)) {
			$attributesTotal = $companiesTable->getTotalCompaniesByObject($searchDetails, "attributes");

			//dump($attributesTotal);
			$attributes = array();
			if (!empty($attributesTotal)) {
				foreach ($attributesTotal as $attrTot) {

					$attrTot->value = intval($attrTot->value);
					if (!empty($attrTot->id) && in_array($attrTot->id, $intersect)) {
						if (!isset($attributes[$attrTot->name])) {
							$attributes[$attrTot->name] = array();
						}
						if(empty($attributes[$attrTot->name][$attrTot->value])){
							$attributes[$attrTot->name][$attrTot->value] = $attrTot;
						}
					}
				}
			}
			$attributes = array_values($attributes);
			$this->searchFilter["attributes"] = $attributes;
			
			//dump($attributes);
		}

		$endTime = microtime(true) - $startTime; // And this at the end of your code
		//echo PHP_EOL . 'Search filter took ' . round($endTime, 4) . ' seconds to run. <br/>';

		return $this->searchFilter;
	}

	public function getCategory() {
		$categoryTable = $this->getTable("Category", "JBusinessTable");
		$category = $categoryTable->getCategoryById($this->categoryId);
		
		if (empty($category)) {
			return $category;
		}
		
		if ($this->appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEntityTranslation($category, CATEGORY_TRANSLATION);
		}
		
		if (!empty($category->description) && $category->description==strip_tags($category->description)) {
			$category->description = str_replace("\n", "<br/>", $category->description);
		}
		
		return $category;
	}

	public function getType() {
		$table = $this->getTable("CompanyTypes", "JTable");
		$item = $table->getCompanyType($this->typeSearch);

		return $item;
	}

	public function getMoreParams() {
		$moreParamsSelected = JFactory::getApplication()->input->getString("moreParams");
		$params = array();

		if (!empty($moreParamsSelected)) {
			$values = explode(";", $moreParamsSelected);
			foreach ($values as $val) {
				if (!empty($val) && !isset($params[$val])) {
					$params[$val] = $val;
				}
			}
		}

		return $params;
	}

	public function getSelectedParams() {
		$session = JFactory::getSession();
		$params = array();
		$values = array();
		
		$this->letter = JFactory::getApplication()->input->getString("letter");
		$selectedParams =JFactory::getApplication()->input->getString("selectedParams");
		
		if (!isset($selectedParams)) {
			$selectedParams = $session->get("filterParams");
		}
		
		if (isset($selectedParams)) {
			$session->set("filterParams", $selectedParams);
		}
		
		if (!empty($selectedParams)) {
			$values = explode(";", $selectedParams);
		}

		$values = array_filter($values);
		foreach ($values as $val) {
			$temp = explode("=", $val);
			if(empty($temp[1])){
			 	continue;
			}
			if (!isset($params[$temp[0]])) {
				$params[$temp[0]] = array();
			}
			if (!empty($temp[0])) {
				array_push($params[$temp[0]], $temp[1]);
			}
		}

		if (!empty($this->categoryId) && !isset($params["category"])) {
			$params["category"][] = $this->categoryId;
		}

		if (!empty($this->countrySearch) && !isset($params["country"])) {
			$params["country"][] = $this->countrySearch;
		}else{
			if(!isset($params["country"])){
				$params["country"] = array();
			}
		}

		if (!empty($this->regionSearch) && !isset($params["region"])) {
			$params["region"][] = $this->regionSearch;
		}

		if (!empty($this->provinceSearch) && !isset($params["province"])) {
			$params["province"][] = $this->provinceSearch;
		}

		if (!empty($this->citySearch) && !isset($params["city"])) {
			$params["city"][] = $this->citySearch;
		}

		if (!empty($this->typeSearch) && !isset($params["type"])) {
			$params["type"][] = $this->typeSearch;
		}

		if (!empty($this->areaSearch) && !isset($params["area"])) {
			$params["area"][] = $this->areaSearch;
		}

		if (!empty($this->membershipSearch) && !isset($params["membership"])) {
			$params["membership"][] = $this->membershipSearch;
		}

		if (!empty($this->packageSearch) && !isset($params["packages"])) {
			$params["package"][] = $this->packageSearch;
		}

		if (!empty($this->starRating) && !isset($params["starRating"])) {
			$params["starRating"][] = $this->starRating;
		}

		if (!empty($this->letter) && !isset($params["letter"])) {
			$params["letter"][] = $this->letter;
		}

		$customAttributes = array();
		foreach ($params as $key=>$value) {
			if (strpos($key, "attribute")===0) {
				$attributeId = explode("_", $key);
				$attributeId = $attributeId[1];
				if (!empty($value)) {
					if (!isset($customAttributes[$attributeId])) {
						$customAttributes[$attributeId] = array();
					}

					$customAttributes[$attributeId] = array_merge($customAttributes[$attributeId], $value);
				}
				//$session->set('customAtrributes', "");
			}
		}

		if (!empty($customAttributes)) {
			foreach ($customAttributes as &$customAttribute) {
				if (is_array($customAttribute)) {
					$customAttribute = implode(",", $customAttribute);
				}
			}
			//$session->set('customAtrributes', $customAttributes);
			$params["custom-attributes"][] = $customAttributes;

		}

		foreach ($params as &$param) {
			if (in_array('', $param)) {
				unset($param[array_search('', $param)]);
			}
		}

		if (in_array('', $params)) {
			unset($params[array_search('', $params)]);
		}

		$params["selectedParams"] = $selectedParams;

		return $params;
	}

	public function getMobileCategoriesFilter() {
		$categoriesFilter = array();
		if(!empty($this->mobileAppSettings->mobile_company_categories_filter)) {
			$categoriesFilter = explode(',', $this->mobileAppSettings->mobile_company_categories_filter);
		}

		return $categoriesFilter;
	}
	
	public function getSelectedCategories() {
		$categories = array();
		$session = JFactory::getSession();

		$selectedCat = JFactory::getApplication()->input->getString("categories");
		if (empty($selectedCat)) {
			$selectedCat = $session->get("categories", null);	
		}

		if (!empty($selectedCat)) {
			$categories = explode(";", $selectedCat);
		}
		
		if (!empty($this->categoryId) && empty($selectedCat)) {
			$categories[]=$this->categoryId;
		}
		
		if (in_array('', $categories)) {
			unset($categories[array_search('', $categories)]);
		}

		$categories = array_unique($categories);

		return $categories;
	}

	public function getLocation() {
		return $this->location;
	}
	
	public function getCustomAttributeValues() {
		$attributeTable = $this->getTable("Attribute", "JTable");
		
		if (empty($this->customAtrributes)) {
			return null;
		}

		$customAttributes = $this->customAtrributes;
		$result = array();
		
		$excludeAttrIds = array();
		//remove string values
		
		foreach ($customAttributes as $key=>$value) {
			if (is_numeric($value)) {
				$result[$key]=$value;
			}else{
				//in the case of multiple checkbox select we need to determine if we have multiple selections and exclude that in the front-end.
				$values = explode(",",$value);
				if(is_array($values)){
					$added = false;
					foreach($values as $val){
						if (is_numeric($val)) {
							$added = true;
							$result[]=$val;
						}
					}

					if($added){
						$excludeAttrIds[] = $key;
					}
				}
			}
		}

		$attributeValIds = implode(",", $result);
		$customAttributeValues = $attributeTable->getCustomAttributeValues($attributeValIds);

		//group attributes by id and translate name
		$result = array();
		if(!empty($customAttributeValues)){
			foreach($customAttributeValues as $val){
				if ($this->appSettings->enable_multilingual) {
					$val->name = JBusinessDirectoryTranslations::getTranslatedItemName($val->name);
				}
			}
		}
		
		//add string values
		if(!empty($customAttributeValues)){
			foreach ($customAttributes as $key=>$value) {
				if (!is_numeric($value) && !in_array($key, $excludeAttrIds)) {
					$obj = new stdClass();
					$obj->attribute_id = $key;
					if ($this->appSettings->enable_multilingual) {
						$obj->name = JBusinessDirectoryTranslations::getTranslatedItemName($value);
					}else{
						$obj->name = $value;
					}
					$customAttributeValues[]=$obj;
				}
			}
		}
		
		return $customAttributeValues;
	}
	
	/**
	 * Generate order by values
	 *
	 * @return multitype:stdClass
	 */
	public function getSortByConfiguration($check = false) {

		$orderByFields = "";
		if (!empty($this->appSettings->order_by_fields)) {
			$orderByFields = explode(",", $this->appSettings->order_by_fields);
		} else {
			return;
		}

		$states = array();

		if (in_array("most_popular", $orderByFields)) {
			$state = new stdClass();
			$state->value = 'viewCount desc';
			$state->text = JTEXT::_("LNG_MOST_POPULAR");
			$states[] = $state;
		}

		if (in_array("packageOrder", $orderByFields)) {
			$state = new stdClass();
			$state->value = 'packageOrder desc';
			$state->text = JTEXT::_("LNG_RELEVANCE");
			$states[] = $state;
		}

		if (in_array("id_desc", $orderByFields)) {
			$state = new stdClass();
			$state->value = 'id desc';
			$state->text = JTEXT::_("LNG_LAST_ADDED");
			$states[] = $state;
		}

		if (in_array("id_asc", $orderByFields)) {
			$state = new stdClass();
			$state->value = 'id asc';
			$state->text = JTEXT::_('LNG_FIRST_ADDED');
			$states[] = $state;
		}

		if (in_array("companyName_asc", $orderByFields)) {
			$state = new stdClass();
			$state->value = 'companyName asc';
			$state->text = JTEXT::_("A-Z");
			$states[] = $state;
		}

		if (in_array("companyName_desc", $orderByFields)) {
			$state = new stdClass();
			$state->value = 'companyName desc';
			$state->text = JTEXT::_("Z-A");
			$states[] = $state;
		}
		
		if (in_array("city", $orderByFields)) {
			$state = new stdClass();
			$state->value = 'city asc';
			$state->text = JTEXT::_("LNG_CITY");
			$states[] = $state;
		}
	
		if (in_array("ordering", $orderByFields)) {
			$state = new stdClass();
			$state->value = 'ordering asc';
			$state->text = JTEXT::_("LNG_DEFAULT");
			$states[] = $state;
		}

		if ($this->appSettings->enable_reviews == 1 && in_array("review_score", $orderByFields)) {
			$state = new stdClass();
			$state->value = 'review_score desc';
			$state->text = JTEXT::_("LNG_RATING");
			$states[] = $state;
		}

		if ($this->appSettings->enable_reviews == 1 && in_array("review_count", $orderByFields)) {
			$state = new stdClass();
			$state->value = 'nr_reviews desc';
			$state->text = JTEXT::_("LNG_REVIEW_COUNT");
			$states[] = $state;
		}

		if (!empty($this->location) && in_array("distance", $orderByFields) || $check && in_array("distance", $orderByFields)) {
			$state = new stdClass();
			$state->value = 'distance asc';
			$state->text = JTEXT::_("LNG_DISTANCE");
			$states[] = $state;
		}
		
		return $states;
	}

	public function getCountry() {
		$country = null;
		if (!empty($this->countrySearch)) {
			$countryTable = $this->getTable("Country", "JTable");
			$country      = $countryTable->getCountry($this->countrySearch);
			if ($this->appSettings->enable_multilingual) {
				JBusinessDirectoryTranslations::updateEntityTranslation($country, COUNTRY_TRANSLATION);
			}
		}

		return $country;
	}

	public function getRegion() {
		$region = null;
		if (!empty($this->regionSearch)) {
			if ($this->appSettings->limit_cities_regions != 1) {
				$region = new stdClass();
				$region->region = $this->regionSearch;
				$region->regionName = $this->regionSearch;
			} else {
				$table  = $this->getTable("Region", "JTable");
				$region = $table->getRegion($this->regionSearch);
			}
		}

		return $region;
	}

	public function getCity() {
		$city = null;
		if (!empty($this->citySearch)) {
			if ($this->appSettings->limit_cities_regions != 1) {
				$city = new stdClass();
				$city->city = $this->citySearch;
				$city->cityName = $this->citySearch;
			} else {
				$table = $this->getTable("City", "JTable");
				$city = $table->getCity($this->citySearch);
			}
		}

		return $city;
	}

	public function getCompanyType() {
		$type = null;
		if (!empty($this->typeSearch)) {
			$companyTypesTable = $this->getTable("CompanyTypes", "JTable");
			$type = $companyTypesTable->getCompanyType($this->typeSearch);

			if ($this->appSettings->enable_multilingual) {
				JBusinessDirectoryTranslations::updateEntityTranslation($type, TYPE_TRANSLATION);
			}
		}
		return $type;
	}

	public function getRegionsByCountryAjax($countryId, $type = ITEM_TYPE_BUSINESS) {
		$field = "county";
		$valField = "county";
		if ($this->appSettings->limit_cities_regions != 1) {
			$countryTable = $this->getTable("Country", "JTable");
			$results = $countryTable->getRegionsByCountry($countryId, $type);
		} else {
			$table = $this->getTable('Region');
			$results = $table->getRegionsByCountry($countryId);

			$field = "name";
			$valField = "id";
		}

		$options = '';
		if ($results) {
			$options .= '<option value="0" selected>'.JText::_("LNG_ALL_REGIONS").'</option>';
			foreach ($results as $region) {
				$options .= '<option value="'.$region->$valField.'">'.$region->$field.'</option>';
			}
		}

		return $options;
	}

	public function getCitiesByRegionAjax($region, $type = ITEM_TYPE_BUSINESS) {
		$field    = "city";
		$valField = "city";
		if ($this->appSettings->limit_cities_regions != 1) {
			$countryTable = $this->getTable("Country", "JTable");
			$results      = $countryTable->getCitiesByRegion($region, $type);
		} else {
			$table   = $this->getTable('City');
			$results = $table->getCitiesByRegion($region);

			$field    = "name";
			$valField = "id";
		}

		$options = '';
		if ($results) {
			$options .= '<option value="0" selected>'.JText::_("LNG_ALL_CITIES").'</option>';
			foreach ($results as $city) {
				$options .= '<option value="'.$city->$valField.'">'.$city->$field.'</option>';
			}
		}

		return $options;
	}

	public function getCitiesByCountryAjax($countryId, $type = ITEM_TYPE_BUSINESS) {
		$field    = "city";
		$valField = "city";
		if ($this->appSettings->limit_cities_regions != 1) {
			$countryTable = $this->getTable("Country", "JTable");
			$results      = $countryTable->getCitiesByCountry($countryId, $type);
		} else {
			$table   = $this->getTable('City');
			$results = $table->getCitiesByCountry($countryId);

			$field    = "name";
			$valField = "id";
		}

		$options = '';
		if ($results) {
			$options .= '<option value="0" selected>'.JText::_("LNG_ALL_CITIES").'</option>';
			foreach ($results as $city) {
				$options .= '<option value="'.$city->$valField.'">'.$city->$field.'</option>';
			}
		}

		return $options;
	}

	public function getUsedLetter() {
		$companiesTable = $this->getTable("Company");

		$letters = $companiesTable->getUsedLettersForActiveBusiness();
		$result = array();
		foreach ($letters as $letter) {
			$result[$letter->letter] = $letter->letter;
		}

		return $result;
	}
	
	public function getBookmarks() {
		$user = JBusinessUtil::getUser();
		$bookmarkTable = $this->getTable("Bookmark", "JTable");
		$bookmarks = $bookmarkTable->getBookmarks($user->ID);
		
		$result = array();
		if (!empty($bookmarks)) {
			foreach ($bookmarks as $bookmark) {
				$result[]=$bookmark->company_id;
			}
		}
		
		return $result;
	}

	public function getCountries() {
		$result = array();
		$countriesTable = $this->getTable("Country");
		$countries = $countriesTable->getCountries();
		foreach ($countries as $country) {
			$result[$country->id] = $country;
		}

		return $result;
	}

	public function getLocationSuggestions($keyword) {
		$suggestionList = array();

		$language = JBusinessUtil::getLanguageTag();
		
		$table      = $this->getTable("City");
		$cities = $table->getCitySuggestions($keyword);

		if (!empty($cities)) {

			$suggestion        = new stdClass();
			$suggestion->value = "";
			$suggestion->label = JText::_("LNG_MATCHING_CITIES");
			$suggestion->parent_class='small';
			$suggestion->header= 1 ;
			$suggestion->type  = SUGGESTION_TYPE_BUSINESS;
			$suggestionList[]  = $suggestion;

			foreach ($cities as $city) {
				$suggestion        = new stdClass();
				$suggestion->value = $city->id;
				$suggestion->label = $city->name.", ".$city->region_name;
				$suggestion->class = '';
				$suggestion->parent_class='item-listing';
				$suggestion->city_id = $city->id;
				$suggestion->region_id = $city->region_id;
				$suggestion->country_id = $city->country_id;
				$suggestionList[]  = $suggestion;
			}
		}

		$table      = $this->getTable("Region");
		$regions = $table->getRegionSuggestions($keyword, $language);

		if (!empty($regions)) {
			
			$suggestion        = new stdClass();
			$suggestion->value = "";
			$suggestion->label = JText::_("LNG_MATCHING_REGIONS");
			$suggestion->parent_class='small';
			$suggestion->header= 1 ;
			$suggestion->type  = SUGGESTION_TYPE_BUSINESS;
			$suggestionList[]  = $suggestion;

			foreach ($regions as $region) {
				$suggestion        = new stdClass();
				$suggestion->value = $region->name;
				$suggestion->label = $region->name.", ".$region->country_name;
				$suggestion->city_id = "";
				$suggestion->region_id = $region->id;
				$suggestion->country_id = $region->country_id;
				$suggestion->class = '';
				$suggestion->parent_class='item-listing';
				$suggestion->type  = SUGGESTION_TYPE_BUSINESS;
				$suggestionList[]  = $suggestion;
			}
		}

		$suggestionList = json_encode($suggestionList);
		return $suggestionList;
	}
}
