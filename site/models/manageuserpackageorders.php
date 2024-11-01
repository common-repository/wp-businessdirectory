<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.'com_jbusinessdirectory'.DS.'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'orders.php');

class JBusinessDirectoryModelManageUserPackageOrders extends JBusinessDirectoryModelOrders {
	public function __construct() {
		parent::__construct();
		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->_total = 0;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param string $type The table type to instantiate
	 * @param string $prefix A prefix for the table class name. Optional.
	 * @param array $config Configuration array for model. Optional.
	 * @return bool|JTable A database object
	 */
	public function getTable($type = 'Orders', $prefix = 'JTable', $config = array()) {
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
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
        $user = JBusinessUtil::getUser();

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'inv.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_orders') . ' AS inv');

		// Join over the company types
		$query->select('p.name as packageName');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_packages') . ' AS p ON p.id=inv.package_id');

        $query->where("inv.company_id = -1 and inv.user_id = $user->ID");

		// Add the list ordering clause.
		$query->order('inv.id asc');

		return $query;
	}
}
