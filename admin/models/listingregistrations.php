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
class JBusinessDirectoryModelListingRegistrations extends JModelList {
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
				'id', 'cj.id',
				'status', 'cj.approved',
				'company_id','cj.company_id',
				'joined_company_id','cj.joined_company_id'
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
	protected function getListQuery($userId = null) {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select all fields from the table.
		$query->select($this->getState('list.select', 'cj.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_company_registered').' AS cj');

		$query->select('cp.name as mainCompanyName');
		$query->leftJoin($db->quoteName('#__jbusinessdirectory_companies') . ' AS cp ON cp.id=cj.company_id');

		$query->select('cp1.name as joinedCompanyName');
		$query->leftJoin($db->quoteName('#__jbusinessdirectory_companies') . ' AS cp1 ON cp1.id=cj.joined_company_id');

		$query->select('us.display_name as userName');
		$query->join('LEFT', $db->quoteName('#__users').' AS us ON us.id=cj.user_id');

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where("cp.name LIKE '%" . trim($db->escape($search)) . "%' or cp1.name LIKE '%" . trim($db->escape($search)) . "%' 
              or us.display_name LIKE '%" . trim($db->escape($search)) . "%' or us.display_name LIKE '%" . trim($db->escape($search)) . "%' 
             ");
		}

		if (!empty($userId)) {
			//owner of main Listing
			$query->where("cp.userId = '" . $userId . "' ");
		}

		$query->group('cj.id');

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'cj.company_id');
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
	protected function populateState($ordering = 'cj.id', $direction = 'ASC') {
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

	public function changeAprovalState($id, $state) {
		$table = $this->getTable('RegisteredCompany', 'JTable');
		return $table->changeState($id, $state);
	}

	public function delete(&$itemIds) {
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		ArrayHelper::toInteger($itemIds);
	
		// Get a group row instance.
		$table = $this->getTable('RegisteredCompany', 'JTable');
	
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
}
