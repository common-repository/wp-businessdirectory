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
class JBusinessDirectoryModelSubscriptions extends JModelList {
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
				'id', 'sb.id',
				"subscription_id", 'sb.subscription_id',
				"company", 'c.name',
                "amount", 'sb.amount',
				"created", 'sb.created',
				"paid_at", 'sb.start_date',
				"end_date", 'sb.end_date',
				"processor_type", 'sb.processor_type',
				"state", 'sb.state'
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
		$query->select($this->getState('list.select', 'sb.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_subscriptions').' AS sb');

		// Join over the companies
		$query->select(' c.name as company_name');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_companies') . ' AS c ON c.id=sb.company_id');
		
		$statusId = $this->getState('filter.state_id');
		if (is_numeric($statusId)) {
			$query->where("sb.status =" . (int) $statusId);
		}

		$companyId = $this->getState('filter.company_id');
		if (is_numeric($companyId)) {
			$query->where("sb.company_id =" . (int) $companyId);
		}

        $processorType = $this->getState('filter.processor_type');
		if (!empty($processorType)) {
			$query->where("sb.processor_type ='" . $processorType . "'");
		}

		$query->group('sb.id');

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'sb.id');
		$orderDirn = $this->state->get('list.direction', 'ASC');

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
	protected function populateState($ordering = 'sb.id', $direction = 'desc') {
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$statusId = $app->getUserStateFromRequest($this->context . '.filter.state_id', 'filter_state_id');
		$this->setState('filter.state_id', $statusId);

		$companyId = $app->getUserStateFromRequest($this->context . '.filter.company_id', 'filter_company_id');
		$this->setState('filter.company_id', $companyId);

		$packageId = $app->getUserStateFromRequest($this->context . '.filter.processor_type', 'filter_processor_type');
		$this->setState('filter.processor_type', $packageId);

		$startDate = $this->getUserStateFromRequest($this->context . '.filter.start_date', 'filter_start_date', '');
		$this->setState('filter.start_date', $startDate);

		$endDate = $this->getUserStateFromRequest($this->context . '.filter.end_date', 'filter_end_date', '');
		$this->setState('filter.end_date', $endDate);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context . '.orderdir', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState($ordering, $direction);
	}
}
