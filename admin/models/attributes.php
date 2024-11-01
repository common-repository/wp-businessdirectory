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
 * @subpackage com_jbusinessdirectory
 */
class JBusinessDirectoryModelAttributes extends JModelList {
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
				'id', 'a.id',
				'title', 'a.name',
				'name', 'at.name',
				'status', 'a.status',
				'attribute_type', 'a.attribute_type',
				'ordering', 'a.ordering'
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
		$query->select($this->getState('list.select', 'a.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_attributes').' AS a');
		
		$query->select($this->getState('list.select', 'at.name as attributeTypeName'));
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_attribute_types').' AS at on at.id=a.type');
		
		$query->select($this->getState('list.select', 'GROUP_CONCAT(DISTINCT ao.name ORDER BY ao.id asc) as options'));
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_attribute_options').' AS ao on ao.attribute_id = a.id');


		$attributeType = $this->getState('filter.attribute_type');
		if (is_numeric($attributeType) && $attributeType != 0) {
			$query->where('a.attribute_type ='.(int) $attributeType);
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where("(a.name LIKE '%".trim($db->escape($search))."%' or 
							at.name LIKE '%".trim($db->escape($search))."%')");
		}

		$stateId = $this->getState('filter.state_id');
		if (is_numeric($stateId)) {
			$query->where('a.status ='.(int) $stateId);
		}

		$query->group('a.id');

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'a.ordering');
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
	protected function populateState($ordering = 'a.id', $direction = 'desc') {
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$stateId = $app->getUserStateFromRequest($this->context.'.filter.state_id', 'filter_state_id');
		$this->setState('filter.state_id', $stateId);

		$attributeType = $app->getUserStateFromRequest($this->context.'.filter.attribute_type', 'filter_attribute_type', ATTRIBUTE_TYPE_BUSINESS);
		$this->setState('filter.attribute_type', $attributeType);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdir', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState($ordering, $direction);
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

	public function getAttributeTypes() {
		$states = array();
		$state = new stdClass();
		$state->value = 1;
		$state->text = JTEXT::_("LNG_COMPANY");
		$states[] = $state;

		$state = new stdClass();
		$state->value = 2;
		$state->text = JTEXT::_("LNG_OFFER");
		$states[] = $state;

		$state = new stdClass();
		$state->value = 3;
		$state->text = JTEXT::_("LNG_EVENT");
		$states[] = $state;

		$state = new stdClass();
		$state->value = 4;
		$state->text = JTEXT::_("LNG_VIDEO");
		$states[] = $state;

		return $states;
	}
}
