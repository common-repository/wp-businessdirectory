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
JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');

class JBusinessDirectoryModelActivityItinerary extends JModelList {

	/**
	 * constructor method
	 */
	public function __construct() {
		parent::__construct();

		$this->appSettings = JBusinessUtil::getApplicationSettings();
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array   Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Company', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get a list of companies and their events by categories
	 * @return mixed
	 */
	private function getCompaniesAndEvents() {
		$startDate = JFactory::getApplication()->input->get("startDate");
		if (empty($startDate)) {
			$startDate = date('Y-m-d');
		}
		$startDate = JBusinessUtil::convertToMysqlFormat($startDate);

		$searchDetails = array();
		$showPendingApproval = ($this->appSettings->enable_item_moderation=='0' || ($this->appSettings->enable_item_moderation=='1' && $this->appSettings->show_pending_approval == '1'));
		$enablePackages = $this->appSettings->enable_packages;

		$searchDetails["showPendingApproval"] = $showPendingApproval;
		$searchDetails["enablePackages"] = $enablePackages;
		$companiesTable = $this->getTable();
		$results = $companiesTable->getCompaniesAndEventsCategorically($startDate, $searchDetails);

		$eventsTable = $this->getTable('Event');
		foreach ($results as $result) {
			if (!empty($result->eventIds)) {
				$ids = $result->eventIds;
				$result->events = $eventsTable->getEventsByIds($ids);
			}
		}

		return $results;
	}

	/**
	 * Gets an array of opening hours that belong to a particular business, and returns
	 * the hours only for a specific day
	 *
	 * @param $hours array containing the opening hours for all week
	 * @param $day string containing the date of which the opening hours are required
	 * @param bool|false $onlyStartTime if true, only the start time will be returned
	 * @return string containing the opening hours in the format of "startTime - endTime"
	 */
	private function getOpeningHoursPerDay($hours, $day, $onlyStartTime = false) {
		$hours = explode(",", $hours);
		$day = strtotime($day);

		$day = (int)date('w', $day);
		$result = '';
		$i = ($day*2)-2;

		if (empty($hours[$i]) && empty($hours[$i+1])) {
			$result = "closed";
			return $result;
		}

		if (!empty($hours[$i])) {
			$result .= $hours[$i];
		}
		if (!$onlyStartTime && !empty($hours[$i+1])) {
			if (!empty($hours[$i])) {
				$result .= ' -';
			}
			$result .= ' '.$hours[$i+1];
		}

		return $result;
	}

	/**
	 * Method that processes a list of companies and their events categorically inside a particular date time interval.
	 * This method returns an array of dates, where for each date there is an array of categories, and for each category
	 * there is a list of objects (companies and events) belonging to that specific category.
	 *
	 * @return array|null
	 */
	public function getItems() {
		$items = array();
		$startDate = JFactory::getApplication()->input->get('startDate', null);
		$endDate = JFactory::getApplication()->input->get('endDate', null);

		if (empty($startDate) && empty($endDate)) {
			$startDate = date('Y-m-d');
			$endDate = date('Y-m-d');
		}

		//If date interval is not fully set, return null
		if ((empty($startDate) || empty($endDate)) || (strtotime($endDate) < strtotime($startDate))) {
			$items = null;
			return $items;
		} else {
			$results = $this->getCompaniesAndEvents();
		}

		//Gets an array of each of the days belonging to the date interval
		$days = JBusinessUtil::getAllDatesInInterval($startDate, $endDate);

		foreach ($days as $day) {
			$items[$day] = array();
			foreach ($results as $result) {
				$val = new stdClass();
				//If the business has some events belonging to it, then show only the start time for that business
				$onlyStartTime = false;
				if (!empty($result->events)) {
					$onlyStartTime = true;
				}
				$val->hours = $this->getOpeningHoursPerDay($result->business_hours, $day, $onlyStartTime);
				//If the business is closed for a specific day, do not include it in the array for that date
				if ($val->hours != "closed") {
					$val->id = $result->id;
					$val->name = $result->name;
					$val->link = JBusinessUtil::getCompanyLink($result);
					$val->type = 0; //Business type
					$val->address = $result->address;
					if (!isset($items[$day][$result->categoryName])) {
						$items[$day][$result->categoryName] = array();
					}
					$items[$day][$result->categoryName][] = $val;
				}

				//Process the events of a business
				if (!empty($result->events)) {
					foreach ($result->events as $event) {
						//Include the event in the array only if the current date is inside the event date interval
						if (JBusinessUtil::checkDateInterval($event->start_date, $event->end_date, $day, true, true)) {
							$val = new stdClass();
							$val->id = $event->id;
							$val->name = $event->name;
							$val->link = JBusinessUtil::getEventLink($event->id, $event->alias);
							$val->address = $event->address;
							$val->hours = '';
							//Format the start time and end time for the event
							if (!empty($event->start_time)) {
								$val->hours .= JBusinessUtil::getTimeText($event->start_time);
							}
							if (!empty($event->start_time) && !empty($event->end_time)) {
								$val->hours .= ' - ';
							}
							if (!empty($event->end_time)) {
								$val->hours .= JBusinessUtil::getTimeText($event->end_time);
							}
							$val->type = 1; //Event type
							$items[$day][$result->categoryName][] = $val;
						}
					}
				}
			}
		}

		return $items;
	}

	/**
	 * Gets the id, type and date of a specific activity, retrieves additional data about
	 * that activity from the respective tables and rearranges the data to be rendered properly
	 * on the front end layout.
	 *
	 * Saves this data in the session, retrieves all data from the session including the new
	 * activity that was added, arranges them into HTML and returns the output as a string,
	 *
	 * @param $data array containing id, type, and date of the activity
	 * @return string containing the html representation of the data
	 */
	public function addToTripAjax($data) {
		if ($data["type"] == 0) {
			$companiesTable = $this->getTable();
			$result = $companiesTable->getCompany($data["id"]);
			$result->hours = $this->getOpeningHoursPerDay($result->business_hours, $data["day"]);
			$result->link = JBusinessUtil::getCompanyLink($result);
			$result->contact = $result->phone.' '.$result->email;
			$result->type = 0;
		} else {
			$eventsTable = $this->getTable('Event');
			$result = $eventsTable->getEvent($data["id"]);
			$result->hours = '';
			if (!empty($result->start_time)) {
				$result->hours .= JBusinessUtil::getTimeText($result->start_time);
			}
			if (!empty($result->start_time) && !empty($result->end_time)) {
				$result->hours .= ' - ';
			}
			if (!empty($result->end_time)) {
				$result->hours .= JBusinessUtil::getTimeText($result->end_time);
			}
			$result->link = JBusinessUtil::getEventLink($result->id, $result->alias);
			$result->contact = $result->contact_phone.' '.$result->contact_email;
			$result->type = 1;
		}

		$result->day = $data["day"];

		$this->saveDataToSession($result);
		$activities = $this->getActivities();

		$empty = true;
		$html = '';
		foreach ($activities[$result->day] as $activity) {
			$empty = false;
			$html .= $this->arrangeDataToHtml($activity);
		}

		if ($empty) {
			$html = JText::_('LNG_NO_ACTIVITIES_ADDED');
		}

		return $html;
	}

	/**
	 * Gets the id, type and date of an activity, and removes it from the session variable.
	 * After that, it retrieves the list of activities remaining in the session, arranges them
	 * into html and returns the output as a string.
	 *
	 * @param $data array containing id, type, and date of the activity
	 * @return string containing the html representation of the data
	 */
	public function removeFromTripAjax($data) {
		$items = $_SESSION['itineraryData'];

		foreach ($items[$data["day"]] as $key=>$val) {
			if ($val->id == $data["id"] && $val->type == $data["type"]) {
				unset($items[$data["day"]][$key]);
			}
		}

		$_SESSION['itineraryData'] = $items;

		$activities = $this->getActivities();

		$empty = true;
		$html = '';
		foreach ($activities[$data["day"]] as $activity) {
			$empty = false;
			$html .= $this->arrangeDataToHtml($activity);
		}

		if ($empty) {
			$html = JText::_('LNG_NO_ACTIVITIES_ADDED');
		}

		return $html;
	}

	/**
	 * Gets an object containing all properties of an activity and arranges
	 * them into html.
	 *
	 * @param $item object containing all properties of an activity
	 * @return string html output
	 */
	private function arrangeDataToHtml($item) {
		$html = '';
		$html .= '<a href="'.$item->link.'">'.$item->name.'</a>&nbsp;&nbsp;&nbsp;('.$item->hours.')';
		$html .= '<br/>';
		$html .= $item->address;
		$html .= '<br/>';
		$html .= $item->contact;
		$html .= '<hr/>';

		return $html;
	}

	/**
	 * Gets an object containing an activity, and saves all it's data in the session variable
	 *
	 * @param $item object representing an activity
	 */
	public function saveDataToSession($item) {
		$itineraryData =  !empty($_SESSION['itineraryData'])?$_SESSION['itineraryData']:null;

		if (!isset($itineraryData[$item->day])) {
			$itineraryData[$item->day] = array();
		}

		$itineraryData[$item->day][] = $item;

		$_SESSION['itineraryData'] = $itineraryData;
	}

	/**
	 * Returns an array of activities present in the session, or null if the
	 * session is not set
	 *
	 * @return array|null
	 */
	public function getActivities() {
		$data = !empty($_SESSION['itineraryData'])?$_SESSION['itineraryData']:null;

		return $data;
	}

	/**
	 * If session is set, returns an array formatted in the type of "array['date']['id']['type']",
	 * where each index represents the 3 values needed to determine a single activity. It is then
	 * used in the front end to check each if a specific activity is stored in the session.
	 *
	 * If session is not set, returns null
	 *
	 * @return array|null
	 */
	public function getSelectedActivities() {
		$selected = array();
		if (empty($_SESSION['itineraryData'])) {
			$selected = null;
			return $selected;
		}

		foreach ($_SESSION['itineraryData'] as $key=>$val) {
			$selected[$key] = array();
			foreach ($val as $item) {
				$selected[$key][$item->id][$item->type] = 1;
			}
		}

		return $selected;
	}
}
