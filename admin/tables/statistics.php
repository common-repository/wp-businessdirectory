<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class JTableStatistics extends JTable {
	/**
	 * Constructor
	 *
	 * @param JDatabaseDriver $db Database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__jbusinessdirectory_statistics', 'id', $db);
	}

	public function setKey($k) {
		$this->_tbl_key = $k;
	}

	public function getStatsToArchive($limitstart = 0, $limit = 0) {
		$db    = JFactory::getDBO();

		$query = "SELECT max(id) as last_id, item_id, item_type, date, type, IFNULL(article_id, 0) AS article_id, COUNT(*) AS item_count
					FROM #__jbusinessdirectory_statistics
					GROUP BY item_id , item_type , type , date , article_id
					order by last_id ";

		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}

	public function archiveStatistics($dataToArchive) {
		$db =JFactory::getDBO();
		$query = "insert into #__jbusinessdirectory_statistics_archive(item_id,item_type,date,type,article_id,item_count) values ";
		foreach ($dataToArchive as $data) {
			$query = $query . "('" . $db->escape($data->item_id) . "','" . $db->escape($data->item_type) . "','" . $db->escape($data->date) . "','" . $db->escape($data->type) . "','" . $db->escape($data->article_id) . "','" . $db->escape($data->item_count) . "'),";
		}
		$query = substr($query, 0, -1);

		$query .= "on duplicate key update item_count = item_count + $data->item_count";

		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
		return true;
	}

	public function deleteOldStatistics($id){
		$db =JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_statistics where id <= $id";
		$db->setQuery($query);
		if (!$db->execute()) {
			echo 'INSERT / UPDATE sql STATEMENT error !';
			return false;
		}
		return true;
	}

	public function getStatistics($objectType, $actionType, $categoryId, $groupBy, $itemId, $start_date, $end_date, $userId) {
		switch ($objectType) {
			case STATISTIC_ITEM_OFFER:
				$leftJoin = "left join #__jbusinessdirectory_company_offers as comp on comp.id = arch.item_id";
				if (!empty($categoryId)) {
					$leftJoin .= " left join #__jbusinessdirectory_company_offer_category cc on comp.id=cc.offerId";
				}
				break;
			case STATISTIC_ITEM_EVENT:
				$leftJoin = "left join #__jbusinessdirectory_company_events as comp on comp.id = arch.item_id";
				if (!empty($categoryId)) {
					$leftJoin .= " left join #__jbusinessdirectory_company_event_category cc on comp.id=cc.eventId";
				}
				break;
			case STATISTIC_ITEM_SESSION_LOCATION:
				$leftJoin = "left join #__jbusinessdirectory_conference_session_locations as comp on comp.id = arch.item_id";
				break;
			default:
				$leftJoin = "left join #__jbusinessdirectory_companies as comp on comp.id = arch.item_id";
				if (!empty($categoryId)) {
					$leftJoin .= " left join #__jbusinessdirectory_company_category cc on comp.id=cc.companyId";
				}
				break;
		}

		$whereItemId = "";
		if (!empty($itemId)) {
			$whereItemId = " and comp.id = '$itemId'";
		}

		switch ($groupBy) {
			case STATISTIC_ORDER_BY_DAY:
				$groupBy = "group by year(arch.date), month(arch.date), day(arch.date)";
				break;
			case STATISTIC_ORDER_BY_MONTH:
				$groupBy = "group by year(arch.date), month(arch.date)";
				break;
			default:
				$groupBy = "group by year(arch.date)";
				break;
		}

		$whereCat = "";
		if (!empty($categoryId)) {
			$whereCat = " and cc.categoryId=$categoryId";
		}

		$whereUser = '';
		if (!empty($userId)) {
			switch ($objectType) {
				case STATISTIC_ITEM_OFFER:
					$whereUser = " and comp.user_id='$userId'";
					break;
				case STATISTIC_ITEM_EVENT:
					$whereUser = " and comp.user_id='$userId'";
					break;
				case STATISTIC_ITEM_SESSION_LOCATION:
					$whereUser = '';
					break;
				default:
					$whereUser = " and comp.userId='$userId'";
					break;
			}
		}

		$db = JFactory::getDBO();
		$query = "select DATE_FORMAT(date, '%Y-%m-%d') as date, sum(arch.item_count) as value 
					from #__jbusinessdirectory_statistics_archive as arch
					".($leftJoin)."
					where (CAST(arch.date AS DATE) between '$start_date' and '$end_date') and arch.type='".$actionType."' 
					and arch.item_type='".$objectType."' $whereItemId $whereCat $whereUser
					$groupBy
					having date IS NOT NULL
					order by arch.date asc";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getTotalNumberOfStatistics() {
        $db =JFactory::getDBO();
        $query = "select count(*) as nr_statistics from #__jbusinessdirectory_statistics ";
        $db->setQuery($query);
        $result = $db->loadResult();
		
		return (int)$result->nr_statistics;
    }

	public function getStatisticsForExport($startDate, $endDate){
		$db = JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_statistics_archive where date <= '$endDate'
		AND date >= '$startDate'";
		$db->setQuery($query);
        return $db->loadObjectList();
	}

	public function deleteByDate($startDate, $endDate){
		$db =JFactory::getDBO();
		$query = "delete from #__jbusinessdirectory_statistics_archive where date <= '$endDate'
				  AND date >= '$startDate'";
		$db->setQuery($query);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

}
