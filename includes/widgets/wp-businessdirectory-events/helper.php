<?php
/**
 * @package    WBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2023 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

abstract class modJBusinessEventsHelper {
	public static function getList($params) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$searchDetails = array();

		$categoriesIds = $params->get('categoryIds');
		if (isset($categoriesIds) && count($categoriesIds)>0 && $categoriesIds[0]!= 0 && $categoriesIds[0]!= "") {
			$searchDetails["categoriesIds"] = $categoriesIds;
		}

		if(isset($_REQUEST["current_event"]) && $params->get('event_link')){
			$event = $_REQUEST["current_event"];
			$searchDetails["categoriesIDs"] = array($event->main_subcategory);
			$searchDetails["excluded_event"] = $event->id;
		}

		$packages  = $params->get('packages');
		if (!empty($packages)) {
			$searchDetails["packages"] = $packages;
		}

		$ordering = $params->get('order');
		if ($ordering == 1) {
			$orderBy ="co.created desc";
		} elseif ($ordering == 2) {
			$orderBy ="co.id desc";
		} elseif ($ordering == 3) {
			$orderBy ="co.name asc";
		} elseif ($ordering == 4) {
			$orderBy ="co.start_date asc";
		} else {
			$orderBy = " rand() ";
		}

		$nrResults = $params->get('count');

		$searchDetails["enablePackages"] = $appSettings->enable_packages;
		$searchDetails["showPendingApproval"] = ($appSettings->enable_item_moderation=='0' || ($appSettings->enable_item_moderation=='1' && $appSettings->show_pending_approval == '1'));
		$searchDetails["orderBy"] = $orderBy;
		$searchDetails["featured"] = $params->get('only_featured');
		$searchDetails["citySearch"] = $params->get('city');
		$searchDetails["regionSearch"] = $params->get('region');
		$searchDetails["typeSearch"] = $params->get('type');

		$radius = $params->get('radius');
		$latitude = JFactory::getApplication()->input->get("latitude");
		$longitude = JFactory::getApplication()->input->get("longitude");

		$searchDetails["radius"] = $radius;
		if ($params->get('geo_location')) {
			$searchDetails["latitude"] = $latitude;
			$searchDetails["longitude"] = $longitude;
		}

		JTable::addIncludePath(WP_BUSINESSDIRECTORY_PATH . 'admin/tables');
		$eventsTable = JTable::getInstance("Event", "JTable");
		$events =  $eventsTable->getEventsByCategories($searchDetails, 0, $nrResults);

		foreach ($events as $event) {
			$event->link = JBusinessUtil::getEventLink($event->id, $event->alias, true);
			$event->picture_path = $event->picture_path ? str_replace(" ", "%20", $event->picture_path): "";
			$event->logoLocation = $event->picture_path;
			//          $event->mainCategoryLink = JBusinessUtil::getEventCategoryLink($event->mainCategoryId, $event->mainCategoryAlias);
		}
		
		if ($appSettings->enable_multilingual) {
			JBusinessDirectoryTranslations::updateEventsTranslation($events);
			JBusinessDirectoryTranslations::updateEventTypesTranslation($events);
		}
		
		return $events;
	}
}
