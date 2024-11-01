<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');


class JBusinessDirectoryControllerEvents extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	public function __construct() {
		parent::__construct();
	}

	public function getCalendarEvents() {
		$input = JFactory::getApplication()->input;
		$input->set('limitstart', 0);
		$input->set('limit', 0);
		$input->set('startDate', $input->get("start"));
		$input->set('endDate', $input->get("end"));

		$model = $this->getModel('events');
		$events = $model->getCalendarEvents();
		

		echo json_encode($events);
		exit;
	}

	public function initializeGetItemsAjax(){
        $model = $this->getModel('Events');
        
        $total = $model->getTotal();

        $response = new stdClass();
        $response->total_results = $total;

        JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
    }

    public function getItemsAjax(){
        
        $appSettings = JBusinessUtil::getApplicationSettings();
        $view = $this->getView("Events","Html");

        $app = JFactory::getApplication();
        // Get pagination request variables
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $appSettings->dir_list_limit, 'int');

        $input = JFactory::getApplication()->input;
        $start = $input->getInt("start");
        $total_results = $input->getInt("total_results");

        $model = $this->getModel('Events');
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
		$response->items_type = ITEM_TYPE_EVENT;
		$response->locations = JBusinessUtil::prepareEventsMapLocations($items);
		
        JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
    }

	public function getSearchFilter() {
		$model = $this->getModel('Events');
		$session = JFactory::getSession();

		$location = $model->getLocation();
		$radius = $session->get('ev-radius');
		$searchFilters = $model->getSeachFilter();
		$category = $model->getCategory();
		$selectedCategories = $model->getSelectedCategories();
		$selectedParams = $model->getSelectedParams();
		$filterMonths = $model->getFilterMonths();
		$categoryId = $session->get('ev-categorySearch');

		$response = new stdClass();
		$response->location = $location;
		$response->radius = $radius;
		$response->category = $category;
		$response->categoryId = $categoryId;
		$response->selectedCategories = $selectedCategories;
		$response->searchFilter = $searchFilters;
		$response->selectedParams = $selectedParams;
		$response->filterMonths = $filterMonths;
		$response->startDate = JBusinessUtil::convertToMysqlFormat($session->get('ev-startDate', null));

		JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
	}
}
