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
 * Company Messages Model
 *
 * @package     JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryModelMessages extends JModelList {
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
				'id', 'cm.id',
				'name', 'cm.name',
				'surname', 'cm.surname',
				'email', 'cm.email',
				'message', 'cm.message',
				'type', 'cm.type',
				'companyName', 'bc.name',
				'contactName', 'cc.contact_name'
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$typeId = $app->getUserStateFromRequest($this->context . '.filter.type', 'filter_type');
		$this->setState('filter.type', $typeId);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState('cm.id', 'DESC');
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
		// Create a new query object
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'cm.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_messages') . ' AS cm');

		$query->select('bc.name as companyName');
		$query->leftJoin($db->quoteName('#__jbusinessdirectory_companies') . ' AS bc ON bc.id=cm.item_id and cm.type="'.MESSAGE_TYPE_BUSINESS.'"');

		$query->select('cc.contact_name as contactName, cc.contact_email as contactEmail');
		$query->leftJoin($db->quoteName('#__jbusinessdirectory_company_contact') . ' AS cc ON cc.id=cm.contact_id');

		$query->select('co.subject as offerName');
		$query->leftJoin($db->quoteName('#__jbusinessdirectory_company_offers') . ' AS co ON co.id=cm.item_id and cm.type="'.MESSAGE_TYPE_OFFER.'"');

		$query->select('ev.name as eventName');
		$query->leftJoin($db->quoteName('#__jbusinessdirectory_company_events') . ' AS ev ON ev.id=cm.item_id and cm.type="'.MESSAGE_TYPE_EVENT.'"');

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$val = trim($db->escape($search));
			$query->where("bc.name LIKE '%" . $val . "%' OR cm.name LIKE '%" . $val . "%' OR cm.surname LIKE '%" . $val . "%' OR cm.email LIKE '%" . $val . "%'
             OR cc.contact_name LIKE '%" . $val . "%' OR co.subject LIKE '%" . $val . "%' OR ev.name LIKE '%" . $val . "%' ");
		}

		$typeId = $this->getState('filter.type');
		if (!empty($typeId)) {
			$query->where('cm.type='.$typeId);
		}

		$query->group('cm.id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'cm.date')) . ' ' . $db->escape($this->getState('list.direction', 'DESC')));

		return $query;
	}

	public function getUnreadMessages() {
		$table = JTable::getInstance("Messages", "JTable");
		$result = $table->getTotalUnreadMessages();

		return $result;
	}
}
