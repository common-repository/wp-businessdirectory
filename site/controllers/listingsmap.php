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
		$response->startDate = $session->get('ev-startDate', null);

		JBusinessUtil::sendJsonResponse($response, RESPONSE_STATUS_SUCCESS, '');
	}
}
