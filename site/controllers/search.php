<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryControllerSearch extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	public function __construct() {
		JFactory::getApplication()->input->set("requestType", "name");
		parent::__construct();
	}

	public function showCompaniesFromCategory() {
		parent::display();
	}

	public function searchCompaniesByName() {
		parent::display();
	}

	public function searchCompaniesByPhone() {
		JFactory::getApplication()->input->set("requestType", "phone");
		parent::display();
	}

	public function getRegionsByCountryAjax() {
		$countryId = JFactory::getApplication()->input->getInt('countryId');
		$type      = JFactory::getApplication()->input->getInt('type', ITEM_TYPE_BUSINESS);
		$model     = $this->getModel('Search');
		$result    = $model->getRegionsByCountryAjax($countryId, $type);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function getCitiesByRegionAjax() {
		$region = JFactory::getApplication()->input->getString('region');
		$type   = JFactory::getApplication()->input->getInt('type', ITEM_TYPE_BUSINESS);
		$model  = $this->getModel('Search');
		$result = $model->getCitiesByRegionAjax($region, $type);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}

	public function getCitiesByCountryAjax() {
		$countryId = JFactory::getApplication()->input->getInt('countryId');
		$type      = JFactory::getApplication()->input->getInt('type', ITEM_TYPE_BUSINESS);
		$model     = $this->getModel('Search');
		$result    = $model->getCitiesByCountryAjax($countryId, $type);

		/* Send as JSON */
		header("Content-Type: application/json", true);
		echo json_encode($result);
		exit;
	}
	
	public function getSearchFilter() {
		$model = $this->getModel('Search');
		$session = JFactory::getSession();

		$location = $model->getLocation();
		$radius = $session->get('radius');
		$searchFilters = $model->getSearchFilter();
		$category = $model->getCategory();
		
		$selectedCategories = $model->getSelectedCategories();
		$selectedParams = $model->getSelectedParams();
		$searchKeyword = $session->get('searchkeyword');
		$customAttributesValues = $model->getCustomAttributeValues();
		$zipCode = $session->get('zipcode');
		$onlyLocal = $session->get('onlyLocal');

		$categorySearch = $session->get("categorySearch");

		$response = new stdClass();
		$response->location = $location;
		$response->radius = $radius;
		$response->onlyLocal = $onlyLocal;
		$response->category = $category;
		$response->selectedCategories = $selectedCategories;
		$response->searchFilter = $searchFilters;
		$response->selectedParams = $selectedParams;
		$response->searchKeyword = $searchKeyword;
        $response->customAttributesValues = $customAttributesValues;
        $response->zipCode = $zipCode;
		$response->categorySearch = $categorySearch;

		JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
	}

	public function initializeGetItemsAjax(){
		$model = $this->getModel('Search');
		
		$total = $model->getTotal();

		$response = new stdClass();
		$response->total_results = $total;

		JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
	}

	public function getItemsAjax(){
		
		$appSettings = JBusinessUtil::getApplicationSettings();
		$view = $this->getView("Search","Html");
		
		$app = JFactory::getApplication();
		// Get pagination request variables
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $appSettings->dir_list_limit, 'int');

		$input = JFactory::getApplication()->input;
		$start = $input->getInt("start",0);
		$total_results = $input->getInt("total_results");

		$model = $this->getModel('Search');
		$companies = $model->getItems($start, $limit);
		$view->pagination = $model->getPagination();

		$response = new stdClass();

		ob_start();
		$view->displayCompanies($companies);
		$response->listitems =  ob_get_contents();
		$response->listitems = mb_convert_encoding($response->listitems, 'UTF-8', 'UTF-8');
		ob_end_clean();

		ob_start();
		$view->displayCompanies($companies, true);
		$response->griditems = ob_get_contents();
		$response->griditems = mb_convert_encoding($response->griditems, 'UTF-8', 'UTF-8');
		ob_end_clean();

		$response->items_count = $start+count($companies);
		$response->show_more = $response->items_count < $total_results;
		$response->items_type = ITEM_TYPE_BUSINESS;
		
		$response->locations = JBusinessUtil::prepareCompaniesMapLocations($companies);

		JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
	}

	/**
	 * Builds the form URL
	 *
	 * @return void
	 */
	public function getSearchFormActionAjax(){
		$input = JFactory::getApplication()->input;
		$country = $input->getInt("country");

		$region = $input->getString("region");
		if(is_numeric($region)){
			$region = JBusinessUtil::getRegionById($region)->name;
		}

		$city = $input->getString("city");
		if(is_numeric($city)){
			$city = JBusinessUtil::getCityById($city)->name;
		}

		$category = $input->getString("category");

		$url = JBusinessUtil::getForActionURL($country, $region, $city, $category);

		$response = new stdClass();
		$response->url = $url;
		JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
	}

	/**
	 * Retrieve the location suggestions
	 *
	 * @return void
	 */
	public function getLocationSuggestions(){

		$keyword = JFactory::getApplication()->input->getString('term', null);

		if (empty($keyword)) {
			JFactory::getApplication()->close();
		}
		
		$model = $this->getModel("Search");
		
		$suggestions = $model->getLocationSuggestions($keyword);
		header('Content-Type: application/json');
		echo $suggestions;
		
		JFactory::getApplication()->close();
	}
}