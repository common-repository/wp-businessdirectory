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
use MVC\Utilities\ArrayHelper;

/**
 * List Model.
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryModelSearchLogs extends JModelList {
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'item_type', 'sla.item_type',
				'object_type', 'sla.object_type',
				'date', 'sla.date'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    The table type to instantiate
	 * @param   string    A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'SearchLog', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems() {
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();

		// If empty or an error, just return.
		if (empty($items)) {
			return array();
		} else {
			$itemTypes = $this->getSearchItemTypes();
			$objectTypes = $this->getSearchObjectTypes();
			foreach ($items as $key => $item) {
				if ($item->has_text == 0) {
					switch ($item->item_type) {
						case SEARCH_LOG_TYPE:
							if ($item->object_type == SEARCH_LOG_TYPE_LISTING) {
								$item->itemName = $item->companyType;
							} elseif ($item->object_type == SEARCH_LOG_TYPE_EVENT) {
								$item->itemName = $item->eventType;
							} else {
								$item->itemName = $item->offerType;
							}
							break;
						case SEARCH_LOG_COUNTRY:
							$item->itemName = $item->countryName;
							break;
						case SEARCH_LOG_CATEGORY:
							$item->itemName = $item->categoryName;
							break;
						case SEARCH_LOG_CUSTOM_ATTRIBUTE:
							$item->itemName = $item->attributeOptionName;
							break;
					}
				} else {
					if ($item->item_type == SEARCH_LOG_LOCATION) {
						$location = explode('##', $item->value);
						$item->itemName = JText::_('LNG_LATITUDE').": ".$location[0]."; ".JText::_('LNG_LONGITUDE').": ".$location[1];
					} else {
						$item->itemName = $item->value;
					}
				}
				$item->item_type = $itemTypes[$item->item_type];
				$item->object_type = $objectTypes[$item->object_type];
			}
		}


		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select all fields from the table.
		$query->select($this->getState('list.select', 'sla.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_search_logs_archive').' AS sla');

		// Join over the categories
		$query->select('ca.name as categoryName');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_categories') . ' AS ca ON ca.id=sla.value and sla.item_type="'.SEARCH_LOG_CATEGORY.'"');

		// Join over the attributes
		$query->select('cua.name as attributeOptionName');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_attribute_options') . ' AS cua ON cua.id=sla.value and sla.item_type="'.SEARCH_LOG_CUSTOM_ATTRIBUTE.'" and sla.has_text="0"');

		// Join over the countries
		$query->select('co.country_name as countryName');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_countries') . ' AS co ON co.id=sla.value and sla.item_type="'.SEARCH_LOG_COUNTRY.'"');

		// Join over the countries
		$query->select('ct.name as companyType');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_types') . ' AS ct ON ct.id=sla.value and sla.item_type="'.SEARCH_LOG_TYPE.'" and sla.object_type="'.SEARCH_LOG_TYPE_LISTING.'"');

		// Join over the countries
		$query->select('cot.name as offerType');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_offer_types') . ' AS cot ON cot.id=sla.value and sla.item_type="'.SEARCH_LOG_TYPE.'" and sla.object_type="'.SEARCH_LOG_TYPE_OFFER.'"');

		// Join over the countries
		$query->select('cet.name as eventType');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_event_types') . ' AS cet ON cet.id=sla.value and sla.item_type="'.SEARCH_LOG_TYPE.'" and sla.object_type="'.SEARCH_LOG_TYPE_EVENT.'"');


		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where("sla.value LIKE '%" . trim($db->escape($search)) . "%' or
             ca.name LIKE '%" . trim($db->escape($search)) . "%' or
             cua.name LIKE '%" . trim($db->escape($search)) . "%' or
             co.country_name LIKE '%" . trim($db->escape($search)) . "%' or
             ct.name LIKE '%" . trim($db->escape($search)) . "%' or
             cot.name LIKE '%" . trim($db->escape($search)) . "%' or
             cet.name LIKE '%" . trim($db->escape($search)) . "%'");
		}
		
		$itemType = $this->getState('filter.item_type');
		if (!empty($itemType)) {
			$query->where("sla.item_type ='".$itemType."'");
		}
		
		$objectType = $this->getState('filter.object_type');
		if (!empty($objectType)) {
			$query->where("sla.object_type ='".$objectType."'");
		}

		// Filter offer orders by start and end date
		$startDate = $this->getState('filter.start_date');
		$endDate = $this->getState('filter.end_date');
		if (!empty($startDate) && !empty($endDate)) {
			$query->where("sla.date between '" . JBusinessUtil::convertToMysqlFormat($startDate) . " 00:00:00' and '" . JBusinessUtil::convertToMysqlFormat($endDate) . " 23:59:59'");
		} elseif (!empty($startDate)) {
			$query->where("sla.date >= '" . JBusinessUtil::convertToMysqlFormat($startDate) . " 00:00:00'");
		} elseif (!empty($endDate)) {
			$query->where("sla.date <= '" . JBusinessUtil::convertToMysqlFormat($endDate) . " 23:59:59'");
		}

		$query->group('sla.id');

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'sla.date');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = "sla.id", $direction = "desc") {
		$app = JFactory::getApplication('administrator');
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$val = $app->getUserStateFromRequest($this->context.'.filter.item_type', 'filter_item_type');
		$this->setState('filter.item_type', $val);

		$val = $app->getUserStateFromRequest($this->context.'.filter.object_type', 'filter_object_type');
		$this->setState('filter.object_type', $val);

		$val = $app->getUserStateFromRequest($this->context.'.filter.start_date', 'filter_start_date');
		$this->setState('filter.start_date', $val);

		$val = $app->getUserStateFromRequest($this->context.'.filter.end_date', 'filter_end_date');
		$this->setState('filter.end_date', $val);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdir', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	public function getSearchItemTypes() {
		return array(
			SEARCH_LOG_KEYWORD => JText::_('LNG_SEARCH_LOG_KEYWORD'),
			SEARCH_LOG_CATEGORY => JText::_('LNG_SEARCH_LOG_CATEGORY'),
			SEARCH_LOG_TYPE => JText::_('LNG_SEARCH_LOG_TYPE'),
			SEARCH_LOG_LOCATION => JText::_('LNG_SEARCH_LOG_LOCATION'),
			SEARCH_LOG_COUNTRY => JText::_('LNG_SEARCH_LOG_COUNTRY'),
			SEARCH_LOG_PROVINCE => JText::_('LNG_SEARCH_LOG_PROVINCE'),
			SEARCH_LOG_REGION => JText::_('LNG_SEARCH_LOG_REGION'),
			SEARCH_LOG_CITY => JText::_('LNG_SEARCH_LOG_CITY'),
			SEARCH_LOG_CUSTOM_ATTRIBUTE => JText::_('LNG_SEARCH_LOG_CUSTOM_ATTRIBUTE'),
			SEARCH_LOG_MIN_PRICE => JText::_('LNG_SEARCH_LOG_MIN_PRICE'),
			SEARCH_LOG_MAX_PRICE => JText::_('LNG_SEARCH_LOG_MAX_PRICE'),
			SEARCH_LOG_START_DATE => JText::_('LNG_SEARCH_LOG_START_DATE'),
			SEARCH_LOG_END_DATE => JText::_('LNG_SEARCH_LOG_END_DATE')
		);
	}

	public function getSearchObjectTypes() {
		return array(
			SEARCH_LOG_TYPE_LISTING => JText::_('LNG_SEARCH_LOG_TYPE_LISTING'),
			SEARCH_LOG_TYPE_OFFER => JText::_('LNG_SEARCH_LOG_TYPE_OFFER'),
			SEARCH_LOG_TYPE_EVENT => JText::_('LNG_SEARCH_LOG_TYPE_EVENT')
		);
	}

	/**
	 * Prepare the CSV data
	 *
	 * @return string
	 * @throws Exception
	 * @since 5.4.0
	 */
	public function getSearchCSV() {
		$jinput = JFactory::getApplication()->input;
		$delimiter = $jinput->getString("delimiter", ",");

		$csv_output = '"' . JText::_('LNG_SEARCHED_ON') . '"' . $delimiter . '"' . JText::_('LNG_SEARCH_TYPE') . '"' . $delimiter .
			'"' . JText::_('LNG_DATE') . '"' . $delimiter . '"' . JText::_('LNG_SEARCHED_FOR') . '"' . $delimiter . '"' . JText::_('LNG_NUMBER_OF_SEARCHES') . '"';
		$csv_output .= "\n";

		$items = $this->getItems();
		if (count($items) > 0) {
			foreach ($items as $item) {
				$date = JBusinessUtil::getDateGeneralFormat($item->date);
				$csv_output .= "\"$item->object_type\"" . $delimiter . "\"$item->item_type\"" . $delimiter
					. "\"$date\"" . $delimiter . "\"$item->itemName\"" . $delimiter . "\"$item->item_count\"";

				$csv_output .= "\n";
			}
		}

		return $csv_output;
	}

	/**
	 * Create and print the csv file
	 * @throws Exception
	 * @since 5.4.0
	 */
	public function exportSearchCsv() {
		$csv_output = $this->getSearchCSV();

		$fileName = "jbusinessdirectory_search_log";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=".$fileName.".csv");
		print $csv_output;
	}

	public function deleteByDate() {
		$jinput    = JFactory::getApplication()->input;
		$startDate = JBusinessUtil::convertToMysqlFormat($jinput->getString('startDate'));
		$endDate =  JBusinessUtil::convertToMysqlFormat($jinput->getString('endDate'));

		$statisticTable = JTable::getInstance("Searchlog", "JTable");
		if(!$statisticTable->deleteByDate($startDate, $endDate)) {
			return false;
		}
		
		return true;
			
	}

	/**
	 * Method to delete search Logs.
	 *
	 * @param $itemIds array  An array of item ids.
	 * @return bool Returns true on success, false on failure.
	 * @since 5.4.0
	 */
	public function delete(&$itemIds) {
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		ArrayHelper::toInteger($itemIds);

		// Get a group row instance.
		$table = $this->getTable();

		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId) {
			if (!$table->delete($itemId)) {
				$this->setError($table->getError());
				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}
	
	/**
	 * Get the archived search logs
	 *
	 * @return boolean
	 */
	public function getArchiveSearchLogs(){
		$table = $this->getTable('SearchLog', 'JTable');
		$dataToArchive = $table->getLogsToArchive(0,500);
		$result = true;
		while (!empty($dataToArchive)) {
			$result = $table->archiveSearchLogs($dataToArchive);
			$dataToArchive = $table->getLogsToArchive(0,500);
		}
		return $result;
	}
}
