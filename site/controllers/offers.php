<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryControllerOffers extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	public function __construct() {
		parent::__construct();
	}

	public function initializeGetItemsAjax(){
		$model = $this->getModel('Offers');
		
		$total = $model->getTotal();

		$response = new stdClass();
		$response->total_results = $total;

		JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
	}

	public function getItemsAjax(){
		
		$appSettings = JBusinessUtil::getApplicationSettings();
		$view = $this->getView("Offers","Html");

		$app = JFactory::getApplication();
		// Get pagination request variables
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $appSettings->dir_list_limit, 'int');

		$input = JFactory::getApplication()->input;
		$start = $input->getInt("start");
		$total_results = $input->getInt("total_results");

		$model = $this->getModel('Offers');
		$items = $model->getItems($start, $limit);
		$response = new stdClass();

		ob_start();
		$view->displayItems($items);
		$response->listitems =  ob_get_contents();
		ob_end_clean();

		ob_start();
		$view->displayItems($items, true);
		$response->griditems = ob_get_contents();
		ob_end_clean();

		$response->items_count = $start+count($items);
		$response->show_more = $response->items_count < $total_results;
		$response->items_type = ITEM_TYPE_OFFER;
		$response->locations = JBusinessUtil::prepareOffersMapLocations($items);

		JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
	}

	public function getSearchFilter() {
		$model = $this->getModel('Offers');
		$session = JFactory::getSession();

		$location = $model->getLocation();
		$radius = $session->get('of-radius');
		$searchFilters = $model->getSeachFilter();
		$category = $model->getCategory();
		$selectedCategories = $model->getSelectedCategories();
		$selectedParams = $model->getSelectedParams();
		$categoryId = $session->get('of-categorySearch');

		$response = new stdClass();
		$response->location = $location;
		$response->radius = $radius;
		$response->category = $category;
		$response->categoryId = $categoryId;
		$response->selectedCategories = $selectedCategories;
		$response->searchFilter = $searchFilters;
		$response->selectedParams = $selectedParams;

		JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
	}
}
