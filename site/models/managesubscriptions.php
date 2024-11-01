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
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'subscriptions.php');

class JBusinessDirectoryModelManageSubscriptions extends JBusinessDirectoryModelSubscriptions {
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Subscription', $prefix = 'JTable', $config = array()) {
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
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JBusinessUtil::getUser();

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'sb.*'));
		$query->from($db->quoteName('#__jbusinessdirectory_subscriptions').' AS sb');

		// Join over the companies
		$query->select(' c.name as company_name');
		$query->join('LEFT', $db->quoteName('#__jbusinessdirectory_companies') . ' AS c ON c.id=sb.company_id');

		$query->group('sb.id');
		
		$query->where("c.userId = $user->id");

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'sb.id');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'sb.id')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}
}