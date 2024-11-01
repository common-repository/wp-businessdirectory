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
 * List Model.
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory

 */
class JBusinessDirectoryModelCities extends JModelList {
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
				'id', 'co.id',
				'name', 'co.name',
				'region', 'rg.name',
				'ordering', 'co.ordering'
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
		$query->select($this->getState('list.select', 'co.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_cities').' AS co');

		$query->select('rg.name as region');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_regions') . ' AS rg ON rg.id = co.region_id');
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where("co.name LIKE '%".trim($db->escape($search))."%'");
		}
	
		$query->group('co.id');

		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'co.id');
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
	protected function populateState($ordering = 'co.id', $direction = 'desc') {
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$statusId = $app->getUserStateFromRequest($this->context.'.filter.status_id', 'filter_status_id');
		$this->setState('filter.status_id', $statusId);

		$stateId = $app->getUserStateFromRequest($this->context.'.filter.state_id', 'filter_state_id');
		$this->setState('filter.state_id', $stateId);
	
		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);
	
		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdir', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		parent::populateState($ordering, $direction);
	}
	
	public function getCompanyTypes() {
		$companiesTable = $this->getTable("Company");
		return $companiesTable->getCompanyTypes();
	}
	
	public function getStates() {
		$states = array();
		$state = new stdClass();
		$state->value = 0;
		$state->text = JTEXT::_("LNG_INACTIVE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 1;
		$state->text = JTEXT::_("LNG_ACTIVE");
		$states[] = $state;
	
		return $states;
	}
	
	public function getStatuses() {
		$statuses = array();
		$status = new stdClass();
		$status->value = 0;
		$status->text = JTEXT::_("LNG_NEEDS_CREATION_APPROVAL");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = -1;
		$status->text = JTEXT::_("LNG_DISAPPROVED");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = 1;
		$status->text = JTEXT::_("LNG_APPROVED");
		$statuses[] = $status;
	
		return $statuses;
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param	array	An array of primary key ids.
	 * @param	int		+/-1
	 */
	public function saveorder($pks, $order) {
		// Initialise variables.
		$table		= JTable::getInstance('City');
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
	 * Get all cities or by region (based on region ID)
	 *
	 * @param null $regionId int ID of the region
	 *
	 * @return mixed
	 *
	 * @since 4.9.4
	 */
	public function getCities($regionParam = null) {
		$table = $this->getTable('City', 'JTable');
		if(is_numeric($regionParam)){
			if (is_array($regionParam)) {
				$regionParam = implode(",", $regionParam);
			}
			$cities = $table->getCitiesByRegions($regionParam);
		} else {
			if (empty($regionParam)) {
				$cities = $table->getCities();
			} else {
				$cities = $table->getCitiesByRegionName($regionParam);
			}
		}
		return $cities;
	}
}
