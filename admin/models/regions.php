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

/**
 * Regions List Model.
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory

 */
class JBusinessDirectoryModelRegions extends JModelList {
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
				'id', 'rg.id',
				'name', 'rg.name',
				'country', 'cnt.country_name',
				'ordering', 'rg.ordering'
			);
		}

		parent::__construct($config);
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
		$query->select($this->getState('list.select', 'rg.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_regions').' AS rg');

		$query->select('cnt.country_name as country');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_countries') . ' AS cnt ON cnt.id = rg.country_id');

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where("rg.name LIKE '%".trim($db->escape($search))."%'");
		}

		$query->group('rg.id');

		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'rg.id');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));

		$query->order($db->escape($listOrdering) . ' ' . $listDirn);

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
	protected function populateState($ordering = 'rg.id', $direction = 'desc') {
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdir', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param	array	An array of primary key ids.
	 * @param	int		+/-1
	 */
	public function saveorder($pks, $order) {
		// Initialise variables.
		$table		= JTable::getInstance('Region');
		$conditions	= array();
	
		if (empty($pks)) {
			return JFactory::getApplication()->enqueueMessage(JText::_('COM_USERS_ERROR_LEVELS_NOLEVELS_SELECTED'), 'warning');
		}
	
		// update ordering values
		foreach ($pks as $i => $pk) {
			$table->load((int) $pk);
	
			// Access checks.
			$allow = true;//$user->authorise('core.edit.state', 'com_users');
	
			if (!$allow) {
				// Prune items that you can't change.
				unset($pks[$i]);
				JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'warning');
			} elseif ($table->ordering != $order[$i]) {
				$table->ordering = $order[$i];
				if (!$table->store()) {
					$this->setError($table->getError());
					return false;
				}
			}
		}
	
		// Execute reorder for each category.
		foreach ($conditions as $cond) {
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}
	
		return true;
	}

	/**
	 * Get all regions or by country (based on country ID)
	 *
	 * @param null $countryId int ID of the country
	 *
	 * @return mixed
	 *
	 * @since 4.9.4
	 */
	public function getRegions($countryId = null) {
		$table = $this->getTable('Region', 'JTable');

		if (empty($countryId)) {
			$regions = $table->getRegions();
		} else {
			$regions = $table->getRegionsByCountry($countryId);
		}

		return $regions;
	}
	
	public function getRegionsAndCitiesCSV() {
		$jinput    = JFactory::getApplication()->input;
		$delimiter = $jinput->getString("delimiter", ",");

		$cityTable = JTable::getInstance("City", "JTable");
		$cities = $cityTable->getCitiesForExport();

		$csv_output = "country" . $delimiter ."region" . $delimiter . "city";
        $csv_output .= "\n";

		foreach($cities as $city){
			$regionTable = JTable::getInstance("Region", "JTable");
			$region = $regionTable->getRegion($city->regionId);

			$countryTable = JTable::getInstance("Country", "JTable");
			$country = $countryTable->getCountry($region->country_id);
			
			$csv_output .= "\"$country->country_name\"" . $delimiter . "\"$region->name\"" . $delimiter . "\"$city->cityName\"";
			$csv_output .= "\n";
		}

		return $csv_output;
	}

	public function exportRegionsAndCitiesCSV() {
		$csv_output = $this->getRegionsAndCitiesCSV();

		$fileName = "jbusinessdirectory_regions_and_cities";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=".$fileName.".csv");
		print $csv_output;
	}
}
