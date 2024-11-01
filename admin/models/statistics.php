<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');

use MVC\Installer\Installer;
use MVC\Installer\InstallerHelper;
use MVC\Factory;
use MVC\Filesystem\File;
use MVC\Router\Route;
use MVC\Language\Text;

require_once BD_HELPERS_PATH.'/installer.php';

/**
 * Class JBusinessDirectoryModelJBusinessDirectory
 */
class JBusinessDirectoryModelStatistics extends JModelAdmin {
	/**
	 * JBusinessDirectoryModelJBusinessDirectory constructor.
	 *
	 * @param array $config
	 *
	 * @since 4.9.0
	 */
	public function __construct(array $config = array()) {
		$this->appSettings = JBusinessUtil::getApplicationSettings();

		parent::__construct($config);
	}

	public function getForm($data = array(), $loadData = true) {
	}

	public function getStatistics() {
		$jinput = JFactory::getApplication()->input;

		$start_date     = $jinput->getString('start_date');
		$start_date     = date("Y-m-d", strtotime($start_date));
		$end_date       = $jinput->getString('end_date');
		$end_date       = date("Y-m-d", strtotime($end_date));
		$objectType     = $jinput->getInt('object_type');
		$actionType     = $jinput->getInt('action_type');
		$categoryId     = $jinput->getString('category_id');
		$groupBy        = $jinput->getInt('group_by');
		$itemId         = $jinput->getInt('itemId');
		$isProfile      = $jinput->getInt('isProfile', false);

		$userId = null;
		if ($isProfile) {
			$user = JBusinessUtil::getUser();
			$userId = $user->ID;
		}

		$table = JTable::getInstance('Statistics', 'JTable');
		$result       = $table->getStatistics($objectType, $actionType, $categoryId, $groupBy, $itemId, $start_date, $end_date, $userId);

		if (!empty($result)) {
			//add start date element if it does not exists
			if ($result[0]->date != $start_date) {
				$item        = new stdClass();
				$item->date  = $start_date;
				$item->value = 0;
				array_unshift($result, $item);
			}

			//add end date element if it does not exists
			if (end($result)->date != $end_date) {
				$item        = new stdClass();
				$item->date  = $end_date;
				$item->value = 0;
				array_push($result, $item);
			}
		} else {
			$firstItem        = new stdClass();
			$firstItem->date  = $start_date;
			$firstItem->value = 0;
			array_unshift($result, $firstItem);

			$endItem        = new stdClass();
			$endItem->date  = $end_date;
			$endItem->value = 0;
			array_push($result, $endItem);
		}

		return $result;
	}

	public function getArchiveStatistics() {
		$table = $this->getTable('Statistics', 'JTable');
		$batch = LOG_STATISTIC_ITEMS_BATCH_SIZE;
		$nrRuns = 1;
		do {
			$dataToArchive = $table->getStatsToArchive(0, $batch);
			if (!empty($dataToArchive)) {
				$table->archiveStatistics($dataToArchive);
				$lastId = end($dataToArchive)->last_id;
				$table->deleteOldStatistics($lastId);
			}
			$nrRuns++;
		} while (count($dataToArchive) == $batch && $nrRuns <= NUMBER_OF_ARCHIVE_CYCLES);

		$jinput     = JFactory::getApplication()->input;
		$isAjax     = $jinput->get('isAjax', false);
		if ($isAjax) {
			return true;
		} else {
			$hasDataToArchvie = (count($table->getStatsToArchive(0, 1)) > 0) ? true : false;
			if ($hasDataToArchvie) {
				JFactory::getApplication()->enqueueMessage(JText::_('LNG_THERE_ARE_STILL_DATA_TO_ARCHIVE'), 'Notice');
			}
		}
		return true;
	}

	public function getStatisticsCSV() {
		$jinput    = JFactory::getApplication()->input;
		$startDate = JBusinessUtil::convertToMysqlFormat($jinput->getString('startDate'));
		$endDate =  JBusinessUtil::convertToMysqlFormat($jinput->getString('endDate'));
		$delimiter = $jinput->getString("delimiter", ",");
		$statisticTable = JTable::getInstance("Statistics", "JTable");

		$csv_output = '"' . JText::_('LNG_ID') . '"' . $delimiter . '"' . JText::_('LNG_SEARCHED_FOR') . '"' . $delimiter .
		'"' . JText::_('LNG_SEARCH_TYPE') . '"'  . $delimiter . '"' . JText::_('LNG_DATE') . '"' . $delimiter .'"' . JText::_('LNG_TYPE') . '"' . $delimiter .  '"' . JText::_('LNG_NUMBER_OF_SEARCHES') . '"';
		$csv_output .= "\n";

		$statistics = $statisticTable->getStatisticsForExport($startDate, $endDate);
		foreach ($statistics as $statistic) {
			switch($statistic->item_type){
				case 1:
					$statistic->item_type = JText::_('LNG_BUSINESS_LISTINGS');
					$statistic->item_id   = JBusinessUtil:: getCompany($statistic->item_id)->name;
					break;
				case 2:
					$statistic->item_type = JText::_('LNG_OFFER');
					$statistic->item_id   = JBusinessUtil:: getOffer($statistic->item_id)->subject;
					break;
				case 3:
					$statistic->item_type = JText::_('LNG_EVENT');
					$statistic->item_id   = JBusinessUtil:: getEvent($statistic->item_id)->name;
					break;
				case 4:
					$statistic->item_type = JText::_('LNG_CONFERENCE');
					$statistic->item_id   = JBusinessUtil:: getConference($statistic->item_id)->name;
					break;
				case 5:
					$statistic->item_type = JText::_('LNG_SPEAKER');
					$statistic->item_id   = JBusinessUtil:: getSpeaker($statistic->item_id)->name;
					break;
				case 6:
					$statistic->item_type = JText::_('LNG_SESSION');
					$statistic->item_id   = JBusinessUtil:: getSessionName($statistic->item_id)->name;
					break;
				case 7:
					$statistic->item_type = JText::_('LNG_SESSION_LOCATIONS');
					$statistic->item_id   = JBusinessUtil:: getSessionLocation($statistic->item_id)->name;
					break;
				case 8:
					$statistic->item_type = JText::_('LNG_VIDEO');
					$statistic->item_id   = JBusinessUtil:: getVideo($statistic->item_id)->name;
					break;
				case 9:
					$statistic->item_type = JText::_('LNG_ARTICLE');
					$statistic->item_id   =  JBusinessUtil:: getArticleTitle($statistic->item_id)->title;
					break;		
			}
			
			switch($statistic->type){
				case 0:
					$statistic->type = JText::_('LNG_VIEW');
					break;
				case 1:
					$statistic->type = JText::_('LNG_CONTACTS');
					break;
				case 2:
					$statistic->type = JText::_('LNG_SHARE');
					break;
				case 3:
					$statistic->type = JText::_('LNG_WEBSITE_CLICKS');
					break;	
				case 4:
					$statistic->type = JText::_('LNG_ARTICLE_CLICK');
					break;	
			}

			$date = JBusinessUtil::getDateGeneralFormat($statistic->date);
			$csv_output .= "\"$statistic->id\"" . $delimiter . "\"$statistic->item_id\"" . $delimiter . "\"$statistic->item_type\"" . $delimiter
				. "\"$date\"". $delimiter . "\"$statistic->type\"" . $delimiter . "\"$statistic->item_count\"";
			$csv_output .= "\n";
		}
		return $csv_output;
	}

	public function exportStatisticsCSV() {
		$csv_output = $this->getStatisticsCSV();

		$fileName = "jbusinessdirectory_statistics_archive";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=".$fileName.".csv");
		print $csv_output;
		
	}
	
	public function deleteByDate() {
		$jinput    = JFactory::getApplication()->input;
		$startDate = JBusinessUtil::convertToMysqlFormat($jinput->getString('startDate'));
		$endDate =  JBusinessUtil::convertToMysqlFormat($jinput->getString('endDate'));

		$statisticTable = JTable::getInstance("Statistics", "JTable");
		if(!$statisticTable->deleteByDate($startDate, $endDate)) {
			return false;
		}
		
		return true;
	}
}
